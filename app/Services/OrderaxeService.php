<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\StockEntryItem;
use App\Models\ItemPrice;
use App\Models\SalesAgent;
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

    public function fetchOrders($timestamp = 0, $limit = 100)
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
                'timestamp' => $timestamp,
                'limit' => $limit
            ]);

        if ($response->successful()) {
            return $response->json('data');
        }

        Log::error('Orderaxe Fetch Orders Failed', ['response' => $response->body()]);
        return null;
    }

    public function syncOrders($limit = 100)
    {
        $orders = $this->fetchOrders(0, $limit);

        if (!$orders)
            return 0;

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

            if (in_array($orderNo, ['100008782', '100008821'])) return false;

            $effectiveDateMs = $orderData['updated_at'] ?? $orderData['created_at'] ?? 0;
            $orderDate = date('Y-m-d');
            if ($effectiveDateMs > 0) {
                $orderDate = date('Y-m-d', (int)($effectiveDateMs / 1000));
                if ($orderDate < '2026-07-02') {
                    return false;
                }
            }

            $existingOrder = null;
            if ($orderAxeId) {
                $existingOrder = SalesOrder::where('orderaxe_id', $orderAxeId)->first();
            }
            if (!$existingOrder && $orderNo) {
                $existingOrder = SalesOrder::where('order_no', $orderNo)->first();
            }

            $customerData = $orderData['retailer'] ?? [];
            $referenceId = $customerData['reference_id'] ?? null;
            $customer = null;

            if (!empty($referenceId)) {
                $customer = Customer::where('code', $referenceId)->first();
            }

            if (!$customer) {
                $customerName = $customerData['alias']['name'] ?? ($customerData['org']['name'] ?? 'Unknown Orderaxe Customer');
                $cleanedCustomerName = trim(preg_replace('/\s*\(.*\)/', '', $customerName));
                $customer = Customer::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($cleanedCustomerName) . '%'])->first();
            }

            if (!$customer) {
                $customerName = $customerData['alias']['name'] ?? ($customerData['org']['name'] ?? 'Unknown Orderaxe Customer');
                Log::warning('Orderaxe Sync: Customer not found. Skipping order.', [
                    'customer_name' => $customerName,
                    'reference_id' => $referenceId,
                    'order_no' => $orderNo
                ]);
                return false;
            }

            $agentId = null;
            if (!empty($orderData['distributor']['name'])) {
                $distributorName = trim($orderData['distributor']['name']);
                $salesAgent = SalesAgent::whereRaw('LOWER(name) = ?', [strtolower($distributorName)])->first();
                if ($salesAgent) {
                    $agentId = $salesAgent->id;
                }
            }

            $zoneId = null;
            if (!empty($orderData['distributor']['reference_id'])) {
                $refZone = trim($orderData['distributor']['reference_id']);
                if (preg_match('/Zone-(\d+)/i', $refZone, $matches)) {
                    $num = $matches[1];
                    if ($num == 1) {
                        $zoneModel = \App\Models\Zone::where('zone_name', 'like', '%Zone%I%')->orWhere('zone_name', 'like', '%Zone%1%')->first();
                    } else {
                        $zoneModel = \App\Models\Zone::where('zone_name', 'like', '%Zone%' . $num . '%')->first();
                    }
                    if ($zoneModel) {
                        $zoneId = $zoneModel->id;
                    }
                }
            }

            if (!$zoneId && $customer) {
                $zoneId = $customer->zone_id;
            }

            $orderaxeRefId = $orderData['retailer']['reference_id'] ?? null;
            $deliveryDate = null;
            if (!empty($orderData['delivery_date'])) {
                $deliveryDate = date('Y-m-d', (int)($orderData['delivery_date'] / 1000));
            }

            if ($existingOrder) {
                $updateData = [];
                if ($existingOrder->customer_id != $customer->id) {
                    $updateData['customer_id'] = $customer->id;
                }
                if (is_null($existingOrder->agent_id) && $agentId) {
                    $updateData['agent_id'] = $agentId;
                }
                if (is_null($existingOrder->zone_id) && $zoneId) {
                    $updateData['zone_id'] = $zoneId;
                }
                
                $submittedBy = isset($orderData['updated_by']) && is_array($orderData['updated_by']) && count($orderData['updated_by']) > 0 ? $orderData['updated_by'][0]['name'] : null;
                if (is_null($existingOrder->submitted_by) && $submittedBy) {
                    $updateData['submitted_by'] = $submittedBy;
                }

                if (is_null($existingOrder->orderaxe_ref_id) && $orderaxeRefId) {
                    $updateData['orderaxe_ref_id'] = $orderaxeRefId;
                }
                
                if (is_null($existingOrder->delivery_date) && $deliveryDate) {
                    $updateData['delivery_date'] = $deliveryDate;
                }

                if (is_null($existingOrder->internal_remarks) && !empty($orderData['remarks'])) {
                    $updateData['internal_remarks'] = $orderData['remarks'];
                }

                if (!$existingOrder->so_date || $existingOrder->so_date->format('Y-m-d') !== $orderDate) {
                    $updateData['so_date'] = $orderDate;
                    $updateData['request_date'] = $orderDate;
                }

                if (!empty($updateData)) {
                    $existingOrder->update($updateData);
                }

                $products = $orderData['products'] ?? [];
                foreach ($products as $product) {
                    $combinations = $product['combinations'] ?? [];
                    foreach ($combinations as $itemData) {
                        $barcode = $itemData['sku'] ?? $itemData['barcode'] ?? null;
                        if (!$barcode) continue;

                        $attributes = $itemData['attributes'] ?? [];
                        $apiColor = null;
                        $apiSize = null;
                        $apiFit = null;
                        foreach ($attributes as $attr) {
                            $attrId = $attr['attr_id'] ?? null;
                            $attrName = strtolower($attr['name'] ?? $attr['key'] ?? '');
                            $attrVal = $attr['value'] ?? $attr['val'] ?? null;
                            if ($attrId === '672d9a34a4af6e35050547fe' || $attrName === 'color') {
                                $apiColor = $attrVal;
                            } elseif ($attrId === '672d9a34a4af6e35050547fd' || $attrName === 'size') {
                                $apiSize = $attrVal;
                            } elseif ($attrId === '672d9a34a4af6e35050547ff' || $attrName === 'fit' || $attrName === 'sleeve') {
                                $apiFit = $attrVal;
                            }
                        }

                        $orderaxeItemName = $product['name'] ?? $product['title'] ?? $product['product_name'] ?? $itemData['name'] ?? $itemData['title'] ?? $itemData['variationDescription'] ?? null;

                        $sleeveDbValues = [];
                        if ($apiFit) {
                            $apiFitUpper = strtoupper(trim($apiFit));
                            if ($apiFitUpper === 'FS' || $apiFitUpper === 'F/S' || $apiFitUpper === 'FULL') {
                                $sleeveDbValues = ['Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve'];
                            } elseif ($apiFitUpper === 'HS' || $apiFitUpper === 'H/S' || $apiFitUpper === 'HALF') {
                                $sleeveDbValues = ['Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve'];
                            } else {
                                $sleeveDbValues = [$apiFit];
                            }
                        }

                        $stockEntryItemQuery = StockEntryItem::where(function ($q) use ($barcode) {
                                $q->where('sku', $barcode)
                                  ->orWhere('barcode', $barcode);
                            })
                            ->where('stock_type', 'finished_goods');

                        if (!empty($apiSize)) {
                            $stockEntryItemQuery->where('size', $apiSize);
                        }

                        if (!empty($sleeveDbValues)) {
                            $stockEntryItemQuery->whereIn('sleeve_type', $sleeveDbValues);
                        }

                        $stockEntryItem = $stockEntryItemQuery->orderByRaw('(qty_in - qty_out) > 0 DESC')
                            ->orderBy('id', 'desc')
                            ->first();

                        $updateItemData = [];
                        
                        if ($apiColor) {
                            $updateItemData['api_color'] = $apiColor;
                        }

                        if ($orderaxeItemName) {
                            $updateItemData['item_name'] = $orderaxeItemName;
                        }

                        if ($stockEntryItem) {
                            $updateItemData['art_no'] = $stockEntryItem->art_no;
                        } else {
                            if ($orderaxeItemName) {
                                $updateItemData['art_no'] = strtoupper(trim(str_ireplace('Aero Cut', '', $orderaxeItemName)));
                            }
                        }

                        if (!empty($updateItemData)) {
                            SalesOrderItem::where('sale_order_id', $existingOrder->id)
                                ->where('sku', $barcode)
                                ->update($updateItemData);
                        }
                    }
                }
                
                return false;
            }

            $salesOrder = SalesOrder::create([
                'so_no'        => SalesOrder::generateSoNo(),
                'order_no'     => $orderNo,
                'orderaxe_id'  => $orderAxeId,
                'orderaxe_ref_id' => $orderaxeRefId,
                'so_date'      => $orderDate,
                'request_date' => $orderDate,
                'delivery_date'=> $deliveryDate,
                'customer_id'  => $customer->id,
                'agent_id'     => $agentId,
                'status'       => 'Pending',
                'submitted_by' => isset($orderData['updated_by']) && is_array($orderData['updated_by']) && count($orderData['updated_by']) > 0 ? $orderData['updated_by'][0]['name'] : null,
                'total_qty'    => $orderData['overall_quantity'] ?? 0,
                'total_amount' => $orderData['total_amount'] ?? 0,
                'order_type'   => 'Customer Order',
                'store_id'     => null,
                'season_id'    => null,
                'zone_id'      => $zoneId,
                'created_by'   => 1,
                'billing_address' => $this->formatAddress($customerData['alias']['address'] ?? []),
                'shipping_address' => $this->formatAddress($customerData['alias']['address'] ?? []),
                'internal_remarks' => $orderData['remarks'] ?? null, 
            ]);
            $products = $orderData['products'] ?? [];
            $totalQty = 0;
            $totalAmount = 0;

            foreach ($products as $product) {
                $combinations = $product['combinations'] ?? []; 
                foreach ($combinations as $itemData) {
                    $barcode = $itemData['barcode'] ?? null;    
                    $qty = $itemData['quantity'] ?? 0;         
                    $rate = $itemData['price'] ?? $itemData['rate'] ?? $itemData['mrp'] ?? 0;             
                    $artNo = null;
                    $stockEntryItem = null;

                    $attributes = $itemData['attributes'] ?? []; 
                    $apiSize = null;
                    $apiColor = null;
                    $apiFit = null;

                    foreach ($attributes as $attr) {
                        $attrId = $attr['attr_id'] ?? null;
                        $attrName = strtolower($attr['name'] ?? $attr['key'] ?? '');
                        $attrVal = $attr['value'] ?? $attr['val'] ?? null;

                        if ($attrId === '672d9a34a4af6e35050547fd' || $attrName === 'size') {
                            $apiSize = $attrVal;
                        } elseif ($attrId === '672d9a34a4af6e35050547fe' || $attrName === 'color') {
                            $apiColor = $attrVal;
                        } elseif ($attrId === '672d9a34a4af6e35050547ff' || $attrName === 'fit' || $attrName === 'sleeve') {
                            $apiFit = $attrVal;
                        }
                    }

                    $finishedItemCode = null;
                    $colorId = null;
                    $uomId = 'PCS';
                    $sizeId = null;
                    $sleeveType = null;
                    $barcodeMaster = null;

                    $orderaxeItemName = $product['name'] ?? $product['title'] ?? $product['product_name'] ?? $itemData['name'] ?? $itemData['title'] ?? $itemData['variationDescription'] ?? null;

                    if ($barcode) {
                        $barcode = trim($barcode);

                        $sleeveDbValues = [];
                        if ($apiFit) {
                            $apiFitUpper = strtoupper(trim($apiFit));
                            if ($apiFitUpper === 'FS' || $apiFitUpper === 'F/S' || $apiFitUpper === 'FULL') {
                                $sleeveDbValues = ['Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve'];
                            } elseif ($apiFitUpper === 'HS' || $apiFitUpper === 'H/S' || $apiFitUpper === 'HALF') {
                                $sleeveDbValues = ['Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve'];
                            } else {
                                $sleeveDbValues = [$apiFit];
                            }
                        }

                        $stockEntryItemQuery = StockEntryItem::where(function ($q) use ($barcode) {
                                $q->where('sku', $barcode)
                                  ->orWhere('barcode', $barcode);
                            })
                            ->where('stock_type', 'finished_goods');

                        if (!empty($apiSize)) {
                            $stockEntryItemQuery->where('size', $apiSize);
                        }

                        if (!empty($sleeveDbValues)) {
                            $stockEntryItemQuery->whereIn('sleeve_type', $sleeveDbValues);
                        }

                        $stockEntryItem = $stockEntryItemQuery->orderByRaw('(qty_in - qty_out) > 0 DESC')
                            ->orderBy('id', 'desc')
                            ->first();
                        
                        if ($stockEntryItem) {
                            $artNo = $stockEntryItem->art_no;
                            $finishedItemCode = $stockEntryItem->finished_item_code;
                            $colorId = $stockEntryItem->color_id;
                            $uomId = $stockEntryItem->uom_id ?? 'PCS';
                            $sizeId = $stockEntryItem->size ?? $apiSize;
                            $sleeveType = $stockEntryItem->sleeve_type;
                        } else {
                            $artNo = null;
                            if ($orderaxeItemName) {
                                $artNo = strtoupper(trim(str_ireplace('Aero Cut', '', $orderaxeItemName)));
                            }
                            
                            Log::warning('Orderaxe Sync: Exact StockEntryItem match failed (barcode + size + sleeve). Using Orderaxe name and generated Art No.', [
                                'barcode' => $barcode,
                                'size' => $apiSize,
                                'sleeve' => $apiFit,
                                'order_no' => $orderNo
                            ]);
                        }

                        if (!$sizeId) {
                            $sizeId = $apiSize;
                        }

                        if ($finishedItemCode && empty($itemData['price']) && empty($itemData['rate'])) {
                            $itemPrice = ItemPrice::where('finished_item_code', $finishedItemCode)
                                ->where('status', 'Active')
                                ->where('effective_from', '<=', date('Y-m-d'))
                                ->orderBy('effective_from', 'desc')
                                ->first();

                            if ($itemPrice) {
                                $rate = $itemPrice->selling_price;
                            }
                        }
                    }

                    $amount = $qty * $rate;
                    $totalQty += $qty;
                    $totalAmount += $amount;

                    SalesOrderItem::create([
                        'sale_order_id' => $salesOrder->id,
                        'item_id' => null,
                        'brand_cat_id' => null,
                        'category_name' => $product['category']['name'] ?? null,
                        'categories_path_val' => $product['categories_path_val'] ?? null,
                        'qty' => $qty,
                        'rate' => $rate,
                        'mrp' => $itemData['mrp'] ?? 0,
                        'amount' => $amount,
                        'art_no' => $artNo,
                        'item_name' => $orderaxeItemName,
                        'stock_entry_item_id' => $stockEntryItem->id ?? null,
                        'sku' => $barcode,
                        'color_id' => $colorId,
                        'api_color' => $apiColor,
                        'uom_id' => 'PCS',
                        'size_id' => $sizeId,
                        'sleeve' => $apiFit ? [$apiFit] : ($sleeveType ? [$sleeveType] : null),
                    ]);
                }
            }

            $setting = \App\Models\Setting::first();
            $businessStateId = $setting ? $setting->state_id : null;
            $customerStateId = $customer->state_id;

            $otherState = 0;
            $cgstPercent = 0.00;
            $sgstPercent = 0.00;
            $igstPercent = 0.00;

            if ($businessStateId && $customerStateId) {
                if ($businessStateId != $customerStateId) {
                    $otherState = 1;
                    $igstPercent = floatval($setting->igst ?? 18.00);
                } else {
                    $otherState = 0;
                    $cgstPercent = floatval($setting->cgst ?? 9.00);
                    $sgstPercent = floatval($setting->sgst ?? 9.00);
                }
            } else {
                $otherState = 0;
                $cgstPercent = floatval($setting->cgst ?? 9.00);
                $sgstPercent = floatval($setting->sgst ?? 9.00);
            }

            $totalTaxPercent = $otherState ? $igstPercent : ($cgstPercent + $sgstPercent);
            $taxAmount = round($totalAmount * ($totalTaxPercent / 100), 2);
            $grandTotal = $totalAmount + $taxAmount;

            $agentCommValue = 0;
            if ($agentId) {
                $agent = \App\Models\SalesAgent::find($agentId);
                if ($agent) {
                    $agentCommValue = (float) ($agent->commission_value ?? 0);
                }
            }
            $commAmount = $totalQty * $agentCommValue;

            $salesOrder->update([
                'total_qty' => $totalQty,
                'sub_total_qty' => $totalAmount,
                'commission_percent' => $agentCommValue,
                'commission_amount' => $commAmount,
                'taxable_amount' => $totalAmount,
                'other_state' => $otherState,
                'cgst_percent' => $cgstPercent,
                'sgst_percent' => $sgstPercent,
                'igst_percent' => $igstPercent,
                'tax_amount' => $taxAmount,
                'total_amount' => $grandTotal,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Orderaxe Sync Error for Order ' . ($orderData['order_no'] ?? 'Unknown'), [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    private function formatAddress(array $address): string
    {
        $parts = array_filter([
            $address['address_line1'] ?? '',
            $address['address_line2'] ?? '',
            $address['city'] ?? '',
        ]);

        $cleanedParts = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if (!empty($part)) {
                $lines = preg_split('/\r\n|\r|\n/', $part);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $lastPart = end($cleanedParts);
                        if (!$lastPart || strtolower($lastPart) !== strtolower($line)) {
                            if (!in_array(strtolower($line), array_map('strtolower', $cleanedParts))) {
                                $cleanedParts[] = $line;
                            }
                        }
                    }
                }
            }
        }

        return implode(', ', $cleanedParts);
    }
}
