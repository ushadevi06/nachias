<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\BrandCategory;
use App\Models\Item;
use App\Models\Uom;
use App\Models\Color;
use App\Models\Setting;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\StoreType;
use App\Models\SalesAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Log;
use App\Models\Brand;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesInvoiceReportExport;
use App\Exports\SalesInvoiceItemsExport;

class SalesInvoiceController extends Controller
{
    public static function getActiveItemPrice($finishedItemCode, $artNo, $sizeName = null)
    {
        if (!$finishedItemCode || !$artNo) {
            return null;
        }

        $baseCode = preg_replace('/-(FS|HS)$/i', '', $finishedItemCode);
        $candidateCodes = [
            $finishedItemCode,
            $baseCode . '-FS',
            $baseCode . '-HS',
            $baseCode
        ];
        $candidateCodes = array_values(array_unique(array_filter($candidateCodes)));

        $cleanedArtNo = str_replace(' ', '', $artNo);

        $query = \DB::table('item_prices')
            ->whereIn('finished_item_code', $candidateCodes)
            ->whereRaw("REPLACE(art_no, ' ', '') = ?", [$cleanedArtNo])
            ->where('status', 'Active')
            ->whereNull('deleted_at')
            ->whereDate('effective_from', '<=', now());

        if ($sizeName) {
            $pricesList = (clone $query)
                ->where(function($q) use ($sizeName) {
                    $q->where('size', $sizeName)
                      ->orWhereNull('size');
                })
                ->get();
        } else {
            $pricesList = $query->get();
        }

        if ($pricesList->isEmpty()) {
            return null;
        }

        $sorted = $pricesList->sort(function($a, $b) use ($finishedItemCode, $candidateCodes, $sizeName) {
            if ($sizeName) {
                $aSizeMatch = ($a->size === $sizeName) ? 1 : 0;
                $bSizeMatch = ($b->size === $sizeName) ? 1 : 0;
                if ($aSizeMatch !== $bSizeMatch) {
                    return $bSizeMatch - $aSizeMatch;
                }
            }

            $aIndex = array_search($a->finished_item_code, $candidateCodes);
            $bIndex = array_search($b->finished_item_code, $candidateCodes);
            if ($aIndex !== $bIndex) {
                return $aIndex - $bIndex;
            }

            $aEff = strtotime($a->effective_from);
            $bEff = strtotime($b->effective_from);
            if ($aEff !== $bEff) {
                return $bEff - $aEff;
            }

            return $b->id - $a->id;
        });

        $best = $sorted->first();
        if ($best) {
            if (floatval($best->unit_price) <= 0 && floatval($best->selling_price) > 0) {
                $best->unit_price = round(floatval($best->selling_price) / 1.5);
            }
        }

        return $best;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $baseQuery = SalesInvoice::query();

            if ($request->customer_id) {
                $baseQuery->where('customer_id', $request->customer_id);
            }

            if ($request->filled('store_id')) {
                if ((int)$request->store_id === 3) {
                    $baseQuery->where(function($q) {
                        $q->where('store_id', 3)->orWhereNull('store_id');
                    });
                } else {
                    $baseQuery->where('store_id', $request->store_id);
                }
            }

            if ($request->status) {
                if ($request->status === 'Cancelled') {
                    $baseQuery->where(function($q) {
                        $q->where('invoice_status', 'Cancelled')
                          ->orWhere('einvoice_status', 'cancelled');
                    });
                } else {
                    $baseQuery->where('invoice_status', $request->status);
                }
            }

            if ($request->inv_date_range) {
                $dates = explode(' to ', $request->inv_date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $baseQuery->whereBetween('inv_date', [$startDate, $endDate]);
                } elseif (count($dates) == 1) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $baseQuery->whereDate('inv_date', $startDate);
                }
            }

            $totalRecords = (clone $baseQuery)->count();

            if ($request->has('search') && !empty($request->search['value'])) {
                $search = trim($request->search['value']);
                $numericSearch = str_replace([',', '₹', 'Rs.', ' '], '', $search);

                $matchedCustomerIds = Customer::where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%")->pluck('id')->toArray();

                $matchedSoIds = SalesOrder::where('so_no', 'like', "%{$search}%")->orWhere('order_no', 'like', "%{$search}%")->orWhere('orderaxe_id', 'like', "%{$search}%")->orWhere('orderaxe_ref_id', 'like', "%{$search}%")->pluck('id')->toArray();

                $matchedStoreIds = StoreType::where('store_type_name', 'like', "%{$search}%")->pluck('id')->toArray();

                $baseQuery->where(function ($q) use ($search, $numericSearch, $matchedCustomerIds, $matchedSoIds, $matchedStoreIds) {
                    $q->where('inv_no', 'like', "%{$search}%");

                    if (preg_match('/^CD\/(\d+)/i', $search, $sm)) {
                        $sNum = (int)$sm[1];
                        if ($sNum > SalesInvoice::CDW_CD_OFFSET) {
                            $dbNum = ($sNum >= 316) ? ($sNum - SalesInvoice::CDW_CD_OFFSET + 1) : ($sNum - SalesInvoice::CDW_CD_OFFSET);
                            $cdwEq = 'CDW/' . $dbNum;
                            $q->orWhere('inv_no', 'like', "%{$cdwEq}%");
                        }
                    } elseif (is_numeric(trim($search))) {
                        $numVal = (int)trim($search);
                        if ($numVal > SalesInvoice::CDW_CD_OFFSET) {
                            $cdwNum = ($numVal >= 316) ? ($numVal - SalesInvoice::CDW_CD_OFFSET + 1) : ($numVal - SalesInvoice::CDW_CD_OFFSET);
                            $q->orWhere('inv_no', 'like', "%CDW/{$cdwNum}/%");
                        }
                    } elseif (stripos($search, 'CD') !== false && stripos($search, 'CD') === false) {
                        $q->orWhere('inv_no', 'like', "%CDW/%");
                    }

                    if (preg_match('/^\d{1,2}-\d{1,2}(-\d{2,4})?$/', $search)) {
                        $parts = explode('-', $search);
                        if (count($parts) == 3 && strlen($parts[2]) == 4) {
                            try {
                                $d = Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
                                $q->orWhere('inv_date', $d);
                            } catch (\Exception $e) {
                                $q->orWhereRaw("DATE_FORMAT(inv_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
                            }
                        } else {
                            $q->orWhereRaw("DATE_FORMAT(inv_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
                        }
                    }

                    if (is_numeric($numericSearch) && $numericSearch !== '') {
                        $q->orWhere('sub_total', 'like', "%{$numericSearch}%")
                          ->orWhere('discount', 'like', "%{$numericSearch}%")
                          ->orWhere('grand_total', 'like', "%{$numericSearch}%");
                    }

                    $q->orWhere('invoice_status', 'like', "%{$search}%")
                      ->orWhere('delivery_status', 'like', "%{$search}%");

                    if (!empty($matchedCustomerIds)) {
                        $q->orWhereIn('customer_id', $matchedCustomerIds);
                    }

                    if (!empty($matchedStoreIds)) {
                        $q->orWhereIn('store_id', $matchedStoreIds);
                        if (in_array(3, $matchedStoreIds)) {
                            $q->orWhereNull('store_id');
                        }
                    }

                    if (!empty($matchedSoIds)) {
                        $q->orWhereIn('so_id', $matchedSoIds);
                        foreach ($matchedSoIds as $sId) {
                            $q->orWhereRaw('FIND_IN_SET(?, so_ids)', [$sId]);
                        }
                    }
                });
            }

            $totals = (clone $baseQuery)->select(DB::raw('
                COUNT(*) as total_count,
                COALESCE(SUM(sub_total), 0) as total_sub_total,
                COALESCE(SUM(discount), 0) as total_discount,
                COALESCE(SUM(other_charges), 0) as total_other_charges,
                COALESCE(SUM(grand_total), 0) as total_grand_total
            '))->first();

            $filteredRecords = $totals->total_count ?? 0;
            $overallSubTotal = (float)($totals->total_sub_total ?? 0);
            $overallDiscount = (float)($totals->total_discount ?? 0);
            $overallOtherCharges = (float)($totals->total_other_charges ?? 0);
            $overallTaxable = $overallSubTotal - $overallDiscount + $overallOtherCharges;
            $overallGrandTotal = (float)($totals->total_grand_total ?? 0);

            $overallTotalQty = (float) DB::table('sales_invoice_items')->whereIn('sales_invoice_id', (clone $baseQuery)->select('id'))->sum('quantity');
            $query = (clone $baseQuery)
                ->with([
                    'customer:id,name,code',
                    'salesOrder:id,so_no,order_no',
                    'store:id,store_type_name'
                ])
                ->withSum('items as total_qty', 'quantity')
                ->withCount('items as total_items')
                ->orderBy('id', 'desc');

            if ($request->has('start') && $request->has('length') && $request->length != '-1') {
                $query->skip($request->start)->take($request->length);
            }

            $invoices = $query->get();
            $data = [];
            $count = $request->has('start') ? $request->start + 1 : 1;

            foreach ($invoices as $inv) {
                $statusOptions = '';
                $allStatuses = ['Draft', 'Unpaid/Credit', 'Partially Paid', 'Paid', 'Cancelled'];
                $currentStatus = $inv->invoice_status;

                foreach ($allStatuses as $status) {
                    $selected = ($currentStatus === $status) ? 'selected' : '';
                    $disabled = '';
                    if ($currentStatus === 'Cancelled') {
                        $disabled = ($status !== 'Cancelled') ? 'disabled' : '';
                    } elseif ($currentStatus === 'Draft') {
                        $disabled = '';
                    } elseif ($currentStatus === 'Unpaid/Credit' && $status === 'Draft') {
                        $disabled = 'disabled';
                    } elseif ($currentStatus === 'Partially Paid' && $status !== 'Paid') {
                        $disabled = 'disabled';
                    } elseif ($currentStatus === 'Paid' && $status !== 'Paid') {
                        $disabled = 'disabled';
                    }
                    $statusOptions .= "<option value=\"{$status}\" {$selected} {$disabled}>{$status}</option>";
                }

                $statusDropdown = '
                <div class="form-floating form-floating-outline">
                    <select class="form-select inv-status-change" data-id="' . $inv->id . '">
                        ' . $statusOptions . '
                    </select>
                </div>
                <div class="status_msg_' . $inv->id . ' mt-1" style="font-size:10px;"></div>';

                $eInvoiceBtn = '';
                $isCancelled = ($inv->invoice_status === 'Cancelled' || $inv->einvoice_status === 'cancelled');
                $isRmStore = in_array((int)$inv->store_id, [1, 2]);

                if ($isRmStore) {
                    $eInvoiceBtn = '';
                } elseif ($inv->einvoice_status === 'cancelled') {
                    $eInvoiceBtn = '<button type="button" class="btn btn-warning" title="E-Invoice Cancelled" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;" disabled><i class="ri ri-close-circle-line"></i> Cancelled</button>';
                    
                    $recreatedInvoice = \App\Models\SalesInvoice::where('customer_id', $inv->customer_id)
                        ->where('so_ids', $inv->so_ids)
                        ->where('id', '!=', $inv->id)
                        ->where(function($q) {
                            $q->whereNull('einvoice_status')->orWhere('einvoice_status', '!=', 'cancelled');
                        })
                        ->exists();
                    
                    if (!$recreatedInvoice) {
                        $eInvoiceBtn .= '<a href="' . url('sales_invoices/recreate/' . $inv->id) . '" class="btn btn-outline-primary" title="Recreate / Copy to New Invoice" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-file-copy-line"></i></a>';
                    }
                } elseif ($inv->invoice_status === 'Cancelled') {
                    $eInvoiceBtn = '';
                } elseif ($inv->irn) {
                    $ackDateTime = $inv->ack_date ? \Carbon\Carbon::parse($inv->ack_date) : null;
                    $isExpired = $ackDateTime ? $ackDateTime->diffInHours(now()) >= 24 : false;
                    if ($isExpired) {
                        $eInvoiceBtn = '<button type="button" class="btn btn-secondary einvoice-expired-btn" data-id="' . $inv->id . '" title="E-Invoice Cancellation Window Expired" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-close-circle-line"></i></button>';
                    } else {
                        $eInvoiceBtn = '<button type="button" class="btn btn-danger einvoice-cancel-btn" data-id="' . $inv->id . '" title="Cancel E-Invoice" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-close-circle-line"></i></button>';
                    }
                } else {
                    $eInvoiceBtn = '<button type="button" class="btn btn-info einvoice-generate-btn" data-id="' . $inv->id . '" title="Generate E-Invoice" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-receipt-line"></i></button>';
                }

                $editBtn = '';
                if (!$isCancelled) {
                    $editBtn = '<a href="' . url('sales_invoices/add/' . $inv->id) . '" class="btn btn-edit" title="Edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                $action = '<div class="button-box d-flex align-items-center">
                    <a href="' . url('sales_invoices/view/' . $inv->id) . '" class="btn btn-view" title="View"><i class="icon-base ri ri-eye-line"></i></a>
                    ' . $editBtn . '
                    ' . $eInvoiceBtn . '
                </div>';

                $storeName = 'Finished Goods';
                $storeBadgeClass = 'bg-label-primary';
                if ((int)$inv->store_id === 1 || ($inv->store && stripos($inv->store->store_type_name, 'fabric') !== false)) {
                    $storeName = $inv->store ? $inv->store->store_type_name : 'Fabric';
                    $storeBadgeClass = 'bg-label-info';
                } elseif ((int)$inv->store_id === 2 || ($inv->store && stripos($inv->store->store_type_name, 'access') !== false)) {
                    $storeName = $inv->store ? $inv->store->store_type_name : 'Accessories';
                    $storeBadgeClass = 'bg-label-warning';
                } elseif ($inv->store) {
                    $storeName = $inv->store->store_type_name;
                }

                $data[] = [
                    'id' => $inv->id,
                    'DT_RowIndex' => $count++,
                    'inv_no' => $inv->inv_no . ($isCancelled ? '<br><span class="badge bg-danger mt-1" style="font-size:10px;"><i class="ri ri-close-circle-line align-middle me-1"></i> Cancelled</span>' : ($inv->irn && $inv->einvoice_status !== 'cancelled' ? '<br><span class="badge bg-label-success text-dark mt-1" style="font-size:10px;"><i class="ri ri-checkbox-circle-line align-middle me-1"></i> E-invoice Generated</span>' : '')),
                    'inv_date' => $inv->inv_date ? $inv->inv_date->format('d-m-Y') : '',
                    'store_name' => '<span class="badge ' . $storeBadgeClass . '">' . e($storeName) . '</span>',
                    'raw_store_name' => $storeName,
                    'customer_name' => ($inv->customer ? $inv->customer->name : '-') . ($inv->customer ? ' <span  class="mini-title">(' . $inv->customer->code . ')</span>' : ''),
                    'so_no' => ($inv->salesOrder ? $inv->salesOrder->so_no : '-') . ($inv->salesOrder && $inv->salesOrder->order_no ? '<br><span class="badge bg-label-info mt-1" style="font-size:10px;">' . $inv->salesOrder->order_no . '</span>' : ''),
                    'total_items' => $inv->total_items ?? 0,
                    'total_qty' => $inv->total_qty ?? 0,
                    'sub_total' => '₹' . number_format($inv->sub_total, 2),
                    'raw_sub_total' => $inv->sub_total,
                    'discount' => '₹' . number_format($inv->discount ?? 0, 2),
                    'raw_discount' => $inv->discount ?? 0,
                    'taxable_value' => '₹' . number_format($inv->total > 0 ? $inv->total : ($inv->sub_total - ($inv->discount ?? 0) + ($inv->other_charges ?? 0)), 2),
                    'raw_taxable_value' => $inv->total > 0 ? $inv->total : ($inv->sub_total - ($inv->discount ?? 0) + ($inv->other_charges ?? 0)),
                    'grand_total' => '₹' . number_format($inv->grand_total, 2),
                    'raw_grand_total' => $inv->grand_total,
                    'status' => $statusDropdown,
                    'status_text' => $inv->invoice_status,
                    'delivery_status' => $inv->delivery_status,
                    'action' => $action,
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
                'overallTotalQty' => $overallTotalQty ?? 0,
                'overallSubTotal' => $overallSubTotal ?? 0,
                'overallDiscount' => $overallDiscount ?? 0,
                'overallTaxable' => $overallTaxable ?? 0,
                'overallGrandTotal' => $overallGrandTotal ?? 0,
            ]);
        }

        $customers = Customer::select('id', 'name', 'code')->orderBy('name')->get();
        $stores = StoreType::active()->orderBy('id')->get();
        return view('sales_invoice.view', compact('customers', 'stores'));
    }

    public function getNextInvoiceNo(Request $request)
    {
        $brandId = $request->brand_id;
        $invDateStr = $request->inv_date;
        $storeId = (int)($request->store_id ?? 0);
        $isRawMaterialStore = in_array($storeId, [1, 2]);
        
        if (!$isRawMaterialStore && !$brandId) {
            return response()->json(['success' => false, 'message' => 'Brand ID is required.']);
        }

        if (!$invDateStr) {
            return response()->json(['success' => false, 'message' => 'Invoice Date is required.']);
        }

        try {
            $date = Carbon::createFromFormat('d-m-Y', $invDateStr);
        } catch (\Exception $e) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', $invDateStr);
            } catch (\Exception $ex) {
                return response()->json(['success' => false, 'message' => 'Invalid date format.']);
            }
        }

        if ($isRawMaterialStore) {
            $generatedInvNo = $this->generateRawMaterialInvoiceNumber($date);
        } else {
            $generatedInvNo = $this->generateInvoiceNumber($brandId, $date);
        }

        return response()->json([
            'success' => true,
            'inv_no' => SalesInvoice::formatDisplayInvNo($generatedInvNo)
        ]);
    }

    public function getStoreStockItems(Request $request)
    {
        $storeId = (int)$request->store_id;
        if (!in_array($storeId, [1, 2])) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $stockItems = DB::table('stock_entry_items as sei')
            ->join('stock_entries as se', 'sei.stock_entry_id', '=', 'se.id')
            ->leftJoin('raw_materials as rm', 'sei.raw_material_id', '=', 'rm.id')
            ->leftJoin('uoms as u', 'sei.uom_id', '=', 'u.id')
            ->leftJoin('styles as s', 'sei.style_id', '=', 's.id')
            ->whereNull('sei.deleted_at')
            ->whereNull('se.deleted_at')
            ->where('sei.stock_type', 'raw_material')
            ->where(function($q) use ($storeId) {
                $q->where('sei.store_type_id', $storeId)
                  ->orWhere('se.store_type_id', $storeId)
                  ->orWhere(function($q2) use ($storeId) {
                      $q2->whereNull('sei.store_type_id')
                         ->where('rm.store_category_id', $storeId);
                  });
            })
            ->whereRaw('(sei.qty_in - COALESCE(sei.qty_out, 0)) > 0')
            ->select([
                'sei.id as stock_entry_item_id',
                'sei.raw_material_id',
                'sei.style_id',
                's.style_name',
                'sei.art_no',
                'sei.uom_id',
                'sei.price as rate',
                DB::raw('(sei.qty_in - COALESCE(sei.qty_out, 0)) as available_qty'),
                'rm.name as raw_material_name',
                'rm.code as raw_material_code',
                'u.uom_name',
                'u.uom_code'
            ])
            ->orderBy('rm.name', 'asc')
            ->get();

        $formatted = $stockItems->map(function ($item) use ($storeId) {
            $name = $item->raw_material_name ?: 'Raw Material #' . $item->raw_material_id;
            $hsn = '';
            $uomCode = $item->uom_code ?: ($item->uom_name ?: 'PCS');
            $uomName = $item->uom_name ?: ($item->uom_code ?: 'PCS');
            $artNo = $item->art_no ?: '-';
            $styleName = ($storeId == 1 && $item->style_name) ? $item->style_name : '';
            
            $displayLabel = $name;
            if ($styleName) {
                $displayLabel .= " | Style: {$styleName}";
            }
            $displayLabel .= " | Art No: {$artNo} | Avail: {$item->available_qty} {$uomCode}";

            return [
                'stock_entry_item_id' => $item->stock_entry_item_id,
                'raw_material_id' => $item->raw_material_id,
                'style_id' => $item->style_id,
                'style_name' => $styleName,
                'item_name' => $name,
                'art_no' => $artNo,
                'uom_id' => $item->uom_id,
                'uom_name' => $uomName,
                'uom_code' => $uomCode,
                'hsn_sac' => $hsn,
                'available_qty' => (float)$item->available_qty,
                'rate' => (float)$item->rate,
                'display_label' => $displayLabel
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit sales-invoice')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create sales-invoice')) {
                return unauthorizedRedirect();
            }
        }

        if ($id) {
            $existingInvoice = SalesInvoice::findOrFail($id);
            if ($existingInvoice->einvoice_status === 'cancelled' || $existingInvoice->invoice_status === 'Cancelled') {
                return redirect('sales_invoices')->with('error', 'Cannot edit a cancelled invoice.');
            }
        }

        if ($request->isMethod('post')) {
            $selectedStoreId = (int)($request->store_id ?? 0);
            $isRawMaterialStore = in_array($selectedStoreId, [1, 2]);
            $selectedBrandId = $request->brand_id;
            
            if ($request->inv_date && ($selectedBrandId || $isRawMaterialStore)) {
                try {
                    $selectedDate = null;
                    try {
                        $selectedDate = Carbon::createFromFormat('d-m-Y', $request->inv_date);
                    } catch (\Exception $e) {
                        $selectedDate = Carbon::createFromFormat('Y-m-d', $request->inv_date);
                    }

                    $regenerate = false;
                    if ($request->is_manual_inv_no == '1') {
                        $regenerate = false;
                    } else if (!$id) {
                        $regenerate = true;
                    } else {
                        $invoice = SalesInvoice::findOrFail($id);
                        $origDate = Carbon::parse($invoice->inv_date);
                        $origYear = (int)$origDate->format('Y');
                        $origMonth = (int)$origDate->format('m');
                        $origFYStart = ($origMonth >= 4) ? $origYear : ($origYear - 1);
                        
                        $selYear = (int)$selectedDate->format('Y');
                        $selMonth = (int)$selectedDate->format('m');
                        $selFYStart = ($selMonth >= 4) ? $selYear : ($selYear - 1);

                        if ($isRawMaterialStore) {
                            if ($origFYStart != $selFYStart || !in_array((int)$invoice->store_id, [1, 2])) {
                                $regenerate = true;
                            } else {
                                $request->merge(['inv_no' => $invoice->raw_inv_no ?: $invoice->getRawOriginal('inv_no')]);
                            }
                        } else {
                            if ($invoice->brand_id != $selectedBrandId || $origFYStart != $selFYStart) {
                                $regenerate = true;
                            } else {
                                $request->merge(['inv_no' => $invoice->raw_inv_no ?: $invoice->getRawOriginal('inv_no')]);
                            }
                        }
                    }

                    if ($regenerate) {
                        if ($isRawMaterialStore) {
                            $generatedInvNo = $this->generateRawMaterialInvoiceNumber($selectedDate, $id);
                        } else {
                            $generatedInvNo = $this->generateInvoiceNumber($selectedBrandId, $selectedDate, $id);
                        }
                        $request->merge(['inv_no' => $generatedInvNo]);
                    }
                } catch (\Exception $e) {
                }
            }

            if ($request->inv_no) {
                $request->merge(['inv_no' => SalesInvoice::formatDbInvNo($request->inv_no)]);
            }

            $isCancelled = ($request->invoice_status === 'Cancelled');

            $rules = [
                'store_id' => 'required|exists:store_types,id',
                'brand_id' => $isRawMaterialStore ? 'nullable|exists:brands,id' : 'required|exists:brands,id',
                'inv_no' => ['required', 'string', 'min:1', 'max:16', 'regex:/^[a-zA-Z1-9][a-zA-Z0-9\/\-]*$/', 'unique:sales_invoices,inv_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'],
                'inv_date' => 'required|date_format:d-m-Y',
                'so_ids' => $isRawMaterialStore ? 'nullable|array' : 'required|array',
                'so_ids.*' => 'exists:sales_orders,id',
                'customer_id' => 'required|exists:customers,id',
                'delivery_address' => 'required|min:3|max:255|regex:/^[^<>]*$/',
                'remarks' => 'nullable|min:3|max:255|regex:/^[^<>]*$/',
                'invoice_status' => 'required|in:Draft,Unpaid/Credit,Paid,Partially Paid,Cancelled',
                'payment_mode' => 'nullable',
                'no_of_box' => 'nullable|integer|min:1',
                'cgst_percent' => 'nullable|numeric|min:0|max:100',
                'cgst_amount' => 'nullable|numeric|min:0',
                'sgst_percent' => 'nullable|numeric|min:0|max:100',
                'sgst_amount' => 'nullable|numeric|min:0',
                'igst_percent' => 'nullable|numeric|min:0|max:100',
                'igst_amount' => 'nullable|numeric|min:0',
                'hsn_sac' => $isCancelled ? 'nullable|string|max:50' : ($isRawMaterialStore ? 'nullable|string|max:50' : 'required|string|max:50'),
                'sales_discount' => 'nullable|numeric|min:0|max:100',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'box_discount_amount' => 'nullable|numeric|min:0',
                'signature_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'attachment_file' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'tran_doc_date' => 'nullable|date_format:d-m-Y',
                'due_date' => 'nullable|date_format:d-m-Y',
                'transporter_id' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'vehicle_no' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'tran_doc_no' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'lr_no' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
            ];

            if ($isCancelled) {
                $rules['items'] = 'nullable|array';
                $rules['items.*.quantity'] = 'nullable|numeric';
                $rules['items.*.rate'] = 'nullable|numeric';
                $rules['items.*.mrp'] = 'nullable|numeric';
            } else {
                $rules['items'] = 'required|array|min:1';
                $rules['items.*.quantity'] = 'required|numeric|min:0.01';
                $rules['items.*.rate'] = 'required|numeric|min:0.01';
                $rules['items.*.mrp'] = $isRawMaterialStore ? 'nullable|numeric' : 'required|numeric|min:0.01';
                $rules['items.*.hsn_sac'] = 'required|string|max:50';
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, [
                '*.required'      => 'This field is required.',
                '*.unique'        => 'This field already exists.',
                '*.exists'        => 'Selected value is invalid.',
                '*.date_format'   => 'Please enter a valid date in DD-MM-YYYY format.',
                '*.numeric'       => 'This field must be a number.',
                '*.regex'         => 'This field is an invalid format.',
                '*.not_regex'     => 'This field is an invalid format.',
                'igst_percent.min' => 'IGST cannot be negative. Please enter 0 or a positive value.',
                'igst_percent.max' => 'IGST percentage cannot exceed 100%.',
                'cgst_percent.min' => 'CGST cannot be negative. Please enter 0 or a positive value.',
                'cgst_percent.max' => 'CGST percentage cannot exceed 100%.',
                'sgst_percent.min' => 'SGST cannot be negative. Please enter 0 or a positive value.',
                'sgst_percent.max' => 'SGST percentage cannot exceed 100%.',
                'sales_discount.max'   => 'Sales discount percentage cannot exceed 100%.',
                'sales_discount.min'   => 'Sales discount percentage cannot be negative.',
                'discount_percent.max' => 'Discount percentage cannot exceed 100%.',
                'discount_percent.min' => 'Discount percentage cannot be negative.',
                'items.required'            => 'At least one item is required to save the invoice.',
                'items.min'                 => 'At least one item is required to save the invoice.',
                'items.*.rate.min' => 'Price must be greater than 0.00.',
                'items.*.mrp.min' => 'MRP must be greater than 0.00.',
                'items.*.quantity.required' => 'Quantity is required.',
                'items.*.quantity.numeric'  => 'Quantity must be a valid number.',
                'items.*.quantity.min'      => 'Quantity must be greater than 0.00.',
                'items.*.rate.required'     => 'Price is required.',
                'items.*.rate.numeric'      => 'Price must be a valid number.',
                'items.*.hsn_sac.required'  => 'HSN Code is required for each item.',
                '*.min'           => 'This field must be at least :min characters.',
                '*.max'           => 'This field must be at most :max characters.',
            ]);

            $validator->after(function ($validator) use ($request, $isCancelled, $isRawMaterialStore) {
                if (!$isCancelled && !$isRawMaterialStore && is_array($request->items)) {
                    foreach ($request->items as $index => $item) {
                        $mrp = floatval($item['mrp'] ?? 0);
                        $rate = floatval($item['rate'] ?? 0);
                        $artNo = $item['art_no'] ?? '-';
                        if ($mrp > 0 && abs($mrp - $rate) < 0.001) {
                            $validator->errors()->add("items.{$index}.rate", "MRP and Price cannot be the same (Art No: {$artNo}).");
                            $validator->errors()->add("items.{$index}.mrp", "MRP and Price cannot be the same (Art No: {$artNo}).");
                        }
                    }
                }
            });

            if ($validator->fails()) {
                $redirect = redirect()->back()->withErrors($validator)->withInput();
                if ($validator->errors()->has('items')) {
                    $redirect->with('stock_alert', 'At least one item is required to save the invoice. If you want to cancel this invoice, please change Status to Cancelled.');
                }
                return $redirect;
            }

            if (!$isCancelled && !empty($request->items)) {
                try {
                    $this->validateStockAvailability($request->items, $id);
                } catch (\Exception $e) {
                    return back()->withInput()->with('stock_alert', $e->getMessage());
                }
            }

            DB::beginTransaction();
            try {
                $invoiceData = $request->only([
                    'inv_no', 'brand_id', 'customer_id', 'store_id', 'agent_id', 'delivery_address', 'remarks',
                    'invoice_status', 'payment_mode', 'extra_input', 'due_date',
                    'notes', 'transporter_name', 'transporter_id', 'transport_mode',
                    'vehicle_no', 'veh_type', 'transport_distance', 'tran_doc_no', 'tran_doc_date',
                    'lr_no', 'no_of_box', 'hsn_sac', 'sub_total', 'sales_discount', 'box_discount_amount', 'discount_percent', 'discount', 
                    'commission_percent', 'commission_amount', 'total', 'other_state',
                    'tax_amount', 'igst_percent', 'igst', 'cgst_percent', 'cgst', 'sgst_percent', 'sgst',
                    'other_charges', 'round_off_type', 'round_off',
                    'grand_total', 'due_amount'
                ]);
                if ($request->tran_doc_date) {
                    $invoiceData['tran_doc_date'] = Carbon::createFromFormat('d-m-Y', $request->tran_doc_date)->format('Y-m-d');
                }
                $invoiceData['lr_no'] = $request->tran_doc_no ?? $request->lr_no;

                $invoiceData['inv_date'] = Carbon::createFromFormat('d-m-Y', $request->inv_date)->format('Y-m-d');
                if ($request->due_date) {
                    $invoiceData['due_date'] = Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d');
                }
                $cust = Customer::find($request->customer_id);
                $setting = Setting::first();
                $companyStateId = $setting ? $setting->state_id : 1;
                $custStateId = $cust ? $cust->state_id : null;
                $isOtherState = ($custStateId && $companyStateId) ? ($custStateId != $companyStateId) : ($request->other_state == 'yes');

                $invoiceData['show_fields'] = $request->show_fields ?? [];
                $invoiceData['delivery_show_fields'] = $request->delivery_show_fields ?? [];
                $invoiceData['other_state'] = $isOtherState;
                if (!$isOtherState) {
                    $invoiceData['igst_percent'] = 0;
                    $invoiceData['igst'] = 0;
                } else {
                    $invoiceData['cgst_percent'] = 0;
                    $invoiceData['cgst'] = 0;
                    $invoiceData['sgst_percent'] = 0;
                    $invoiceData['sgst'] = 0;
                }
                $invoiceData['so_ids'] = !empty($request->so_ids) ? json_encode(array_values(array_filter((array)$request->so_ids))) : null;
                $invoiceData['so_id'] = !empty($request->so_ids) && is_array($request->so_ids) ? ($request->so_ids[0] ?? null) : null;

                if ($request->hasFile('signature_file')) {
                    if (!empty($invoice->signature_file)) {
                        $oldFile = public_path('uploads/sales_invoices/signatures/' . $invoice->signature_file);
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                    $dir = public_path('uploads/sales_invoices/signatures');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $file = $request->file('signature_file');
                    $fileName = time() . '_sig_' . $file->getClientOriginalName();
                    $file->move($dir, $fileName);
                    $invoiceData['signature_file'] = $fileName;
                }

                if ($request->hasFile('attachment_file'))   {
                    if (!empty($invoice->attachment_file)) {
                        $oldFile = public_path('uploads/sales_invoices/attachments/' . $invoice->attachment_file);
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                    $dir = public_path('uploads/sales_invoices/attachments');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $file = $request->file('attachment_file');
                    $fileName = time() . '_att_' . $file->getClientOriginalName();
                    $file->move($dir, $fileName);
                    $invoiceData['attachment_file'] = $fileName;
                }
                $submittedItems = $request->items ?? [];
                $calculatedSubTotal = 0;
                $calculatedTotalQty = 0;
                foreach ($submittedItems as $item) {
                    $qty = (float)($item['quantity'] ?? 0);
                    $mrp = (float)($item['mrp'] ?? 0);
                    $rate = (float)($item['rate'] ?? 0);
                    $price = $rate > 0 ? $rate : $mrp;
                    $calculatedSubTotal += $qty * $price;
                    $calculatedTotalQty += $qty;
                }

                $salesDiscPercent = (float)($request->sales_discount ?? 0);
                if ($salesDiscPercent <= 0 && $request->customer_id) {
                    $customer = \App\Models\Customer::find($request->customer_id);
                    if ($customer && $customer->sales_discount) {
                        $salesDiscPercent = (float)$customer->sales_discount;
                    }
                }
                $boxDiscountPerPc = (float)($request->box_discount_amount ?? 0);
                $salesDiscountValue = ($calculatedSubTotal * $salesDiscPercent) / 100;
                $boxDiscountValue = $calculatedTotalQty * $boxDiscountPerPc;
                $calculatedDiscount = $salesDiscountValue + $boxDiscountValue;

                $calculatedTotal = $calculatedSubTotal - $calculatedDiscount;
                $otherCharges = (float)($request->other_charges ?? 0);
                $taxableAmount = $calculatedTotal + $otherCharges;

                $cgst = 0; $sgst = 0; $igst = 0;
                if ($request->other_state == 'yes') {
                    $igstPercent = (float)($request->igst_percent ?? 0);
                    $igst = ($taxableAmount * $igstPercent) / 100;
                } else {
                    $cgstPercent = (float)($request->cgst_percent ?? 0);
                    $sgstPercent = (float)($request->sgst_percent ?? 0);
                    $cgst = ($taxableAmount * $cgstPercent) / 100;
                    $sgst = ($taxableAmount * $sgstPercent) / 100;
                }
                $calculatedTaxAmount = $cgst + $sgst + $igst;
                
                $totalBeforeRoundOff = $taxableAmount + $calculatedTaxAmount;
                $calculatedGrandTotal = round($totalBeforeRoundOff);
                $calculatedRoundOff = abs($calculatedGrandTotal - $totalBeforeRoundOff);
                $calculatedRoundOffType = ($calculatedGrandTotal >= $totalBeforeRoundOff) ? 'Add' : 'Less';

                $invoiceData['sub_total'] = $calculatedSubTotal;
                $invoiceData['sales_discount'] = $salesDiscPercent;
                $invoiceData['discount_percent'] = $salesDiscPercent;
                $invoiceData['discount'] = $calculatedDiscount;
                $invoiceData['total'] = $taxableAmount;
                $invoiceData['cgst'] = $cgst;
                $invoiceData['sgst'] = $sgst;
                $invoiceData['igst'] = $igst;
                $invoiceData['tax_amount'] = $calculatedTaxAmount;
                $invoiceData['other_charges'] = $otherCharges;
                $invoiceData['round_off'] = $calculatedRoundOff;
                $invoiceData['round_off_type'] = $calculatedRoundOffType;
                $invoiceData['grand_total'] = $calculatedGrandTotal;
               
                $oldQuantities = [];
                if ($id) {
                    $invoice = SalesInvoice::with('items')->findOrFail($id);
                    foreach($invoice->items as $oItem) {
                        $oldQuantities[$oItem->id] = ['quantity' => $oItem->quantity, 'inv_no' => $invoice->inv_no, 'stock_entry_item_id' => $oItem->stock_entry_item_id];
                    }
                    
                    $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];
                    $invoiceData['received_amount'] = 0.00;
                    $invoiceData['due_amount'] = $calculatedGrandTotal;
                    $invoiceData['updated_by'] = auth()->id();

                    $invoice->update($invoiceData);             
                          
                    
                    $itemIds = collect($submittedItems)->pluck('id')->filter()->toArray();
                    $deletedItems = $invoice->items()->whereNotIn('id', $itemIds)->get();
                    foreach ($deletedItems as $dItem) {
                        $this->sequentialStockRevert($dItem, $dItem->quantity);
                    }
                    
                    $invoice->items()->whereNotIn('id', $itemIds)->forceDelete();
                } else {
                    $invoiceData['received_amount'] = 0.00;
                    $invoiceData['due_amount'] = $calculatedGrandTotal;
                    $invoiceData['created_by'] = auth()->id();
                    $invoice = SalesInvoice::create($invoiceData);
                }
                $invoiceId = $invoice->id;
                foreach ($submittedItems as $item) {
                    $isExtra = !empty($item['is_extra']);

                    $apiColor = $item['api_color'] ?? null;
                    if (empty($apiColor)) {
                        $artNo = $item['art_no'] ?? '';
                        if (!empty($artNo) && strpos($artNo, '-') !== false) {
                            $parts = explode('-', $artNo);
                            $lastPart = trim(end($parts));
                            if (is_numeric($lastPart)) {
                                $apiColor = $lastPart;
                            }
                        }
                    }
                    if (empty($apiColor)) {
                        $apiColor = 'A';
                    }

                    $stockEntryItemId = !empty($item['stock_entry_item_id']) ? $item['stock_entry_item_id'] : null;
                    if (empty($stockEntryItemId) && !empty($item['sku'])) {
                        $seItem = \App\Models\StockEntryItem::where('sku', $item['sku'])->whereNull('deleted_at')->whereRaw('(qty_in - COALESCE(qty_out, 0)) > 0')->orderBy('id', 'asc')->first();
                        if ($seItem) {
                            $stockEntryItemId = $seItem->id;
                        }
                    }

                    if (!$isExtra && !empty($invoiceData['so_ids'])) {
                        $soIdsArr = is_array($invoiceData['so_ids']) ? $invoiceData['so_ids'] : json_decode($invoiceData['so_ids'], true);
                        if (!empty($soIdsArr)) {
                            $existsInSO = \App\Models\SalesOrderItem::whereIn('sale_order_id', $soIdsArr)
                                ->where(function($q) use ($item, $apiColor, $stockEntryItemId) {
                                    if ($stockEntryItemId) {
                                        $q->where('stock_entry_item_id', $stockEntryItemId);
                                    } else {
                                        $q->where('sku', $item['sku'] ?? '')
                                          ->where('size_id', $item['size_id'] ?? $item['size'] ?? '');
                                    }
                                })->exists();
                            
                            if (!$existsInSO) {
                                $isExtra = true;
                            }
                        }
                    }

                    $itemQty = (float)($item['quantity'] ?? 0);
                    $itemMrp = (float)($item['mrp'] ?? 0);
                    $itemRate = (float)($item['rate'] ?? 0);
                    if ($itemRate <= 0 && $itemMrp > 0) {
                        $itemRate = $itemMrp;
                    }
                    if ($itemMrp <= 0 && $itemRate > 0) {
                        $itemMrp = $itemRate;
                    }
                    $itemAmount = round($itemQty * $itemRate, 2);

                    SalesInvoiceItem::updateOrCreate(
                        ['id' => !empty($item['id']) ? $item['id'] : null],
                        [
                            'sales_invoice_id' => $invoiceId,
                            'sku' => $item['sku'] ?? null,
                            'uom_id' => $item['uom_id'] ?? null,
                            'quantity' => $itemQty,
                            'rate' => $itemRate,
                            'mrp' => $itemMrp,
                            'amount' => $itemAmount,
                            'hsn_sac' => !empty($item['hsn_sac']) ? $item['hsn_sac'] : ($request->hsn_sac ?? null),
                            'art_no' => $item['art_no'] ?? null,
                            'size' => $item['size'] ?? null,
                            'color_id' => $item['color_id'] ?? null,
                            'api_color' => $apiColor,
                            'sleeve_type' => $item['sleeve_type'] ?? null,
                            'stock_entry_item_id' => $stockEntryItemId,
                            'is_extra' => $isExtra,
                        ]
                    );
                }
                
                if ($invoice->invoice_status === 'Cancelled') {
                    $this->adjustStock($invoiceId, true);
                } else {
                    $this->adjustStock($invoiceId, false, $oldQuantities ?? []);
                }

                $invoice->load(['items', 'customer.city', 'customer.place']);
                $totalPcs = (int) $invoice->items->sum('quantity');
                $boxCount = !empty($invoice->no_of_box) ? (int) $invoice->no_of_box : '-';
                
                $customerName = $invoice->customer->name ?? '-';
                $customerAddress = implode(', ', array_filter([$invoice->customer->address_line_1 ?? '', $invoice->customer->address_line_2 ?? '', $invoice->customer->address_line_3 ?? '']));
                $location = $invoice->customer->city->city_name ?? ($invoice->customer->place->place_name ?? '-');
                
                $qrDetails = "Customer: {$customerName}\nAddress: {$customerAddress}\nLocation: {$location}\nInvoice No: {$invoice->inv_no}\nPieces: {$totalPcs}\nBoxes: {$boxCount}";
                $invoice->update(['qr_details' => $qrDetails]);

                $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];
                if (in_array($invoice->invoice_status, $activeStatuses)) {
                    $invoice->load('items');
                }

                $invoice->refresh();
                if (function_exists('addLog')) {
                    addLog($id ? 'update' : 'create', 'Sales Invoice', 'sales_invoices', $invoice->id, $id ? $existingInvoice->toArray() : null, $invoice->toArray());
                }

                DB::commit();
                
                $msg = $id ? 'Sale Invoice updated successfully' : 'Sale Invoice created successfully';
                return redirect('sales_invoices')->with('success', $msg);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save invoice: ' . $e->getMessage()]);
            }
        }

        $invoice = $id ? SalesInvoice::with(['items.brandCategory', 'items.item.brand', 'items.item.style', 'items.color', 'items.uom', 'items.sizeRatio', 'salesOrder'])->findOrFail($id) : null;
        if ($invoice && $invoice->einvoice_status === 'cancelled') {
            return redirect('sales_invoices')->with('error', 'Cancelled invoices cannot be edited.');
        }
        $customers = Customer::active()->orderBy('id', 'desc')->get();
        $brandCategories = BrandCategory::active()->get();
        $uoms = Uom::active()->get();
        $stores = StoreType::where('status', 'Active')->orderBy('id', 'desc')->get();
        $sales_agent = SalesAgent::where('status', 'Active')->orderBy('id', 'desc')->get();

        $brands = \App\Models\Brand::active()
            ->where(function ($query) use ($invoice) {
                $query->whereHas('storeCategories', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('category_name', 'like', '%finished goods%')
                            ->orWhere('code', 'like', '%fgs%');
                    });
                });
                if ($invoice && $invoice->brand_id) {
                    $query->orWhere('id', $invoice->brand_id);
                }
            })
            ->orderBy('brand_name', 'asc')
            ->get();
        $transportModes = \App\Models\TransportMode::where('status', 'Active')->orderBy('id', 'desc')->get();

        if ($invoice) {
            $soIds = [];
            if (!empty($invoice->so_ids)) {
                $decoded = is_array($invoice->so_ids) ? $invoice->so_ids : json_decode($invoice->so_ids, true);
                if (is_array($decoded)) {
                    $soIds = $decoded;
                }
            }
            if (empty($soIds) && !empty($invoice->so_id)) {
                $soIds = [$invoice->so_id];
            }
            $soIds = array_values(array_filter(array_unique($soIds)));

            $saleOrders = SalesOrder::whereIn('id', $soIds)
                ->orWhere(function($q) use ($invoice) {
                    $q->where('customer_id', $invoice->customer_id)
                      ->whereIn('status', ['Approved', 'Dispatched']);
                })
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $saleOrders = collect(); 
        }

        $setting = Setting::first();
        $web_settings = $setting;

        return view('sales_invoice.add', compact('invoice', 'customers', 'saleOrders', 'brandCategories', 'uoms', 'stores', 'sales_agent', 'brands', 'transportModes', 'setting', 'web_settings'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details sales-invoice')) {
            return unauthorizedRedirect();
        }
        $invoice = SalesInvoice::with(['customer', 'salesOrder', 'items.brandCategory', 'items.item', 'items.color', 'items.uom'])->findOrFail($id);
        return view('sales_invoice.view_details', compact('invoice'));
    }
    
    private function getCustomerPendingItems($customerId, $currentInvoiceId = null)
    {
        $allCustomerSOs = SalesOrder::with(['items'])->where('customer_id', $customerId)->whereIn('status', ['Approved', 'Dispatched'])->orderBy('id', 'asc')->get();
        
        $soItemOrdered = [];
        $soItemInvoiced = [];

        foreach($allCustomerSOs as $so) {
            foreach($so->items as $item) {
                $itemId = $item->stock_entry_item_id ?? '';
                if (!isset($soItemOrdered[$so->id][$itemId])) {
                    $soItemOrdered[$so->id][$itemId] = 0;
                }
                $soItemOrdered[$so->id][$itemId] += $item->qty;
            }
        }

        $invoices = DB::table('sales_invoices')
            ->where('customer_id', $customerId)
            ->where(function($q) {
                $q->whereNull('einvoice_status')
                  ->orWhere('einvoice_status', '!=', 'cancelled');
            })
            ->when($currentInvoiceId, function($q) use ($currentInvoiceId) {
                $q->where('id', '!=', $currentInvoiceId);
            })
            ->get(['id', 'so_ids', 'so_id']);

        foreach($invoices as $inv) {
            $so_ids = json_decode($inv->so_ids, true);
            if (!$so_ids && $inv->so_id) {
                $so_ids = [(string)$inv->so_id];
            }
            if (!$so_ids) continue;

            $invItems = DB::table('sales_invoice_items')->where('sales_invoice_id', $inv->id)->get();

            foreach($invItems as $invItem) {
                $itemId = $invItem->stock_entry_item_id ?? '';
                $qtyToAllocate = $invItem->quantity;

                foreach($so_ids as $so_id) {
                    if ($qtyToAllocate <= 0) break;

                    $ordered = $soItemOrdered[$so_id][$itemId] ?? 0;
                    $alreadyInvoiced = $soItemInvoiced[$so_id][$itemId] ?? 0;
                    $pending = $ordered - $alreadyInvoiced;

                    if ($pending > 0) {
                        $allocate = min($pending, $qtyToAllocate);
                        $soItemInvoiced[$so_id][$itemId] = $alreadyInvoiced + $allocate;
                        $qtyToAllocate -= $allocate;
                    }
                }
                
                if ($qtyToAllocate > 0 && count($so_ids) > 0) {
                    $first_so = $so_ids[0];
                    $soItemInvoiced[$first_so][$itemId] = ($soItemInvoiced[$first_so][$itemId] ?? 0) + $qtyToAllocate;
                }
            }
        }

        return $soItemInvoiced;
    }

    public function getCustomerSalesOrders(Request $request)
    {
        $customerId = $request->customer_id;
        $currentInvoiceId = $request->invoice_id ?? null;
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Customer ID required']);
        }

        $saleOrders = SalesOrder::with(['items'])->where('customer_id', $customerId)->whereIn('status', ['Approved', 'Dispatched'])->orderBy('id', 'desc')->get();
        $soItemInvoiced = $this->getCustomerPendingItems($customerId, $currentInvoiceId);

        $data = $saleOrders->map(function($so) use ($soItemInvoiced) {
            $totalQty = 0;
            $invoicedQty = 0;

            foreach ($so->items as $item) {
                $totalQty += $item->qty;
            }
            
            if (isset($soItemInvoiced[$so->id])) {
                foreach ($soItemInvoiced[$so->id] as $qty) {
                    $invoicedQty += $qty;
                }
            }

            $pendingQty = max(0, $totalQty - $invoicedQty);
            if ($pendingQty <= 0) {
                return null;
            }

            return [
                'id'          => $so->id,
                'so_no'       => $so->so_no,
                'order_no'    => $so->order_no,
                'so_date'     => $so->so_date ? $so->so_date->format('d-m-Y') : '',
                'total_qty'   => $totalQty,
                'pending_qty' => $pendingQty,
            ];
        })->filter(fn($so) => $so !== null)->values();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getMultipleSaleOrdersDetails(Request $request)
    {
        $soIds = $request->so_ids;
        if (empty($soIds) || !is_array($soIds)) {
            return response()->json(['success' => false, 'message' => 'No Sales Orders selected']);
        }

        $saleOrders = SalesOrder::with([
            'items.brandCategory', 
            'items.item.brand', 
            'items.item.style', 
            'items.stockEntryItem.item.brand', 
            'items.stockEntryItem.item.style', 
            'items.uom', 
            'items.size', 
            'items.color', 
            'customer',
            'charges'
        ])->whereIn('id', $soIds)->get();

        if ($saleOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Sale Orders not found']);
        }

        $customerId = $saleOrders->first()->customer_id;
        $currentInvoiceId = $request->invoice_id ?? null;
        $soItemInvoiced = $this->getCustomerPendingItems($customerId, $currentInvoiceId);

        $allSkus = [];
        foreach ($saleOrders as $so) {
            foreach ($so->items as $item) {
                if ($item->sku) {
                    $allSkus[] = $item->sku;
                }
            }
        }
        $allSkus = array_unique(array_filter($allSkus));

        $prefetchedStockEntryItems = collect();
        if (!empty($allSkus)) {
            $prefetchedStockEntryItems = \App\Models\StockEntryItem::where(function ($q) use ($allSkus) {
                    $q->whereIn('sku', $allSkus)
                      ->orWhereIn('barcode', $allSkus);
                })
                ->where('stock_type', 'finished_goods')
                ->whereNull('deleted_at')
                ->get();
        }

        $allItems = collect();
        $billingAddress = '';
        $shippingAddress = '';
        $transporterName = '';
        $transportModeId = null;

        $transportModes = [];
        foreach ($saleOrders as $so) {
            if (!empty($so->transport_mode_id)) {
                $transportModes[] = $so->transport_mode_id;
            }
            if (empty($billingAddress) && $so->customer) {
                $c = $so->customer;
                $billingAddress = implode(', ', array_filter([$c->address_line_1, $c->address_line_2, $c->address_line_3, $c->city, $c->state, $c->pincode]));
                $shippingAddress = $so->shipping_address;
                $transporterName = $so->transporter_name;
            }

            foreach ($so->items as $item) {
                $invoicedQty = $soItemInvoiced[$so->id][$item->stock_entry_item_id] ?? 0;
                $pendingQty = max(0, $item->qty - $invoicedQty);
                
                if ($pendingQty > 0) {
                    $brandName = '';
                    $itemName = '';
                    $sleeveType = is_array($item->sleeve) ? ($item->sleeve[0] ?? '') : $item->sleeve;

                    $stockEntryItem = $item->stockEntryItem;
                    if (!$stockEntryItem && $item->sku) {
                        $stockEntryItem = $prefetchedStockEntryItems->first(function ($se) use ($item) {
                            return $se->sku === $item->sku || $se->barcode === $item->sku;
                        });
                    }

                    if ($item->item) {
                        if ($item->item->brand) $brandName = $item->item->brand->brand_name;
                        elseif ($item->brandCategory) $brandName = $item->brandCategory->name;
                        
                        if ($item->item->style) $itemName = $item->item->style->style_name;
                        else $itemName = $item->item->name;
                    } elseif ($stockEntryItem) {
                        if ($stockEntryItem->item) {
                            $seItem = $stockEntryItem->item;
                            if ($seItem->brand) $brandName = $seItem->brand->brand_name;
                            elseif ($seItem->brandCategory) $brandName = $seItem->brandCategory->name;

                            if ($seItem->style) $itemName = $seItem->style->style_name;
                            else $itemName = $seItem->name;
                        } else {
                            $brandName = $stockEntryItem->finished_item_code;
                        }
                        if (empty($sleeveType)) $sleeveType = $stockEntryItem->sleeve_type;
                    } elseif ($item->brandCategory) {
                        $brandName = $item->brandCategory->name;
                    } 

                    $stockQty = 0;
                    if ($item->sku) {
                        $stockQtyQuery = DB::table('stock_entry_items')
                            ->where(function($q) use($item) {
                                $q->where('sku', $item->sku)
                                  ->orWhere('barcode', $item->sku);
                            })
                            ->where('stock_type', 'finished_goods')
                            ->whereNull('deleted_at');
                        if ($item->size_id) {
                            $stockQtyQuery->where('size', $item->size_id);
                        }
                        if ($item->item && $item->item->code) {
                            $stockQtyQuery->where('finished_item_code', $item->item->code);
                        } elseif ($stockEntryItem && $stockEntryItem->finished_item_code) {
                            $stockQtyQuery->where('finished_item_code', $stockEntryItem->finished_item_code);
                        }
                        $stockQty = $stockQtyQuery->sum(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
                    } elseif ($item->stock_entry_item_id) {
                        $stockQty = DB::table('stock_entry_items')->where('id', $item->stock_entry_item_id)->whereNull('deleted_at')->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
                    } elseif ($stockEntryItem) {
                        $stockQty = DB::table('stock_entry_items')->where('id', $stockEntryItem->id)->whereNull('deleted_at')->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
                    }

                    if (empty($brandName)) {
                        $brandName = $item->item_name;
                        $itemName = '';
                    }
                   
                    $finishedItemCode = '';
                    if ($item->item && $item->item->code) {
                        $finishedItemCode = $item->item->code;
                    } elseif ($stockEntryItem && $stockEntryItem->finished_item_code) {
                        $finishedItemCode = $stockEntryItem->finished_item_code;
                    }
                    $finalMrp = $item->mrp ?? 0;
                    $finalRate = $item->rate ?? 0;

                    $artNo = $item->art_no;
                    if ($stockEntryItem && !empty($stockEntryItem->art_no)) {
                        $artNo = $stockEntryItem->art_no;
                    }

                    $priceFromMaster = false;
                    $finalMrp = 0;
                    $finalRate = 0;

                    if ($finishedItemCode && $artNo) {
                        $sizeName = $item->size ? $item->size->size : ($item->size_id ?: null);
                        $itemPrice = self::getActiveItemPrice($finishedItemCode, $artNo, $sizeName);
                        if ($itemPrice) {
                            $finalMrp = (float)$itemPrice->selling_price;
                            $finalRate = (float)$itemPrice->unit_price;
                            $priceFromMaster = true;
                        }
                    }

                    $itemData = [
                        'brand_id' => $item->brand_cat_id,
                        'brand_name' => $brandName ?: '',
                        'item_id' => $item->item_id,
                        'item_name' => $itemName ?: ($item->item_name ?? ''),
                        'item_code' => $item->item ? $item->item->code : ($stockEntryItem ? $stockEntryItem->finished_item_code : ''),
                        'uom_id' => $item->uom_id,
                        'uom_code' => $item->uom_id ?: '',
                        'qty' => $pendingQty, 
                        'stock_qty' => (float)$stockQty,
                        'rate' => $finalRate,
                        'mrp' => $finalMrp,
                        'amount' => (float)$finalRate * $pendingQty,
                        'art_no' => $artNo,
                        'is_price_from_master' => $priceFromMaster,
                        'price_converted_from_mrp' => !$priceFromMaster,
                        'hsn_sac' => $item->hsn_sac ?? null,
                        'sku' => $item->sku,
                        'size_id' => $item->size_id,
                        'size_name' => $item->size ? $item->size->size : '',
                        'color_id' => $item->color_id,
                        'color_name' => $item->color ? $item->color->color_name : '',
                        'api_color' => $item->api_color,
                        'sleeve' => $sleeveType ?: '',
                        'stock_entry_item_id' => $item->stock_entry_item_id ?: ($stockEntryItem ? $stockEntryItem->id : null),
                    ];

                    $existingItemKey = $allItems->search(function($i) use ($itemData) {
                        if (!empty($itemData['stock_entry_item_id']) && !empty($i['stock_entry_item_id'])) {
                            return $i['stock_entry_item_id'] == $itemData['stock_entry_item_id'] && $i['rate'] == $itemData['rate'];
                        } else {
                            return $i['sku'] == $itemData['sku'] && 
                                   $i['rate'] == $itemData['rate'] && 
                                   $i['size_id'] == $itemData['size_id'] && 
                                   $i['color_id'] == $itemData['color_id'] && 
                                   $i['art_no'] == $itemData['art_no'] && 
                                   $i['sleeve'] == $itemData['sleeve'] &&
                                   $i['item_code'] == $itemData['item_code'];
                        }
                    });
                    
                    if ($existingItemKey !== false) {
                        $existingItem = $allItems[$existingItemKey];
                        $existingItem['qty'] += $pendingQty;
                        $allItems[$existingItemKey] = $existingItem;
                    } else {
                        $allItems->push($itemData);
                    }
                }
            }
        }
        
        $transportModes = array_unique($transportModes);
        $transportModeId = (count($transportModes) === 1) ? reset($transportModes) : null;

        $firstSo = $saleOrders->first();

        $totalSubTotal = 0;
        $totalDiscountAmount = 0;
        $totalCourierCharges = 0;
        foreach ($saleOrders as $so) {
            $totalSubTotal += $so->sub_total ?? 0;
            $totalDiscountAmount += $so->discount_amount ?? 0;
        }
        $setting = Setting::first();
        $companyStateId = $setting ? $setting->state_id : 1;
        $custStateId = $firstSo->customer ? $firstSo->customer->state_id : null;
        
        $isOtherState = false;
        if ($custStateId && $companyStateId) {
            $isOtherState = ($custStateId != $companyStateId);
        } else {
            $isOtherState = (bool)$firstSo->other_state;
        }

        $cgstPercent = $isOtherState ? 0 : ($setting->cgst ?? 2.5);
        $sgstPercent = $isOtherState ? 0 : ($setting->sgst ?? 2.5);
        $igstPercent = $isOtherState ? ($setting->igst ?? 5.0) : 0;

        $weightedDiscountPercent = $totalSubTotal > 0 ? round(($totalDiscountAmount / $totalSubTotal) * 100, 2) : ($firstSo->discount_percent ?? 0);
        return response()->json([
            'success' => true,
            'customer_id' => $firstSo->customer_id,
            'store_id' => $firstSo->store_id,
            'agent_id' => $firstSo->agent_id,
            'commission_percent' => $firstSo->commission_percent,
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
            'other_state' => $isOtherState ? 'yes' : 'no',
            'discount_percent' => $weightedDiscountPercent,
            'igst_percent' => $igstPercent,
            'cgst_percent' => $cgstPercent,
            'sgst_percent' => $sgstPercent,
            'transporter_name' => $transporterName,
            'transport_gst_no' => $firstSo->transport_gst_no,
            'transport_mode_id' => $transportModeId,
            'sales_discount' => (!empty($firstSo->sales_discount_percent) && (float)$firstSo->sales_discount_percent > 0) ? $firstSo->sales_discount_percent : ($firstSo->customer->sales_discount ?? 0),
            'box_discount_amount' => (!empty($firstSo->box_discount_amount) && (float)$firstSo->box_discount_amount > 0) ? $firstSo->box_discount_amount : ($firstSo->customer->box_discount_amount ?? 0),
            'courier_charge' => $totalCourierCharges,
            'items' => $allItems->values()
        ]);
    }

    public function getSaleOrderDetails($id)
    {
        $so = SalesOrder::with([
            'items.brandCategory', 
            'items.item.brand', 
            'items.item.style', 
            'items.stockEntryItem.item.brand', 
            'items.stockEntryItem.item.style', 
            'items.uom', 
            'items.size', 
            'items.color', 
            'customer',
            'charges'
        ])->find($id);
        if (!$so) {
            return response()->json(['success' => false, 'message' => 'Sale Order not found']);
        }

        $allSkus = [];
        foreach ($so->items as $item) {
            if ($item->sku) {
                $allSkus[] = $item->sku;
            }
        }
        $allSkus = array_unique(array_filter($allSkus));

        $prefetchedStockEntryItems = collect();
        if (!empty($allSkus)) {
            $prefetchedStockEntryItems = \App\Models\StockEntryItem::where(function ($q) use ($allSkus) {
                    $q->whereIn('sku', $allSkus)
                      ->orWhereIn('barcode', $allSkus);
                })
                ->where('stock_type', 'finished_goods')
                ->whereNull('deleted_at')
                ->get();
        }

        $items = $so->items->map(function($item) use ($prefetchedStockEntryItems) {
            $brandName = '';
            $itemName = '';
            $sleeveType = is_array($item->sleeve) ? ($item->sleeve[0] ?? '') : $item->sleeve;

            $stockEntryItem = $item->stockEntryItem;
            if (!$stockEntryItem && $item->sku) {
                $stockEntryItem = $prefetchedStockEntryItems->first(function ($se) use ($item) {
                    return $se->sku === $item->sku || $se->barcode === $item->sku;
                });
            }

            if ($item->item) {
                if ($item->item->brand) {
                    $brandName = $item->item->brand->brand_name;
                } elseif ($item->brandCategory) {
                    $brandName = $item->brandCategory->name;
                }

                if ($item->item->style) {
                    $itemName = $item->item->style->style_name;
                } else {
                    $itemName = $item->item->name;
                }
            } 
            elseif ($stockEntryItem) {
                if ($stockEntryItem->item) {
                    $seItem = $stockEntryItem->item;
                    if ($seItem->brand) {
                        $brandName = $seItem->brand->brand_name;
                    } elseif ($seItem->brandCategory) {
                        $brandName = $seItem->brandCategory->name;
                    }

                    if ($seItem->style) {
                        $itemName = $seItem->style->style_name;
                    } else {
                        $itemName = $seItem->name;
                    }
                } else {
                    $brandName = $stockEntryItem->finished_item_code;
                }
                
                if (empty($sleeveType)) {
                    $sleeveType = $stockEntryItem->sleeve_type;
                }
            }
            elseif ($item->brandCategory) {
                $brandName = $item->brandCategory->name;
            }

            $stockQty = 0;
            if ($item->sku) {
                $stockQtyQuery = DB::table('stock_entry_items')
                    ->where(function($q) use($item) {
                        $q->where('sku', $item->sku)
                          ->orWhere('barcode', $item->sku);
                    })
                    ->where('stock_type', 'finished_goods')
                    ->whereNull('deleted_at');
                if ($item->size_id) {
                    $stockQtyQuery->where('size', $item->size_id);
                }
                if ($item->item && $item->item->code) {
                    $stockQtyQuery->where('finished_item_code', $item->item->code);
                } elseif ($stockEntryItem && $stockEntryItem->finished_item_code) {
                    $stockQtyQuery->where('finished_item_code', $stockEntryItem->finished_item_code);
                }
                $stockQty = $stockQtyQuery->sum(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
            } elseif ($item->stock_entry_item_id) {
                $stockQty = DB::table('stock_entry_items')->where('id', $item->stock_entry_item_id)->whereNull('deleted_at')->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
            } elseif ($stockEntryItem) {
                $stockQty = DB::table('stock_entry_items')->where('id', $stockEntryItem->id)->whereNull('deleted_at')->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
            }

            if (empty($brandName)) {
                $brandName = $item->item_name;
                $itemName = '';
            }

            $finishedItemCode = '';
            if ($item->item && $item->item->code) {
                $finishedItemCode = $item->item->code;
            } elseif ($stockEntryItem && $stockEntryItem->finished_item_code) {
                $finishedItemCode = $stockEntryItem->finished_item_code;
            }

            $finalMrp = $item->mrp ?? 0;
            $finalRate = $item->rate ?? 0;

            $artNo = $item->art_no;
            if ($stockEntryItem && !empty($stockEntryItem->art_no)) {
                $artNo = $stockEntryItem->art_no;
            }

            $priceFromMaster = false;
            $finalMrp = 0;
            $finalRate = 0;

            if ($finishedItemCode && $artNo) {
                $sizeName = $item->size ? $item->size->size : ($item->size_id ?: null);
                $itemPrice = self::getActiveItemPrice($finishedItemCode, $artNo, $sizeName);
                if ($itemPrice) {
                    $finalMrp = (float)$itemPrice->selling_price;
                    $finalRate = (float)$itemPrice->unit_price;
                    $priceFromMaster = true;
                }
            }

            return [
                'brand_id' => $item->brand_cat_id,
                'brand_name' => $brandName ?: '',
                'item_id' => $item->item_id,
                'item_name' => $itemName ?: ($item->item_name ?? ''),
                'item_code' => $item->item ? $item->item->code : ($stockEntryItem ? $stockEntryItem->finished_item_code : ''),
                'uom_id' => $item->uom_id,
                'uom_code' => $item->uom_id ?: '',
                'qty' => $item->qty,
                'stock_qty' => (float)$stockQty,
                'rate' => $finalRate,
                'mrp' => $finalMrp,
                'amount' => (float)$finalRate * $item->qty,
                'art_no' => $artNo,
                'is_price_from_master' => $priceFromMaster,
                'price_converted_from_mrp' => !$priceFromMaster,
                'hsn_sac' => $item->hsn_sac ?? null,
                'sku' => $item->sku,
                'size_id' => $item->size_id,
                'size_name' => $item->size ? $item->size->size : '',
                'color_id' => $item->color_id,
                'color_name' => $item->color ? $item->color->color_name : '',
                'api_color' => $item->api_color,
                'sleeve' => $sleeveType ?: '',
                'stock_entry_item_id' => $item->stock_entry_item_id ?: ($stockEntryItem ? $stockEntryItem->id : null),
            ];
        });

        $billingAddress = '';
        if ($so->customer) {
            $c = $so->customer;
            $billingAddress = implode(', ', array_filter([$c->address_line_1, $c->address_line_2, $c->address_line_3, $c->city, $c->state, $c->pincode]));
        }
        $courierCharge = 0;

        return response()->json([
            'success' => true,
            'customer_id' => $so->customer_id,
            'store_id' => $so->store_id,
            'agent_id' => $so->agent_id,
            'commission_percent' => $so->commission_percent,
            'billing_address' => $billingAddress,
            'shipping_address' => $so->shipping_address,
            'other_state' => $so->other_state ? 'yes' : 'no',
            'discount_percent' => $so->discount_percent,
            'igst_percent' => $so->igst_percent,
            'cgst_percent' => $so->cgst_percent,
            'sgst_percent' => $so->sgst_percent,
            'transporter_name' => $so->transporter_name,
            'transport_gst_no' => $so->transport_gst_no,
            'transport_mode_id' => $so->transport_mode_id,
            'sales_discount' => $so->sales_discount_percent ?? ($so->customer->sales_discount ?? 0),
            'box_discount_amount' => $so->box_discount_amount ?? ($so->customer->box_discount_amount ?? 0),
            'courier_charge' => $courierCharge,
            'items' => $items
        ]);
    }
    
    public function updateStatus(Request $request, $id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);
        $oldStatus = $invoice->invoice_status;
        $newStatus = $request->status;
        $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];

        if ($oldStatus === 'Cancelled' && $newStatus !== 'Cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change status of a cancelled invoice.',
            ], 422);
        }

        if ($newStatus === 'Paid') {
            $totalPaid = DB::table('payments')->where('reference_id', $id)->where('reference_type', 'Customer Collection')->whereNull('deleted_at')->sum('amount');

            $balance = $invoice->grand_total - $totalPaid;

            if ($balance > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot mark as Paid. Outstanding balance of ' . number_format($balance, 2) . ' still remaining. Total Invoice: ' . number_format($invoice->grand_total, 2) . ', Total Paid: ' . number_format($totalPaid, 2),
                ], 422);
            }
        }
        $oldData = $invoice->toArray();
        $invoice->invoice_status = $newStatus;
        $invoice->save();
        $newData = $invoice->fresh()->toArray();
        addLog('update_status', 'Sales Invoice Status', 'sales_invoices', $id, $oldData, $newData);

        if ($oldStatus !== 'Cancelled' && $newStatus === 'Cancelled') {
            $this->adjustStock($id, true);
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function destroy($id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            $this->adjustStock($id, true);
            addLog('delete', 'Sales Invoice', 'sales_invoices', $id, $invoice->toArray(), null);
            $invoice->delete();
            
            DB::commit();
            return redirect('sales_invoices')->with('success', 'Sales Invoice deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }
    public function downloadPdf($id)
    {
        $invoice = SalesInvoice::with([
            'brand',
            'customer.state', 
            'customer.city', 
            'salesOrder.salesAgent', 
            'items' => function($q) {
                $q->orderBy('art_no');
            },
            'items.brandCategory', 
            'items.item.uom', 
            'items.uom'
        ])->findOrFail($id);
        
        $setting = Setting::with(['state', 'city'])->first();
        
        $discountRatio = $invoice->sub_total > 0 ? (($invoice->discount ?? 0) / $invoice->sub_total) : 0;
        $taxSummary = [];
        foreach ($invoice->items as $item) {
            $hsn = $item->hsn_sac ?: '-';
            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $invoice->cgst_percent,
                    'cgst_amount' => 0,
                    'sgst_rate' => $invoice->sgst_percent,
                    'sgst_amount' => 0,
                    'igst_rate' => $invoice->igst_percent,
                    'igst_amount' => 0,
                ];
            }
            $taxSummary[$hsn]['taxable_value'] += $item->amount - ($item->amount * $discountRatio);
        }

        foreach ($taxSummary as &$summary) {
            $summary['cgst_amount'] = ($summary['taxable_value'] * $summary['cgst_rate']) / 100;
            $summary['sgst_amount'] = ($summary['taxable_value'] * $summary['sgst_rate']) / 100;
            $summary['igst_amount'] = ($summary['taxable_value'] * $summary['igst_rate']) / 100;
        }

        if (($invoice->other_charges ?? 0) > 0) {
            $courierHsn = '9968';
            $courierAmt = (float)$invoice->other_charges;
            $isOtherState = (bool)($invoice->other_state ?? false);
            $cgstRate = (float)($invoice->cgst_percent ?? 0);
            $sgstRate = (float)($invoice->sgst_percent ?? 0);
            $igstRate = (float)($invoice->igst_percent ?? 0);
            
            $taxSummary[$courierHsn] = [
                'hsn' => $courierHsn,
                'taxable_value' => $courierAmt,
                'cgst_rate' => $isOtherState ? 0 : $cgstRate,
                'cgst_amount' => $isOtherState ? 0 : ($courierAmt * $cgstRate) / 100,
                'sgst_rate' => $isOtherState ? 0 : $sgstRate,
                'sgst_amount' => $isOtherState ? 0 : ($courierAmt * $sgstRate) / 100,
                'igst_rate' => $isOtherState ? $igstRate : 0,
                'igst_amount' => $isOtherState ? ($courierAmt * $igstRate) / 100 : 0,
            ];
        }

        $totalInWords = numberToWords($invoice->grand_total);
        $totalTaxInWords = numberToWords($invoice->tax_amount);

        $pdf = Pdf::loadView('sales_invoice.pdf', compact('invoice', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords'));
        $pdf->setPaper('A4', 'portrait');
        
        $safeInvoiceNo = str_replace(['/', '\\'], '_', $invoice->inv_no);
        return $pdf->stream('Sales_Invoice_' . $safeInvoiceNo . '.pdf');
    }

    public function print($id)
    {
        $invoice = SalesInvoice::with([
            'brand',
            'customer.state', 
            'customer.city', 
            'salesOrder.salesAgent', 
            'items' => function($q) {
                $q->orderBy('art_no');
            },
            'items.brandCategory', 
            'items.item.uom', 
            'items.uom'
        ])->findOrFail($id);
        
        $setting = Setting::with(['state', 'city'])->first();
        
        $discountRatio = $invoice->sub_total > 0 ? (($invoice->discount ?? 0) / $invoice->sub_total) : 0;
        $taxSummary = [];
        foreach ($invoice->items as $item) {
            $hsn = $item->hsn_sac ?: '-';
            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $invoice->cgst_percent,
                    'cgst_amount' => 0,
                    'sgst_rate' => $invoice->sgst_percent,
                    'sgst_amount' => 0,
                    'igst_rate' => $invoice->igst_percent,
                    'igst_amount' => 0,
                ];
            }
            $taxSummary[$hsn]['taxable_value'] += $item->amount - ($item->amount * $discountRatio);
        }

        foreach ($taxSummary as &$summary) {
            $summary['cgst_amount'] = ($summary['taxable_value'] * $summary['cgst_rate']) / 100;
            $summary['sgst_amount'] = ($summary['taxable_value'] * $summary['sgst_rate']) / 100;
            $summary['igst_amount'] = ($summary['taxable_value'] * $summary['igst_rate']) / 100;
        }

        if (($invoice->other_charges ?? 0) > 0) {
            $courierHsn = '9968';
            $courierAmt = (float)$invoice->other_charges;
            $isOtherState = (bool)($invoice->other_state ?? false);
            $cgstRate = (float)($invoice->cgst_percent ?? 0);
            $sgstRate = (float)($invoice->sgst_percent ?? 0);
            $igstRate = (float)($invoice->igst_percent ?? 0);
            
            $taxSummary[$courierHsn] = [
                'hsn' => $courierHsn,
                'taxable_value' => $courierAmt,
                'cgst_rate' => $isOtherState ? 0 : $cgstRate,
                'cgst_amount' => $isOtherState ? 0 : ($courierAmt * $cgstRate) / 100,
                'sgst_rate' => $isOtherState ? 0 : $sgstRate,
                'sgst_amount' => $isOtherState ? 0 : ($courierAmt * $sgstRate) / 100,
                'igst_rate' => $isOtherState ? $igstRate : 0,
                'igst_amount' => $isOtherState ? ($courierAmt * $igstRate) / 100 : 0,
            ];
        }

        $totalInWords = numberToWords($invoice->grand_total);
        $totalTaxInWords = numberToWords($invoice->tax_amount);
        $is_print = true;

        return view('sales_invoice.pdf', compact('invoice', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords', 'is_print'));
    }
    public function printSticker($id)
    {
        $invoice = SalesInvoice::with(['customer.state', 'customer.city', 'customer.place'])->findOrFail($id);
        $totalPcs = $invoice->items->sum('quantity');
        $boxCount = (int) ($invoice->no_of_box ?: 1);
        $setting = Setting::with(['state', 'city'])->first();
        $is_print = true;
        return view('sales_invoice.sticker', compact('invoice', 'totalPcs', 'setting', 'is_print', 'boxCount'));
    }

    public function printTransportSticker($id)
    {
        $invoice = SalesInvoice::with(['customer.state', 'customer.city', 'customer.place'])->findOrFail($id);
        $totalPcs = $invoice->items->sum('quantity');
        $boxCount = (int) ($invoice->no_of_box ?: 1);
        $setting = Setting::with(['state', 'city'])->first();
        $is_print = true;
        return view('sales_invoice.transport_sticker', compact('invoice', 'totalPcs', 'setting', 'is_print', 'boxCount'));
    }

    public function printCourierSticker($id)
    {
        $invoice = SalesInvoice::with(['customer.state', 'customer.city', 'customer.place'])->findOrFail($id);
        $totalPcs = $invoice->items->sum('quantity');
        $boxCount = (int) ($invoice->no_of_box ?: 1);
        $setting = Setting::with(['state', 'city'])->first();
        $is_print = true;
        return view('sales_invoice.courier_sticker', compact('invoice', 'totalPcs', 'setting', 'is_print', 'boxCount'));
    }

    public function downloadDeliveryOrder($id)
    {
        $invoice = SalesInvoice::with([
            'customer.state', 
            'customer.city', 
            'salesOrder.salesAgent', 
            'items.brandCategory', 
            'items.item.uom', 
            'items.uom'
        ])->findOrFail($id);
        
        $setting = Setting::with(['state', 'city'])->first();
        $totalPcs = $invoice->items->sum('quantity');

        $pdf = Pdf::loadView('sales_invoice.delivery_order', compact('invoice', 'setting', 'totalPcs'));
        $pdf->setPaper('A4', 'portrait');
        
        $safeInvoiceNo = str_replace(['/', '\\'], '_', $invoice->inv_no);
        return $pdf->stream('Delivery_Order_' . $safeInvoiceNo . '.pdf');
    }

    public function downloadOpenDeliveryOrder($id)
    {
        $invoice = SalesInvoice::with([
            'customer.state', 
            'customer.city', 
            'salesOrder.salesAgent', 
            'items.brandCategory', 
            'items.item.uom', 
            'items.uom'
        ])->findOrFail($id);
        
        $setting = Setting::with(['state', 'city'])->first();
        $totalPcs = $invoice->items->sum('quantity');

        $pdf = Pdf::loadView('sales_invoice.open_delivery_order', compact('invoice', 'setting', 'totalPcs'));
        $pdf->setPaper('A4', 'portrait');
        
        $safeInvoiceNo = str_replace(['/', '\\'], '_', $invoice->inv_no);
        return $pdf->stream('Open_Delivery_Order_' . $safeInvoiceNo . '.pdf');
    }

    public function generateEInvoice(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $invoice = SalesInvoice::findOrFail($id);
        if (in_array((int)$invoice->store_id, [1, 2])) {
            return response()->json([
                'success' => false,
                'message' => 'e-Invoice / IRN is not applicable for Fabric and Accessories invoices.'
            ]);
        }
        if ($invoice->einvoice_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This E-Invoice has been cancelled on IRP. According to GST guidelines, you cannot reuse this invoice number to generate a new E-Invoice. You must issue a new sales invoice with a new invoice number.'
            ]);
        }
        if ($invoice->irn) {
            return response()->json(['success' => false, 'message' => 'E-Invoice already generated']);
        }
        
        if (strlen($invoice->inv_no) < 1 || strlen($invoice->inv_no) > 16 || !preg_match('/^[a-zA-Z1-9][a-zA-Z0-9\/\-]*$/', $invoice->inv_no)) {
            return response()->json([
                'success' => false,
                'message' => 'The Sales Invoice Number is invalid for e-Invoice API. It must be 1-16 characters long, start with a letter or 1-9, and contain only letters, numbers, hyphens, or forward slashes.'
            ]);
        }

        $transporterData = [];
        if (!empty($invoice->vehicle_no)) {
            $transporterData = [
                'transport_distance' => $invoice->transport_distance,
                'transport_mode' => $invoice->transport_mode,
                'transporter_id' => $invoice->transporter_id,
                'transporter_name' => $invoice->transporter_name,
                'tran_doc_date' => $invoice->tran_doc_date ? Carbon::parse($invoice->tran_doc_date)->format('d/m/Y') : null,
                'tran_doc_no' => $invoice->tran_doc_no,
                'vehicle_no' => $invoice->vehicle_no,
                'veh_type' => $invoice->veh_type,
            ];
        }

        $result = $eInvoiceService->generateEInvoice($invoice, $transporterData);
        if (!$result['success'] && isset($result['message'])) {
            if (strpos($result['message'], 'is cancelled and document date') !== false) {
                preg_match('/GSTIN - ([A-Z0-9]+)/', $result['message'], $matches);
                $gstin = $matches[1] ?? 'Unknown';
                $invoiceDate = \Carbon\Carbon::parse($invoice->inv_date)->format('d-m-Y');
                
                $result['message'] = "<strong>e-Invoice Generation Failed</strong><br><br>"
                                   . "<strong>Reason:</strong> The customer's GSTIN has been cancelled before the invoice date. Therefore, an e-Invoice cannot be generated for this invoice.<br><br>"
                                   . "<strong>Customer GSTIN:</strong> " . $gstin . "<br>"
                                   . "<strong>Invoice Date:</strong> " . $invoiceDate . "<br><br>"
                                   . "Please verify the customer's GST registration status or update the GSTIN before generating the e-Invoice.";
            } elseif (strpos($result['message'], 'field POS must match') !== false || strpos($result['message'], 'field State must') !== false) {
                $result['message'] = "<strong>e-Invoice Generation Failed</strong><br><br>"
                                   . "<strong>Reason:</strong> The customer's State or Place of Supply (POS) State Code is invalid or missing.<br><br>"
                                   . "<strong>Please verify the following before generating the e-Invoice:</strong><br>"
                                   . "<ul style='text-align: left; margin-bottom: 0; padding-left: 20px;'>"
                                   . "<li>Ensure the State is selected correctly.</li>"
                                   . "<li>Ensure the State Code (POS) is a valid 2-digit GST State Code (e.g., 33 for Tamil Nadu, 29 for Karnataka).</li>"
                                   . "<li>Update the customer master if the State or State Code is incorrect.</li>"
                                   . "</ul>";
            }
        }
        if ($result['success']) {
            if (function_exists('addLog')) {
                $invoice->refresh();
                addLog('generate_einvoice', 'Sales Invoice E-Invoice Generated', 'sales_invoices', $invoice->id, null, $invoice->toArray());
            }
        }

        return response()->json($result);
    }

    public function getIrn(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $invoice = SalesInvoice::findOrFail($id);
        if (in_array((int)$invoice->store_id, [1, 2])) {
            return response()->json([
                'success' => false,
                'message' => 'e-Invoice / IRN is not applicable for Fabric and Accessories invoices.'
            ]);
        }
        if ($invoice->einvoice_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This E-Invoice has been cancelled on IRP. You cannot get IRN for a cancelled invoice.'
            ]);
        }
        
        $result = $eInvoiceService->getOrSyncIRN($invoice);
        if ($result['success']) {
            if (function_exists('addLog')) {
                $invoice->refresh();
                addLog('get_irn', 'Sales Invoice IRN Synced / Recovered from TaxPro', 'sales_invoices', $invoice->id, null, $invoice->toArray());
            }
        }

        return response()->json($result);
    }

    public function generateEWayBill(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $invoice = SalesInvoice::findOrFail($id);

        if (in_array((int)$invoice->store_id, [1, 2])) {
            return response()->json([
                'success' => false,
                'message' => 'e-Way Bill via e-Invoice is not applicable for Fabric and Accessories invoices.'
            ]);
        }

        if (empty($invoice->irn)) {
            return response()->json([
                'success' => false,
                'message' => 'Generate E-Invoice first before E-Way Bill.'
            ]);
        }

        $result = $eInvoiceService->generateEWayBill($invoice, $request->all());

        if ($result['success']) {
            if (function_exists('addLog')) {
                $invoice->refresh();
                addLog('generate_ewaybill', 'Sales Invoice E-Way Bill Generated', 'sales_invoices', $invoice->id, null, $invoice->toArray());
            }
        }

        return response()->json($result);
    }

    public function cancelEInvoice(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $invoice = SalesInvoice::findOrFail($id);

        if (in_array((int)$invoice->store_id, [1, 2])) {
            return response()->json([
                'success' => false,
                'message' => 'e-Invoice is not applicable for Fabric and Accessories invoices.'
            ]);
        }

        if (empty($invoice->irn)) {
            return response()->json([
                'success' => false,
                'message' => 'No active E-Invoice found for this sales invoice.'
            ]);
        }

        if ($invoice->ack_date) {
            $ackDateTime = \Carbon\Carbon::parse($invoice->ack_date);
            if ($ackDateTime->diffInHours(now()) >= 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cancellation time window expired. According to GST guidelines, an E-Invoice cannot be cancelled after 24 hours of generation. Please issue a Credit Note instead.'
                ]);
            }
        }

        $cancelReason = $request->input('cancel_reason', '2');
        $cancelRemarks = $request->input('cancel_remarks', 'Data Entry Mistake');

        $oldData = $invoice->toArray();

        if (!empty($invoice->eway_bill_no)) {
            if ($invoice->eway_bill_date) {
                $ewbDateTime = \Carbon\Carbon::parse($invoice->eway_bill_date);
                if ($ewbDateTime->diffInHours(now()) >= 24) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot cancel. E-Way Bill cancellation time window (24 hours) has expired.'
                    ]);
                }
            }

            $ewbResult = $eInvoiceService->cancelEWayBill($invoice, $cancelReason, $cancelRemarks);
            if (!$ewbResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel linked E-Way Bill first: ' . $ewbResult['message']
                ]);
            }
            $invoice->refresh();
        }

        $result = $eInvoiceService->cancelEInvoice($invoice, $cancelReason, $cancelRemarks);

        if ($result['success']) {
            try {
                \DB::beginTransaction();
                
                $invoice->refresh();

                if (!$invoice->stock_reverted) {
                    $this->adjustStock($invoice->id, true);
                    
                    $invoice->update([
                        'invoice_status' => 'Cancelled',
                        'cancellation_date' => now(),
                        'cancel_reason' => $cancelReason,
                        'cancel_remarks' => $cancelRemarks,
                        'cancelled_by' => auth()->id() ?? 1,
                        'stock_reverted' => true,
                    ]);
                } else {
                    $invoice->update([
                        'invoice_status' => 'Cancelled',
                        'cancellation_date' => now(),
                        'cancel_reason' => $cancelReason,
                        'cancel_remarks' => $cancelRemarks,
                        'cancelled_by' => auth()->id() ?? 1,
                    ]);
                }

                $newData = $invoice->toArray();
                addLog('cancel_einvoice', 'Sales Invoice E-Invoice Cancelled', 'sales_invoices', $id, $oldData, $newData);

                \DB::commit();

                if (!empty($oldData['eway_bill_no'])) {
                    $result['message'] = 'E-Way Bill and E-Invoice cancelled successfully, and stock reverted.';
                } else {
                    $result['message'] = 'E-Invoice cancelled successfully, and stock reverted.';
                }
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Failed to update local records after E-Invoice cancellation: ' . $e->getMessage());
                $result['message'] = 'E-Invoice cancelled on portal, but local stock reversion failed. Please contact admin.';
            }
        }

        return response()->json($result);
    }

    public function cancelEWayBill(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $invoice = SalesInvoice::findOrFail($id);

        if (empty($invoice->eway_bill_no)) {
            return response()->json([
                'success' => false,
                'message' => 'No active E-Way Bill found for this invoice.'
            ]);
        }

        if ($invoice->eway_bill_date) {
            $ewbDateTime = \Carbon\Carbon::parse($invoice->eway_bill_date);
            if ($ewbDateTime->diffInHours(now()) >= 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cancellation time window expired. According to GST guidelines, an E-Way Bill cannot be cancelled after 24 hours of generation.'
                ]);
            }
        }

        $cancelReason = $request->input('cancel_reason', '2');
        $cancelRemarks = $request->input('cancel_remarks', 'Data Entry Mistake');

        $oldData = $invoice->toArray();
        $result = $eInvoiceService->cancelEWayBill($invoice, $cancelReason, $cancelRemarks);

        if ($result['success']) {
            $invoice->refresh();
            $newData = $invoice->toArray();
            addLog('cancel_ewaybill', 'Sales Invoice E-Way Bill Cancelled', 'sales_invoices', $id, $oldData, $newData);
        }

        return response()->json($result);
    }

    public function recreate($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create sales-invoice')) {
            return unauthorizedRedirect();
        }

        $original = SalesInvoice::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            $nextInvNumber = $this->generateInvoiceNumber($original->brand_id, now());

            $newInvoice = $original->replicate();
            $newInvoice->inv_no = $nextInvNumber;
            $newInvoice->inv_date = now();
            $newInvoice->invoice_status = 'Draft';
            $newInvoice->einvoice_status = null;
            $newInvoice->irn = null;
            $newInvoice->ack_no = null;
            $newInvoice->ack_date = null;
            $newInvoice->signed_qr_code = null;
            $newInvoice->eway_bill_no = null;
            $newInvoice->eway_bill_date = null;
            $newInvoice->eway_bill_valid_till = null;
            $newInvoice->received_amount = 0.00;
            $newInvoice->save();

            foreach ($original->items as $item) {
                $newItem = $item->replicate();
                $newItem->sales_invoice_id = $newInvoice->id;
                $newItem->save();
            }

            DB::commit();

            return redirect('sales_invoices/add/' . $newInvoice->id)->with('success', 'Invoice details copied successfully. New Invoice ' . $newInvoice->inv_no . ' created as Draft.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to copy invoice: ' . $e->getMessage());
        }
    }

    public function calculateDistance(Request $request)
    {
        $fromPincode = trim($request->query('from_pincode'));
        $toPincode = trim($request->query('to_pincode'));

        if (empty($fromPincode) || empty($toPincode)) {
            return response()->json(['success' => false, 'message' => 'Both pincodes are required.']);
        }

        try {
            $fromCoords = $this->getGeocode($fromPincode);
            $toCoords = $this->getGeocode($toPincode);

            if (!$fromCoords || !$toCoords) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to resolve coordinates. Please enter distance manually.'
                ]);
            }

            $straightDistance = $this->getDistance(
                $fromCoords['lat'], $fromCoords['lng'],
                $toCoords['lat'], $toCoords['lng']
            );

            $roadDistance = $straightDistance * 1.1576;

            return response()->json([
                'success' => true,
                'from_pincode' => $fromPincode,
                'to_pincode' => $toPincode,
                'from_coords' => $fromCoords,
                'to_coords' => $toCoords,
                'straight_line_km' => round($straightDistance, 2),
                'distance' => round($roadDistance),
                'note' => 'Estimated road distance (Haversine distance multiplied by a 1.1576 correction factor).'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getGeocode($pincode)
    {
        $cleanPincode = preg_replace('/[^0-9]/', '', $pincode);
        if (strlen($cleanPincode) !== 6) {
            return null;
        }

        return \Illuminate\Support\Facades\Cache::remember("geocode_in_{$cleanPincode}", now()->addDays(30), function () use ($cleanPincode) {
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'User-Agent' => 'NachiasERP/1.0 (admin@nachias.com)'
                ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                    'postalcode' => $cleanPincode,
                    'countrycodes' => 'in',
                    'format' => 'json',
                    'limit' => 1
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                        return [
                            'lat' => (float)$data[0]['lat'],
                            'lng' => (float)$data[0]['lon'],
                            'source' => 'nominatim_postal'
                        ];
                    }
                }

                $postalResponse = \Illuminate\Support\Facades\Http::timeout(5)->get("https://api.postalpincode.in/pincode/{$cleanPincode}");
                if ($postalResponse->successful()) {
                    $postalData = $postalResponse->json();
                    if (!empty($postalData) && isset($postalData[0]['Status']) && $postalData[0]['Status'] === 'Success' && !empty($postalData[0]['PostOffice'])) {
                        $postOffice = $postalData[0]['PostOffice'][0];
                        $locality = $postOffice['Name'] ?? '';
                        $district = $postOffice['District'] ?? '';
                        $state = $postOffice['State'] ?? '';

                        $queries = [];
                        if ($locality && $district && $state) {
                            $queries[] = "{$locality}, {$district}, {$state}, India";
                        }
                        if ($district && $state) {
                            $queries[] = "{$district}, {$state}, India";
                        }

                        foreach ($queries as $q) {
                            $geoResponse = \Illuminate\Support\Facades\Http::withHeaders([
                                'User-Agent' => 'NachiasERP/1.0 (admin@nachias.com)'
                            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                                'q' => $q,
                                'countrycodes' => 'in',
                                'format' => 'json',
                                'limit' => 1
                            ]);

                            if ($geoResponse->successful()) {
                                $geoData = $geoResponse->json();
                                if (!empty($geoData) && isset($geoData[0]['lat']) && isset($geoData[0]['lon'])) {
                                    return [
                                        'lat' => (float)$geoData[0]['lat'],
                                        'lng' => (float)$geoData[0]['lon'],
                                        'source' => 'postal_in_fallback'
                                    ];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Geocoding failed for pincode {$cleanPincode}: " . $e->getMessage());
            }
            return null;
        });
    }

    public function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    public function scanItems($id)
    {
        $invoice = SalesInvoice::with(['customer', 'items.item', 'items.stockEntryItem'])->findOrFail($id);
        $setting = Setting::first();
        return view('sales_invoice.scan_items', compact('invoice', 'setting'));
    }

    public function saveScanProgress(Request $request, $id)
    {
        $invoice = SalesInvoice::findOrFail($id);
        if ($invoice->delivery_status === 'Dispatched') {
            return response()->json(['success' => false, 'message' => 'Invoice is already dispatched and locked.'], 400);
        }
        
        if (strtolower($invoice->einvoice_status) === 'cancelled' || $invoice->invoice_status === 'Cancelled') {
            return response()->json(['success' => false, 'message' => 'Invoice is cancelled. Scanning is not allowed.'], 400);
        }

        $items = $request->input('items', []);
        foreach ($items as $itemData) {
            $itemId = $itemData['id'];
            $scannedQty = $itemData['scanned_qty'];
            
            $invoiceItem = SalesInvoiceItem::where('sales_invoice_id', $id)->where('id', $itemId)->first();
            if ($invoiceItem) {
                if ($scannedQty <= $invoiceItem->quantity) {
                    $invoiceItem->update(['scanned_qty' => $scannedQty]);
                }
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Progress saved']);
    }

    public function completeDispatch($id)
    {
        $invoice = SalesInvoice::findOrFail($id);
        if (strtolower($invoice->einvoice_status) === 'cancelled' || $invoice->invoice_status === 'Cancelled') {
            return response()->json(['success' => false, 'message' => 'Cannot dispatch a cancelled invoice.'], 400);
        }
        $totalScanned = SalesInvoiceItem::where('sales_invoice_id', $id)->sum('scanned_qty');
        if ($totalScanned <= 0) {
            return response()->json(['success' => false, 'message' => 'Cannot dispatch! No items have been scanned yet. Please scan items first.'], 400);
        }
        $invoice->update([
            'delivery_status' => 'Dispatched',
            'dispatch_completed_at' => now()
        ]);
        return response()->json(['success' => true, 'message' => 'Dispatch completed successfully']);
    }

    public function updateDeliveryStatus(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('edit sales-invoice')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'invoice_id' => 'required|exists:sales_invoices,id',
            'delivery_status' => 'required|in:Pending,Dispatched,Partially Delivered,Delivered,Cancel'
        ]);
        $invoice = SalesInvoice::findOrFail($request->invoice_id);
        $currentStatus = $invoice->delivery_status;
        $newStatus = $request->delivery_status;
        if ($currentStatus === 'Delivered' && in_array($newStatus, ['Pending', 'Dispatched'])) {
            return response()->json(['success' => false, 'message' => 'Cannot revert a Delivered invoice back to Pending or Dispatched.'], 400);
        }
        if ($currentStatus === 'Cancel' && $newStatus !== 'Cancel') {
            return response()->json(['success' => false, 'message' => 'Cannot change status of a Cancelled delivery.'], 400);
        }
        if ($currentStatus === 'Pending' && $newStatus === 'Delivered') {
            return response()->json(['success' => false, 'message' => 'Invoice must be Dispatched before it can be Delivered.'], 400);
        }
        if ($newStatus === 'Dispatched') {
            $totalScanned = SalesInvoiceItem::where('sales_invoice_id', $request->invoice_id)->sum('scanned_qty');
            if ($totalScanned <= 0) {
                return response()->json(['success' => false, 'message' => 'Cannot change status to Dispatched! No items have been scanned yet.'], 400);
            }
        }
        $invoice->update([
            'delivery_status' => $newStatus
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Delivery Status updated successfully'
        ]);
    }
    public function getFinishedGoodsStock(Request $request)
    {
        $search = $request->get('q');
        
        $storeCategory = \App\Models\StoreCategory::where('name', 'like', '%Finished Goods%')->first();
        $storeCategoryId = $storeCategory ? $storeCategory->id : 4;
        
        $query = \App\Models\StockEntryItem::select(
            'stock_entry_items.id', 
            'stock_entry_items.art_no', 
            'stock_entry_items.size', 
            'stock_entry_items.color_id',
            'stock_entry_items.sleeve_type',
            'stock_entry_items.price',
            'stock_entry_items.mrp',
            'stock_entry_items.sku',
            'stock_entry_items.finished_item_code',
            'colors.name as color_name',
            \DB::raw('(stock_entry_items.qty_in - COALESCE(stock_entry_items.qty_out, 0)) as available_qty')
        )
        ->join('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
        ->leftJoin('colors', 'stock_entry_items.color_id', '=', 'colors.id')
        ->where('stock_entries.store_category_id', $storeCategoryId)
        ->having('available_qty', '>', 0);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('stock_entry_items.art_no', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.sku', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.size', 'like', "%{$search}%")
                  ->orWhere('colors.name', 'like', "%{$search}%");
            });
        }
        
        $items = $query->limit(50)->get();
        
        $results = [];
        foreach ($items as $item) {
            $displayName = $item->art_no;
            if ($item->color_name) $displayName .= " - " . $item->color_name;
            if ($item->size) $displayName .= " - " . $item->size;
            if ($item->sleeve_type) $displayName .= " - " . $item->sleeve_type;
            
            $itemPrice = \DB::table('item_prices')
                ->where('finished_item_code', $item->finished_item_code)
                ->where('art_no', $item->art_no)
                ->where('size', $item->size)
                ->where('status', 'Active')
                ->whereNull('deleted_at')
                ->whereDate('effective_from', '<=', now())
                ->orderBy('effective_from', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if (!$itemPrice) {
                $itemPrice = \DB::table('item_prices')
                    ->where('finished_item_code', $item->finished_item_code)
                    ->where('art_no', $item->art_no)
                    ->whereNull('size')
                    ->where('status', 'Active')
                    ->whereNull('deleted_at')
                    ->whereDate('effective_from', '<=', now())
                    ->orderBy('effective_from', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
            }

            $mrp = $itemPrice ? $itemPrice->selling_price : 0;
            $price = $itemPrice ? $itemPrice->unit_price : 0;

            $results[] = [
                'id' => $item->id,
                'text' => $displayName . " (Stock: {$item->available_qty})",
                'art_no' => $item->art_no,
                'size' => $item->size,
                'color_id' => $item->color_id,
                'color_name' => $item->color_name,
                'sleeve_type' => $item->sleeve_type,
                'price' => $price,
                'mrp' => $mrp,
                'sku' => $item->sku,
                'available_qty' => $item->available_qty,
            ];
        }
        
        return response()->json(['results' => $results]);
    }
    private function validateStockAvailability($items, $invoiceId = null)
    {
        $existingInvoiceItems = [];
        if ($invoiceId) {
            $existingInvoiceItems = SalesInvoiceItem::where('sales_invoice_id', $invoiceId)->get();
            
            $stockFieldsChanged = false;
            $reqItemsMap = [];
            foreach ($items as $reqItem) {
                if (!empty($reqItem['id'])) {
                    $reqItemsMap[$reqItem['id']] = $reqItem;
                } else {
                    $stockFieldsChanged = true;
                    break;
                }
            }
            
            if (!$stockFieldsChanged) {
                if (count($items) !== $existingInvoiceItems->count()) {
                    $stockFieldsChanged = true;
                } else {
                    foreach ($existingInvoiceItems as $ei) {
                        if (!isset($reqItemsMap[$ei->id])) {
                            $stockFieldsChanged = true;
                            break;
                        }
                        
                        $reqItem = $reqItemsMap[$ei->id];
                        if (
                            $ei->quantity != ($reqItem['quantity'] ?? 0) ||
                            $ei->sku != ($reqItem['sku'] ?? null) ||
                            $ei->size != ($reqItem['size'] ?? null) ||
                            $ei->art_no != ($reqItem['art_no'] ?? null) ||
                            $ei->color_id != ($reqItem['color_id'] ?? null) ||
                            $ei->sleeve_type != ($reqItem['sleeve_type'] ?? null) ||
                            $ei->stock_entry_item_id != ($reqItem['stock_entry_item_id'] ?? null)
                        ) {
                            $stockFieldsChanged = true;
                            break;
                        }
                    }
                }
            }
            
            if (!$stockFieldsChanged) {
                return;
            }
        }

        $requiredQuantities = [];
        foreach ($items as $item) {
            $sku = $item['sku'] ?? null;
            $size = $item['size'] ?? null;
            $sleeve_type = $item['sleeve_type'] ?? null;
            $stockEntryItemId = $item['stock_entry_item_id'] ?? null;
            
            $finishedItemCode = null;
            $artNo = $item['art_no'] ?? null;
            if ($stockEntryItemId) {
                $stItem = \App\Models\StockEntryItem::find($stockEntryItemId);
                if ($stItem) {
                    if ($stItem->stock_type === 'raw_material') {
                        $rmName = DB::table('raw_materials')->where('id', $stItem->raw_material_id)->value('name');
                        $rawMaterialName = $rmName ?: ($stItem->art_no ?: 'Raw Material');
                        $identifier = 'rm_' . $stockEntryItemId;
                        if (!isset($requiredQuantities[$identifier])) {
                            $requiredQuantities[$identifier] = [
                                'sku' => null,
                                'is_raw_material' => true,
                                'stock_entry_item_id' => $stockEntryItemId,
                                'qty' => 0,
                                'display' => $rawMaterialName
                            ];
                        }
                        $requiredQuantities[$identifier]['qty'] += (float)($item['quantity'] ?? 0);
                        continue;
                    }

                    $finishedItemCode = $stItem->finished_item_code;
                    $artNo = $stItem->art_no;
                }
            }
            
            $identifier = $sku ? $sku . '_' . $size . '_' . $sleeve_type . '_' . $finishedItemCode . '_' . $artNo : ($item['item_name'] ?? $item['art_no'] ?? 'unknown');
            
            if (!isset($requiredQuantities[$identifier])) {
                $requiredQuantities[$identifier] = [
                    'sku' => $sku,
                    'size' => $size,
                    'sleeve_type' => $sleeve_type,
                    'finished_item_code' => $finishedItemCode,
                    'art_no' => $artNo,
                    'qty' => 0,
                    'display' => $sku ?: $identifier
                ];
            }
            $requiredQuantities[$identifier]['qty'] += (float)($item['quantity'] ?? 0);
            
            if (!$stockEntryItemId) {
                $stockQuery = clone StockEntryItem::where('stock_type', 'finished_goods')->whereNull('deleted_at');
                if (!empty($item['sku'])) {
                    $stockQuery->where(function ($q) use ($item) {
                        $q->where('sku', $item['sku'])->orWhere('barcode', $item['sku']);
                    });
                } else {
                    $stockQuery->where(function ($q) use ($item) {
                        $fic = $item['item_name'] ?? ($item['art_no'] ?? '');
                        $q->where('finished_item_code', $fic)
                          ->orWhere('art_no', $fic);
                    });
                }
                if (!empty($item['size'])) {
                    $stockQuery->where('size', $item['size']);
                }
                if (!empty($item['art_no'])) {
                    $stockQuery->where('art_no', $item['art_no']);
                }
                if (!empty($item['sleeve_type'])) {
                    $sleeveUpper = strtoupper(trim($item['sleeve_type']));
                    if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                        $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
                    } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                        $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
                    } else {
                        $sleeveDbValues = [$item['sleeve_type']];
                    }
                    $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
                }

                $matchedStockItem = $stockQuery->orderByRaw('(qty_in - qty_out) > 0 DESC')->orderBy('id', 'desc')->first();
                if ($matchedStockItem) {
                    $stockEntryItemId = $matchedStockItem->id;
                    $requiredQuantities[$identifier]['finished_item_code'] = $matchedStockItem->finished_item_code;
                    $requiredQuantities[$identifier]['art_no'] = $matchedStockItem->art_no;
                }
            }

            if (!$stockEntryItemId && $sku) {
                throw new \Exception("Could not find matching stock for item: " . ($sku ?: ($item['item_name'] ?? 'Unknown Item')));
            }
        }

        foreach ($requiredQuantities as $req) {
            if (!empty($req['is_raw_material'])) {
                $stItem = StockEntryItem::find($req['stock_entry_item_id']);
                if (!$stItem) {
                    throw new \Exception("Stock item not found for " . $req['display']);
                }
                $alreadyDeducted = 0;
                if ($invoiceId) {
                    foreach ($existingInvoiceItems as $ei) {
                        if ($ei->stock_entry_item_id == $req['stock_entry_item_id']) {
                            $alreadyDeducted += $ei->quantity;
                        }
                    }
                }
                $effectiveAvailable = ($stItem->qty_in - $stItem->qty_out) + $alreadyDeducted;
                if ($effectiveAvailable < $req['qty']) {
                    throw new \Exception("Insufficient stock for " . $req['display'] . " (Available: " . $effectiveAvailable . ", Required: " . $req['qty'] . ")");
                }
                continue;
            }

            if (!$req['sku']) continue;

            $stockQuery = StockEntryItem::where('stock_type', 'finished_goods')
                ->whereNull('deleted_at')
                ->where(function ($q) use ($req) {
                    $q->where('sku', $req['sku'])->orWhere('barcode', $req['sku']);
                });
            if ($req['size']) {
                $stockQuery->where('size', $req['size']);
            }
            if ($req['art_no']) {
                $stockQuery->where('art_no', $req['art_no']);
            }
            if ($req['finished_item_code']) {
                $stockQuery->where('finished_item_code', $req['finished_item_code']);
            }
            if ($req['art_no']) {
                $stockQuery->where('art_no', $req['art_no']);
            }
            if ($req['sleeve_type']) {
                $sleeveUpper = strtoupper(trim($req['sleeve_type']));
                if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                    $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
                } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                    $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
                } else {
                    $sleeveDbValues = [$req['sleeve_type']];
                }
                $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
            }
            
            $totalIn = (clone $stockQuery)->sum('qty_in');
            $totalOut = (clone $stockQuery)->sum('qty_out');
            
            $alreadyDeducted = 0;
            if ($invoiceId) {
                foreach ($existingInvoiceItems as $ei) {
                    if ($ei->sku === $req['sku'] && $ei->size == $req['size']) {
                        $alreadyDeducted += $ei->quantity;
                    }
                }
            }
            $effectiveAvailable = ($totalIn - $totalOut) + $alreadyDeducted;

            if ($effectiveAvailable < $req['qty']) {
                \Illuminate\Support\Facades\Log::error("Validation Failed:", [
                    'sku' => $req['sku'],
                    'req_qty' => $req['qty'],
                    'totalIn' => $totalIn,
                    'totalOut' => $totalOut,
                    'alreadyDeducted' => $alreadyDeducted,
                    'existing_skus' => collect($existingInvoiceItems)->pluck('sku')->toArray()
                ]);
                throw new \Exception("Insufficient stock for " . $req['display'] . " (Available: " . $effectiveAvailable . ", Required: " . $req['qty'] . ")");
            }
        }
    }

    private function createReturnStockEntry($item, $returnQty, $invNo)
    {
        if ($returnQty <= 0 || !$item->stock_entry_item_id) return;

        $originalStockItem = \App\Models\StockEntryItem::find($item->stock_entry_item_id);
        if (!$originalStockItem) return;

        $stockEntry = \App\Models\StockEntry::create([
            'stock_entry_no' => 'SR-' . time() . '-' . rand(10, 99),
            'stock_date' => date('Y-m-d'),
            'entry_type' => 'Finished Goods',
            'remarks' => 'Sales Return for Invoice ' . $invNo,
            'status' => 'Posted',
            'created_by' => auth()->id() ?? 1,
        ]);

        $newItemData = $originalStockItem->toArray();
        unset($newItemData['id']);
        unset($newItemData['created_at']);
        unset($newItemData['updated_at']);
        unset($newItemData['deleted_at']);
        
        $newItemData['stock_entry_id'] = $stockEntry->id;
        $newItemData['qty_in'] = $returnQty;
        $newItemData['qty_out'] = 0;

        \App\Models\StockEntryItem::create($newItemData);
    }

    private function adjustStock($salesInvoiceId, $revert = false, $oldQuantities = [])
    {
        $items = SalesInvoiceItem::where('sales_invoice_id', $salesInvoiceId)->get();
        foreach ($items as $item) {
            $stockEntryItemId = $item->stock_entry_item_id;

            if (!$stockEntryItemId && !$revert) {
                $matchedStockItem = null;
                if (!empty($item->sku)) {
                    $stockQuery = StockEntryItem::where('stock_type', 'finished_goods')
                        ->whereNull('deleted_at')
                        ->where(function ($q) use ($item) {
                            $q->where('sku', $item->sku)
                              ->orWhere('barcode', $item->sku);
                        });

                    if (!empty($item->size)) {
                        $stockQuery->where('size', $item->size);
                    }
                    if (!empty($item->art_no)) {
                        $stockQuery->where('art_no', $item->art_no);
                    }

                    if (!empty($item->sleeve_type)) {
                        $sleeveUpper = strtoupper(trim($item->sleeve_type));
                        if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                            $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
                        } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                            $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
                        } else {
                            $sleeveDbValues = [$item->sleeve_type];
                        }
                        $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
                    }

                    $matchedStockItem = $stockQuery->orderByRaw('(qty_in - qty_out) > 0 DESC')->orderBy('id', 'desc')->first();
                }

                if (!$matchedStockItem) {
                    $finishedItemCode = $item->item_name ?? $item->art_no;
                    if ($finishedItemCode) {
                        $stockQuery3 = StockEntryItem::where('stock_type', 'finished_goods')
                            ->whereNull('deleted_at')
                            ->where(function ($q) use ($finishedItemCode) {
                                $q->where('finished_item_code', $finishedItemCode)
                                  ->orWhere('art_no', $finishedItemCode);
                            });

                        if (!empty($item->color_id)) {
                            $stockQuery3->where(function ($q) use ($item) {
                                $q->where('color_id', $item->color_id)->orWhereNull('color_id');
                            });
                        }
                        if (!empty($item->size)) {
                            $stockQuery3->where('size', $item->size);
                        }
                        if (!empty($item->sleeve_type)) {
                            $sleeveUpper = strtoupper(trim($item->sleeve_type));
                            if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                                $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
                            } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                                $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
                            } else {
                                $sleeveDbValues = [$item->sleeve_type];
                            }
                            $stockQuery3->whereIn('sleeve_type', $sleeveDbValues);
                        }

                        $matchedStockItem = $stockQuery3->orderByRaw('(qty_in - qty_out) > 0 DESC')->orderBy('id', 'desc')->first();
                    }
                }

                if ($matchedStockItem) {
                    $stockEntryItemId = $matchedStockItem->id;
                    $item->update([
                        'stock_entry_item_id' => $stockEntryItemId,
                        'art_no' => $matchedStockItem->art_no ?? $item->art_no
                    ]);
                }
            }

            if ($stockEntryItemId) {
                if ($revert) {
                    $this->sequentialStockRevert($item, $item->quantity);
                } else {
                    if (!empty($oldQuantities) && isset($oldQuantities[$item->id])) {
                        $oldQty = $oldQuantities[$item->id]['quantity'];
                        $delta = $item->quantity - $oldQty;
                        
                        if ($delta > 0) {
                            $this->sequentialStockDeduct($item, $delta);
                        } elseif ($delta < 0) {
                            $returnQty = abs($delta);
                            $this->sequentialStockRevert($item, $returnQty);
                        }
                    } else {
                        $this->sequentialStockDeduct($item, $item->quantity);
                    }
                }
            }
        }
    }

    private function sequentialStockDeduct($item, $quantityToDeduct)
    {
        if ($quantityToDeduct <= 0) return;

        if ($item->stock_entry_item_id) {
            $rawStItem = StockEntryItem::find($item->stock_entry_item_id);
            if ($rawStItem && $rawStItem->stock_type === 'raw_material') {
                $rawStItem->increment('qty_out', $quantityToDeduct);
                $allocations = !empty($item->stock_allocations) && is_array($item->stock_allocations) ? $item->stock_allocations : [];
                $foundAlloc = false;
                foreach ($allocations as &$al) {
                    if (($al['stock_entry_item_id'] ?? null) == $rawStItem->id) {
                        $al['qty'] = (float)($al['qty'] ?? 0) + $quantityToDeduct;
                        $foundAlloc = true;
                        break;
                    }
                }
                unset($al);
                if (!$foundAlloc) {
                    $allocations[] = [
                        'stock_entry_item_id' => $rawStItem->id,
                        'qty' => $quantityToDeduct
                    ];
                }
                $item->update(['stock_allocations' => $allocations]);
                return;
            }
        }

        $stockQuery = StockEntryItem::where('stock_type', 'finished_goods')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($item) {
                if (!empty($item->sku)) {
                    $q->where('sku', $item->sku)->orWhere('barcode', $item->sku);
                } else {
                    $code = $item->item_name ?? $item->art_no;
                    $q->where('finished_item_code', $code)->orWhere('art_no', $code);
                }
            });

        if ($item->stock_entry_item_id) {
            $finishedItemCode = \App\Models\StockEntryItem::where('id', $item->stock_entry_item_id)->value('finished_item_code');
            if ($finishedItemCode) {
                $stockQuery->where('finished_item_code', $finishedItemCode);
            }
        }

        if (!empty($item->size)) {
            $stockQuery->where('size', $item->size);
        }

        if (!empty($item->art_no)) {
            $stockQuery->where('art_no', $item->art_no);
        }

        if (!empty($item->sleeve_type)) {
            $sleeveUpper = strtoupper(trim($item->sleeve_type));
            if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
            } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
            } else {
                $sleeveDbValues = [$item->sleeve_type];
            }
            $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
        }

        $stockQuery->orderByRaw('CASE WHEN id = ' . (int)($item->stock_entry_item_id ?: 0) . ' THEN 0 ELSE 1 END');
        $availableItems = $stockQuery->orderByRaw('(qty_in - qty_out) DESC')->orderBy('id', 'asc')->get();
        $remaining = (float)$quantityToDeduct;

        $firstDeductedId = null;
        $allocations = !empty($item->stock_allocations) && is_array($item->stock_allocations) ? $item->stock_allocations : [];

        foreach ($availableItems as $stItem) {
            if ($remaining <= 0) break;
            
            $balance = (float)$stItem->qty_in - (float)$stItem->qty_out;
            
            if ($balance > 0) {
                $deduct = min($balance, $remaining);
                $stItem->increment('qty_out', $deduct);
                
                $foundAlloc = false;
                foreach ($allocations as &$al) {
                    if (($al['stock_entry_item_id'] ?? null) == $stItem->id) {
                        $al['qty'] = (float)($al['qty'] ?? 0) + $deduct;
                        $foundAlloc = true;
                        break;
                    }
                }
                unset($al);

                if (!$foundAlloc) {
                    $allocations[] = [
                        'stock_entry_item_id' => $stItem->id,
                        'qty' => $deduct
                    ];
                }

                $remaining -= $deduct;
                if (!$firstDeductedId) {
                    $firstDeductedId = $stItem->id;
                }
            }
        }

        $updates = ['stock_allocations' => $allocations];
        if ($firstDeductedId && $item->stock_entry_item_id != $firstDeductedId) {
            $updates['stock_entry_item_id'] = $firstDeductedId;
        }
        $item->update($updates);

        if ($remaining > 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'stock' => 'Insufficient stock. You are trying to invoice ' . $quantityToDeduct . ' items, but there is not enough available in the warehouse.'
            ]);
        }
    }

    private function sequentialStockRevert($item, $quantityToRevert)
    {
        if ($quantityToRevert <= 0) return;

        $remaining = (float)$quantityToRevert;
        $allocations = !empty($item->stock_allocations) && is_array($item->stock_allocations) ? $item->stock_allocations : [];


        if (!empty($allocations)) {
            $allocations = array_reverse($allocations);
            foreach ($allocations as $idx => &$alloc) {
                if ($remaining <= 0) break;
                $allocId = $alloc['stock_entry_item_id'] ?? null;
                $allocQty = (float)($alloc['qty'] ?? 0);
                if ($allocId && $allocQty > 0) {
                    $stItem = StockEntryItem::find($allocId);
                    if ($stItem && $stItem->qty_out > 0) {
                        $revert = min((float)$stItem->qty_out, min($allocQty, $remaining));
                        $stItem->decrement('qty_out', $revert);
                        $alloc['qty'] = max(0, $allocQty - $revert);
                        $remaining -= $revert;
                    }
                }
            }
            unset($alloc);
            $allocations = array_values(array_filter(array_reverse($allocations), fn($a) => ($a['qty'] ?? 0) > 0));
            $item->update(['stock_allocations' => $allocations]);
        }

        if ($remaining > 0) {
            $stockQuery = StockEntryItem::where('stock_type', 'finished_goods')
                ->whereNull('deleted_at')
                ->where(function ($q) use ($item) {
                    if (!empty($item->sku)) {
                        $q->where('sku', $item->sku)->orWhere('barcode', $item->sku);
                    } else {
                        $code = $item->item_name ?? $item->art_no;
                        $q->where('finished_item_code', $code)->orWhere('art_no', $code);
                    }
                });

            if ($item->stock_entry_item_id) {
                $finishedItemCode = \App\Models\StockEntryItem::where('id', $item->stock_entry_item_id)->value('finished_item_code');
                if ($finishedItemCode) {
                    $stockQuery->where('finished_item_code', $finishedItemCode);
                }
            }

            if (!empty($item->size)) {
                $stockQuery->where('size', $item->size);
            }

            if (!empty($item->art_no)) {
                $stockQuery->where('art_no', $item->art_no);
            }

            if (!empty($item->sleeve_type)) {
                $sleeveUpper = strtoupper(trim($item->sleeve_type));
                if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                    $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
                } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                    $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
                } else {
                    $sleeveDbValues = [$item->sleeve_type];
                }
                $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
            }

            $stockQuery->orderByRaw('CASE WHEN id = ' . (int)($item->stock_entry_item_id ?: 0) . ' THEN 0 ELSE 1 END');
            $deductedItems = $stockQuery->where('qty_out', '>', 0)->orderBy('id', 'desc')->get();

            foreach ($deductedItems as $stItem) {
                if ($remaining <= 0) break;
                
                $out = (float)$stItem->qty_out;
                $revert = min($out, $remaining);
                $stItem->decrement('qty_out', $revert);
                $remaining -= $revert;
            }

            if ($remaining > 0 && $item->stock_entry_item_id) {
                $fallbackItem = StockEntryItem::find($item->stock_entry_item_id);
                if ($fallbackItem && $fallbackItem->qty_out > 0) {
                    $dec = min((float)$fallbackItem->qty_out, $remaining);
                    $fallbackItem->decrement('qty_out', $dec);
                }
            }
        }
    }

    public function report(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view sales-invoice-report')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = SalesInvoice::with(['customer', 'brand'])
                ->where(function($q) {
                    $q->whereNotNull('irn')
                      ->orWhere(function($q2) {
                          $q2->whereNull('irn')
                             ->whereHas('customer', function($q3) {
                                 $q3->where('name', 'like', '%CASH%');
                             });
                      });
                })->orderBy('id', 'desc');

            if ($request->customer_id) {
                $query->where('customer_id', $request->customer_id);
            }
            if ($request->brand_id) {
                $query->where('brand_id', $request->brand_id);
            }
            if ($request->inv_no) {
                $dbInvNo = SalesInvoice::formatDbInvNo($request->inv_no);
                $query->where(function($q) use ($request, $dbInvNo) {
                    $q->where('inv_no', $request->inv_no)->orWhere('inv_no', $dbInvNo);
                });
            }
            if ($request->inv_date_range) {
                $dates = explode(' to ', $request->inv_date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('inv_date', [$startDate, $endDate]);
                } elseif (count($dates) == 1) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $query->whereDate('inv_date', $startDate);
                }
            }
            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $numericSearch = str_replace([',', '₹', 'Rs.', ' '], '', $search);

                $query->where(function ($q) use ($search, $numericSearch) {
                    $q->where('inv_no', 'like', "%{$search}%");

                    if (preg_match('/^CD\/(\d+)/i', $search, $sm)) {
                        $sNum = (int)$sm[1];
                        if ($sNum > SalesInvoice::CDW_CD_OFFSET) {
                            $dbNum = ($sNum >= 316) ? ($sNum - SalesInvoice::CDW_CD_OFFSET + 1) : ($sNum - SalesInvoice::CDW_CD_OFFSET);
                            $cdwEq = 'CDW/' . $dbNum;
                            $q->orWhere('inv_no', 'like', "%{$cdwEq}%");
                        }
                    } elseif (is_numeric(trim($search))) {
                        $numVal = (int)trim($search);
                        if ($numVal > SalesInvoice::CDW_CD_OFFSET) {
                            $cdwNum = ($numVal >= 316) ? ($numVal - SalesInvoice::CDW_CD_OFFSET + 1) : ($numVal - SalesInvoice::CDW_CD_OFFSET);
                            $q->orWhere('inv_no', 'like', "%CDW/{$cdwNum}/%");
                        }
                    } elseif (stripos($search, 'CD') !== false && stripos($search, 'CDW') === false) {
                        $q->orWhere('inv_no', 'like', "%CDW/%");
                    }
                    $q->orWhereRaw("DATE_FORMAT(inv_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                      ->orWhere('sub_total', 'like', "%{$numericSearch}%")
                      ->orWhere('discount', 'like', "%{$numericSearch}%")
                      ->orWhere('grand_total', 'like', "%{$numericSearch}%")
                      ->orWhere('invoice_status', 'like', "%{$search}%")
                      ->orWhere('delivery_status', 'like', "%{$search}%")
                      ->orWhereRaw("(sub_total - COALESCE(discount, 0)) LIKE ?", ["%{$numericSearch}%"])
                      ->orWhereHas('customer', function($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('brand', function($q3) use ($search) {
                          $q3->where('brand_name', 'like', "%{$search}%");
                      });
                });
            }

            $filteredRecords = $query->count();

            if ($request->has('start') && $request->has('length') && $request->length != -1) {
                $query->skip($request->start)->take($request->length);
            }
            $invoices = $query->get();
            $data = [];
            $count = $request->has('start') ? $request->start + 1 : 1;

            foreach ($invoices as $inv) {
                $isCancelled = ($inv->invoice_status === 'Cancelled' || $inv->einvoice_status === 'cancelled');
                $invNoDisplay = $inv->inv_no;
                if ($isCancelled) {
                    $invNoDisplay .= '<br><span class="badge bg-danger" style="font-size:10px;"><i class="ri ri-close-circle-line me-1"></i> Cancelled</span>';
                }

                $data[] = [
                    'id' => $inv->id,
                    'DT_RowIndex' => $count++,
                    'inv_no' => $invNoDisplay,
                    'inv_date' => $inv->inv_date ? $inv->inv_date->format('d-m-Y') : '-',
                    'customer_name' => $inv->customer ? $inv->customer->name : '-',
                    'brand_name' => $inv->brand ? $inv->brand->brand_name : '-',
                    'total_qty' => $inv->items()->sum('quantity'),
                    'grand_total' => '₹' . number_format($inv->grand_total, 2),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords, 
                'data' => $data
            ]);
        }

        $customers = Customer::orderBy('name')->get();
        $brands = Brand::orderBy('brand_name')->get();
        return view('sales_invoice.report', compact('customers', 'brands'));
    }

    public function exportReport(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('export sales-invoice-report')) {
            return unauthorizedRedirect();
        }
        $filters = $request->only(['customer_id', 'brand_id', 'inv_date_range', 'inv_no']);
        return Excel::download(new SalesInvoiceReportExport($filters), 'Sales_Invoice_Report.xlsx');
    }

    public function exportItemsReport(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('export sales-invoice-report')) {
            return unauthorizedRedirect();
        }
        $filters = $request->only(['customer_id', 'brand_id', 'inv_date_range', 'inv_no']);
        return Excel::download(new SalesInvoiceItemsExport($filters), 'Sales_Invoice_Items.xlsx');
    }
    private function generateInvoiceNumber($brandId, $date, $ignoreId = null)
    {
        $brand = \App\Models\Brand::find($brandId);
        if (!$brand) {
            return '';
        }

        $brandCode = trim($brand->code);
        if (empty($brandCode)) {
            $brandCode = strtoupper(substr($brand->brand_name, 0, 2));
        }

        if ($brandCode === 'CDC') {
            $brandCode = 'CDS';
        } elseif ($brandCode === 'CBC') {
            $brandCode = 'CB';
        } elseif ($brandCode === 'CFC') {
            $brandCode = 'CF';
        }

        $year = (int)$date->format('Y');
        $month = (int)$date->format('m');
        $startYear = ($month >= 4) ? $year : ($year - 1);
        $endYear = $startYear + 1;
        $financialYear = substr($startYear, -2) . '-' . substr($endYear, -2);

        $maxRunningNo = 0;
        if ($financialYear === '26-27') {
            if ($brandCode === 'CW') {
                $maxRunningNo = 1157;
            } elseif ($brandCode === 'CDS') {
                $maxRunningNo = 735;
            } elseif ($brandCode === 'CB') {
                $maxRunningNo = 506;
            } elseif ($brandCode === 'CD') {
                $maxRunningNo = 179;
            } elseif ($brandCode === 'CF') {
                $maxRunningNo = 1493;
            }
        }
        $invoices = SalesInvoice::where('inv_no', 'like', "{$brandCode}/%/{$financialYear}")
            ->when($ignoreId, function ($q) use ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            })
            ->get(['inv_no']);

        foreach ($invoices as $inv) {
            $rawNo = $inv->raw_inv_no ?: $inv->getRawOriginal('inv_no');
            $parts = explode('/', $rawNo);
            if (count($parts) === 3) {
                $runningNo = (int)$parts[1];
                if ($runningNo > $maxRunningNo) {
                    $maxRunningNo = $runningNo;
                }
            }
        }

        $nextRunningNo = $maxRunningNo + 1;
        return "{$brandCode}/{$nextRunningNo}/{$financialYear}";
    }

    private function generateRawMaterialInvoiceNumber($date, $ignoreId = null)
    {
        $prefix = 'NFPL';

        $year = (int)$date->format('Y');
        $month = (int)$date->format('m');
        $startYear = ($month >= 4) ? $year : ($year - 1);
        $endYear = $startYear + 1;
        $financialYear = substr($startYear, -2) . '-' . substr($endYear, -2);

        $maxRunningNo = 0;

        $invoices = SalesInvoice::where('inv_no', 'like', "{$prefix}/%/{$financialYear}")
            ->when($ignoreId, function ($q) use ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            })
            ->get(['inv_no']);

        foreach ($invoices as $inv) {
            $rawNo = $inv->raw_inv_no ?: $inv->getRawOriginal('inv_no');
            $parts = explode('/', $rawNo);
            if (count($parts) === 3) {
                $runningNo = (int)$parts[1];
                if ($runningNo > $maxRunningNo) {
                    $maxRunningNo = $runningNo;
                }
            }
        }

        $nextRunningNo = $maxRunningNo + 1;
        return "{$prefix}/{$nextRunningNo}/{$financialYear}";
    }
}
