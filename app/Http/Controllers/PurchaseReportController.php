<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PurchaseOrder;
use App\Models\Supplier;

class PurchaseReportController extends Controller
{
    public function fabricStore(Request $request)
    {
        if ($request->ajax() || $request->has('fetch_report')) {
            $purchaseOrders = $this->getPurchaseOrders(1, $request);
            $stockData = $this->getStockData(1, $request); 
            
            $html = [
                'po-report' => view('reports.purchase_reports._po_supplier_wise', [
                    'purchaseOrders' => $purchaseOrders,
                    'qtyLabel' => 'Meters'
                ])->render(),
                'stock-report' => view('reports.purchase_reports._stock_report', [
                    'stockData' => $stockData,
                    'isFabric' => true
                ])->render(),
                'ageing-report' => view('reports.purchase_reports._ageing_report', [
                    'ageingData' => $this->getStockAgeingData(1, $request),
                    'isFabric' => true
                ])->render(),
                'consumption-report' => view('reports.purchase_reports._consumption_report', [
                    'consumptionData' => $this->getConsumptionData($request)
                ])->render(),
                'minstock-report' => view('reports.purchase_reports._minstock_report', [
                    'minStockData' => $this->getMinStockData(1, $request),
                    'isFabric' => true
                ])->render(),
                'return-report' => view('reports.purchase_reports._return_report', [
                    'returnGoodsData' => $this->getReturnGoodsData(1, $request),
                    'isFabric' => true
                ])->render(),
                'performance-report' => view('reports.purchase_reports._supplier_performance', [
                    'performanceData' => $this->getFabricSupplierPerformanceData($request)
                ])->render(),
            ];

            return response()->json($html);
        }

        $suppliers = Supplier::where('status', 'Active')->get();
        return view('reports.purchase_reports.fabric_store', compact('suppliers'));
    }

    public function accessoriesStore(Request $request)
    {
        if ($request->ajax() || $request->has('fetch_report')) {
            $purchaseOrders = $this->getPurchaseOrders(2, $request);
            $stockData = $this->getStockData(2, $request); 
            
            $html = [
                'po-report' => view('reports.purchase_reports._po_supplier_wise', [
                    'purchaseOrders' => $purchaseOrders,
                    'qtyLabel' => 'Qty'
                ])->render(),
                'stock-report' => view('reports.purchase_reports._stock_report', [
                    'stockData' => $stockData,
                    'isFabric' => false
                ])->render(),
                'ageing-report' => view('reports.purchase_reports._ageing_report', [
                    'ageingData' => $this->getStockAgeingData(2, $request),
                    'isFabric' => false
                ])->render(),
                'cost-report' => view('reports.purchase_reports._cost_report', [
                    'costData' => $this->getAverageCostData($request)
                ])->render(),
                'minstock-report' => view('reports.purchase_reports._minstock_report', [
                    'minStockData' => $this->getMinStockData(2, $request),
                    'isFabric' => false
                ])->render(),
                'return-report' => view('reports.purchase_reports._return_report', [
                    'returnGoodsData' => $this->getReturnGoodsData(2, $request),
                    'isFabric' => false
                ])->render(),
                'performance-report' => view('reports.purchase_reports._supplier_performance', [
                    'performanceData' => $this->getAccessoriesSupplierPerformanceData($request)
                ])->render(),
            ];

            return response()->json($html);
        }

        $suppliers = Supplier::where('status', 'Active')->get();
        return view('reports.purchase_reports.accessories_store', compact('suppliers'));
    }

    private function buildSupplierPerformanceData($suppliers, Request $request)
    {
        $performanceData = [];
        foreach ($suppliers as $supplier) {
            $poQuery = $supplier->purchaseOrders();
            if ($request->from_date) {
                $poQuery->where('po_date', '>=', date('Y-m-d', strtotime($request->from_date)));
            }
            if ($request->to_date) {
                $poQuery->where('po_date', '<=', date('Y-m-d', strtotime($request->to_date)));
            }

            $dnQuery = $supplier->debitNotes();
            if ($request->from_date) {
                $dnQuery->where('debit_note_date', '>=', date('Y-m-d', strtotime($request->from_date)));
            }
            if ($request->to_date) {
                $dnQuery->where('debit_note_date', '<=', date('Y-m-d', strtotime($request->to_date)));
            }

            $poCount = $poQuery->count();
            $totalPoValue = $poQuery->sum('total_amount');
            $dnCount = $dnQuery->count();
            $returnRate = $poCount > 0 ? ($dnCount / $poCount) * 100 : 0;

            if ($poCount > 0 || $dnCount > 0) {
                $performanceData[] = [
                    'supplier_name' => $supplier->name,
                    'po_count' => $poCount,
                    'total_po_value' => $totalPoValue,
                    'dn_count' => $dnCount,
                    'return_rate' => number_format($returnRate, 2)
                ];
            }
        }
        return $performanceData;
    }

    private function getFabricSupplierPerformanceData(Request $request)
    {
        $query = Supplier::with(['purchaseOrders', 'debitNotes', 'storeType'])->where('status', 'Active')->whereHas('storeType', function($q) { $q->where('id', 1); }); 
        if ($request->supplier_id) {
            $query->where('id', $request->supplier_id);
        }
        $suppliers = $query->get();
        return $this->buildSupplierPerformanceData($suppliers, $request);
    }

    private function getAccessoriesSupplierPerformanceData(Request $request)
    {
        $query = Supplier::with(['purchaseOrders', 'debitNotes', 'storeType'])
            ->where('status', 'Active')
            ->whereHas('storeType', function($q) { $q->where('id', 2); }); 
        if ($request->supplier_id) {
            $query->where('id', $request->supplier_id);
        }
        $suppliers = $query->get();
        return $this->buildSupplierPerformanceData($suppliers, $request);
    }

    private function getAverageCostData(Request $request)
    {
        $query = \App\Models\PurchaseInvoiceItem::with(['rawMaterial', 'purchaseInvoice.supplier'])
            ->whereHas('rawMaterial', function($q) {
                $q->where('store_category_id', 2);
            });

        if ($request->from_date) {
            $query->whereHas('purchaseInvoice', function($q) use ($request) {
                $q->whereDate('invoice_date', '>=', date('Y-m-d', strtotime($request->from_date)));
            });
        }
        if ($request->to_date) {
            $query->whereHas('purchaseInvoice', function($q) use ($request) {
                $q->whereDate('invoice_date', '<=', date('Y-m-d', strtotime($request->to_date)));
            });
        }
        if ($request->supplier_id) {
            $query->whereHas('purchaseInvoice', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        $items = $query->get();
        $grouped = [];

        foreach ($items as $item) {
            $key = $item->raw_material_id;
            if (!$key) continue;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'item_name' => $item->rawMaterial ? $item->rawMaterial->name : 'N/A',
                    'total_qty' => 0,
                    'total_amount' => 0,
                ];
            }

            $grouped[$key]['total_qty'] += $item->quantity;
            $grouped[$key]['total_amount'] += $item->amount;
        }

        $result = [];
        foreach ($grouped as $g) {
            if ($g['total_qty'] > 0) {
                $g['average_cost'] = $g['total_amount'] / $g['total_qty'];
                $result[] = $g;
            }
        }

        return $result;
    }

    private function getPurchaseOrders($storeTypeId, Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'items.purchaseInvoiceItems'])->where('store_type_id', $storeTypeId);

        if ($request->from_date) {
            $query->whereDate('po_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $query->whereDate('po_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchaseOrders = $query->get();

        foreach ($purchaseOrders as $po) {
            $po->total_ordered = $po->items->sum('quantity');
            $po->total_received = 0;
            foreach ($po->items as $item) {
                $po->total_received += $item->purchaseInvoiceItems->sum('qty_received');
            }
            $po->total_pending = max(0, $po->total_ordered - $po->total_received);
        }

        return $purchaseOrders;
    }

    private function getStockData($storeCategoryId, Request $request)
    {
        $query = \App\Models\StockEntryItem::with(['rawMaterial', 'item.brand', 'brand', 'style', 'color', 'fabricType', 'fabricWidth'])->where('store_category_id', $storeCategoryId);

        if ($request->supplier_id) {
            $query->whereHas('grnEntryItem.grnEntry', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        $toDate = $request->to_date ? date('Y-m-d 23:59:59', strtotime($request->to_date)) : now();
        $query->where('created_at', '<=', $toDate);
        
        $stockItems = $query->get();

        $grouped = [];
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;

        foreach ($stockItems as $item) {
            $key = ($item->raw_material_id ?: 0) . '-' . ($item->item_id ?: 0) . '-' . ($item->style_id ?: 0) . '-' . ($item->color_id ?: 0) . '-' . ($item->fabric_type_id ?: 0) . '-' . ($item->size ?: '') . '-' . ($item->brand_id ?: 0) . '-' . ($item->fabric_width_id ?: 0);
            
            if (!isset($grouped[$key])) {
                $brandName = 'N/A';
                if ($item->brand) {
                    $brandName = $item->brand->brand_name;
                } elseif ($item->item && $item->item->brand) {
                    $brandName = $item->item->brand->name;
                }

                $itemName = 'N/A';
                if ($item->rawMaterial) {
                    $itemName = $item->rawMaterial->name;
                } elseif ($item->item) {
                    $itemName = $item->item->name;
                }

                $minStock = 0;
                if ($item->rawMaterial) {
                    $minStock = $item->rawMaterial->min_stock;
                } elseif ($item->item) {
                    $minStock = $item->item->min_stock ?? 0;
                }

                $grouped[$key] = [
                    'raw_material_id' => $item->raw_material_id ?: 0,
                    'brand' => $brandName,
                    'item_name' => $itemName,
                    'style' => $item->style ? $item->style->style_name : 'N/A',
                    'color' => $item->color ? $item->color->color_name : 'N/A',
                    'fabric_type' => $item->fabricType ? $item->fabricType->fabric_type : 'N/A',
                    'width' => $item->fabricWidth ? $item->fabricWidth->width : ($item->size ?: 'N/A'),
                    'min_stock' => $minStock,
                    'opening' => 0,
                    'inward' => 0,
                    'outward' => 0,
                    'closing' => 0,
                    'avg_cost'        => 0,
                    'closing_cost'    => 0,
                ];
            }

            $date = $item->created_at->format('Y-m-d');

            if ($fromDate && $date < $fromDate) {
                $grouped[$key]['opening'] += ($item->qty_in - $item->qty_out);
            } else {
                $grouped[$key]['inward'] += $item->qty_in;
                $grouped[$key]['outward'] += $item->qty_out;
            }
        }
        $rawMaterialIds = collect($grouped)->pluck('raw_material_id')->filter()->unique()->toArray();
        $avgCosts = [];
        if (!empty($rawMaterialIds)) {
            $costQuery = \App\Models\PurchaseInvoiceItem::whereIn('raw_material_id', $rawMaterialIds);

            if ($request->to_date) {
                $costQuery->whereHas('purchaseInvoice', function($q) use ($request) {
                    $q->whereDate('invoice_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                });
            }

            $costItems = $costQuery->select(
                'raw_material_id',
                \Illuminate\Support\Facades\DB::raw('SUM(amount) as total_amount'),
                \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_qty')
            )->groupBy('raw_material_id')->get();

            foreach ($costItems as $c) {
                $avgCosts[$c->raw_material_id] = $c->total_qty > 0
                    ? (float) $c->total_amount / (float) $c->total_qty
                    : 0;
            }
        }

        foreach ($grouped as &$group) {
            $group['closing']      = $group['opening'] + $group['inward'] - $group['outward'];
            $group['avg_cost']     = $avgCosts[$group['raw_material_id']] ?? 0;
            $group['closing_cost'] = $group['closing'] * $group['avg_cost'];
        }
        unset($group);
        return array_values(array_filter($grouped, function($g) {
            return $g['opening'] != 0 || $g['inward'] != 0 || $g['outward'] != 0 || $g['closing'] != 0;
        }));
    }

    private function getStockAgeingData($storeCategoryId, Request $request)
    {
        $query = \App\Models\StockEntryItem::with(['rawMaterial', 'item.brand', 'brand', 'style', 'color', 'fabricType', 'fabricWidth'])
            ->where('store_category_id', $storeCategoryId);

        if ($request->supplier_id) {
            $query->whereHas('grnEntryItem.grnEntry', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        $toDate = $request->to_date ? date('Y-m-d 23:59:59', strtotime($request->to_date)) : now();
        $query->where('created_at', '<=', $toDate);
        
        $stockItems = $query->get();

        $grouped = [];

        foreach ($stockItems as $item) {
            $key = ($item->raw_material_id ?: 0) . '-' . ($item->item_id ?: 0) . '-' . ($item->style_id ?: 0) . '-' . ($item->color_id ?: 0) . '-' . ($item->fabric_type_id ?: 0) . '-' . ($item->size ?: '') . '-' . ($item->brand_id ?: 0) . '-' . ($item->fabric_width_id ?: 0);
            
            if (!isset($grouped[$key])) {
                $brandName = 'N/A';
                if ($item->brand) {
                    $brandName = $item->brand->brand_name;
                } elseif ($item->item && $item->item->brand) {
                    $brandName = $item->item->brand->name;
                }

                $itemName = 'N/A';
                if ($item->rawMaterial) {
                    $itemName = $item->rawMaterial->name;
                } elseif ($item->item) {
                    $itemName = $item->item->name;
                }

                $grouped[$key] = [
                    'brand' => $brandName,
                    'item_name' => $itemName,
                    'style' => $item->style ? $item->style->style_name : 'N/A',
                    'color' => $item->color ? $item->color->color_name : 'N/A',
                    'fabric_type' => $item->fabricType ? $item->fabricType->fabric_type : 'N/A',
                    'width' => $item->fabricWidth ? $item->fabricWidth->width : ($item->size ?: 'N/A'),
                    '0_30' => 0,
                    '31_60' => 0,
                    '61_90' => 0,
                    '91_plus' => 0,
                    'total' => 0,
                ];
            }

            $netQty = $item->qty_in - $item->qty_out;
            
            if ($netQty > 0) {
                $targetDate = $request->to_date ? \Carbon\Carbon::parse($request->to_date) : now();
                $itemDate = \Carbon\Carbon::parse($item->created_at);
                $ageInDays = $itemDate->diffInDays($targetDate);

                if ($ageInDays <= 30) {
                    $grouped[$key]['0_30'] += $netQty;
                } elseif ($ageInDays <= 60) {
                    $grouped[$key]['31_60'] += $netQty;
                } elseif ($ageInDays <= 90) {
                    $grouped[$key]['61_90'] += $netQty;
                } else {
                    $grouped[$key]['91_plus'] += $netQty;
                }
                $grouped[$key]['total'] += $netQty;
            }
        }

        return array_values(array_filter($grouped, function($g) {
            return $g['total'] > 0;
        }));
    }

    private function getConsumptionData(Request $request)
    {
        $query = \App\Models\JobCardEntry::with('brand')->where('status', '!=', 'cancelled')->whereNotNull('job_card_date');

        if ($request->from_date) {
            $query->whereDate('job_card_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $query->whereDate('job_card_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        $jobCards = $query->get();

        $consumptionData = [];

        foreach ($jobCards as $jobCard) {
            $garments = $jobCard->grand_total_qty;
            $average = $jobCard->average;
            $totalFabric = $garments * $average;

            if ($garments > 0 && $totalFabric > 0) {
                $consumptionData[] = [
                    'date' => $jobCard->job_card_date,
                    'job_card_no' => $jobCard->job_card_no,
                    'brand' => $jobCard->brand ? $jobCard->brand->brand_name : 'N/A',
                    'total_garments' => $garments,
                    'total_fabric' => $totalFabric,
                    'average' => $average,
                    'status' => $jobCard->status,
                ];
            }
        }

        return $consumptionData;
    }

    private function getMinStockData($storeCategoryId, Request $request)
    {
        $allStock = $this->getStockData($storeCategoryId, $request);
        
        $lowStockItems = [];
        
        foreach ($allStock as $item) {
            if ($item['min_stock'] > 0 && $item['closing'] <= $item['min_stock']) {
                $item['shortage'] = $item['min_stock'] - $item['closing'];
                $lowStockItems[] = $item;
            }
        }
        
        return $lowStockItems;
    }

    private function getReturnGoodsData($storeCategoryId, Request $request)
    {
        $query = \Illuminate\Support\Facades\DB::table('debit_note_items as items')
            ->join('debit_notes as dn', 'items.debit_note_id', '=', 'dn.id')
            ->join('raw_materials as rm', 'items.raw_material_id', '=', 'rm.id')
            ->join('suppliers as sup', 'dn.supplier_id', '=', 'sup.id')
            ->where('rm.store_category_id', $storeCategoryId)
            ->whereNull('dn.deleted_at')
            ->whereNull('items.deleted_at');

        if ($request->from_date) {
            $query->whereDate('dn.debit_note_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $query->whereDate('dn.debit_note_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        if ($request->supplier_id) {
            $query->where('dn.supplier_id', $request->supplier_id);
        }

        return $query->select([
            'dn.debit_note_date as return_date',
            'dn.debit_note_no as return_no',
            'sup.name as supplier_name',
            'rm.name as item_name',
            'items.quantity',
            'items.rate',
            'items.amount',
            'dn.reason'
        ])->orderBy('dn.debit_note_date', 'desc')->get();
    }
}
