<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\StockEntryItem;
use App\Models\ItemPrice;
use Illuminate\Support\Facades\Log;

class OrderaxeService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.orderaxe.base_url');
        $this->clientId = config('services.orderaxe.client_id');
        $this->clientSecret = config('services.orderaxe.client_secret');
    }

    public function getToken()
    {
        $response = Http::withOptions(['verify' => false])->post($this->baseUrl . '/script/generateJwt', [
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret
        ]);

        if ($response->successful()) {
            return $response->json('token');
        }

        Log::error('Orderaxe Auth Failed', ['response' => $response->body()]);
        return null;
    }

    public function fetchOrders($timestamp = 0)
    {
        $token = $this->getToken();
        if (!$token) return null;

        $authHeader = (stripos($token, 'Bearer ') === 0) ? $token : 'Bearer ' . $token;

        $response = Http::withOptions(['verify' => false])
            ->withHeaders([
                'Authorization' => $authHeader,
                'Accept' => 'application/json'
            ])
            ->get($this->baseUrl . '/tpc/v1/orders', [
                'timestamp' => $timestamp
            ]);

        if ($response->successful()) {
            return $response->json('data');
        }

        Log::error('Orderaxe Fetch Orders Failed', ['response' => $response->body()]);
        return null;
    }

    public function syncOrders($limit = null)
    {
        $orders = $this->fetchOrders(0);

        if (!$orders) return 0;

        if ($limit) {
            $orders = array_slice($orders, 0, $limit);
        }

        $syncCount = 0;
        foreach ($orders as $orderData) {
            if ($this->processOrder($orderData)) {
                $syncCount++;
            }
        }

        return $syncCount;
    }

    protected function processOrder($orderData)
    {
        try {
            $orderAxeId = $orderData['_id'] ?? null;
            $orderNo = $orderData['order_no'] ?? null;

            if (!$orderNo) return false;

            if ($orderAxeId && SalesOrder::where('orderaxe_id', $orderAxeId)->exists()) {
                return false;
            }

            if (!$orderAxeId && SalesOrder::where('so_no', $orderNo)->exists()) {
                return false;
            }
            $customerData = $orderData['retailer'] ?? [];
            $customerName = $customerData['alias']['name'] ?? ($customerData['org']['name'] ?? 'Unknown Orderaxe Customer');
            $cleanedCustomerName = trim(preg_replace('/\s*\(.*\)/', '', $customerName));
            $customer = Customer::where('name', 'Like', '%' . $cleanedCustomerName . '%')->first();
            if (!$customer) {
                Log::warning('Orderaxe Sync: Customer not found. Skipping order.', [
                    'customer_name' => $customerName,
                    'order_no' => $orderNo
                ]);
                return false;
            }

            $salesOrder = SalesOrder::create([
                'so_no'        => $orderNo,
                'orderaxe_id'  => $orderAxeId,
                'so_date'      => date('Y-m-d', ($orderData['created_at'] / 1000)),
                'request_date' => date('Y-m-d', ($orderData['created_at'] / 1000)),
                'customer_id'  => $customer->id,
                'status'       => 'Pending',
                'total_qty'    => $orderData['overall_quantity'] ?? 0,
                'total_amount' => $orderData['total_amount'] ?? 0,
                'order_type'   => 'Customer Order',
                'store_id'     => null,
                'season_id'    => null,
                'zone_id'      => null,
                'created_by'   => 1,
            ]);

            $combinations = $orderData['combinations'] ?? [];
            $totalQty = 0;
            $totalAmount = 0;

            foreach ($combinations as $itemData) {
                $barcode = $itemData['barcode'] ?? null;
                $qty = $itemData['quantity'] ?? 0;
                $stockEntryItem = null;
                $rate = $itemData['mrp'] ?? 0;
                $artNo = null;

                $attributes = $itemData['attributes'] ?? [];
                $apiSize = $attributes[0]['val'] ?? null;
                $apiColor = $attributes[1]['val'] ?? null;
                $apiFit = $attributes[2]['val'] ?? null;

                if ($barcode) {
                    $barcode = trim($barcode);
                    $stockEntryItem = StockEntryItem::with('item')->where('sku', $barcode)->where('stock_type', 'finished_goods')->first();
                    if ($stockEntryItem) {
                        $artNo = $stockEntryItem->art_no;
                        $itemPrice = ItemPrice::where('finished_item_code', $stockEntryItem->finished_item_code)->where('status', 'Active')->where('effective_from', '<=', date('Y-m-d'))->orderBy('effective_from', 'desc')->first();
                        
                        if ($itemPrice) {
                            $rate = $itemPrice->selling_price;
                        }
                    } else {
                        Log::warning('Orderaxe Sync: Barcode match failed for item.', [
                            'barcode' => $barcode,
                            'order_no' => $orderNo,
                            'variation' => $itemData['variationDescription'] ?? 'N/A'
                        ]);
                    }
                }

                $amount = $qty * $rate;
                $totalQty += $qty;
                $totalAmount += $amount;

                SalesOrderItem::create([
                    'sale_order_id' => $salesOrder->id,
                    'item_id' => $stockEntryItem->item_id ?? null,
                    'brand_cat_id' => $stockEntryItem->item->brand_category_id ?? null,
                    'qty' => $qty,
                    'rate' => $rate,
                    'mrp' => $itemData['mrp'] ?? 0,
                    'amount' => $amount,
                    'art_no' => $artNo,
                    'stock_entry_item_id' => $stockEntryItem->id ?? null,
                    'sku' => $barcode,
                    'color_id' => $stockEntryItem->color_id ?? null,
                    'uom_id' => $stockEntryItem->uom_id ?? null,
                    'size_id' => $stockEntryItem->size_ratio_id ?? null,
                    'sleeve' => $apiFit ? [$apiFit] : ($stockEntryItem->sleeve_type ? [$stockEntryItem->sleeve_type] : null),
                ]);
            }

            $salesOrder->update([
                'total_qty' => $totalQty,
                'total_amount' => $totalAmount,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Orderaxe Sync Error for Order ' . ($orderData['order_no'] ?? 'Unknown'), [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
