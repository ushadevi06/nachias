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
        if ($request->has('export') && $request->export === 'brandwise-minstock-excel') {
            return $this->exportBrandwiseMinStockExcel($request);
        }

        if ($request->ajax() && ($request->has('draw') || ($request->has('report_type') && !$request->has('fetch_report')))) {
            if ($request->report_type === 'stock-report-drilldown') {
                return $this->getStockReportDrilldownData($request);
            }
            return $this->getReportJson($request, true);
        }

        if ($request->ajax() && $request->has('fetch_report')) {
            $html = [
                'po-report' => view('reports.purchase_reports._po_supplier_wise', ['qtyLabel' => 'Meters'])->render(),
                'stock-report' => view('reports.purchase_reports._stock_report', ['isFabric' => true])->render(),
                'ageing-report' => view('reports.purchase_reports._ageing_report', ['isFabric' => true])->render(),
                'consumption-report' => view('reports.purchase_reports._consumption_report')->render(),
                'minstock-report' => view('reports.purchase_reports._minstock_report', ['isFabric' => true])->render(),
                'brandwise-minstock-report' => view('reports.purchase_reports._brandwise_minstock_report')->render(),
                'return-report' => view('reports.purchase_reports._return_report', ['isFabric' => true])->render(),
                'performance-report' => view('reports.purchase_reports._supplier_performance')->render(),
                'casino-po-report' => view('reports.purchase_reports._casino_po_report')->render(),
            ];

            return response()->json($html);
        }

        $suppliers = Supplier::where('status', 'Active')->get();
        $brands = \App\Models\Brand::active()->orderBy('brand_name', 'asc')->get();
        $artNos = \App\Models\GrnEntryItem::whereNotNull('art_no')
            ->where('art_no', '!=', '')
            ->distinct()
            ->orderBy('art_no', 'asc')
            ->pluck('art_no');
        return view('reports.purchase_reports.fabric_store', compact('suppliers', 'artNos', 'brands'));
    }

    public function accessoriesStore(Request $request)
    {
        if ($request->ajax() && ($request->has('draw') || ($request->has('report_type') && !$request->has('fetch_report')))) {
            return $this->getReportJson($request, false);
        }

        if ($request->ajax() && $request->has('fetch_report')) {
            $html = [
                'po-report' => view('reports.purchase_reports._po_supplier_wise', ['qtyLabel' => 'Qty'])->render(),
                'stock-report' => view('reports.purchase_reports._stock_report', ['isFabric' => false])->render(),
                'ageing-report' => view('reports.purchase_reports._ageing_report', ['isFabric' => false])->render(),
                'cost-report' => view('reports.purchase_reports._cost_report')->render(),
                'minstock-report' => view('reports.purchase_reports._minstock_report', ['isFabric' => false])->render(),
                'return-report' => view('reports.purchase_reports._return_report', ['isFabric' => false])->render(),
                'performance-report' => view('reports.purchase_reports._supplier_performance')->render(),
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

    private function getStockData($storeCategoryId, Request $request, $isStockReport = false, $isMinStock = false)
    {
        $query = \App\Models\StockEntryItem::with(['rawMaterial.artNos', 'item.brand', 'brand', 'style', 'color', 'fabricType', 'fabricWidth'])->where('store_category_id', $storeCategoryId);

        if ($request->supplier_id) {
            $query->whereHas('grnEntryItem.grnEntry', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        if ($isMinStock && $request->art_no) {
            $query->where(function($q) use ($request) {
                $q->where('art_no', $request->art_no)
                  ->orWhereHas('rawMaterial.artNos', function($sub) use ($request) {
                      $sub->where('art_no', $request->art_no);
                  });
            });
        }

        $toDate = $request->to_date ? date('Y-m-d 23:59:59', strtotime($request->to_date)) : now();
        $query->where('created_at', '<=', $toDate);
        
        $stockItems = $query->get();

        if ($storeCategoryId == 1 && $isStockReport) {
            $grouped = [];
            $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : date('Y-m-d');

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
                        'fabric_width_id' => $fabricWidthId,
                        'brand' => $brandName,
                        'width' => $item->fabricWidth ? $item->fabricWidth->width : ($item->size ?: 'N/A'),
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

                if ($date < $fromDate) {
                    $grouped[$key]['opening'] += $qty;
                } else {
                    $grouped[$key]['inward'] += $item->qty_in;
                    $grouped[$key]['outward'] += $item->qty_out;
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
            $artNoVal = trim($item->art_no ?? '');
            if (empty($artNoVal) && $item->rawMaterial && $item->rawMaterial->artNos->count() > 0) {
                $artNoVal = $item->rawMaterial->artNos->pluck('art_no')->implode(', ');
            }
            if (empty($artNoVal)) {
                $artNoVal = 'N/A';
            }

            $key = $artNoVal . '-' . ($item->raw_material_id ?: 0) . '-' . ($item->item_id ?: 0) . '-' . ($item->style_id ?: 0) . '-' . ($item->color_id ?: 0) . '-' . ($item->fabric_type_id ?: 0) . '-' . ($item->size ?: '') . '-' . ($item->brand_id ?: 0) . '-' . ($item->fabric_width_id ?: 0);
            
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
                    'art_no' => $artNoVal,
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
        $query = \App\Models\StockEntryItem::with(['rawMaterial.artNos', 'item.brand', 'brand', 'style', 'color', 'fabricType', 'fabricWidth'])
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
                    'art_no' => ($item->rawMaterial && $item->rawMaterial->artNos->count() > 0) ? $item->rawMaterial->artNos->pluck('art_no')->implode(', ') : 'N/A',
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
        $allStock = $this->getStockData($storeCategoryId, $request, false, true);
        $lowStockItems = [];
        
        foreach ($allStock as $item) {
            $shortage = $item['min_stock'] - $item['closing'];
            $item['shortage'] = $shortage > 0 ? $shortage : 0;
            $lowStockItems[] = $item;
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
        
        $casinoBrands = \App\Models\Brand::where('brand_name', 'like', 'CASINO%')->get();
            
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
        $draw = (int) $request->input('draw', 1);
        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }

        $data = [];
        $totalRecords = 0;
        $filteredRecords = 0;
        $totals = null;

        switch ($reportType) {
            case 'stock-report-styles':
                return $this->getStockReportStylesData($request);

            case 'stock-report-drilldown':
                return $this->getStockReportDrilldownData($request);

            case 'po-report':
                $baseQuery = PurchaseOrder::where('store_type_id', $storeCategoryId);
                if ($request->from_date) {
                    $baseQuery->whereDate('po_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $baseQuery->whereDate('po_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->supplier_id) {
                    $baseQuery->where('supplier_id', $request->supplier_id);
                }

                $totalRecords = (clone $baseQuery)->count();

                $query = clone $baseQuery;
                if (!empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->where('po_number', 'like', "%{$search}%")
                            ->orWhere('remarks', 'like', "%{$search}%")
                            ->orWhereHas('supplier', function ($sq) use ($search) {
                                $sq->where('name', 'like', "%{$search}%");
                            });
                    });
                }
                $filteredRecords = (clone $query)->count();

                $filteredPoIds = (clone $query)->pluck('id');
                $sumOrdered = 0;
                $sumReceived = 0;
                if ($filteredPoIds->isNotEmpty()) {
                    $sumOrdered = (float) DB::table('purchase_order_items')
                        ->whereIn('purchase_order_id', $filteredPoIds)
                        ->whereNull('deleted_at')
                        ->sum('quantity');

                    $sumReceived = (float) DB::table('purchase_invoice_items')
                        ->join('purchase_order_items', 'purchase_invoice_items.purchase_order_item_id', '=', 'purchase_order_items.id')
                        ->whereIn('purchase_order_items.purchase_order_id', $filteredPoIds)
                        ->whereNull('purchase_invoice_items.deleted_at')
                        ->whereNull('purchase_order_items.deleted_at')
                        ->sum('purchase_invoice_items.qty_received');
                }
                $sumPending = max(0, $sumOrdered - $sumReceived);

                $totals = [
                    'total_ordered' => number_format($sumOrdered, 2),
                    'total_received' => number_format($sumReceived, 2),
                    'total_pending' => number_format($sumPending, 2),
                ];

                $query->with(['supplier', 'items.rawMaterial', 'items.purchaseInvoiceItems'])
                    ->orderBy('id', 'desc');

                if ($length != -1) {
                    $query->offset($start)->limit($length);
                }

                $purchaseOrders = $query->get();
                $count = $start + 1;

                foreach ($purchaseOrders as $po) {
                    $totalOrderedPo = (float) $po->items->sum('quantity');
                    $totalReceivedPo = 0;
                    $itemsData = [];
                    $itemSno = 1;

                    foreach ($po->items as $item) {
                        $itemOrd = (float) $item->quantity;
                        $itemRec = (float) $item->purchaseInvoiceItems->sum('qty_received');
                        $itemBal = max(0, $itemOrd - $itemRec);
                        $totalReceivedPo += $itemRec;

                        $itemsData[] = [
                            'sno' => $itemSno++,
                            'material_name' => optional($item->rawMaterial)->name ?: 'N/A',
                            'ordered' => number_format($itemOrd, 2),
                            'received' => number_format($itemRec, 2),
                            'balance' => number_format($itemBal, 2),
                        ];
                    }
                    $totalPendingPo = max(0, $totalOrderedPo - $totalReceivedPo);

                    $delayHtml = '-';
                    if ($po->due_date) {
                        if ($totalPendingPo <= 0 || strtolower($po->status) == 'closed' || $po->is_self_closed) {
                            $delayHtml = '<span class="badge bg-label-success">Completed</span>';
                        } else {
                            $dueDate = \Carbon\Carbon::parse($po->due_date)->startOfDay();
                            $today = now()->startOfDay();
                            if ($today->gt($dueDate)) {
                                $diffDays = $today->diffInDays($dueDate);
                                $delayHtml = '<span class="text-danger fw-bold">' . $diffDays . ' Days</span>';
                            } else {
                                $delayHtml = '<span class="text-success">On Time</span>';
                            }
                        }
                    }

                    $data[] = [
                        'DT_RowIndex' => $count++,
                        'po_number' => '<strong>' . htmlspecialchars($po->po_number) . '</strong>',
                        'po_number_raw' => $po->po_number,
                        'po_date' => $po->po_date ? $po->po_date->format('d-M-Y') : '-',
                        'supplier_name' => optional($po->supplier)->name ?? '-',
                        'total_ordered' => number_format($totalOrderedPo, 2),
                        'total_received' => number_format($totalReceivedPo, 2),
                        'total_pending' => number_format($totalPendingPo, 2),
                        'order_date' => $po->reference_date ? $po->reference_date->format('d-M-Y') : ($po->po_date ? $po->po_date->format('d-M-Y') : '-'),
                        'expected_delivery' => $po->due_date ? $po->due_date->format('d-M-Y') : '-',
                        'delay' => $delayHtml,
                        'remarks' => htmlspecialchars($po->remarks ?: '-'),
                        'items' => $itemsData,
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
                            'opening' => number_format($stock['opening'], 2),
                            'inward' => number_format($stock['inward'], 2),
                            'outward' => number_format($stock['outward'], 2),
                            'closing' => number_format($stock['closing'], 2),
                            'brand_id' => $stock['brand_id'] ?? 0,
                            'fabric_width_id' => $stock['fabric_width_id'] ?? 0,
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
                        return (strpos(strtolower($item['art_no'] ?? ''), strtolower($search)) !== false)
                            || (strpos(strtolower($item['brand'] ?? ''), strtolower($search)) !== false)
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
                    if ($row['closing'] <= 0) {
                        $statusBadge = '<span class="badge bg-label-danger">Out of Stock</span>';
                    } elseif ($row['closing'] <= $row['min_stock']) {
                        $statusBadge = '<span class="badge bg-label-warning">Low Stock</span>';
                    } else {
                        $statusBadge = '<span class="badge bg-label-success">Excess Stock</span>';
                    }

                    if ($isFabric) {
                        $data[] = [
                            'DT_RowIndex' => $count++,
                            'art_no' => $row['art_no'] ?? 'N/A',
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

            case 'brandwise-minstock-report':
                $brandwiseData = $this->getBrandwiseMinStockData($request);
                $totalRecords = count($brandwiseData);

                if (!empty($search)) {
                    $searchLower = strtolower($search);
                    $brandwiseData = array_filter($brandwiseData, function($item) use ($searchLower) {
                        // Search art_no
                        if (strpos(strtolower($item['art_no'] ?? ''), $searchLower) !== false) return true;
                        // Search inside WIP rows c_no & remarks
                        foreach ($item['matrix']['wips'] ?? [] as $wip) {
                            if (strpos(strtolower($wip['unit'] ?? ''), $searchLower) !== false) return true;
                            if (strpos(strtolower($wip['c_no'] ?? ''), $searchLower) !== false) return true;
                            if (strpos(strtolower($wip['remarks'] ?? ''), $searchLower) !== false) return true;
                        }
                        return false;
                    });
                }

                $filteredRecords = count($brandwiseData);

                if ($length != -1) {
                    $brandwiseData = array_slice($brandwiseData, $start, $length);
                }

                $data = array_values($brandwiseData);
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

    public function getBrandwiseMinStockData(Request $request)
    {
        $brandId  = $request->brand_id;
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
        $toDate   = $request->to_date   ? date('Y-m-d', strtotime($request->to_date))   : null;
        
        if (!$brandId) {
            $firstBrand = DB::table('brands')
                ->where('status', 'Active')
                ->whereNull('deleted_at')
                ->where('brand_name', 'like', 'CASINO%')
                ->first() 
                ?? DB::table('brands')->where('status', 'Active')->whereNull('deleted_at')->first();
            $brandId = $firstBrand ? $firstBrand->id : null;
        }

        if (!$brandId) {
            return [];
        }

        $artNos = DB::table('stock_entry_items')
            ->where('brand_id', $brandId)
            ->whereNotNull('art_no')
            ->where('art_no', '!=', '')
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('art_no')
            ->toArray();

        if (empty($artNos)) {
            $artNos = DB::table('job_card_fabric_details as jcfd')
                ->join('job_card_entries as jce', 'jcfd.job_card_entry_id', '=', 'jce.id')
                ->where('jce.brand_id', $brandId)
                ->whereNotNull('jcfd.art_no')
                ->where('jcfd.art_no', '!=', '')
                ->whereNull('jcfd.deleted_at')
                ->distinct()
                ->pluck('jcfd.art_no')
                ->toArray();
        }

        if (empty($artNos)) {
            return [];
        }

        // -------------------------------------------------------------
        // BRAND-LEVEL AGGREGATES (Run ONCE, outside loop)
        // -------------------------------------------------------------
        // Dhoti Stock (store_type_id = 2 or store_category_id = 2 for selected brand)
        $dhotiStockVal = floatval(DB::table('stock_entry_items as sei')
            ->leftJoin('stock_entries as se', 'sei.stock_entry_id', '=', 'se.id')
            ->leftJoin('grn_entries as ge', 'se.grn_entry_id', '=', 'ge.id')
            ->leftJoin('purchase_invoices as pi', 'ge.purchase_invoice_id', '=', 'pi.id')
            ->leftJoin('purchase_orders as po', 'pi.purchase_order_id', '=', 'po.id')
            ->leftJoin('raw_materials as rm', 'sei.item_id', '=', 'rm.id')
            ->leftJoin('items as it', 'sei.item_id', '=', 'it.id')
            ->where('sei.brand_id', $brandId)
            ->where(function($q) {
                $q->where('sei.store_type_id', 2)
                  ->orWhere('se.store_type_id', 2)
                  ->orWhere('po.store_type_id', 2)
                  ->orWhere('rm.store_category_id', 2)
                  ->orWhere('it.store_category_id', 2);
            })
            ->whereNull('sei.deleted_at')
            ->sum(DB::raw('sei.qty_in - COALESCE(sei.qty_out, 0)')));

        // Dhoti Reorder (PO Qty for store_type_id = 2 or store_category_id = 2 for selected brand)
        $dhotiReorderVal = floatval(DB::table('purchase_order_items as poi')
            ->join('purchase_orders as po', 'poi.purchase_order_id', '=', 'po.id')
            ->leftJoin('raw_materials as rm', 'poi.raw_material_id', '=', 'rm.id')
            ->where('poi.brand_id', $brandId)
            ->where(function($q) {
                $q->where('po.store_type_id', 2)
                  ->orWhere('rm.store_category_id', 2);
            })
            ->whereNull('po.deleted_at')
            ->whereNull('poi.deleted_at')
            ->sum('poi.quantity'));

        // Dhoti Received (for selected brand)
        $dhotiReceivedVal = floatval(DB::table('grn_entry_items as gei')
            ->join('grn_entries as ge', 'gei.grn_entry_id', '=', 'ge.id')
            ->leftJoin('purchase_invoices as pi', 'ge.purchase_invoice_id', '=', 'pi.id')
            ->leftJoin('purchase_orders as po', 'pi.purchase_order_id', '=', 'po.id')
            ->leftJoin('purchase_order_items as poi', 'poi.purchase_order_id', '=', 'po.id')
            ->leftJoin('raw_materials as rm', 'poi.raw_material_id', '=', 'rm.id')
            ->where('poi.brand_id', $brandId)
            ->where(function($q) {
                $q->where('po.store_type_id', 2)
                  ->orWhere('rm.store_category_id', 2);
            })
            ->whereNull('ge.deleted_at')
            ->whereNull('gei.deleted_at')
            ->whereNull('pi.deleted_at')
            ->whereNull('po.deleted_at')
            ->sum('gei.qty_received'));

        $dhotiPendingVal = max(0, $dhotiReorderVal - $dhotiReceivedVal);

        // -------------------------------------------------------------
        // BULK QUERIES FOR ALL ART NOS (Run ONCE, outside loop)
        // -------------------------------------------------------------
        // Order Fabric PO Qty per art_no
        $orderFabricMap = DB::table('grn_entry_items as gei')
            ->join('grn_entries as ge', 'gei.grn_entry_id', '=', 'ge.id')
            ->leftJoin('purchase_invoices as pi', 'ge.purchase_invoice_id', '=', 'pi.id')
            ->leftJoin('purchase_orders as po', 'pi.purchase_order_id', '=', 'po.id')
            ->where('po.store_type_id', 1)
            ->whereIn('gei.art_no', $artNos)
            ->whereNull('ge.deleted_at')
            ->whereNull('gei.deleted_at')
            ->whereNull('pi.deleted_at')
            ->whereNull('po.deleted_at')
            ->select('gei.art_no', DB::raw('SUM(gei.qty_ordered) as total_qty'))
            ->groupBy('gei.art_no')
            ->pluck('total_qty', 'art_no');

        // Fabric Stock per art_no (include store_type_id = 1 or stock_type = raw_material)
        $fabricStockMap = DB::table('stock_entry_items')
            ->where(function($q) {
                $q->where('store_type_id', 1)
                  ->orWhere('stock_type', 'raw_material');
            })
            ->where('brand_id', $brandId)
            ->whereIn('art_no', $artNos)
            ->whereNull('deleted_at')
            ->select('art_no', DB::raw('SUM(qty_in - COALESCE(qty_out, 0)) as total_stock'))
            ->groupBy('art_no')
            ->pluck('total_stock', 'art_no');

        // FG Min Stock records bulk
        $minRecordsBulk = DB::table('fg_min_stocks as fms')
            ->join('stock_entry_items as sei', 'fms.stock_entry_item_id', '=', 'sei.id')
            ->whereIn('sei.art_no', $artNos)
            ->where('fms.status', 'Active')
            ->whereNull('fms.deleted_at')
            ->whereNull('sei.deleted_at')
            ->select('sei.art_no', 'sei.sleeve_type', 'sei.size', 'fms.min_stock')
            ->get()
            ->groupBy('art_no');

        // FG Current Stock bulk
        $fgQuery = DB::table('stock_entry_items')
            ->whereIn('art_no', $artNos)
            ->where('stock_type', 'finished_goods')
            ->whereNull('deleted_at');
        if ($fromDate) {
            $fgQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $fgQuery->whereDate('created_at', '<=', $toDate);
        }
        $fgRecordsBulk = $fgQuery->select('art_no', 'sleeve_type', 'size', DB::raw('SUM(qty_in - COALESCE(qty_out, 0)) as total_qty'))
            ->groupBy('art_no', 'sleeve_type', 'size')
            ->get()
            ->groupBy('art_no');

        // WIP bulk - Group by art_no, job_card_id, job_card_no, remarks, size
        $wipQuery = DB::table('job_card_matrix_quantities as jcmq')
            ->join('job_card_fabric_details as jcfd', 'jcmq.job_card_fabric_detail_id', '=', 'jcfd.id')
            ->join('job_card_entries as jce', 'jcfd.job_card_entry_id', '=', 'jce.id')
            ->leftJoin('service_providers as sp', 'jce.service_provider_id', '=', 'sp.id')
            ->whereIn('jcfd.art_no', $artNos)
            ->whereNotIn('jce.status', ['cancelled', 'Completed'])
            ->whereNull('jcmq.deleted_at')
            ->whereNull('jcfd.deleted_at');
        if ($fromDate) {
            $wipQuery->whereDate('jce.job_card_date', '>=', $fromDate);
        }
        if ($toDate) {
            $wipQuery->whereDate('jce.job_card_date', '<=', $toDate);
        }
        $wipRecordsBulk = $wipQuery->select(
                'jcfd.art_no',
                'jce.id as job_card_id',
                'jce.job_card_no',
                'jce.remarks',
                'sp.name as unit_name',
                'jcmq.size',
                DB::raw('SUM(COALESCE(jcmq.qty_fs, 0)) as wip_fs'),
                DB::raw('SUM(COALESCE(jcmq.qty_hs, 0)) as wip_hs')
            )
            ->groupBy('jcfd.art_no', 'jce.id', 'jce.job_card_no', 'jce.remarks', 'sp.name', 'jcmq.size')
            ->get()
            ->groupBy('art_no');

        $sizes = ['36', '38', '40', '42', '44', '46', '48', '50'];
        $result = [];

        foreach ($artNos as $artNo) {
            $orderFabricVal = floatval($orderFabricMap[$artNo] ?? 0);
            $fabricStockVal = floatval($fabricStockMap[$artNo] ?? 0);

            $minRecords    = $minRecordsBulk->get($artNo, collect());
            $fgRecords     = $fgRecordsBulk->get($artNo, collect());
            $artWipRecords = $wipRecordsBulk->get($artNo, collect());

            // Build Matrix Arrays
            $matrixMin = ['fs' => [], 'hs' => [], 'fs_tl' => 0, 'hs_tl' => 0, 'gross_total' => 0];
            $matrixFg  = ['fs' => [], 'hs' => [], 'fs_tl' => 0, 'hs_tl' => 0, 'gross_total' => 0];
            $matrixTot = ['fs' => [], 'hs' => [], 'fs_tl' => 0, 'hs_tl' => 0, 'gross_total' => 0];

            // 1. MIN
            foreach ($sizes as $sz) {
                $minFsVal = floatval($minRecords->filter(function($item) use ($sz) {
                    $st = strtoupper(trim($item->sleeve_type ?? ''));
                    return in_array($st, ['F/S', 'FULL', 'F', 'FS']) && $item->size == $sz;
                })->sum('min_stock'));

                $minHsVal = floatval($minRecords->filter(function($item) use ($sz) {
                    $st = strtoupper(trim($item->sleeve_type ?? ''));
                    return in_array($st, ['H/S', 'HALF', 'H', 'HS']) && $item->size == $sz;
                })->sum('min_stock'));

                $matrixMin['fs'][$sz] = $minFsVal;
                $matrixMin['hs'][$sz] = $minHsVal;
                $matrixMin['fs_tl'] += $minFsVal;
                $matrixMin['hs_tl'] += $minHsVal;
            }
            $matrixMin['gross_total'] = $matrixMin['fs_tl'] + $matrixMin['hs_tl'];

            // 2. FG
            foreach ($sizes as $sz) {
                $fgFsVal = floatval($fgRecords->filter(function($item) use ($sz) {
                    $st = strtoupper(trim($item->sleeve_type ?? ''));
                    return in_array($st, ['F/S', 'FULL', 'F', 'FS']) && $item->size == $sz;
                })->sum('total_qty'));

                $fgHsVal = floatval($fgRecords->filter(function($item) use ($sz) {
                    $st = strtoupper(trim($item->sleeve_type ?? ''));
                    return in_array($st, ['H/S', 'HALF', 'H', 'HS']) && $item->size == $sz;
                })->sum('total_qty'));

                $matrixFg['fs'][$sz] = $fgFsVal;
                $matrixFg['hs'][$sz] = $fgHsVal;
                $matrixFg['fs_tl'] += $fgFsVal;
                $matrixFg['hs_tl'] += $fgHsVal;

                // Seed total with FG
                $matrixTot['fs'][$sz] = $fgFsVal;
                $matrixTot['hs'][$sz] = $fgHsVal;
            }
            $matrixFg['gross_total'] = $matrixFg['fs_tl'] + $matrixFg['hs_tl'];

            // 3. WIP rows (WIP-1, WIP-2, ...)
            $wipByJc = $artWipRecords->groupBy('job_card_no');
            $wipList = [];
            $wipIdx = 1;

            if ($wipByJc->count() > 0) {
                foreach ($wipByJc as $jcNo => $jcSizes) {
                    $firstRec = $jcSizes->first();
                    $wipItem = [
                        'label' => 'WIP-' . $wipIdx++,
                        'unit' => ($firstRec && !empty($firstRec->unit_name)) ? $firstRec->unit_name : '-',
                        'c_no' => $jcNo,
                        'remarks' => ($firstRec && !empty($firstRec->remarks)) ? $firstRec->remarks : '-',
                        'fs' => [],
                        'hs' => [],
                        'fs_tl' => 0,
                        'hs_tl' => 0,
                        'gross_total' => 0,
                    ];
                    foreach ($sizes as $sz) {
                        $szRow = $jcSizes->firstWhere('size', $sz);
                        $wFs = floatval($szRow ? $szRow->wip_fs : 0);
                        $wHs = floatval($szRow ? $szRow->wip_hs : 0);
                        $wipItem['fs'][$sz] = $wFs;
                        $wipItem['hs'][$sz] = $wHs;
                        $wipItem['fs_tl'] += $wFs;
                        $wipItem['hs_tl'] += $wHs;

                        // Add to TOTAL
                        $matrixTot['fs'][$sz] += $wFs;
                        $matrixTot['hs'][$sz] += $wHs;
                    }
                    $wipItem['gross_total'] = $wipItem['fs_tl'] + $wipItem['hs_tl'];
                    $wipList[] = $wipItem;
                }
            } else {
                $emptyWip = [
                    'label' => 'WIP-1',
                    'unit' => '-',
                    'c_no' => '-',
                    'remarks' => '-',
                    'fs' => [],
                    'hs' => [],
                    'fs_tl' => 0,
                    'hs_tl' => 0,
                    'gross_total' => 0,
                ];
                foreach ($sizes as $sz) {
                    $emptyWip['fs'][$sz] = 0;
                    $emptyWip['hs'][$sz] = 0;
                }
                $wipList[] = $emptyWip;
            }

            // Calculate TOTAL T/L and gross_total
            foreach ($sizes as $sz) {
                $matrixTot['fs_tl'] += $matrixTot['fs'][$sz];
                $matrixTot['hs_tl'] += $matrixTot['hs'][$sz];
            }
            $matrixTot['gross_total'] = $matrixTot['fs_tl'] + $matrixTot['hs_tl'];

            $result[] = [
                'art_no'        => $artNo,
                'order_fabric'  => number_format($orderFabricVal, 2),
                'fabric_stock'  => number_format($fabricStockVal, 2),
                'dhoti_stock'   => number_format($dhotiStockVal, 2),
                'dhoti_reorder' => number_format($dhotiReorderVal, 2),
                'dhoti_pending' => number_format($dhotiPendingVal, 2),
                'matrix'        => [
                    'min'   => $matrixMin,
                    'fg'    => $matrixFg,
                    'wips'  => $wipList,
                    'total' => $matrixTot,
                ]
            ];
        }

        return $result;
    }

    public function exportBrandwiseMinStockExcel(Request $request)
    {
        $brandwiseData = $this->getBrandwiseMinStockData($request);

        $search = $request->search;
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $brandwiseData = array_filter($brandwiseData, function($item) use ($searchLower) {
                if (strpos(strtolower($item['art_no'] ?? ''), $searchLower) !== false) return true;
                foreach ($item['matrix']['wips'] ?? [] as $wip) {
                    if (strpos(strtolower($wip['unit'] ?? ''), $searchLower) !== false) return true;
                    if (strpos(strtolower($wip['c_no'] ?? ''), $searchLower) !== false) return true;
                    if (strpos(strtolower($wip['remarks'] ?? ''), $searchLower) !== false) return true;
                }
                return false;
            });
        }

        $brandwiseData = array_values($brandwiseData);

        $brandName = 'CASINO DHOTI MATCHING';
        if ($request->filled('brand_id')) {
            $brandObj = \App\Models\Brand::find($request->brand_id);
            if ($brandObj) {
                $brandName = $brandObj->brand_name;
            }
        }
        $reportDate = date('d.m.Y');
        $title = strtoupper($brandName) . ' STOCK DETAILS ' . $reportDate;
        $fileName = str_replace([' ', '/', '\\', ':', '*'], '_', strtoupper($brandName)) . '_STOCK_DETAILS_' . date('Ymd_His') . '.xlsx';
        $isDhotiBrand = (strpos(strtoupper($brandName), 'CASINO DHOTI SHIRTS') !== false);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BrandwiseMinStockExport($brandwiseData, $brandName, $reportDate, $title, $isDhotiBrand),
            $fileName
        );
    }

    private function getStockReportStylesData(Request $request)
    {
        $brandId = $request->brand_id;
        $brandName = trim($request->brand ?? '');
        $fabricWidthId = $request->fabric_width_id;
        $widthName = trim($request->width ?? '');
        $toDate = $request->to_date ? date('Y-m-d 23:59:59', strtotime($request->to_date)) : null;

        $bIds = !empty($brandName) && $brandName !== 'N/A' ? DB::table('brands')->where('brand_name', $brandName)->pluck('id')->toArray() : [];
        $wIds = !empty($widthName) && $widthName !== 'N/A' && $widthName !== '-' ? DB::table('fabric_sizes')->where('width', $widthName)->pluck('id')->toArray() : [];

        $query = \App\Models\StockEntryItem::with(['style', 'grnEntryItem'])
            ->where(function($q) {
                $q->where('store_category_id', 1)->orWhereNull('store_category_id');
            })
            ->whereNull('deleted_at');

        if ($brandId && $brandId > 0) {
            $query->where('brand_id', $brandId);
        } elseif (!empty($bIds)) {
            $query->whereIn('brand_id', $bIds);
        } elseif (!empty($brandName) && $brandName !== 'N/A') {
            $query->whereHas('brand', function($b) use ($brandName) {
                $b->where('brand_name', $brandName);
            });
        }

        if ($fabricWidthId && $fabricWidthId > 0) {
            $query->where('fabric_width_id', $fabricWidthId);
        } elseif (!empty($widthName) && $widthName !== 'N/A' && $widthName !== '-') {
            if (!empty($wIds)) {
                $query->where(function($q) use ($wIds, $widthName) {
                    $q->whereIn('fabric_width_id', $wIds)->orWhere('size', $widthName);
                });
            } else {
                $query->where('size', $widthName);
            }
        }

        if ($toDate) {
            $query->where('created_at', '<=', $toDate);
        }
        if ($request->supplier_id) {
            $query->whereHas('grnEntryItem.grnEntry', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        $stockItems = $query->get();
        $styleGroups = [];

        foreach ($stockItems as $item) {
            $styleName = $item->style ? strtoupper($item->style->style_name) : 'OTHER';
            $styleId = $item->style_id ?: 0;
            
            if (!isset($styleGroups[$styleId])) {
                $styleGroups[$styleId] = [
                    'style_id' => $styleId,
                    'style' => $styleName,
                    'total_qty_raw' => 0,
                    'stock_value_raw' => 0,
                ];
            }
            
            $qty = (float)($item->qty_in - $item->qty_out);
            $rate = (float)($item->price ?: 0);
            if ($item->grnEntryItem && $item->grnEntryItem->rate > 0) {
                $rate = (float)$item->grnEntryItem->rate;
            }
            
            $styleGroups[$styleId]['total_qty_raw'] += $qty;
            $styleGroups[$styleId]['stock_value_raw'] += ($qty * $rate);
        }

        $rows = [];
        $totQty = 0;
        $totVal = 0;

        foreach ($styleGroups as $sg) {
            if ($sg['total_qty_raw'] == 0 && $sg['stock_value_raw'] == 0) continue;
            
            $totQty += $sg['total_qty_raw'];
            $totVal += $sg['stock_value_raw'];

            $rows[] = [
                'style_id' => $sg['style_id'],
                'style' => $sg['style'],
                'total_qty' => number_format($sg['total_qty_raw'], 2),
                'stock_value' => '₹' . number_format($sg['stock_value_raw'], 2),
                'total_qty_raw' => $sg['total_qty_raw'],
                'stock_value_raw' => $sg['stock_value_raw'],
            ];
        }

        return response()->json([
            'draw' => intval($request->draw ?? 1),
            'recordsTotal' => count($rows),
            'recordsFiltered' => count($rows),
            'data' => $rows,
            'totals' => [
                'total_qty' => number_format($totQty, 2),
                'stock_value' => '₹' . number_format($totVal, 2),
            ]
        ]);
    }

    private function getStockReportDrilldownData(Request $request)
    {
        if ($request->store_category_id == 2 || $request->is_fabric === 'false' || ($request->has('item_name') && !$request->has('brand'))) {
            return $this->getAccessoriesStockDrilldownData($request);
        }

        $brandId = $request->brand_id;
        $brandName = trim($request->brand ?? '');
        $fabricWidthId = $request->fabric_width_id;
        $widthName = trim($request->width ?? '');
        $styleId = $request->style_id;
        $styleName = trim($request->style_name ?? ($request->style ?? ''));
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
        $toDate = $request->to_date ? date('Y-m-d 23:59:59', strtotime($request->to_date)) : null;

        $bIds = !empty($brandName) && $brandName !== 'N/A' ? DB::table('brands')->where('brand_name', $brandName)->pluck('id')->toArray() : [];
        $wIds = !empty($widthName) && $widthName !== 'N/A' && $widthName !== '-' ? DB::table('fabric_sizes')->where('width', $widthName)->pluck('id')->toArray() : [];

        $query = \App\Models\StockEntryItem::with([
            'stockEntry.grnEntry',
            'grnEntryItem',
            'storeLocation',
            'uom',
            'fabricWidth',
            'rawMaterial',
            'brand',
            'style'
        ])
        ->where(function($q) {
            $q->where('store_category_id', 1)->orWhereNull('store_category_id');
        })
        ->whereNull('deleted_at');

        if ($brandId && $brandId > 0) {
            $query->where('brand_id', $brandId);
        } elseif (!empty($bIds)) {
            $query->whereIn('brand_id', $bIds);
        } elseif (!empty($brandName) && $brandName !== 'N/A') {
            $query->whereHas('brand', function($b) use ($brandName) {
                $b->where('brand_name', $brandName);
            });
        }

        if ($fabricWidthId && $fabricWidthId > 0) {
            $query->where('fabric_width_id', $fabricWidthId);
        } elseif (!empty($widthName) && $widthName !== 'N/A' && $widthName !== '-') {
            if (!empty($wIds)) {
                $query->where(function($q) use ($wIds, $widthName) {
                    $q->whereIn('fabric_width_id', $wIds)->orWhere('size', $widthName);
                });
            } else {
                $query->where('size', $widthName);
            }
        }

        if ($styleId && $styleId > 0) {
            $query->where('style_id', $styleId);
        } elseif (!empty($styleName) && $styleName !== 'N/A' && $styleName !== 'OTHER') {
            $query->whereHas('style', function($st) use ($styleName) {
                $st->where('style_name', 'like', "%{$styleName}%");
            });
        }

        if ($toDate) {
            $query->where('created_at', '<=', $toDate);
        }
        if ($request->supplier_id) {
            $query->whereHas('grnEntryItem.grnEntry', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        // Limit maximum records to prevent browser crash if filters are empty
        $stockItems = $query->take(2000)->get();

        $stockItemIds = $stockItems->pluck('id')->toArray();

        $jcIssuesByStockItem = [];
        if (!empty($stockItemIds)) {
            $jcIssuesByStockItem = DB::table('job_card_issue_items as jcii')
                ->join('job_card_entries as jce', 'jcii.job_card_entry_id', '=', 'jce.id')
                ->whereIn('jcii.stock_entry_item_id', $stockItemIds)
                ->whereNull('jcii.deleted_at')
                ->select(
                    'jcii.stock_entry_item_id',
                    'jce.job_card_no',
                    'jce.job_card_date',
                    'jcii.qty_issue',
                    'jcii.unit_price',
                    'jcii.total_cost'
                )
                ->get()
                ->groupBy('stock_entry_item_id');
        }

        $drilldownRows = [];
        $sno = 1;

        $totOpeningQty = 0; $totOpeningVal = 0;
        $totInwardQty = 0;  $totInwardVal = 0;
        $totOutwardQty = 0; $totOutwardVal = 0;
        $totClosingQty = 0; $totClosingVal = 0;

        foreach ($stockItems as $item) {
            $width = $item->fabricWidth ? $item->fabricWidth->width : ($item->size ?: ($widthName ?: '-'));
            $location = $item->storeLocation ? $item->storeLocation->store_location : '-';
            $artNo = $item->art_no ?: ($item->rawMaterial ? $item->rawMaterial->name : '-');
            $uom = $item->uom ? $item->uom->uom_code : 'MTR';
            
            $inwardDate = '-';
            $docType = 'RECEIPTS';
            $docNumber = '-';
            $rate = (float)($item->price ?: 0);
            
            if ($item->grnEntryItem) {
                $rate = (float)($item->grnEntryItem->rate ?: $rate);
                if ($item->grnEntryItem->grnEntry) {
                    $docNumber = $item->grnEntryItem->grnEntry->grn_number ?: ($item->grnEntryItem->grnEntry->grn_no ?: $docNumber);
                }
            }
            
            if ($item->stockEntry) {
                $stockDate = $item->stockEntry->stock_date ? date('d-m-Y', strtotime($item->stockEntry->stock_date)) : '-';
                $inwardDate = $stockDate;
                
                if ($item->stockEntry->grnEntry) {
                    $docNumber = $item->stockEntry->grnEntry->grn_number ?: ($item->stockEntry->grnEntry->grn_no ?: $docNumber);
                    if ($item->stockEntry->grnEntry->grn_date) {
                        $inwardDate = date('d-m-Y', strtotime($item->stockEntry->grnEntry->grn_date));
                    }
                } elseif ($docNumber === '-') {
                    $docNumber = $item->stockEntry->stock_entry_no ?: '-';
                }
            }
            
            $qtyIn = (float)$item->qty_in;
            $date = $item->created_at ? $item->created_at->format('Y-m-d') : '';
            $remarks = strtolower($item->stockEntry ? ($item->stockEntry->remarks ?? '') : '');
            $rawEntryType = strtolower($item->stockEntry ? ($item->stockEntry->entry_type ?? '') : '');
            
            $isOpening = ($fromDate && $date && $date < $fromDate) || str_contains($remarks, 'opening') || str_contains($rawEntryType, 'opening');
            
            $openingQty = $isOpening ? $qtyIn : 0;
            $openingVal = $isOpening ? ($openingQty * $rate) : 0;
            
            $inwardQty = !$isOpening ? $qtyIn : 0;
            $inwardVal = !$isOpening ? ($inwardQty * $rate) : 0;

            // Batch-loaded Jobcard Outward Issues
            $jcIssues = $jcIssuesByStockItem[$item->id] ?? collect();
                
            if (count($jcIssues) > 0) {
                foreach ($jcIssues as $jc) {
                    $outwardQty = (float)$jc->qty_issue;
                    $outwardUnitPrice = (float)($jc->unit_price ?: $rate);
                    $outwardVal = (float)($jc->total_cost ?: ($outwardQty * $outwardUnitPrice));
                    $outwardDate = $jc->job_card_date ? date('d-m-Y', strtotime($jc->job_card_date)) : '-';
                    
                    $closingBal = max(0, $qtyIn - $outwardQty);
                    $closingVal = $closingBal * $rate;

                    $totOpeningQty += $openingQty; $totOpeningVal += $openingVal;
                    $totInwardQty += $inwardQty;   $totInwardVal += $inwardVal;
                    $totOutwardQty += $outwardQty; $totOutwardVal += $outwardVal;
                    $totClosingQty += $closingBal; $totClosingVal += $closingVal;
                    
                    $drilldownRows[] = [
                        'sno' => $sno++,
                        'width' => $width,
                        'location' => $location,
                        'art_no' => $artNo,
                        'uom' => $uom,
                        'inward_date' => $inwardDate,
                        'doc_type' => $docType,
                        'doc_number' => $docNumber,
                        'opening_qty' => number_format($openingQty, 2),
                        'opening_value' => '₹ ' . number_format($openingVal, 2),
                        'inward_qty' => number_format($inwardQty, 2),
                        'inward_value' => '₹ ' . number_format($inwardVal, 2),
                        'outward_doc_type' => 'Jobcard',
                        'outward_doc_number' => $jc->job_card_no,
                        'outward_date' => $outwardDate,
                        'outward_qty' => number_format($outwardQty, 2),
                        'outward_value' => '₹ ' . number_format($outwardVal, 2),
                        'closing_qty' => number_format($closingBal, 2),
                        'closing_value' => '₹ ' . number_format($closingVal, 2),
                    ];
                }
            } else {
                $closingBal = (float)($item->qty_in - $item->qty_out);
                $closingVal = $closingBal * $rate;

                $totOpeningQty += $openingQty; $totOpeningVal += $openingVal;
                $totInwardQty += $inwardQty;   $totInwardVal += $inwardVal;
                $totClosingQty += $closingBal; $totClosingVal += $closingVal;
                
                $drilldownRows[] = [
                    'sno' => $sno++,
                    'width' => $width,
                    'location' => $location,
                    'art_no' => $artNo,
                    'uom' => $uom,
                    'inward_date' => $inwardDate,
                    'doc_type' => $docType,
                    'doc_number' => $docNumber,
                    'opening_qty' => number_format($openingQty, 2),
                    'opening_value' => '₹ ' . number_format($openingVal, 2),
                    'inward_qty' => number_format($inwardQty, 2),
                    'inward_value' => '₹ ' . number_format($inwardVal, 2),
                    'outward_doc_type' => '-',
                    'outward_doc_number' => '-',
                    'outward_date' => '-',
                    'outward_qty' => '0.00',
                    'outward_value' => '₹ 0.00',
                    'closing_qty' => number_format($closingBal, 2),
                    'closing_value' => '₹ ' . number_format($closingVal, 2),
                ];
            }
        }

        return response()->json([
            'draw' => intval($request->draw ?? 1),
            'recordsTotal' => count($drilldownRows),
            'recordsFiltered' => count($drilldownRows),
            'data' => $drilldownRows,
            'totals' => [
                'opening_qty' => number_format($totOpeningQty, 2),
                'opening_value' => '₹ ' . number_format($totOpeningVal, 2),
                'inward_qty' => number_format($totInwardQty, 2),
                'inward_value' => '₹ ' . number_format($totInwardVal, 2),
                'outward_qty' => number_format($totOutwardQty, 2),
                'outward_value' => '₹ ' . number_format($totOutwardVal, 2),
                'closing_qty' => number_format($totClosingQty, 2),
                'closing_value' => '₹ ' . number_format($totClosingVal, 2),
            ]
        ]);
    }

    private function getAccessoriesStockDrilldownData(Request $request)
    {
        $rawMaterialId = $request->raw_material_id;
        $itemId = $request->item_id;
        $itemName = trim($request->item_name ?? '');
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
        $toDate = $request->to_date ? date('Y-m-d 23:59:59', strtotime($request->to_date)) : null;

        $query = \App\Models\StockEntryItem::with([
            'stockEntry.grnEntry.supplier',
            'grnEntryItem.grnEntry.supplier',
            'grnEntryItem.grnEntry.purchaseInvoice.purchaseOrder.supplier',
            'storeLocation',
            'uom',
            'rawMaterial',
            'item'
        ])
        ->where('store_category_id', 2)
        ->whereNull('deleted_at');

        if ($rawMaterialId && $rawMaterialId > 0) {
            $query->where('raw_material_id', $rawMaterialId);
        } elseif ($itemId && $itemId > 0) {
            $query->where('item_id', $itemId);
        } elseif (!empty($itemName) && $itemName !== 'N/A') {
            $query->where(function($q) use ($itemName) {
                $q->whereHas('rawMaterial', function($rm) use ($itemName) {
                    $rm->where('name', $itemName);
                })->orWhereHas('item', function($it) use ($itemName) {
                    $it->where('name', $itemName);
                });
            });
        }

        if ($toDate) {
            $query->where('created_at', '<=', $toDate);
        }
        if ($request->supplier_id) {
            $query->whereHas('grnEntryItem.grnEntry', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        $stockItems = $query->take(2000)->get();
        $stockItemIds = $stockItems->pluck('id')->toArray();

        $jcIssuesByStockItem = [];
        if (!empty($stockItemIds)) {
            $jcIssuesByStockItem = DB::table('job_card_issue_items as jcii')
                ->join('job_card_entries as jce', 'jcii.job_card_entry_id', '=', 'jce.id')
                ->whereIn('jcii.stock_entry_item_id', $stockItemIds)
                ->whereNull('jcii.deleted_at')
                ->select(
                    'jcii.stock_entry_item_id',
                    'jce.job_card_no',
                    'jce.job_card_date',
                    'jcii.qty_issue',
                    'jcii.unit_price',
                    'jcii.total_cost'
                )
                ->get()
                ->groupBy('stock_entry_item_id');
        }

        $drilldownRows = [];
        $sno = 1;

        $totOpeningQty = 0; $totOpeningVal = 0;
        $totInwardQty = 0;  $totInwardVal = 0;
        $totOutwardQty = 0; $totOutwardVal = 0;
        $totClosingQty = 0; $totClosingVal = 0;

        foreach ($stockItems as $item) {
            $desc = $item->rawMaterial ? $item->rawMaterial->name : ($item->item ? $item->item->name : ($itemName ?: 'N/A'));
            $uom = $item->uom ? $item->uom->uom_code : 'NOS';
            $rate = (float)($item->price ?: ($item->grnEntryItem ? $item->grnEntryItem->rate : 0));

            $docNumber = '-';
            $inwardDate = '-';
            $supplierName = '-';
            $orderInfo = '-';
            $deliveryDate = '-';

            if ($item->grnEntryItem && $item->grnEntryItem->grnEntry) {
                $grn = $item->grnEntryItem->grnEntry;
                $docNumber = $grn->grn_number ?: ($grn->grn_no ?: $docNumber);
                if ($grn->grn_date) {
                    $inwardDate = date('d-m-Y', strtotime($grn->grn_date));
                }
                if ($grn->supplier) {
                    $supplierName = $grn->supplier->name;
                }
                if ($grn->purchaseInvoice && $grn->purchaseInvoice->purchaseOrder) {
                    $po = $grn->purchaseInvoice->purchaseOrder;
                    $orderInfo = 'PO ' . $po->po_number . ($po->po_date ? ' (' . date('d-m-Y', strtotime($po->po_date)) . ')' : '');
                    if ($po->due_date) {
                        $deliveryDate = date('d-m-Y', strtotime($po->due_date));
                    }
                    if ($supplierName === '-' && $po->supplier) {
                        $supplierName = $po->supplier->name;
                    }
                }
            }

            if ($docNumber === '-' && $item->stockEntry) {
                $docNumber = $item->stockEntry->stock_entry_no ?: '-';
                if ($item->stockEntry->stock_date) {
                    $inwardDate = date('d-m-Y', strtotime($item->stockEntry->stock_date));
                }
            }

            if ($inwardDate === '-' && $item->created_at) {
                $inwardDate = $item->created_at->format('d-m-Y');
            }

            $qtyIn = (float)$item->qty_in;
            $date = $item->created_at ? $item->created_at->format('Y-m-d') : '';
            $remarks = strtolower($item->stockEntry ? ($item->stockEntry->remarks ?? '') : '');
            $rawEntryType = strtolower($item->stockEntry ? ($item->stockEntry->entry_type ?? '') : '');
            
            $isOpening = ($fromDate && $date && $date < $fromDate) || str_contains($remarks, 'opening') || str_contains($rawEntryType, 'opening');

            $openingQty = $isOpening ? $qtyIn : 0;
            $openingVal = $isOpening ? ($openingQty * $rate) : 0;

            $inwardQty = !$isOpening ? $qtyIn : 0;
            $inwardVal = !$isOpening ? ($inwardQty * $rate) : 0;

            $minStock = (float)($item->rawMaterial ? $item->rawMaterial->min_stock : ($item->item ? ($item->item->min_stock ?? 0) : 0));
            $deliveryDays = 15;

            $jcIssues = $jcIssuesByStockItem[$item->id] ?? collect();

            if (count($jcIssues) > 0) {
                foreach ($jcIssues as $jc) {
                    $outwardQty = (float)$jc->qty_issue;
                    $outwardUnitPrice = (float)($jc->unit_price ?: $rate);
                    $outwardVal = (float)($jc->total_cost ?: ($outwardQty * $outwardUnitPrice));
                    $outwardDate = $jc->job_card_date ? date('d-m-Y', strtotime($jc->job_card_date)) : '-';

                    $closingBal = max(0, $qtyIn - $outwardQty);
                    $closingVal = $closingBal * $rate;

                    $diff = $closingBal - $minStock;
                    $diffHtml = $diff >= 0 
                        ? '<span class="badge bg-label-success fw-bold">+' . number_format($diff, 2) . '</span>'
                        : '<span class="badge bg-label-danger fw-bold">' . number_format($diff, 2) . '</span>';

                    $reorderQty = $minStock > 0 ? max(0, ($minStock * 2) - $closingBal) : 0;

                    $totOpeningQty += $openingQty; $totOpeningVal += $openingVal;
                    $totInwardQty += $inwardQty;   $totInwardVal += $inwardVal;
                    $totOutwardQty += $outwardQty; $totOutwardVal += $outwardVal;
                    $totClosingQty += $closingBal; $totClosingVal += $closingVal;

                    $drilldownRows[] = [
                        'sno' => $sno++,
                        'description' => $desc,
                        'uom' => $uom,
                        'rate' => '₹ ' . number_format($rate, 2),
                        'opening_qty' => number_format($openingQty, 2),
                        'opening_value' => '₹ ' . number_format($openingVal, 2),
                        'doc_number' => $docNumber,
                        'inward_date' => $inwardDate,
                        'inward_qty' => number_format($inwardQty, 2),
                        'inward_value' => '₹ ' . number_format($inwardVal, 2),
                        'outward_doc_type' => 'JOB CARD',
                        'outward_doc_number' => $jc->job_card_no,
                        'outward_date' => $outwardDate,
                        'outward_qty' => number_format($outwardQty, 2),
                        'outward_value' => '₹ ' . number_format($outwardVal, 2),
                        'closing_stock' => number_format($closingBal, 2),
                        'total_value' => '₹ ' . number_format($closingVal, 2),
                        'delivery_days' => $deliveryDays,
                        'min_stock' => number_format($minStock, 2),
                        'diff' => $diffHtml,
                        'reorder' => number_format($reorderQty, 2),
                        'supplier_name' => $supplierName,
                        'order_info' => $orderInfo,
                        'delivery_date' => $deliveryDate,
                    ];
                }
            } else {
                $closingBal = (float)($item->qty_in - $item->qty_out);
                $closingVal = $closingBal * $rate;

                $diff = $closingBal - $minStock;
                $diffHtml = $diff >= 0 
                    ? '<span class="badge bg-label-success fw-bold">+' . number_format($diff, 2) . '</span>'
                    : '<span class="badge bg-label-danger fw-bold">' . number_format($diff, 2) . '</span>';

                $reorderQty = $minStock > 0 ? max(0, ($minStock * 2) - $closingBal) : 0;

                $totOpeningQty += $openingQty; $totOpeningVal += $openingVal;
                $totInwardQty += $inwardQty;   $totInwardVal += $inwardVal;
                $totClosingQty += $closingBal; $totClosingVal += $closingVal;

                $drilldownRows[] = [
                    'sno' => $sno++,
                    'description' => $desc,
                    'uom' => $uom,
                    'rate' => '₹ ' . number_format($rate, 2),
                    'opening_qty' => number_format($openingQty, 2),
                    'opening_value' => '₹ ' . number_format($openingVal, 2),
                    'doc_number' => $docNumber,
                    'inward_date' => $inwardDate,
                    'inward_qty' => number_format($inwardQty, 2),
                    'inward_value' => '₹ ' . number_format($inwardVal, 2),
                    'outward_doc_type' => '-',
                    'outward_doc_number' => '-',
                    'outward_date' => '-',
                    'outward_qty' => '0.00',
                    'outward_value' => '₹ 0.00',
                    'closing_stock' => number_format($closingBal, 2),
                    'total_value' => '₹ ' . number_format($closingVal, 2),
                    'delivery_days' => $deliveryDays,
                    'min_stock' => number_format($minStock, 2),
                    'diff' => $diffHtml,
                    'reorder' => number_format($reorderQty, 2),
                    'supplier_name' => $supplierName,
                    'order_info' => $orderInfo,
                    'delivery_date' => $deliveryDate,
                ];
            }
        }

        return response()->json([
            'draw' => intval($request->draw ?? 1),
            'recordsTotal' => count($drilldownRows),
            'recordsFiltered' => count($drilldownRows),
            'data' => $drilldownRows,
            'totals' => [
                'opening_qty' => number_format($totOpeningQty, 2),
                'opening_value' => '₹ ' . number_format($totOpeningVal, 2),
                'inward_qty' => number_format($totInwardQty, 2),
                'inward_value' => '₹ ' . number_format($totInwardVal, 2),
                'outward_qty' => number_format($totOutwardQty, 2),
                'outward_value' => '₹ ' . number_format($totOutwardVal, 2),
                'closing_stock' => number_format($totClosingQty, 2),
                'total_value' => '₹ ' . number_format($totClosingVal, 2),
            ]
        ]);
    }
}

