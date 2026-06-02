<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class PurchaseReportController extends Controller
{
    public function fabricStore(Request $request)
    {
        if ($request->ajax() && $request->has('report_type')) {
            return $this->getReportJson($request, true);
        }

        if ($request->ajax() || $request->has('fetch_report')) {
            $purchaseOrders = $this->getPurchaseOrders(1, $request);
            $stockData = $this->getStockData(1, $request, true); 
            
            $html = [
                'po-report' => view('reports.purchase_reports._po_supplier_wise', [
                    'purchaseOrders' => $purchaseOrders,
                    'qtyLabel' => 'Meters'
                ])->render(),
                'stock-report' => view('reports.purchase_reports._stock_report', [
                    'stockData' => $stockData,
                    'isFabric' => true,
                    'reportDate' => $request->to_date ? date('d-m-Y', strtotime($request->to_date)) : date('d-m-Y')
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
                'casino-po-report' => view('reports.purchase_reports._casino_po_report', [
                    'casinoData' => $this->getCasinoPoData($request),
                    'reportDate' => $request->to_date ? date('d.m.Y', strtotime($request->to_date)) : date('d.m.Y')
                ])->render(),
            ];

            return response()->json($html);
        }

        $suppliers = Supplier::where('status', 'Active')->get();
        return view('reports.purchase_reports.fabric_store', compact('suppliers'));
    }

    public function accessoriesStore(Request $request)
    {
        if ($request->ajax() && $request->has('report_type')) {
            return $this->getReportJson($request, false);
        }

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

    private function getStockData($storeCategoryId, Request $request, $isStockReport = false)
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

        if ($storeCategoryId == 1 && $isStockReport) {
            $styles = \App\Models\Style::all()->keyBy(function($s) {
                return strtoupper($s->style_name);
            });
            $plainStyleId = isset($styles['PLAIN']) ? $styles['PLAIN']->id : null;
            $printStyleId = isset($styles['PRINT']) ? $styles['PRINT']->id : null;
            $checkedStyleId = isset($styles['CHECKED']) ? $styles['CHECKED']->id : null;

            $grouped = [];
            $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;

            foreach ($stockItems as $item) {
                $brandId = $item->brand_id ?: 0;
                $fabricWidthId = $item->fabric_width_id ?: 0;
                $key = $brandId . '-' . $fabricWidthId;

                if (!isset($grouped[$key])) {
                    $brandName = 'N/A';
                    if ($item->brand) {
                        $brandName = $item->brand->brand_name;
                    } elseif ($item->item && $item->item->brand) {
                        $brandName = $item->item->brand->name;
                    }

                    $grouped[$key] = [
                        'brand_id' => $brandId,
                        'brand' => $brandName,
                        'width' => $item->fabricWidth ? $item->fabricWidth->width : ($item->size ?: 'N/A'),
                        'plain' => 0,
                        'print' => 0,
                        'checked' => 0,
                        'opening' => 0,
                        'inward' => 0,
                        'outward' => 0,
                        'closing' => 0,
                        'avg_cost' => 0,
                        'closing_cost' => 0
                    ];
                }

                $qty = $item->qty_in - $item->qty_out;
                $date = $item->created_at->format('Y-m-d');

                if ($fromDate && $date < $fromDate) {
                    $grouped[$key]['opening'] += $qty;
                } else {
                    $grouped[$key]['inward'] += $item->qty_in;
                    $grouped[$key]['outward'] += $item->qty_out;
                }

                if ($item->style_id == $plainStyleId) {
                    $grouped[$key]['plain'] += $qty;
                } elseif ($item->style_id == $printStyleId) {
                    $grouped[$key]['print'] += $qty;
                } elseif ($item->style_id == $checkedStyleId) {
                    $grouped[$key]['checked'] += $qty;
                }
            }

            foreach ($grouped as &$group) {
                $group['closing'] = $group['opening'] + $group['inward'] - $group['outward'];
            }
            unset($group);

            return array_values(array_filter($grouped, function($g) {
                return $g['opening'] != 0 || $g['inward'] != 0 || $g['outward'] != 0 || $g['closing'] != 0;
            }));
        }

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
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(quantity) as total_qty')
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
        $query = DB::table('debit_note_items as items')
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

    private function getCasinoPoData(Request $request)
    {
        $styles = \App\Models\Style::all()->keyBy(function($s) {
            return strtoupper($s->style_name);
        });
        
        $plainStyleId = isset($styles['PLAIN']) ? $styles['PLAIN']->id : null;
        $printStyleId = isset($styles['PRINT']) ? $styles['PRINT']->id : null;
        $checkedStyleId = isset($styles['CHECKED']) ? $styles['CHECKED']->id : null;
        $stripedStyleId = isset($styles['STRIPED']) ? $styles['STRIPED']->id : null;
        
        $query = \App\Models\PurchaseOrderItem::with(['brand', 'fabricWidth'])
            ->whereHas('purchaseOrder', function($q) use ($request) {
                $q->where('store_type_id', 1);
                if ($request->from_date) {
                    $q->whereDate('po_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $q->whereDate('po_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->supplier_id) {
                    $q->where('supplier_id', $request->supplier_id);
                }
            })
            ->whereHas('brand', function($q) {
                $q->where('brand_name', 'like', 'CASINO%');
            });
            
        $items = $query->get();
        $grouped = [];
        
        $casinoBrands = \App\Models\Brand::where('brand_name', 'like', 'CASINO%')
            ->where('status', 'Active')
            ->get();
            
        foreach ($casinoBrands as $brand) {
            $brandItems = $items->where('brand_id', $brand->id);
            
            if (!$brandItems->isEmpty()) {
                $byWidth = $brandItems->groupBy('fabric_width_id');
                foreach ($byWidth as $widthId => $wItems) {
                    $widthVal = '';
                    if ($wItems->first() && $wItems->first()->fabricWidth) {
                        $widthVal = $wItems->first()->fabricWidth->width;
                    }
                    
                    $plainMeters = $wItems->where('style_id', $plainStyleId)->sum('quantity');
                    $printMeters = $wItems->where('style_id', $printStyleId)->sum('quantity');
                    $checkedMeters = $wItems->where('style_id', $checkedStyleId)->sum('quantity');
                    $stripedMeters = $wItems->where('style_id', $stripedStyleId)->sum('quantity');
                    $totalMeters = $wItems->sum('quantity');
                    
                    $grouped[] = [
                        'brand_name' => $brand->brand_name,
                        'width' => $widthVal,
                        'plain' => $plainMeters,
                        'print' => $printMeters,
                        'checked' => $checkedMeters,
                        'striped' => $stripedMeters,
                        'total' => $totalMeters,
                    ];
                }
            }
        }
        
        return $grouped;
    }

    private function getReportJson(Request $request, $isFabric)
    {
        $reportType = $request->report_type;
        $storeCategoryId = $isFabric ? 1 : 2;
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search')['value'] ?? null;
        $draw = (int) $request->input('draw', 1);

        $data = [];
        $totalRecords = 0;
        $filteredRecords = 0;
        $totals = null;

        switch ($reportType) {
            case 'po-report':
                $query = PurchaseOrder::with(['supplier', 'items.purchaseInvoiceItems'])->where('store_type_id', $storeCategoryId);
                if ($request->from_date) {
                    $query->whereDate('po_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->whereDate('po_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->supplier_id) {
                    $query->where('supplier_id', $request->supplier_id);
                }

                $totalRecords = $query->count();
                 $allFiltered = $query->get();
                if (!empty($search)) {

                    $allFiltered = $allFiltered->filter(function ($po) use ($search) {

                        $totalOrdered = $po->items->sum('quantity');

                        $totalReceived = 0;
                        foreach ($po->items as $item) {
                            $totalReceived += $item->purchaseInvoiceItems->sum('qty_received');
                        }

                        $totalPending = max(0, $totalOrdered - $totalReceived);

                        $status = (
                            strtolower($po->status) == 'closed' ||
                            $po->is_self_closed ||
                            $totalPending <= 0
                        ) ? 'closed' : 'pending';

                        return
                            stripos($po->po_number, $search) !== false ||
                            stripos(optional($po->supplier)->name ?? '', $search) !== false ||
                            stripos(optional($po->po_date)->format('d-M-Y') ?? '', $search) !== false ||
                            stripos((string)$totalOrdered, $search) !== false ||
                            stripos((string)$totalReceived, $search) !== false ||
                            stripos((string)$totalPending, $search) !== false ||
                            stripos($status, $search) !== false;
                    });
                }
                $filteredRecords = $allFiltered->count();
                
                $sumOrdered = 0;
                $sumReceived = 0;
                $sumPending = 0;
                foreach ($allFiltered as $po) {
                    $to = $po->items->sum('quantity');
                    $tr = 0;
                    foreach ($po->items as $item) {
                        $tr += $item->purchaseInvoiceItems->sum('qty_received');
                    }
                    $tp = max(0, $to - $tr);
                    $sumOrdered += $to;
                    $sumReceived += $tr;
                    $sumPending += $tp;
                }
                $totals = [
                    'total_ordered' => number_format($sumOrdered, 2),
                    'total_received' => number_format($sumReceived, 2),
                    'total_pending' => number_format($sumPending, 2),
                ];

               
                $purchaseOrders = $allFiltered;
                if ($length != -1) {
                    $purchaseOrders = $purchaseOrders->slice($start, $length);
                }

                $count = $start + 1;

                foreach ($purchaseOrders as $po) {
                    $to = $po->items->sum('quantity');
                    $tr = 0;
                    foreach ($po->items as $item) {
                        $tr += $item->purchaseInvoiceItems->sum('qty_received');
                    }
                    $tp = max(0, $to - $tr);

                    $statusBadge = (strtolower($po->status) == 'closed' || $po->is_self_closed || $tp <= 0) 
                        ? '<span class="badge bg-label-success rounded-pill">Closed</span>' 
                        : '<span class="badge bg-label-warning rounded-pill">Pending</span>';

                    $data[] = [
                        'DT_RowIndex' => $count++,
                        'po_number' => $po->po_number,
                        'po_date' => $po->po_date ? $po->po_date->format('d-M-Y') : '-',
                        'supplier_name' => $po->supplier ? $po->supplier->name : '-',
                        'total_ordered' => number_format($to, 2),
                        'total_received' => number_format($tr, 2),
                        'total_pending' => number_format($tp, 2),
                        'status' => $statusBadge,
                    ];
                }
                break;

            case 'stock-report':
                $stockData = $this->getStockData($storeCategoryId, $request, true);
                $totalRecords = count($stockData);
        
                if (!empty($search)) {
                    $search = strtolower(trim($search));

                    $stockData = array_filter($stockData, function ($item) use ($search) {

                        return
                            strpos(strtolower((string)($item['brand'] ?? '')), $search) !== false ||
                            strpos(strtolower((string)($item['item_name'] ?? '')), $search) !== false ||
                            strpos(strtolower((string)($item['width'] ?? '')), $search) !== false ||

                            strpos(number_format((float)($item['plain'] ?? 0), 2), $search) !== false ||
                            strpos(number_format((float)($item['print'] ?? 0), 2), $search) !== false ||
                            strpos(number_format((float)($item['checked'] ?? 0), 2), $search) !== false ||

                            strpos(number_format((float)($item['opening'] ?? 0), 2), $search) !== false ||
                            strpos(number_format((float)($item['inward'] ?? 0), 2), $search) !== false ||
                            strpos(number_format((float)($item['outward'] ?? 0), 2), $search) !== false ||
                            strpos(number_format((float)($item['closing'] ?? 0), 2), $search) !== false ||
                            strpos(number_format((float)($item['closing_cost'] ?? 0), 2), $search) !== false;
                    });
                }
                $filteredRecords = count($stockData);

                if ($isFabric) {
                    $totals = [
                        'opening' => number_format(collect($stockData)->sum('opening'), 2),
                        'inward' => number_format(collect($stockData)->sum('inward'), 2),
                        'outward' => number_format(collect($stockData)->sum('outward'), 2),
                        'closing' => number_format(collect($stockData)->sum('closing'), 2),
                    ];
                } else {
                    $totals = [
                        'opening' => number_format(collect($stockData)->sum('opening'), 2),
                        'inward' => number_format(collect($stockData)->sum('inward'), 2),
                        'outward' => number_format(collect($stockData)->sum('outward'), 2),
                        'closing' => number_format(collect($stockData)->sum('closing'), 2),
                        'closing_cost' => '₹ ' . number_format(collect($stockData)->sum('closing_cost'), 2),
                    ];
                }

                if ($length != -1) {
                    $stockData = array_slice($stockData, $start, $length);
                }

                $count = $start + 1;
                
                foreach ($stockData as $stock) {
                    if ($isFabric) {
                        $data[] = [
                            'DT_RowIndex' => $count++,
                            'brand' => $stock['brand'],
                            'width' => $stock['width'],
                            'plain' => $stock['plain'] > 0 ? number_format($stock['plain'], 2) : '0.00',
                            'print' => $stock['print'] > 0 ? number_format($stock['print'], 2) : '0.00',
                            'checked' => $stock['checked'] > 0 ? number_format($stock['checked'], 2) : '0.00',
                            'opening' => number_format($stock['opening'], 2),
                            'inward' => number_format($stock['inward'], 2),
                            'outward' => number_format($stock['outward'], 2),
                            'closing' => number_format($stock['closing'], 2),
                        ];
                    } else {
                        $data[] = [
                            'DT_RowIndex' => $count++,
                            'item_name' => $stock['item_name'],
                            'opening' => number_format($stock['opening'], 2),
                            'inward' => number_format($stock['inward'], 2),
                            'outward' => number_format($stock['outward'], 2),
                            'closing' => number_format($stock['closing'], 2),
                            'closing_cost' => '₹ ' . number_format($stock['closing_cost'], 2),
                        ];
                    }
                }
                break;

            case 'ageing-report':
                $ageingData = $this->getStockAgeingData($storeCategoryId, $request);
                $totalRecords = count($ageingData);

                if (!empty($search)) {
                    $ageingData = array_filter($ageingData, function($item) use ($search) {
                        return (strpos(strtolower($item['brand'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['item_name'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['style'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['color'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['fabric_type'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['width'] ?? ''), strtolower($search)) !== false);
                    });
                }

                $filteredRecords = count($ageingData);

                $totals = [
                    '0_30' => number_format(collect($ageingData)->sum('0_30'), 2),
                    '31_60' => number_format(collect($ageingData)->sum('31_60'), 2),
                    '61_90' => number_format(collect($ageingData)->sum('61_90'), 2),
                    '91_plus' => number_format(collect($ageingData)->sum('91_plus'), 2),
                    'total' => number_format(collect($ageingData)->sum('total'), 2)
                ];

                if ($length != -1) {
                    $ageingData = array_slice($ageingData, $start, $length);
                }

                $count = $start + 1;
                foreach ($ageingData as $row) {
                    if ($isFabric) {
                        $data[] = [
                            'DT_RowIndex' => $count++,
                            'item_name' => $row['item_name'],
                            'brand' => $row['brand'],
                            'style' => $row['style'],
                            'color' => $row['color'],
                            'fabric_type' => $row['fabric_type'],
                            'width' => $row['width'],
                            'age_0_30' => number_format($row['0_30'], 2),
                            'age_31_60' => number_format($row['31_60'], 2),
                            'age_61_90' => number_format($row['61_90'], 2),
                            'age_91_plus' => number_format($row['91_plus'], 2),
                            'total' => number_format($row['total'], 2),
                        ];
                    } else {
                        $data[] = [
                            'DT_RowIndex' => $count++,
                            'item_name' => $row['item_name'],
                            'age_0_30' => number_format($row['0_30'], 2),
                            'age_31_60' => number_format($row['31_60'], 2),
                            'age_61_90' => number_format($row['61_90'], 2),
                            'age_91_plus' => number_format($row['91_plus'], 2),
                            'total' => number_format($row['total'], 2),
                        ];
                    }
                }
                break;

            case 'consumption-report':
                $consumptionData = $this->getConsumptionData($request);
                $totalRecords = count($consumptionData);

                if (!empty($search)) {
                    $consumptionData = array_filter($consumptionData, function($item) use ($search) {
                        return (strpos(strtolower($item['job_card_no'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['brand'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['status'] ?? ''), strtolower($search)) !== false);
                    });
                }

                $filteredRecords = count($consumptionData);

                $totals = [
                    'total_garments' => number_format(collect($consumptionData)->sum('total_garments')),
                    'total_fabric' => number_format(collect($consumptionData)->sum('total_fabric'), 2)
                ];

                if ($length != -1) {
                    $consumptionData = array_slice($consumptionData, $start, $length);
                }

                $count = $start + 1;
                foreach ($consumptionData as $row) {
                    $statusBadge = '<span class="badge bg-label-primary rounded-pill">' . ucfirst($row['status']) . '</span>';
                    $data[] = [
                        'DT_RowIndex' => $count++,
                        'date' => $row['date'] ? date('d-M-Y', strtotime($row['date'])) : '-',
                        'job_card_no' => $row['job_card_no'],
                        'brand' => $row['brand'],
                        'total_garments' => number_format($row['total_garments']),
                        'total_fabric' => number_format($row['total_fabric'], 2),
                        'average' => number_format($row['average'], 2),
                        'status' => $statusBadge,
                    ];
                }
                break;

            case 'minstock-report':
                $minStockData = $this->getMinStockData($storeCategoryId, $request);
                $totalRecords = count($minStockData);

                if (!empty($search)) {
                    $minStockData = array_filter($minStockData, function($item) use ($search) {
                        return (strpos(strtolower($item['brand'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['item_name'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['style'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['color'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['fabric_type'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['width'] ?? ''), strtolower($search)) !== false);
                    });
                }

                $filteredRecords = count($minStockData);

                if ($length != -1) {
                    $minStockData = array_slice($minStockData, $start, $length);
                }

                $count = $start + 1;
                foreach ($minStockData as $row) {
                    $statusBadge = ($row['closing'] <= 0) 
                        ? '<span class="badge bg-label-danger">Out of Stock</span>' 
                        : '<span class="badge bg-label-warning">Low Stock</span>';

                    if ($isFabric) {
                        $data[] = [
                            'DT_RowIndex' => $count++,
                            'item_name' => $row['item_name'],
                            'brand' => $row['brand'],
                            'style' => $row['style'],
                            'color' => $row['color'],
                            'fabric_type' => $row['fabric_type'],
                            'width' => $row['width'],
                            'min_stock' => number_format($row['min_stock'], 2),
                            'closing' => number_format($row['closing'], 2),
                            'shortage' => number_format($row['shortage'], 2),
                            'status' => $statusBadge,
                        ];
                    } else {
                        $data[] = [
                            'DT_RowIndex' => $count++,
                            'item_name' => $row['item_name'],
                            'min_stock' => number_format($row['min_stock'], 2),
                            'closing' => number_format($row['closing'], 2),
                            'shortage' => number_format($row['shortage'], 2),
                            'status' => $statusBadge,
                        ];
                    }
                }
                break;

            case 'return-report':
                $returnGoodsData = $this->getReturnGoodsData($storeCategoryId, $request)->toArray();
                $totalRecords = count($returnGoodsData);

                if (!empty($search)) {
                    $returnGoodsData = array_filter($returnGoodsData, function($item) use ($search) {
                        $itemArr = (array) $item;
                        return (strpos(strtolower($itemArr['return_no'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($itemArr['supplier_name'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($itemArr['item_name'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($itemArr['reason'] ?? ''), strtolower($search)) !== false);
                    });
                }

                $filteredRecords = count($returnGoodsData);

                $totals = [
                    'quantity' => number_format(collect($returnGoodsData)->sum('quantity'), 2),
                    'amount' => number_format(collect($returnGoodsData)->sum('amount'), 2),
                ];

                if ($length != -1) {
                    $returnGoodsData = array_slice($returnGoodsData, $start, $length);
                }

                $count = $start + 1;
                foreach ($returnGoodsData as $row) {
                    $rowArr = (array) $row;
                    $data[] = [
                        'DT_RowIndex' => $count++,
                        'return_date' => $rowArr['return_date'] ? date('d-M-Y', strtotime($rowArr['return_date'])) : '-',
                        'return_no' => $rowArr['return_no'],
                        'supplier_name' => $rowArr['supplier_name'],
                        'item_name' => $rowArr['item_name'],
                        'quantity' => number_format($rowArr['quantity'], 2),
                        'rate' => number_format($rowArr['rate'], 2),
                        'amount' => number_format($rowArr['amount'], 2),
                        'reason' => $rowArr['reason'] ?: '-',
                    ];
                }
                break;

            case 'performance-report':
                $performanceData = $isFabric 
                    ? $this->getFabricSupplierPerformanceData($request) 
                    : $this->getAccessoriesSupplierPerformanceData($request);
                $totalRecords = count($performanceData);

                if (!empty($search)) {
                    $performanceData = array_filter($performanceData, function($item) use ($search) {
                        return (strpos(strtolower($item['supplier_name'] ?? ''), strtolower($search)) !== false);
                    });
                }

                $filteredRecords = count($performanceData);

                if ($length != -1) {
                    $performanceData = array_slice($performanceData, $start, $length);
                }

                $count = $start + 1;
                foreach ($performanceData as $row) {
                    $returnRateVal = (float) $row['return_rate'];
                    $badgeClass = 'bg-label-success';
                    if ($returnRateVal > 20) {
                        $badgeClass = 'bg-label-danger';
                    } elseif ($returnRateVal > 5) {
                        $badgeClass = 'bg-label-warning';
                    }
                    $returnRateBadge = '<span class="badge ' . $badgeClass . '">' . $row['return_rate'] . '%</span>';

                    $data[] = [
                        'DT_RowIndex' => $count++,
                        'supplier_name' => $row['supplier_name'],
                        'po_count' => number_format($row['po_count']),
                        'total_po_value' => '₹' . number_format($row['total_po_value'], 2),
                        'dn_count' => number_format($row['dn_count']),
                        'return_rate' => $returnRateBadge,
                    ];
                }
                break;

            case 'casino-po-report':
                $casinoData = $this->getCasinoPoData($request);
                $totalRecords = count($casinoData);

                if (!empty($search)) {
                    $casinoData = array_filter($casinoData, function($item) use ($search) {
                        return (strpos(strtolower($item['brand_name'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['width'] ?? ''), strtolower($search)) !== false);
                    });
                }

                $filteredRecords = count($casinoData);

                $totals = [
                    'plain' => number_format(collect($casinoData)->sum('plain'), 2),
                    'print' => number_format(collect($casinoData)->sum('print'), 2),
                    'checked' => number_format(collect($casinoData)->sum('checked'), 2),
                    'striped' => number_format(collect($casinoData)->sum('striped'), 2),
                    'total' => number_format(collect($casinoData)->sum('total'), 2)
                ];

                if ($length != -1) {
                    $casinoData = array_slice($casinoData, $start, $length);
                }

                $count = $start + 1;
                foreach ($casinoData as $row) {
                    $data[] = [
                        'DT_RowIndex' => $count++,
                        'brand_name' => $row['brand_name'],
                        'width' => $row['width'] ?: '-',
                        'plain' => number_format($row['plain'], 2),
                        'print' => number_format($row['print'], 2),
                        'checked' => number_format($row['checked'], 2),
                        'striped' => number_format($row['striped'], 2),
                        'total' => number_format($row['total'], 2),
                    ];
                }
                break;

            case 'cost-report':
                $costData = $this->getAverageCostData($request);
                $totalRecords = count($costData);

                if (!empty($search)) {
                    $costData = array_filter($costData, function($item) use ($search) {
                        return (strpos(strtolower($item['item_name'] ?? ''), strtolower($search)) !== false);
                    });
                }

                $filteredRecords = count($costData);

                if ($length != -1) {
                    $costData = array_slice($costData, $start, $length);
                }

                $count = $start + 1;
                foreach ($costData as $row) {
                    $data[] = [
                        'DT_RowIndex' => $count++,
                        'item_name' => $row['item_name'],
                        'total_qty' => number_format($row['total_qty'], 2),
                        'total_amount' => number_format($row['total_amount'], 2),
                        'average_cost' => number_format($row['average_cost'], 2),
                    ];
                }
                break;
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
            'totals' => $totals,
        ]);
    }
}
