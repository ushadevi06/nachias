<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrderItem;
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
            if ($request->report_type === 'brandwise-po-drilldown') {
                return $this->getBrandwisePoDrilldownData($request);
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
        $artNos = \App\Models\GrnEntryItem::whereNotNull('art_no')->where('art_no', '!=', '')->distinct()->orderBy('art_no', 'asc')->pluck('art_no');
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

        $stockBrandIds = DB::table('stock_entry_items as sei')
            ->leftJoin('raw_materials as rm', 'sei.raw_material_id', '=', 'rm.id')
            ->whereNotNull('sei.brand_id')
            ->where('sei.brand_id', '>', 0)
            ->where(function($q) {
                $q->where('sei.store_category_id', 2)
                  ->orWhere('sei.store_type_id', 2)
                  ->orWhere('rm.store_category_id', 2);
            })
            ->whereNull('sei.deleted_at')
            ->pluck('sei.brand_id')
            ->toArray();

        $poBrandIds = DB::table('purchase_order_items as poi')
            ->leftJoin('purchase_orders as po', 'poi.purchase_order_id', '=', 'po.id')
            ->leftJoin('raw_materials as rm', 'poi.raw_material_id', '=', 'rm.id')
            ->whereNotNull('poi.brand_id')
            ->where('poi.brand_id', '>', 0)
            ->where(function($q) {
                $q->where('poi.store_category_id', 2)
                  ->orWhere('po.store_type_id', 2)
                  ->orWhere('rm.store_category_id', 2);
            })
            ->whereNull('poi.deleted_at')
            ->pluck('poi.brand_id')
            ->toArray();

        $accessoriesBrandIds = array_unique(array_merge($stockBrandIds, $poBrandIds));
        $brands = \App\Models\Brand::whereIn('id', $accessoriesBrandIds)->orderBy('brand_name', 'asc')->get();

        $suppliers = Supplier::where('status', 'Active')->get();
        return view('reports.purchase_reports.accessories_store', compact('suppliers', 'brands'));
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
        $query = Supplier::with(['purchaseOrders', 'debitNotes', 'storeType'])->where('status', 'Active')->whereHas('storeType', function($q) { $q->where('id', 2); }); 
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
        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
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

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
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
                $avgCosts[$c->raw_material_id] = $c->total_qty > 0 ? (float) $c->total_amount / (float) $c->total_qty : 0;
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

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
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
        $query = \App\Models\JobCardEntry::with(['brand', 'issueItems', 'fabricDetails'])->where('status', '!=', 'cancelled')->whereNotNull('job_card_date');

        if ($request->from_date) {
            $query->whereDate('job_card_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $query->whereDate('job_card_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        $jobCards = $query->get();

        $consumptionData = [];

        foreach ($jobCards as $jobCard) {
            $garments = floatval($jobCard->grand_total_qty);
            $wastage = floatval($jobCard->issueItems ? $jobCard->issueItems->sum('qty_wastage') : 0);
            $usedFromItems = floatval($jobCard->issueItems ? $jobCard->issueItems->sum('qty_used') : 0);
            $issueFromItems = floatval($jobCard->issueItems ? $jobCard->issueItems->sum('qty_issue') : 0);
            $usedFromFd = floatval($jobCard->fabricDetails ? $jobCard->fabricDetails->sum('used_qty') : 0);

            if ($usedFromItems > 0) {
                $fabricUsed = $usedFromItems;
            } elseif ($issueFromItems > 0) {
                $fabricUsed = max(0, $issueFromItems - $wastage);
            } elseif ($usedFromFd > 0) {
                $fabricUsed = max(0, $usedFromFd - $wastage);
            } else {
                $fabricUsed = max(0, ($garments * floatval($jobCard->average)) - $wastage);
            }

            $totalFabric = $fabricUsed + $wastage;
            $average = $garments > 0 ? round($totalFabric / $garments, 2) : floatval($jobCard->average);

            if ($garments > 0 && $totalFabric > 0) {
                $consumptionData[] = [
                    'date' => $jobCard->job_card_date,
                    'job_card_no' => $jobCard->job_card_no,
                    'brand' => $jobCard->brand ? $jobCard->brand->brand_name : 'N/A',
                    'total_fabric' => $fabricUsed,
                    'wastage' => $wastage,
                    'total_garments' => $garments,
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
            ->leftJoin('suppliers as sup', 'dn.supplier_id', '=', 'sup.id')
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
            'dn.id as debit_note_id',
            'dn.debit_note_date as return_date',
            'dn.debit_note_no as return_no',
            DB::raw("COALESCE(sup.name, '-') as supplier_name"),
            'rm.name as item_name',
            'items.quantity',
            'items.rate',
            'items.amount as item_amount',
            'dn.sub_total as dn_sub_total',
            'dn.discount_percent as dn_discount_percent',
            'dn.discount_amount as dn_discount_amount',
            'dn.taxable_amount as dn_taxable_amount',
            'dn.cgst_percent as dn_cgst_percent',
            'dn.sgst_percent as dn_sgst_percent',
            'dn.igst_percent as dn_igst_percent',
            'dn.tax_amount as dn_tax_amount',
            'dn.round_off as dn_round_off',
            'dn.round_off_type as dn_round_off_type',
            'dn.grand_total as dn_grand_total',
            'dn.reason'
        ])->orderBy('dn.debit_note_date', 'desc')->get();
    }

    private function getCasinoPoData(Request $request)
    {
        $styles = \App\Models\Style::all()->keyBy(function($s) {
            return strtoupper($s->style_name);
        });
        
        $plainStyleId = isset($styles['PLAIN']) ? $styles['PLAIN']->id : null;
        $whiteStyleId = isset($styles['WHITE']) ? $styles['WHITE']->id : null;
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

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }
            
        $items = $query->get();
        $grouped = [];
        
        $casinoBrandsQuery = \App\Models\Brand::where('brand_name', 'like', 'CASINO%');
        if ($request->brand_id) {
            $casinoBrandsQuery->where('id', $request->brand_id);
        }
        $casinoBrands = $casinoBrandsQuery->get();
            
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
                    $whiteMeters = $wItems->where('style_id', $whiteStyleId)->sum('quantity');
                    $printMeters = $wItems->where('style_id', $printStyleId)->sum('quantity');
                    $checkedMeters = $wItems->where('style_id', $checkedStyleId)->sum('quantity');
                    $stripedMeters = $wItems->where('style_id', $stripedStyleId)->sum('quantity');
                    $totalMeters = $wItems->sum('quantity');
                    
                    $grouped[] = [
                        'brand_name' => $brand->brand_name,
                        'width' => $widthVal,
                        'plain' => $plainMeters,
                        'white' => $whiteMeters,
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
                if ($request->brand_id) {
                    $baseQuery->whereHas('items', function($q) use ($request) {
                        $q->where('brand_id', $request->brand_id);
                    });
                }

                $purchaseOrders = $baseQuery->with([
                    'supplier',
                    'items' => function($q) use ($request) {
                        if ($request->brand_id) {
                            $q->where('brand_id', $request->brand_id);
                        }
                    },
                    'items.rawMaterial',
                    'items.purchaseInvoiceItems.purchaseInvoice',
                    'purchaseInvoices'
                ])->orderBy('id', 'desc')->get();

                $rows = [];
                foreach ($purchaseOrders as $po) {
                    $totalOrderedPo = (float) $po->items->sum('quantity');
                    $totalReceivedPo = 0;
                    $itemsData = [];
                    $itemSno = 1;
                    $latestReceiptDate = null;
                    $materialNames = [];

                    if ($po->purchaseInvoices && $po->purchaseInvoices->isNotEmpty()) {
                        foreach ($po->purchaseInvoices as $inv) {
                            $iDate = $inv->invoice_date ?: $inv->created_at;
                            if ($iDate) {
                                $parsed = \Carbon\Carbon::parse($iDate)->startOfDay();
                                if (!$latestReceiptDate || $parsed->gt($latestReceiptDate)) {
                                    $latestReceiptDate = $parsed;
                                }
                            }
                        }
                    }

                    foreach ($po->items as $item) {
                        $itemOrd = (float) $item->quantity;
                        $itemRec = (float) $item->purchaseInvoiceItems->sum('qty_received');
                        $itemBal = max(0, $itemOrd - $itemRec);
                        $totalReceivedPo += $itemRec;

                        $matName = optional($item->rawMaterial)->name ?: 'N/A';
                        $materialNames[] = $matName;

                        foreach ($item->purchaseInvoiceItems as $pItem) {
                            $iDate = ($pItem->purchaseInvoice ? $pItem->purchaseInvoice->invoice_date : null) ?: $pItem->created_at;
                            if ($iDate) {
                                $parsed = \Carbon\Carbon::parse($iDate)->startOfDay();
                                if (!$latestReceiptDate || $parsed->gt($latestReceiptDate)) {
                                    $latestReceiptDate = $parsed;
                                }
                            }
                        }

                        $itemsData[] = [
                            'sno' => $itemSno++,
                            'material_name' => $matName,
                            'ordered' => number_format($itemOrd, 2),
                            'received' => number_format($itemRec, 2),
                            'balance' => number_format($itemBal, 2),
                        ];
                    }
                    $totalPendingPo = max(0, $totalOrderedPo - $totalReceivedPo);

                    $delayHtml = '-';
                    $delayText = '';
                    if ($po->due_date) {
                        $dueDate = \Carbon\Carbon::parse($po->due_date)->startOfDay();

                        if ($totalPendingPo <= 0 || strtolower($po->status) == 'closed' || $po->is_self_closed) {
                            if ($latestReceiptDate) {
                                if ($latestReceiptDate->gt($dueDate)) {
                                    $diffDays = $latestReceiptDate->diffInDays($dueDate);
                                    $delayHtml = '<span class="badge bg-danger rounded-pill">' . $diffDays . ' Days Delay</span>';
                                    $delayText = $diffDays . ' Days Delay';
                                } else {
                                    $delayHtml = '<span class="badge bg-success rounded-pill">On Time</span>';
                                    $delayText = 'On Time';
                                }
                            } else {
                                $delayHtml = '<span class="badge bg-success rounded-pill">On Time</span>';
                                $delayText = 'On Time';
                            }
                        } else {
                            $today = \Carbon\Carbon::now()->startOfDay();
                            if ($today->gt($dueDate)) {
                                $diffDays = $today->diffInDays($dueDate);
                                $delayHtml = '<span class="badge bg-danger rounded-pill">' . $diffDays . ' Days Overdue</span>';
                                $delayText = $diffDays . ' Days Overdue';
                            } else {
                                $daysLeft = $today->diffInDays($dueDate, false);
                                $delayHtml = '<span class="badge bg-info rounded-pill">' . abs($daysLeft) . ' Days Left</span>';
                                $delayText = abs($daysLeft) . ' Days Left';
                            }
                        }
                    }

                    $poDateFormatted = $po->po_date ? \Carbon\Carbon::parse($po->po_date)->format('d-M-Y') : '-';
                    $orderDateFormatted = $po->reference_date ? \Carbon\Carbon::parse($po->reference_date)->format('d-M-Y') : $poDateFormatted;
                    $expectedDeliveryFormatted = $po->due_date ? \Carbon\Carbon::parse($po->due_date)->format('d-M-Y') : '-';
                    $supplierName = optional($po->supplier)->name ?: 'N/A';
                    $remarksText = $po->remarks ?: '-';

                    $rows[] = [
                        'po_number' => $po->po_number,
                        'po_date' => $poDateFormatted,
                        'supplier' => $supplierName,
                        'supplier_name' => $supplierName,
                        'total_ordered' => number_format($totalOrderedPo, 2),
                        'total_received' => number_format($totalReceivedPo, 2),
                        'total_pending' => number_format($totalPendingPo, 2),
                        'order_date' => $orderDateFormatted,
                        'expected_delivery' => $expectedDeliveryFormatted,
                        'delay' => $delayHtml,
                        'remarks' => htmlspecialchars($remarksText),
                        'items' => $itemsData,
                        '_raw_ordered' => $totalOrderedPo,
                        '_raw_received' => $totalReceivedPo,
                        '_raw_pending' => $totalPendingPo,
                        '_search_text' => strtolower(implode(' ', [
                            $po->po_number,
                            $poDateFormatted,
                            $po->po_date ? date('d-m-Y', strtotime($po->po_date)) : '',
                            $supplierName,
                            $totalOrderedPo,
                            number_format($totalOrderedPo, 2),
                            $totalReceivedPo,
                            number_format($totalReceivedPo, 2),
                            $totalPendingPo,
                            number_format($totalPendingPo, 2),
                            $orderDateFormatted,
                            $expectedDeliveryFormatted,
                            $delayText,
                            $remarksText,
                            implode(' ', $materialNames)
                        ]))
                    ];
                }

                $totalRecords = count($rows);

                if (!empty($search)) {
                    $lowerSearch = strtolower(trim($search));
                    $cleanSearch = str_replace([',', ' ', '%'], '', $lowerSearch);

                    $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch, $cleanSearch) {
                        $st = $r['_search_text'] ?? '';
                        if (strpos($st, $lowerSearch) !== false) {
                            return true;
                        }
                        if ($cleanSearch !== '') {
                            $stClean = str_replace([',', ' ', '%'], '', $st);
                            if (strpos($stClean, $cleanSearch) !== false) {
                                return true;
                            }
                        }
                        return false;
                    }));
                } else {
                    $filteredRows = $rows;
                }

                $filteredRecords = count($filteredRows);

                // Calculate totals for filtered rows
                $sumOrdered = array_sum(array_column($filteredRows, '_raw_ordered'));
                $sumReceived = array_sum(array_column($filteredRows, '_raw_received'));
                $sumPending = array_sum(array_column($filteredRows, '_raw_pending'));

                $totals = [
                    'total_ordered' => number_format($sumOrdered, 2),
                    'total_received' => number_format($sumReceived, 2),
                    'total_pending' => number_format($sumPending, 2),
                ];

                if ($length != -1) {
                    $pageRows = array_slice($filteredRows, $start, $length);
                } else {
                    $pageRows = $filteredRows;
                }

                // Add DT_RowIndex for display
                $count = $start + 1;
                foreach ($pageRows as &$pRow) {
                    $pRow['DT_RowIndex'] = $count++;
                }
                unset($pRow);

                return response()->json([
                    'draw' => intval($draw),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $pageRows,
                    'totals' => $totals,
                ]);

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

                            strpos((string)($item['plain'] ?? ''), $search) !== false ||
                            strpos(number_format((float)($item['plain'] ?? 0), 2), $search) !== false ||
                            strpos((string)($item['print'] ?? ''), $search) !== false ||
                            strpos(number_format((float)($item['print'] ?? 0), 2), $search) !== false ||
                            strpos((string)($item['checked'] ?? ''), $search) !== false ||
                            strpos(number_format((float)($item['checked'] ?? 0), 2), $search) !== false ||

                            strpos((string)($item['opening'] ?? ''), $search) !== false ||
                            strpos(number_format((float)($item['opening'] ?? 0), 2), $search) !== false ||
                            strpos((string)($item['inward'] ?? ''), $search) !== false ||
                            strpos(number_format((float)($item['inward'] ?? 0), 2), $search) !== false ||
                            strpos((string)($item['outward'] ?? ''), $search) !== false ||
                            strpos(number_format((float)($item['outward'] ?? 0), 2), $search) !== false ||
                            strpos((string)($item['closing'] ?? ''), $search) !== false ||
                            strpos(number_format((float)($item['closing'] ?? 0), 2), $search) !== false ||
                            strpos((string)($item['closing_cost'] ?? ''), $search) !== false ||
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
                    $searchLower = strtolower(trim($search));
                    $ageingData = array_filter($ageingData, function($item) use ($searchLower) {
                        return (strpos(strtolower($item['brand'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['item_name'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['style'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['color'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['fabric_type'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['width'] ?? ''), $searchLower) !== false)
                            || (strpos((string)($item['0_30'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['0_30'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['31_60'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['31_60'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['61_90'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['61_90'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['91_plus'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['91_plus'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['total'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['total'] ?? 0), 2), $searchLower) !== false);
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
                    $searchLower = strtolower(trim($search));
                    $consumptionData = array_filter($consumptionData, function($item) use ($searchLower) {
                        $dtStr = $item['date'] ? date('d-M-Y', strtotime($item['date'])) : '';
                        return (strpos(strtolower($item['job_card_no'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['brand'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['status'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($dtStr), $searchLower) !== false)
                            || (strpos((string)($item['total_fabric'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['total_fabric'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['wastage'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['wastage'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['total_garments'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['total_garments'] ?? 0)), $searchLower) !== false)
                            || (strpos((string)($item['average'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['average'] ?? 0), 2), $searchLower) !== false);
                    });
                }

                $filteredRecords = count($consumptionData);

                $totals = [
                    'total_fabric' => number_format(collect($consumptionData)->sum('total_fabric'), 2),
                    'total_wastage' => number_format(collect($consumptionData)->sum('wastage'), 2),
                    'total_garments' => number_format(collect($consumptionData)->sum('total_garments')),
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
                        'total_fabric' => number_format($row['total_fabric'], 2),
                        'wastage' => number_format($row['wastage'], 2),
                        'total_garments' => number_format($row['total_garments']),
                        'average' => number_format($row['average'], 2),
                        'status' => $statusBadge,
                    ];
                }
                break;

            case 'minstock-report':
                $minStockData = $this->getMinStockData($storeCategoryId, $request);
                $totalRecords = count($minStockData);

                if (!empty($search)) {
                    $searchLower = strtolower(trim($search));
                    $minStockData = array_filter($minStockData, function($item) use ($searchLower) {
                        $stText = '';
                        if ($item['closing'] <= 0) {
                            $stText = 'out of stock';
                        } elseif ($item['closing'] <= $item['min_stock']) {
                            $stText = 'low stock';
                        } else {
                            $stText = 'excess stock';
                        }

                        return (strpos(strtolower($item['art_no'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['brand'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['item_name'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['style'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['color'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['fabric_type'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['width'] ?? ''), $searchLower) !== false)
                            || (strpos($stText, $searchLower) !== false)
                            || (strpos((string)($item['min_stock'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['min_stock'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['closing'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['closing'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['shortage'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['shortage'] ?? 0), 2), $searchLower) !== false);
                    });
                }

                $filteredRecords = count($minStockData);

                $totals = [
                    'min_stock' => number_format(collect($minStockData)->sum('min_stock'), 2),
                    'closing' => number_format(collect($minStockData)->sum('closing'), 2),
                    'shortage' => number_format(collect($minStockData)->sum('shortage'), 2),
                ];

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
                    $searchLower = strtolower(trim($search));
                    $returnGoodsData = array_filter($returnGoodsData, function($item) use ($searchLower) {
                        $itemArr = (array) $item;
                        $retDt = $itemArr['return_date'] ? date('d-M-Y', strtotime($itemArr['return_date'])) : '';
                        return (strpos(strtolower($itemArr['return_no'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($itemArr['supplier_name'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($itemArr['item_name'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($itemArr['reason'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($retDt), $searchLower) !== false)
                            || (strpos((string)($itemArr['quantity'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($itemArr['quantity'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($itemArr['rate'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($itemArr['rate'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($itemArr['dn_grand_total'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($itemArr['dn_grand_total'] ?? 0), 2), $searchLower) !== false);
                    });
                }

                $filteredRecords = count($returnGoodsData);

                $calcRows = [];
                $totalQty = 0;
                $totalSubTotal = 0;
                $totalDiscount = 0;
                $totalTaxable = 0;
                $totalCgst = 0;
                $totalSgst = 0;
                $totalIgst = 0;
                $totalRoundOff = 0;
                $totalGrandTotal = 0;

                foreach ($returnGoodsData as $row) {
                    $rowArr = (array) $row;
                    $itemQty = (float) ($rowArr['quantity'] ?? 0);
                    $itemRate = (float) ($rowArr['rate'] ?? 0);
                    $itemAmount = (float) ($rowArr['item_amount'] ?? ($itemQty * $itemRate));
                    $dnSubTotal = (float) ($rowArr['dn_sub_total'] ?? 0);
                    $ratio = ($dnSubTotal > 0) ? ($itemAmount / $dnSubTotal) : 1.0;

                    $subTotal = $itemAmount;
                    $dnDiscount = (float) ($rowArr['dn_discount_amount'] ?? 0);
                    $discount = round($dnDiscount * $ratio, 2);

                    $dnTaxable = (float) ($rowArr['dn_taxable_amount'] ?? 0);
                    if ($dnTaxable > 0) {
                        $taxableValue = round($dnTaxable * $ratio, 2);
                    } else {
                        $taxableValue = round($subTotal - $discount, 2);
                    }

                    $cgstPercent = (float) ($rowArr['dn_cgst_percent'] ?? 0);
                    $sgstPercent = (float) ($rowArr['dn_sgst_percent'] ?? 0);
                    $igstPercent = (float) ($rowArr['dn_igst_percent'] ?? 0);

                    $cgstAmount = round($taxableValue * ($cgstPercent / 100), 2);
                    $sgstAmount = round($taxableValue * ($sgstPercent / 100), 2);
                    $igstAmount = round($taxableValue * ($igstPercent / 100), 2);

                    $dnRoundOff = (float) ($rowArr['dn_round_off'] ?? 0);
                    $roundOffType = $rowArr['dn_round_off_type'] ?? 'Add';
                    $signedRoundOff = ($roundOffType === 'Less') ? -$dnRoundOff : $dnRoundOff;
                    $itemRoundOff = round($signedRoundOff * $ratio, 2);

                    $dnGrandTotal = (float) ($rowArr['dn_grand_total'] ?? 0);
                    if ($ratio >= 0.999 && $ratio <= 1.001) {
                        $totalAmount = $dnGrandTotal;
                        $itemRoundOff = $signedRoundOff;
                        $discount = $dnDiscount;
                        if ($dnTaxable > 0) $taxableValue = $dnTaxable;
                        if ($igstPercent > 0 && (float)($rowArr['dn_tax_amount'] ?? 0) > 0 && $cgstPercent == 0 && $sgstPercent == 0) {
                            $igstAmount = (float)$rowArr['dn_tax_amount'];
                        }
                    } else {
                        $totalAmount = round($taxableValue + $cgstAmount + $sgstAmount + $igstAmount + $itemRoundOff, 2);
                    }

                    $totalQty += $itemQty;
                    $totalSubTotal += $subTotal;
                    $totalDiscount += $discount;
                    $totalTaxable += $taxableValue;
                    $totalCgst += $cgstAmount;
                    $totalSgst += $sgstAmount;
                    $totalIgst += $igstAmount;
                    $totalRoundOff += $itemRoundOff;
                    $totalGrandTotal += $totalAmount;

                    $calcRows[] = [
                        'row' => $rowArr,
                        'sub_total' => $subTotal,
                        'discount' => $discount,
                        'taxable_value' => $taxableValue,
                        'cgst_percent' => $cgstPercent,
                        'cgst_amount' => $cgstAmount,
                        'sgst_percent' => $sgstPercent,
                        'sgst_amount' => $sgstAmount,
                        'igst_percent' => $igstPercent,
                        'igst_amount' => $igstAmount,
                        'round_off' => $itemRoundOff,
                        'total_amount' => $totalAmount,
                    ];
                }

                $totals = [
                    'quantity' => number_format($totalQty, 2),
                    'sub_total' => number_format($totalSubTotal, 2),
                    'discount' => number_format($totalDiscount, 2),
                    'taxable_value' => number_format($totalTaxable, 2),
                    'cgst_amount' => number_format($totalCgst, 2),
                    'sgst_amount' => number_format($totalSgst, 2),
                    'igst_amount' => number_format($totalIgst, 2),
                    'round_off' => ($totalRoundOff != 0 ? ($totalRoundOff > 0 ? '+' : '') . number_format($totalRoundOff, 2) : '0.00'),
                    'grand_total' => number_format($totalGrandTotal, 2),
                ];

                if ($length != -1) {
                    $calcRows = array_slice($calcRows, $start, $length);
                }

                $count = $start + 1;
                foreach ($calcRows as $calc) {
                    $rowArr = $calc['row'];
                    $cgstDisplay = ($calc['cgst_percent'] > 0 || $calc['cgst_amount'] > 0)
                        ? number_format($calc['cgst_percent'], 2) . '% (' . number_format($calc['cgst_amount'], 2) . ')'
                        : '-';
                    $sgstDisplay = ($calc['sgst_percent'] > 0 || $calc['sgst_amount'] > 0)
                        ? number_format($calc['sgst_percent'], 2) . '% (' . number_format($calc['sgst_amount'], 2) . ')'
                        : '-';
                    $igstDisplay = ($calc['igst_percent'] > 0 || $calc['igst_amount'] > 0)
                        ? number_format($calc['igst_percent'], 2) . '% (' . number_format($calc['igst_amount'], 2) . ')'
                        : '-';
                    $roundOffDisplay = ($calc['round_off'] != 0)
                        ? (($calc['round_off'] > 0 ? '+' : '') . number_format($calc['round_off'], 2))
                        : '0.00';

                    $data[] = [
                        'DT_RowIndex' => $count++,
                        'return_date' => $rowArr['return_date'] ? date('d-M-Y', strtotime($rowArr['return_date'])) : '-',
                        'return_no' => $rowArr['return_no'],
                        'supplier_name' => $rowArr['supplier_name'],
                        'item_name' => $rowArr['item_name'],
                        'quantity' => number_format($rowArr['quantity'], 2),
                        'rate' => number_format($rowArr['rate'], 2),
                        'sub_total' => number_format($calc['sub_total'], 2),
                        'discount' => number_format($calc['discount'], 2),
                        'taxable_value' => number_format($calc['taxable_value'], 2),
                        'cgst_percent' => ($calc['cgst_percent'] > 0) ? number_format($calc['cgst_percent'], 2) . '%' : '-',
                        'cgst_amount' => ($calc['cgst_amount'] > 0) ? number_format($calc['cgst_amount'], 2) : '0.00',
                        'sgst_percent' => ($calc['sgst_percent'] > 0) ? number_format($calc['sgst_percent'], 2) . '%' : '-',
                        'sgst_amount' => ($calc['sgst_amount'] > 0) ? number_format($calc['sgst_amount'], 2) : '0.00',
                        'igst_percent' => ($calc['igst_percent'] > 0) ? number_format($calc['igst_percent'], 2) . '%' : '-',
                        'igst_amount' => ($calc['igst_amount'] > 0) ? number_format($calc['igst_amount'], 2) : '0.00',
                        'cgst' => $cgstDisplay,
                        'sgst' => $sgstDisplay,
                        'igst' => $igstDisplay,
                        'round_off' => $roundOffDisplay,
                        'total_amount' => number_format($calc['total_amount'], 2),
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
                    $searchLower = strtolower(trim($search));
                    $performanceData = array_filter($performanceData, function($item) use ($searchLower) {
                        return (strpos(strtolower($item['supplier_name'] ?? ''), $searchLower) !== false)
                            || (strpos((string)($item['po_count'] ?? ''), $searchLower) !== false)
                            || (strpos((string)($item['total_po_value'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['total_po_value'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['dn_count'] ?? ''), $searchLower) !== false)
                            || (strpos((string)($item['return_rate'] ?? ''), $searchLower) !== false)
                            || (strpos(($item['return_rate'] ?? '') . '%', $searchLower) !== false);
                    });
                }

                $filteredRecords = count($performanceData);

                $totPoCount = collect($performanceData)->sum('po_count');
                $totPoValue = collect($performanceData)->sum('total_po_value');
                $totDnCount = collect($performanceData)->sum('dn_count');
                $overallReturnRate = ($totPoCount > 0) ? round(($totDnCount / $totPoCount) * 100, 2) : 0;

                $totals = [
                    'po_count' => number_format($totPoCount),
                    'total_po_value' => '₹ ' . number_format($totPoValue, 2),
                    'dn_count' => number_format($totDnCount),
                    'return_rate' => number_format($overallReturnRate, 2) . '%',
                ];

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
                    $searchLower = strtolower(trim($search));
                    $casinoData = array_filter($casinoData, function($item) use ($searchLower) {
                        return (strpos(strtolower($item['brand_name'] ?? ''), $searchLower) !== false)
                            || (strpos(strtolower($item['width'] ?? ''), $searchLower) !== false)
                            || (strpos((string)($item['plain'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['plain'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['white'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['white'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['print'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['print'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['checked'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['checked'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['striped'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['striped'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['total'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['total'] ?? 0), 2), $searchLower) !== false);
                    });
                }

                $filteredRecords = count($casinoData);

                $totals = [
                    'plain' => number_format(collect($casinoData)->sum('plain'), 2),
                    'white' => number_format(collect($casinoData)->sum('white'), 2),
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
                        'white' => number_format($row['white'], 2),
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
                        if (strpos(strtolower($item['brand_name'] ?? ''), $searchLower) !== false) return true;
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

            case 'brandwise-po-drilldown':
                return $this->getBrandwisePoDrilldownData($request);

            case 'cost-report':
                $costData = $this->getAverageCostData($request);
                $totalRecords = count($costData);

                if (!empty($search)) {
                    $searchLower = strtolower(trim($search));
                    $costData = array_filter($costData, function($item) use ($searchLower) {
                        return (strpos(strtolower($item['item_name'] ?? ''), $searchLower) !== false)
                            || (strpos((string)($item['total_qty'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['total_qty'] ?? 0)), $searchLower) !== false)
                            || (strpos((string)($item['total_amount'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['total_amount'] ?? 0), 2), $searchLower) !== false)
                            || (strpos((string)($item['average_cost'] ?? ''), $searchLower) !== false)
                            || (strpos(number_format((float)($item['average_cost'] ?? 0), 2), $searchLower) !== false);
                    });
                }

                $filteredRecords = count($costData);

                $totQty = collect($costData)->sum('total_qty');
                $totAmount = collect($costData)->sum('total_amount');
                $overallAvgCost = ($totQty > 0) ? round($totAmount / $totQty, 2) : 0;

                $totals = [
                    'total_qty' => number_format($totQty, 2),
                    'total_amount' => '₹ ' . number_format($totAmount, 2),
                    'average_cost' => '₹ ' . number_format($overallAvgCost, 2),
                ];

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
            $firstBrand = DB::table('brands')->where('status', 'Active')->whereNull('deleted_at')->where('brand_name', 'like', 'CASINO%')->first() ?? DB::table('brands')->where('status', 'Active')->whereNull('deleted_at')->first();
            $brandId = $firstBrand ? $firstBrand->id : null;
        }

        if (!$brandId) {
            return [];
        }

        $artNos = DB::table('stock_entry_items')->where('brand_id', $brandId)->whereNotNull('art_no')->where('art_no', '!=', '')->whereNull('deleted_at')->distinct()->pluck('art_no')->toArray();

        if (empty($artNos)) {
            $artNos = DB::table('job_card_fabric_details as jcfd')->join('job_card_entries as jce', 'jcfd.job_card_entry_id', '=', 'jce.id')->where('jce.brand_id', $brandId)->whereNotNull('jcfd.art_no')->where('jcfd.art_no', '!=', '')->whereNull('jcfd.deleted_at')->distinct()->pluck('jcfd.art_no')->toArray();
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
        $orderFabricMap = DB::table('grn_entry_items as gei')->join('grn_entries as ge', 'gei.grn_entry_id', '=', 'ge.id')->leftJoin('purchase_invoices as pi', 'ge.purchase_invoice_id', '=', 'pi.id')->leftJoin('purchase_orders as po', 'pi.purchase_order_id', '=', 'po.id')->where('po.store_type_id', 1)->whereIn('gei.art_no', $artNos)->whereNull('ge.deleted_at')->whereNull('gei.deleted_at')->whereNull('pi.deleted_at')->whereNull('po.deleted_at')->select('gei.art_no', DB::raw('SUM(gei.qty_ordered) as total_qty'))->groupBy('gei.art_no')->pluck('total_qty', 'art_no');

        // Order Fabric PO Numbers per art_no
        $orderFabricPoNumbers = DB::table('grn_entry_items as gei')
            ->join('grn_entries as ge', 'gei.grn_entry_id', '=', 'ge.id')
            ->leftJoin('purchase_invoices as pi', 'ge.purchase_invoice_id', '=', 'pi.id')
            ->leftJoin('purchase_orders as po', 'pi.purchase_order_id', '=', 'po.id')
            ->where('po.store_type_id', 1)
            ->whereIn('gei.art_no', $artNos)
            ->whereNotNull('po.po_number')
            ->whereNull('ge.deleted_at')
            ->whereNull('gei.deleted_at')
            ->whereNull('pi.deleted_at')
            ->whereNull('po.deleted_at')
            ->select('gei.art_no', 'po.po_number')
            ->distinct()
            ->orderBy('po.id', 'desc')
            ->get()
            ->groupBy('art_no');

        // Fabric Stock per art_no (include store_type_id = 1 or stock_type = raw_material)
        $fabricStockMap = DB::table('stock_entry_items')
            ->where(function($q) {
                $q->where('store_type_id', 1)
                  ->orWhere('stock_type', 'raw_material');
            })
            ->where('brand_id', $brandId)
            ->whereIn('art_no', $artNos)
            ->whereNull('deleted_at')
            ->select('art_no', DB::raw('SUM(qty_in - COALESCE(qty_out, 0)) as total_stock'))->groupBy('art_no')->pluck('total_stock', 'art_no');

        // FG Min Stock records bulk
        $minRecordsBulk = DB::table('fg_min_stocks as fms')->join('stock_entry_items as sei', 'fms.stock_entry_item_id', '=', 'sei.id')->whereIn('sei.art_no', $artNos)->where('fms.status', 'Active')->whereNull('fms.deleted_at')->whereNull('sei.deleted_at')->select('sei.art_no', 'sei.sleeve_type', 'sei.size', 'fms.min_stock')->get()->groupBy('art_no');

        // FG Current Stock bulk
        $fgQuery = DB::table('stock_entry_items')->whereIn('art_no', $artNos)->where('stock_type', 'finished_goods')->whereNull('deleted_at');
        if ($fromDate) {
            $fgQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $fgQuery->whereDate('created_at', '<=', $toDate);
        }
        $fgRecordsBulk = $fgQuery->select('art_no', 'sleeve_type', 'size', DB::raw('SUM(qty_in - COALESCE(qty_out, 0)) as total_qty'))->groupBy('art_no', 'sleeve_type', 'size')->get()->groupBy('art_no');

        // Job Cards moved to FG via Posted Production Receipts
        $fgJcIds = DB::table('production_receipts')
            ->where('status', 'Posted')
            ->whereNotNull('job_card_id')
            ->pluck('job_card_id')
            ->unique()
            ->toArray();

        // WIP bulk - Group by art_no, job_card_id, job_card_no, remarks, size
        $wipQuery = DB::table('job_card_matrix_quantities as jcmq')
            ->join('job_card_fabric_details as jcfd', 'jcmq.job_card_fabric_detail_id', '=', 'jcfd.id')
            ->join('job_card_entries as jce', 'jcfd.job_card_entry_id', '=', 'jce.id')
            ->leftJoin('service_providers as sp', 'jce.service_provider_id', '=', 'sp.id')
            ->whereIn('jcfd.art_no', $artNos)
            ->whereNotIn('jce.status', ['cancelled'])
            ->whereNotIn('jce.id', $fgJcIds)
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

        // Bulk preload schedules and tasks for all WIP job cards to resolve Stage Status
        $allWipJcIds = [];
        foreach ($wipRecordsBulk as $artRecords) {
            foreach ($artRecords as $r) {
                if (!empty($r->job_card_id)) {
                    $allWipJcIds[$r->job_card_id] = $r->job_card_id;
                }
            }
        }
        $allWipJcIds = array_values($allWipJcIds);

        $schedulesByJc = collect();
        $tasksByJc = collect();
        $opsByJc = collect();
        if (!empty($allWipJcIds)) {
            $schedulesByJc = DB::table('process_schedules')->whereIn('job_card_entry_id', $allWipJcIds)->whereNull('deleted_at')->select('id', 'job_card_entry_id', 'stage', 'status')->orderBy('id', 'asc')->get()->groupBy('job_card_entry_id');

            $tasksByJc = DB::table('tasks')->whereIn('job_card_entry_id', $allWipJcIds)->whereNull('deleted_at')->select('id', 'job_card_entry_id', 'stage_id', 'status')->get()->groupBy('job_card_entry_id');

            $opsByJc = DB::table('job_card_operations as jco')->leftJoin('operation_stages as os', 'jco.operation_stage_id', '=', 'os.id')->whereIn('jco.job_card_entry_id', $allWipJcIds)->select('jco.id', 'jco.job_card_entry_id', 'jco.operation_stage_id', 'os.operation_stage_name')->orderBy('jco.id', 'asc')->get()->groupBy('job_card_entry_id');
        }

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
                    $stageStatus = $this->resolveJobCardCurrentStage($firstRec->job_card_id ?? 0, $schedulesByJc, $tasksByJc, $opsByJc);

                    // Skip completed or moved to FG
                    if (in_array($firstRec->job_card_id ?? 0, $fgJcIds)) {
                        continue;
                    }

                    $wipItem = [
                        'label' => 'WIP-' . $wipIdx++,
                        'unit' => ($firstRec && !empty($firstRec->unit_name)) ? $firstRec->unit_name : '-',
                        'c_no' => $jcNo,
                        'stage_status' => $stageStatus,
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
            }

            if (empty($wipList)) {
                $emptyWip = [
                    'label' => 'WIP-1',
                    'unit' => '-',
                    'c_no' => '-',
                    'stage_status' => '-',
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

            $artPoNumbers = isset($orderFabricPoNumbers[$artNo]) 
                ? $orderFabricPoNumbers[$artNo]->pluck('po_number')->unique()->values()->toArray() 
                : [];

            $result[] = [
                'art_no'        => $artNo,
                'order_fabric'  => number_format($orderFabricVal, 2),
                'po_numbers'    => $artPoNumbers,
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

    private function resolveJobCardCurrentStage($jobCardId, $schedulesByJc, $tasksByJc, $opsByJc)
    {
        if (!$jobCardId) return '-';

        $schedules = $schedulesByJc->get($jobCardId, collect());
        if ($schedules->isNotEmpty()) {
            $tasks = $tasksByJc->get($jobCardId, collect());

            foreach ($schedules as $schedule) {
                $scheduleTasks = $tasks->where('stage_id', $schedule->id);

                $isCompleted = false;
                if (strtolower(trim($schedule->status ?? '')) === 'completed') {
                    $isCompleted = true;
                } elseif ($scheduleTasks->isNotEmpty() && $scheduleTasks->every(function($t) {
                    return strtolower(trim($t->status ?? '')) === 'completed';
                })) {
                    $isCompleted = true;
                }

                if (!$isCompleted) {
                    return strtoupper(trim($schedule->stage ?? ''));
                }
            }

            return 'COMPLETED';
        }

        $operations = $opsByJc->get($jobCardId, collect());
        if ($operations->isNotEmpty()) {
            $tasks = $tasksByJc->get($jobCardId, collect());

            foreach ($operations as $op) {
                $stageName = strtoupper(trim($op->operation_stage_name ?? ''));
                $osId = $op->operation_stage_id;

                $opTasks = $tasks->filter(function($t) use ($osId) {
                    return $t->stage_id == $osId;
                });

                $isCompleted = false;
                if ($opTasks->isNotEmpty() && $opTasks->every(function($t) {
                    return strtolower(trim($t->status ?? '')) === 'completed';
                })) {
                    $isCompleted = true;
                }

                if (!$isCompleted) {
                    return $stageName ?: 'CUTTING';
                }
            }

            return 'COMPLETED';
        }

        return 'CUTTING';
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
                    if (strpos(strtolower($wip['stage_status'] ?? ''), $searchLower) !== false) return true;
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
        $isDhotiBrand = (strpos(strtoupper($brandName), 'CASINO DHOTI SHIRT') !== false);

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
            'stockEntry.grnEntry.purchaseInvoice',
            'grnEntryItem.grnEntry.purchaseInvoice',
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

        // Preload any purchase invoices for art_nos that do not have a direct GRN link (e.g. opening stock SE00003)
        $artNos = $stockItems->pluck('art_no')->filter()->unique()->toArray();
        $artInvoices = [];
        if (!empty($artNos)) {
            $artInvoices = DB::table('grn_entry_items as gei')
                ->join('grn_entries as ge', 'gei.grn_entry_id', '=', 'ge.id')
                ->join('purchase_invoices as pi', 'ge.purchase_invoice_id', '=', 'pi.id')
                ->whereIn('gei.art_no', $artNos)
                ->whereNull('gei.deleted_at')
                ->whereNull('ge.deleted_at')
                ->whereNull('pi.deleted_at')
                ->whereNotNull('pi.invoice_no')
                ->where('pi.invoice_no', '!=', '')
                ->orderBy('pi.id', 'desc')
                ->pluck('pi.invoice_no', 'gei.art_no')
                ->toArray();
        }

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
            
            $invoiceNo = null;
            if ($item->grnEntryItem) {
                $rate = (float)($item->grnEntryItem->rate ?: $rate);
                if ($item->grnEntryItem->grnEntry) {
                    if ($item->grnEntryItem->grnEntry->purchaseInvoice && !empty($item->grnEntryItem->grnEntry->purchaseInvoice->invoice_no)) {
                        $invoiceNo = $item->grnEntryItem->grnEntry->purchaseInvoice->invoice_no;
                    }
                    $docNumber = $item->grnEntryItem->grnEntry->grn_number ?: ($item->grnEntryItem->grnEntry->grn_no ?: $docNumber);
                }
            }
            
            if ($item->stockEntry) {
                $stockDate = $item->stockEntry->stock_date ? date('d-m-Y', strtotime($item->stockEntry->stock_date)) : '-';
                $inwardDate = $stockDate;
                
                if ($item->stockEntry->grnEntry) {
                    if (!$invoiceNo && $item->stockEntry->grnEntry->purchaseInvoice && !empty($item->stockEntry->grnEntry->purchaseInvoice->invoice_no)) {
                        $invoiceNo = $item->stockEntry->grnEntry->purchaseInvoice->invoice_no;
                    }
                    $docNumber = $item->stockEntry->grnEntry->grn_number ?: ($item->stockEntry->grnEntry->grn_no ?: $docNumber);
                    if ($item->stockEntry->grnEntry->grn_date) {
                        $inwardDate = date('d-m-Y', strtotime($item->stockEntry->grnEntry->grn_date));
                    }
                } elseif ($docNumber === '-') {
                    $docNumber = $item->stockEntry->stock_entry_no ?: '-';
                }
            }

            // Fallback: If no direct invoice found, check if this art_no has a Purchase Invoice in the database
            if (!$invoiceNo && !empty($item->art_no) && isset($artInvoices[$item->art_no])) {
                $invoiceNo = $artInvoices[$item->art_no];
            }
            
            $qtyIn = (float)$item->qty_in;
            $date = $item->created_at ? $item->created_at->format('Y-m-d') : '';
            $remarks = strtolower($item->stockEntry ? ($item->stockEntry->remarks ?? '') : '');
            $rawEntryType = strtolower($item->stockEntry ? ($item->stockEntry->entry_type ?? '') : '');
            
            $isOpening = ($fromDate && $date && $date < $fromDate) || str_contains($remarks, 'opening') || str_contains($rawEntryType, 'opening');

            if ($invoiceNo) {
                $docType = 'INVOICE';
                $docNumber = $invoiceNo;
            } else {
                $docType = $isOpening ? 'OPENING' : 'RECEIPTS';
            }
            
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

    public function getBrandwisePoDrilldownData(Request $request)
    {
        $artNo = $request->art_no;
        if (empty($artNo)) {
            return response()->json([
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'totals' => [
                    'order_qty' => '0.00',
                    'received_qty' => '0.00',
                    'balance_qty' => '0.00',
                ]
            ]);
        }

        $query = DB::table('grn_entry_items as gei')
            ->join('grn_entries as ge', 'gei.grn_entry_id', '=', 'ge.id')
            ->leftJoin('purchase_invoices as pi', 'ge.purchase_invoice_id', '=', 'pi.id')
            ->leftJoin('purchase_orders as po', 'pi.purchase_order_id', '=', 'po.id')
            ->leftJoin('suppliers as sup', 'po.supplier_id', '=', 'sup.id')
            ->where('po.store_type_id', 1)
            ->where('gei.art_no', $artNo)
            ->whereNull('ge.deleted_at')
            ->whereNull('gei.deleted_at')
            ->whereNull('pi.deleted_at')
            ->whereNull('po.deleted_at');

        if ($request->filled('supplier_id')) {
            $query->where('po.supplier_id', $request->supplier_id);
        }

        $records = $query->select(
            'po.id as po_id',
            'po.po_number',
            'po.po_date',
            'po.due_date',
            'po.reference_date',
            'po.status',
            'po.is_self_closed',
            'po.remarks',
            'sup.name as supplier_name',
            'gei.art_no',
            DB::raw('SUM(gei.qty_ordered) as order_qty'),
            DB::raw('SUM(gei.qty_received) as received_qty'),
            DB::raw('MAX(pi.invoice_date) as latest_inv_date')
        )
        ->groupBy(
            'po.id',
            'po.po_number',
            'po.po_date',
            'po.due_date',
            'po.reference_date',
            'po.status',
            'po.is_self_closed',
            'po.remarks',
            'sup.name',
            'gei.art_no'
        )
        ->orderBy('po.id', 'desc')
        ->get();

        $data = [];
        $today = \Carbon\Carbon::today();
        $totalOrder = 0;
        $totalReceived = 0;
        $totalBalance = 0;

        foreach ($records as $index => $row) {
            $orderQty = floatval($row->order_qty ?? 0);
            $receivedQty = floatval($row->received_qty ?? 0);
            $balanceQty = max(0, $orderQty - $receivedQty);

            $totalOrder += $orderQty;
            $totalReceived += $receivedQty;
            $totalBalance += $balanceQty;

            $delayHtml = '<span class="text-muted">-</span>';
            $dueDateObj = !empty($row->due_date) ? \Carbon\Carbon::parse($row->due_date)->startOfDay() : null;
            $isCompleted = ($balanceQty <= 0) || (strtolower($row->status) === 'completed') || (strtolower($row->status) === 'closed') || ($row->is_self_closed == 1);

            if ($dueDateObj) {
                if ($isCompleted) {
                    $compDate = !empty($row->latest_inv_date) ? \Carbon\Carbon::parse($row->latest_inv_date)->startOfDay() : null;
                    if ($compDate && $compDate->gt($dueDateObj)) {
                        $diffDays = $compDate->diffInDays($dueDateObj);
                        $delayHtml = '<span class="text-danger fw-bold">' . $diffDays . ' Days Delay</span>';
                    } else {
                        $delayHtml = '<span class="text-success fw-bold">On Time</span>';
                    }
                } else {
                    if ($today->gt($dueDateObj)) {
                        $diffDays = $today->diffInDays($dueDateObj);
                        $delayHtml = '<span class="text-danger fw-bold">' . $diffDays . ' Days Delay</span>';
                    } else {
                        $delayHtml = '<span class="text-success fw-bold">On Time</span>';
                    }
                }
            }

            $statusText = $row->status ?: 'Pending';
            if ($row->is_self_closed) {
                $statusBadge = '<span class="badge bg-secondary">Self Closed</span>';
            } elseif ($balanceQty <= 0 || strtolower($row->status) === 'completed' || strtolower($row->status) === 'received') {
                $statusBadge = '<span class="badge bg-success">' . ucfirst($statusText) . '</span>';
            } elseif (strtolower($row->status) === 'approved') {
                $statusBadge = '<span class="badge bg-primary">Approved</span>';
            } else {
                $statusBadge = '<span class="badge bg-warning text-dark">' . ucfirst($statusText) . '</span>';
            }

            $data[] = [
                'DT_RowIndex' => $index + 1,
                'po_number' => '<strong>' . htmlspecialchars($row->po_number) . '</strong>',
                'po_date' => $row->po_date ? \Carbon\Carbon::parse($row->po_date)->format('d-M-Y') : '-',
                'supplier_name' => $row->supplier_name ?: '-',
                'art_no' => $row->art_no ?: '-',
                'order_qty' => number_format($orderQty, 2),
                'received_qty' => number_format($receivedQty, 2),
                'balance_qty' => number_format($balanceQty, 2),
                'due_date' => $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d-M-Y') : '-',
                'delay' => $delayHtml,
                'status' => $statusBadge,
                'remarks' => htmlspecialchars($row->remarks ?: '-'),
            ];
        }

        return response()->json([
            'draw' => intval($request->draw ?? 1),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
            'totals' => [
                'order_qty' => number_format($totalOrder, 2),
                'received_qty' => number_format($totalReceived, 2),
                'balance_qty' => number_format($totalBalance, 2),
            ]
        ]);
    }
}


