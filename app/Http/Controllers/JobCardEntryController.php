<?php

namespace App\Http\Controllers;

use App\Models\JobCardEntry;
use App\Models\JobCardCuttingSizeRatio;
use App\Models\JobCardImage;
use App\Models\JobCardFabricDetail;
use App\Models\StockEntryItem;
use App\Models\JobCardOperation;
use App\Models\PurchaseOrder;
use App\Models\Brand;
use App\Models\Season;
use App\Models\ProcessGroup;
use App\Models\SizeRatio;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Fit;
use App\Models\PattiType;
use App\Models\CollarType;
use App\Models\CuffType;
use App\Models\PocketType;
use App\Models\BottomCut;
use App\Models\BrandCategory;
use App\Models\GrnEntry;
use App\Models\GrnEntryItem;
use App\Models\PurchaseInvoice;
use App\Models\JobCardIssueItem;
use App\Models\ServiceProvider;
use App\Models\StoreType;
use App\Models\Item;
use App\Models\JobCardIssueStockDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class JobCardEntryController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $jobCards = JobCardEntry::with(['purchaseOrder', 'brand', 'season', 'processGroup'])->orderBy('id', 'desc')->get();
            
            $data = [];
            foreach ($jobCards as $index => $jc) {
                $status = ($jc->status == 'Production Hold')
                    ? '<span class="badge bg-label-danger">' . $jc->status . '</span>'
                    : '<span class="badge bg-label-warning">' . $jc->status . '</span>';
                $action = '<div class="d-inline-block text-nowrap">';
                if (auth()->id() == 1 || auth()->user()->can('edit job-card')) {
                    $action .= '<a href="' . url('job_card_entries/add/' . $jc->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                
                $action .= '<button class="btn dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base ri ri-more-2-fill"></i></button>';
                $action .= '<div class="dropdown-menu dropdown-menu-end m-0">';
                
                if (auth()->id() == 1 || auth()->user()->can('view job-card')) {
                    $action .= '<a href="' . url('job_card_entries/view/' . $jc->id) . '" class="dropdown-item"><i class="icon-base ri ri-eye-line me-2"></i>View</a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('issue-item job-card')) {
                    $action .= '<a href="' . url('job_card_entries/view-item/' . $jc->id) . '" class="dropdown-item"><i class="icon-base ri ri-list-check-2 me-2"></i>Issue Item</a>';
                }
                
                $action .= '</div></div>';
                
                $data[] = [
                    'DT_RowIndex' => $index + 1,
                    'job_card_no' => $jc->job_card_no,
                    'job_card_date' => date('d-m-Y', strtotime($jc->job_card_date)),
                    'po_number' => $jc->purchaseOrder->po_number ?? '-',
                    'brand' => $jc->brand->brand_name ?? '-',
                    'season' => $jc->season->name ?? '-',
                    'process_group' => $jc->processGroup->name ?? '-',
                    'total_qty' => $jc->grand_total_qty,
                    'status' => $status,
                    'action' => $action
                ];
            }
            return response()->json(['data' => $data]);
        }
        return view('job_card_entry/view');
    }

    public function add(Request $request, $id = null) {
        if ($request->isMethod('post')) {
            $rules = [
                'job_card_no' => 'required|string|max:255|unique:job_card_entries,job_card_no' . ($id ? ',' . $id : ''),
                'reference_no' => 'required|string|max:255',
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'service_provider_id' => 'required|exists:service_providers,id',
                'issue_store_id' => 'required|exists:store_types,id',
                'receipt_store_id' => 'required|exists:store_types,id',
                'issue_date' => 'required|date_format:d-m-Y',
                'delivery_date' => 'required|date_format:d-m-Y',
                'washing' => 'nullable|in:Yes,No',
                'width' => 'nullable|string|max:255',
                'mrp' => 'nullable|numeric',
                'total_qty_fs' => 'nullable|numeric',
                'total_qty_hs' => 'nullable|numeric',
                'season_id' => 'nullable|exists:seasons,id',
                'brand_id' => 'required|exists:brands,id',
                'receipt_store' => 'nullable|string',
                'process_group_id' => 'required|exists:process_groups,id',
                'status' => 'required|string',
                'remarks' => 'nullable|string',
                'fit_id' => 'nullable|exists:fits,id',
                'patti_type_id' => 'nullable|exists:patti_types,id',
                'collar_type_id' => 'nullable|exists:collar_types,id',
                'cuff_type_id' => 'nullable|exists:cuff_types,id',
                'pocket_type_id' => 'nullable|exists:pocket_types,id',
                'bottom_cut_id' => 'nullable|exists:bottom_cuts,id',
                'cutting_master_id' => 'nullable|exists:users,id',
                'cutting_date' => 'nullable|date_format:d-m-Y',
                'cutting_issue_unit' => 'nullable|string',
                'stages' => 'nullable|array|min:1',
                'size_ratio_id' => 'required|exists:size_ratios,id',
                'brand_category_id' => 'required|exists:brand_categories,id',
                'item_id' => 'required|exists:items,id',
            ];

            $messages = [
                '*.required' => 'This field is required',
                'job_card_no.unique' => 'This field already exists.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $data = [
                    'job_card_no' => $request->job_card_no,
                    'reference_no' => $request->reference_no,
                    'purchase_order_id' => $request->purchase_order_id,
                    'service_provider_id' => $request->service_provider_id,
                    'issue_store_id' => $request->issue_store_id,
                    'receipt_store_id' => $request->receipt_store_id,
                    'job_card_date' => date('Y-m-d', strtotime($request->issue_date)),
                    'delivery_date' => $request->delivery_date ? date('Y-m-d', strtotime($request->delivery_date)) : null,
                    'washing' => $request->washing,
                    'width' => $request->width,
                    'mrp' => $request->mrp,
                    'fs_qty' => $request->fs,
                    'hs_qty' => $request->hs,
                    'remarks' => $request->remarks,
                    'status' => $request->status ?? 'Draft',
                    'fit_id' => $request->fit_id,
                    'patti_type_id' => $request->patti_type_id,
                    'collar_type_id' => $request->collar_type_id,
                    'cuff_type_id' => $request->cuff_type_id,
                    'pocket_type_id' => $request->pocket_type_id,
                    'bottom_cut_id' => $request->bottom_cut_id,
                    'cutting_master_id' => $request->cutting_master_id,
                    'cutting_date' => $request->cutting_date ? date('Y-m-d', strtotime($request->cutting_date)) : null,
                    'cutting_issue_unit' => $request->cutting_issue_unit,
                    'price_fs' => $request->price_fs,
                    'price_hs' => $request->price_hs,
                    'total_qty_fs' => $request->total_qty_fs ?? 0,
                    'total_qty_hs' => $request->total_qty_hs ?? 0,
                    'grand_total_qty' => ($request->total_qty_fs ?? 0) + ($request->total_qty_hs ?? 0),
                    'season_id' => $request->season_id,
                    'brand_id' => $request->brand_id,
                    'process_group_id' => $request->process_group_id,
                    'size_ratio_id' => $request->size_ratio_id,
                    'ex_1_label' => $request->ex_1_label,
                    'ex_2_label' => $request->ex_2_label,
                    'brand_category_id' => $request->brand_category_id,
                    'item_id' => $request->item_id,
                ];

                if ($request->season) {
                    $season = Season::where('name', $request->season)->first();
                    $data['season_id'] = $season ? $season->id : null;
                }
                if ($request->brand) {
                    $brand = Brand::where('brand_name', $request->brand)->first();
                    $data['brand_id'] = $brand ? $brand->id : null;
                }
                if ($request->process_group) {
                    $pgCode = explode(' - ', $request->process_group)[0];
                    $pg = ProcessGroup::where('name', 'like', $pgCode . '%')->first();
                    $data['process_group_id'] = $pg ? $pg->id : null;
                }

                if ($id) {
                    $jobCard = JobCardEntry::with('issueItems.fabricDetail')->findOrFail($id);
                    $oldData = $jobCard->toArray();
                    $issueBackup = [];
                    foreach ($jobCard->issueItems as $issue) {
                        $artNo = $issue->fabricDetail->art_no ?? null;
                        if ($artNo) {
                            $issueBackup[$artNo] = [
                                'qty_issue' => $issue->qty_issue,
                                'qty_adjusted' => $issue->qty_adjusted,
                                'qty_wastage' => $issue->qty_wastage,
                                'qty_used' => $issue->qty_used,
                                'bit' => $issue->bit,
                                'balance' => $issue->balance,
                                'average' => $issue->average,
                                'produced_qty' => $issue->produced_qty,
                            ];
                        }
                    }

                    $jobCard->update($data);
                    $jobCard->cuttingSizeRatios()->forceDelete();
                    $jobCard->fabricDetails()->forceDelete();
                    
                    $newData = $jobCard->fresh()->toArray();
                    addLog('update', 'Job Card Entry', 'job_card_entries', $id, $oldData, $newData);
                } else {
                    $data['created_by'] = auth()->id();
                    $jobCard = JobCardEntry::create($data);
                    $newData = $jobCard->toArray();
                    addLog('create', 'Job Card Entry', 'job_card_entries', $jobCard->id, null, $newData);
                    $issueBackup = [];
                }

                if ($request->matrix_items) {
                    foreach ($request->matrix_items as $item) {
                        $jobCard->cuttingSizeRatios()->create([
                            'size' => $item['size'],
                            'ratio' => $item['ratio'] ?? 0,
                            'qty_fs' => $item['qty_fs'] ?? 0,
                            'qty_hs' => $item['qty_hs'] ?? 0,
                            'total_qty' => ($item['qty_fs'] ?? 0) + ($item['qty_hs'] ?? 0),
                        ]);
                    }
                }

                if ($request->article_matrix) {
                    if (!$id) {
                        foreach ($request->article_matrix as $index => $matrix) {
                            $artNo = $matrix['art_no'] ?? null;
                            if (!$artNo) continue;

                            $fabric = collect($request->fabrics)->where('art_no', $artNo)->first() ?? ($request->fabrics[$index] ?? null);
                            $issuedQty = floatval($fabric['mtr'] ?? 0);
                            
                            $required = 0;
                            foreach ($matrix as $key => $val) {
                                if (str_starts_with($key, 'fs_') || str_starts_with($key, 'hs_')) {
                                    $required += floatval($val);
                                }
                            }

                            $required = round($required, 3);
                            $issuedQty = round($issuedQty, 3);
                            
                            if ($required > $issuedQty) {
                                return redirect()->back()->withErrors(['error' => "Stock Error for Art {$artNo}: Required {$required} exceeds Issued {$issuedQty}"])->withInput();
                            }
                        }
                    }

                    foreach ($request->article_matrix as $index => $matrix) {
                        $fabric = collect($request->fabrics)->where('art_no', $matrix['art_no'])->first() ?? ($request->fabrics[$index] ?? null);
                        
                        $fabricDetail = $jobCard->fabricDetails()->create([
                            'art_no'  => $matrix['art_no'],
                            'width'   => $fabric['width'] ?? null,
                            'mtr'     => $fabric['mtr'] ?? null,
                            'in_out'  => $fabric['in_out'] ?? null,
                            'n_patti' => $fabric['n_patti'] ?? null,
                            'fs_qty'  => $fabric['fs_qty'] ?? null,
                            'hs_qty'  => $fabric['hs_qty'] ?? null,
                            'row_total' => 0 
                        ]);

                        if (isset($fabric['consumptions']) && is_array($fabric['consumptions'])) {
                            foreach ($fabric['consumptions'] as $sz => $cons) {
                                $fabricDetail->consumptions()->create([
                                    'size' => $sz,
                                    'fs_cons' => $cons['fs_cons'] ?? null,
                                    'hs_cons' => $cons['hs_cons'] ?? null,
                                ]);
                            }
                        }

                        $rowTotal = 0;
                        $sizeQtys = [];
                        foreach ($matrix as $key => $val) {
                            if (str_starts_with($key, 'fs_')) {
                                $size = substr($key, 3);
                                $sizeQtys[$size]['fs'] = $val;
                            } elseif (str_starts_with($key, 'hs_')) {
                                $size = substr($key, 3);
                                $sizeQtys[$size]['hs'] = $val;
                            }
                        }

                        foreach ($sizeQtys as $size => $qtys) {
                            $qFs = floatval($qtys['fs'] ?? 0);
                            $qHs = floatval($qtys['hs'] ?? 0);
                            if ($qFs > 0 || $qHs > 0) {
                                $fabricDetail->quantities()->create([
                                    'size' => $size,
                                    'qty_fs' => $qFs,
                                    'qty_hs' => $qHs,
                                    'total_qty' => $qFs + $qHs
                                ]);
                                $rowTotal += ($qFs + $qHs);
                            }
                        }
                        $fabricDetail->update(['row_total' => $rowTotal]);

                        if (isset($issueBackup[$matrix['art_no']])) {
                            JobCardIssueItem::create(array_merge($issueBackup[$matrix['art_no']], [
                                'job_card_entry_id' => $jobCard->id,
                                'job_card_article_matrix_id' => $fabricDetail->id,
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
                            ]));
                        }
                    }
                }


                if ($request->has('fabric_images')) {
                    $uploadPath = public_path('uploads/job_card');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    foreach ($request->fabric_images as $index => $files) {
                        if (is_array($files) && count(array_filter($files)) > 0) {
                            $currentArtNo = $request->fabrics[$index]['art_no'] ?? null;
                            
                            if ($id && $currentArtNo) {
                                $oldArtImages = $jobCard->images()->where('art_no', $currentArtNo)->get();
                                foreach ($oldArtImages as $oldImage) {
                                    $oldImagePath = public_path($oldImage->image);
                                    if (file_exists($oldImagePath)) {
                                        unlink($oldImagePath);
                                    }
                                    $oldImage->delete();
                                }
                            }

                            foreach ($files as $file) {
                                if ($file && $file->isValid()) {
                                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                                    $file->move($uploadPath, $fileName);
                                    $jobCard->images()->create([
                                        'image' => 'uploads/job_card/' . $fileName,
                                        'art_no' => $currentArtNo
                                    ]);
                                }
                            }
                        }
                    }
                }

                $totalFabricMtr = $jobCard->fabricDetails()->sum('mtr');
                $grandTotalQty = $jobCard->grand_total_qty ?? 0;
                $overallAverage = ($grandTotalQty > 0) ? ($totalFabricMtr / $grandTotalQty) : 0;
                $jobCard->update(['average' => $overallAverage]);

                if ($request->has('fs_meter')) {
                    $jobCard->sleeveMeters()->updateOrCreate(
                        ['sleeve_type' => 'Full Sleeve'],
                        ['meter' => $request->fs_meter]
                    );
                }
                if ($request->has('hs_meter')) {
                    $jobCard->sleeveMeters()->updateOrCreate(
                        ['sleeve_type' => 'Half Sleeve'],
                        ['meter' => $request->hs_meter]
                    );
                }

                DB::commit();
                return redirect('job_card_entries')->with('success', 'Job Card saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('danger', 'Error: ' . $e->getMessage());
            }
        }

        $jobCard = $id ? JobCardEntry::with(['cuttingSizeRatios', 'images', 'sizeRatio', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'issueItems', 'sleeveMeters'])->findOrFail($id) : null;
        $brandCategories = BrandCategory::where('status', 'Active')->get();
        
        $allPurchaseOrders = PurchaseOrder::with(['items'])->orderBy('id', 'desc')->get();
        
        $purchaseOrders = $allPurchaseOrders->filter(function($po) use ($jobCard) {
            if ($jobCard && $po->id == $jobCard->purchase_order_id) {
                return true;
            }
            if (\App\Models\JobCardEntry::where('purchase_order_id', $po->id)->exists()) {
                return false;
            }
            $hasStock = \App\Models\StockEntry::whereHas('grnEntry.purchaseInvoice', function($q) use ($po) {
                $q->where('purchase_order_id', $po->id);
            })->exists();

            if (!$hasStock) {
                return false;
            }

            return true;
        });

        $brands = Brand::where('status', 'Active')->orderBy('id','desc')->get();
        $seasons = Season::where('status', 'Active')->orderBy('id','desc')->get();
        $processGroups = ProcessGroup::where('status', 'Active')->orderBy('id','desc')->get();
        $sizeRatios = SizeRatio::where('status', 'Active')->orderBy('id','desc')->get();
        $employees = User::where('status', 'Active')->where('id', '!=', 1)->orderBy('id','desc')->get();
        
        $fits = Fit::active()->orderBy('id','desc')->get();
        $pattiTypes = PattiType::active()->orderBy('id','desc')->get();
        $collarTypes = CollarType::active()->orderBy('id','desc')->get();
        $cuffTypes = CuffType::active()->orderBy('id','desc')->get();
        $pocketTypes = PocketType::active()->orderBy('id','desc')->get();
        $bottomCuts = BottomCut::active()->orderBy('id','desc')->get();
        $cuttingMasters = User::active()->where('id', '!=', 1)->orderBy('id','desc')->get();
        
        $plants = ServiceProvider::where('is_plant', 1)->where('status', 'Active')->get();
        $storeTypes = StoreType::where('status', 'Active')->orderBy('id','desc')->get();

        return view('job_card_entry/add', compact(
            'jobCard', 'purchaseOrders', 'brands', 'seasons', 
            'processGroups', 'sizeRatios', 'employees',
            'fits', 'pattiTypes', 'collarTypes', 'cuffTypes', 'pocketTypes', 
            'bottomCuts', 'cuttingMasters', 'plants', 'storeTypes',
            'brandCategories'
        ));
    }

    public function view_details($id) {
        $jobCard = JobCardEntry::with(['purchaseOrder', 'brand', 'season', 'processGroup', 'cuttingSizeRatios', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'images', 'sleeveMeters', 'fit', 'pattiType', 'collarType', 'cuffType', 'pocketType', 'bottomCut'])->findOrFail($id);
        return view('job_card_entry/view_details', compact('jobCard'));
    }

    public function view_jc_item($id) {
        $jobCard = JobCardEntry::with(['brand', 'item', 'issueStore', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'purchaseOrder.items.rawMaterial.uom', 'purchaseOrder.supplier', 'purchaseOrder.items.uom', 'purchaseOrder.items.brand', 'purchaseOrder.items.style', 'issueItems', 'sleeveMeters'])->findOrFail($id);
        $issueItemMap = $jobCard->issueItems->keyBy('job_card_article_matrix_id');
        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
        $grnItems = GrnEntryItem::whereIn('grn_entry_id', function($query) use ($invoiceIds) {
            $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
        })->with(['purchaseInvoiceItem.rawMaterial.uom', 'purchaseInvoiceItem.uom', 'fabricType', 'storeLocation'])->get();

        $artMaterialMap = [];
        $artLocationMap = [];
        $artUomMap = [];
        $artPriceMap = [];
        foreach ($grnItems as $item) {
            $name = $item->purchaseInvoiceItem->rawMaterial->name ?? ($item->fabricType->name ?? null);
            if ($name && !isset($artMaterialMap[$item->art_no])) {
                $artMaterialMap[$item->art_no] = $name;
            }
            if ($item->storeLocation && !isset($artLocationMap[$item->art_no])) {
                $artLocationMap[$item->art_no] = $item->storeLocation->store_location;
            }

            $rate = $item->purchaseInvoiceItem->rate ?? ($item->rate ?? 0);
            if ($rate > 0 && !isset($artPriceMap[$item->art_no])) {
                $artPriceMap[$item->art_no] = $rate;
            }

            $uom = $item->purchaseInvoiceItem->uom->uom_code ?? ($item->purchaseInvoiceItem->rawMaterial->uom->uom_code ?? null);
            if (!$uom) {
                $rm = \App\Models\RawMaterial::where('code', $item->art_no)->first();
                $uom = $rm->uom->uom_code ?? null;
            }
            if ($uom && !isset($artUomMap[$item->art_no])) {
                $artUomMap[$item->art_no] = $uom;
            }
        }

        return view('job_card_entry/view_jc_item', compact('jobCard', 'artMaterialMap', 'artLocationMap', 'artUomMap', 'artPriceMap', 'issueItemMap'));
    } 

    public function issue_items(Request $request, $id) {
        $jobCard = JobCardEntry::with(['purchaseOrder.items'])->findOrFail($id);
        $oldData = $jobCard->load('issueItems')->toArray();
        
        if ($request->isMethod('post')) {
            DB::beginTransaction();
            try {
                if ($request->items) {
                    $existingItems = JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->with('stockDetails')->get();
                    $updatedItems = [];
                    $totalIssuedFabric = 0;
                    $allRevertedStockIds = [];

                    foreach ($request->items as $matrixId => $itemData) {
                        $existingItem = $existingItems->first(function($item) use ($matrixId) {
                            return $item->job_card_article_matrix_id == $matrixId;
                        });

                        if ($existingItem) {
                            if ($existingItem->stockDetails->isNotEmpty()) {
                                foreach ($existingItem->stockDetails as $detail) {
                                    $stockItem = StockEntryItem::withTrashed()->find($detail->stock_entry_item_id);
                                    if ($stockItem) {
                                        $stockItem->qty_out = max(0, (float)($stockItem->qty_out ?? 0) - $detail->qty); 
                                        $stockItem->save();
                                        $allRevertedStockIds[] = $stockItem->id;
                                    }
                                    $detail->delete(); 
                                }
                            } elseif ($existingItem->stock_entry_item_id) {
                                $stockItem = StockEntryItem::withTrashed()->find($existingItem->stock_entry_item_id);
                                if ($stockItem) {
                                    $stockItem->qty_out = max(0, (float)($stockItem->qty_out ?? 0) - $existingItem->qty_used);
                                    $stockItem->save();
                                    $allRevertedStockIds[] = $stockItem->id;
                                }
                            }
                        }
                    }
                    $allRevertedStockIds = array_unique($allRevertedStockIds);
                    
                    foreach ($request->items as $matrixId => $itemData) {
                        $qtyIssue = floatval($itemData['qty_issue'] ?? 0);
                        $qtyUsed = floatval($itemData['qty_used'] ?? 0);
                        $totalIssuedFabric += $qtyIssue;
                        $matrix = JobCardFabricDetail::find($matrixId);
                        $artNo = $matrix->art_no ?? '';
                        
                        $existingItem = $existingItems->first(function($item) use ($matrixId) {
                            return $item->job_card_article_matrix_id == $matrixId;
                        });

                        $unitPrice = 0; 
                        $tempStockUsage = []; 
                        
                        if ($artNo && $qtyIssue > 0) {
                            $query = StockEntryItem::whereRaw('(qty_in - COALESCE(qty_out, 0)) > 0')->orderBy('id', 'asc');
                            $poFiltered = false;
                            $debugPath = 'none';
                            
                            if ($jobCard->purchaseOrder) {
                                $poItems = $jobCard->purchaseOrder->items;
                                $poItem = $poItems->where('art_no', $artNo)->first();
                                if ($poItem && $poItem->raw_material_id) {
                                    $query->where('raw_material_id', $poItem->raw_material_id);
                                    $poFiltered = true;
                                } 
                            } 

                            if (!$poFiltered) {
                                $artNo = trim($artNo);
                                $grnDirectQuery = clone $query;
                                $grnDirectQuery->whereHas('grnEntryItem', function($q) use ($artNo) {
                                    $q->where('art_no', $artNo);
                                });

                                $grnDirectCount = $grnDirectQuery->count();
                                
                                if ($grnDirectCount > 0) {
                                    $query = $grnDirectQuery;
                                } else {
                                    $rawMaterialByName = \App\Models\RawMaterial::where('name', 'LIKE', "%$artNo%")->first();
                                    $rawMaterialByCode = \App\Models\RawMaterial::where('code', 'LIKE', "%$artNo%")->first();
                                    $targetRmId = $rawMaterialByName->id ?? ($rawMaterialByCode->id ?? null);
                                    
                                    if ($targetRmId) {
                                        $rmQuery = clone $query;
                                        $rmQuery->where('raw_material_id', $targetRmId);                                            
                                        if ($rmQuery->count() > 0) {
                                            $query = $rmQuery;
                                        } else {
                                            $query->whereHas('grnEntryItem.purchaseInvoiceItem', function($q) use ($targetRmId) {
                                                $q->where('raw_material_id', $targetRmId);
                                            });
                                        }
                                    } else {
                                        $query->whereHas('grnEntryItem.purchaseInvoiceItem.purchaseOrderItem', function($q) use ($artNo) {
                                            $q->where('art_no', $artNo);
                                        });
                                    }
                                }
                            }
                            
                            $stockCandidates = $query->get();
                            
                            if (!empty($allRevertedStockIds)) {
                                $revertedItems = StockEntryItem::whereIn('id', $allRevertedStockIds)->get();
                                $stockCandidates = $revertedItems->merge($stockCandidates)->unique('id');
                            }

                            $remainingToDeduct = $qtyUsed;
                            $weightedCost = 0;
                            
                            $totalAvailable = $stockCandidates->sum(function($item) {
                                return max(0, $item->qty_in - (float)($item->qty_out ?? 0));
                            });
                            
                            foreach ($stockCandidates as $stockItem) {
                                if ($remainingToDeduct <= 0) break;

                                $available = $stockItem->qty_in - (float)($stockItem->qty_out ?? 0);
                                if ($available <= 0) continue; 

                                $take = min($available, $remainingToDeduct);
                                
                                $stockItem->qty_out = (float)($stockItem->qty_out ?? 0) + $take;
                                $stockItem->save();
                                
                                $tempStockUsage[] = ['stock_entry_item_id' => $stockItem->id, 'qty' => $take];
                                $weightedCost += ($take * $stockItem->price);
                                $remainingToDeduct = round($remainingToDeduct - $take, 4);
                            }
                            if ($remainingToDeduct > 0.001) { 
                                throw new \Exception("Insufficient stock for Art No: $artNo. Unable to deduct used quantity. Shortage: " . $remainingToDeduct);
                            }

                            $unitPrice = ($qtyUsed > 0) ? ($weightedCost / $qtyUsed) : 0;
                        }
                        
                        if (isset($itemData['is_manual_price']) && $itemData['is_manual_price'] == 1 && isset($itemData['unit_price'])) {
                            $unitPrice = floatval($itemData['unit_price']);
                        } elseif (isset($itemData['unit_price']) && $unitPrice == 0 && $qtyIssue == 0) {
                        }

                        $qtyUsed = floatval($itemData['qty_used'] ?? 0);
                        $producedQty = floatval($itemData['produced_qty'] ?? 0);
                        $totalCost = $qtyUsed * $unitPrice;
                        $costPerPc = ($producedQty > 0) ? ($totalCost / $producedQty) : 0;
                        
                        $data = [
                            'job_card_entry_id' => $jobCard->id,
                            'job_card_article_matrix_id' => $matrixId,
                            'qty_issue' => $qtyIssue,
                            'qty_adjusted' => $itemData['qty_adjusted'] ?? 0,
                            'qty_wastage' => $itemData['qty_wastage'] ?? 0,
                            'qty_used' => $qtyUsed,
                            'bit' => $itemData['bit'] ?? 0,
                            'balance' => $itemData['balance'] ?? 0,
                            'average' => $itemData['average'] ?? 0,
                            'produced_qty' => $producedQty,
                            'unit_price' => $unitPrice,
                            'total_cost' => $totalCost,
                            'cost_per_pc' => $costPerPc,
                            'stock_entry_item_id' => $tempStockUsage[0]['stock_entry_item_id'] ?? null,
                            'updated_by' => auth()->id(),
                        ];

                        if ($existingItem) {
                            $issueItem = $existingItem;
                            $issueItem->update($data);
                        } else {
                            $data['created_by'] = auth()->id();
                            $issueItem = JobCardIssueItem::create($data);
                        }
                        
                        foreach ($tempStockUsage as $usage) {
                            JobCardIssueStockDetail::create([
                                'job_card_issue_item_id' => $issueItem->id,
                                'stock_entry_item_id' => $usage['stock_entry_item_id'],
                                'qty' => $usage['qty']
                            ]);
                        }

                        $updatedItems[$matrixId] = [
                            'unit_price' => number_format($unitPrice, 2, '.', ''),
                            'total_cost' => number_format($totalCost, 2, '.', ''),
                            'cost_per_pc' => number_format($costPerPc, 2, '.', ''),
                        ];
                    }
                    $totalFabricMtr = $jobCard->fabricDetails()->sum('mtr');
                    $grandTotalQty = $jobCard->grand_total_qty ?? 0;
                    $overallAverage = ($grandTotalQty > 0) ? ($totalFabricMtr / $grandTotalQty) : 0;
                    
                    $totalPrice = 0;
                    foreach ($updatedItems as $matrixId => $itemData) {
                        if (isset($itemData['cost_per_pc'])) {
                            $totalPrice += floatval($itemData['cost_per_pc']);
                        }
                    }
                    
                    $jobCard->update([
                        'average' => $overallAverage,
                        'price_fs' => $totalPrice,
                        'price_hs' => 0
                    ]);
                }
                $newData = $jobCard->fresh(['issueItems'])->toArray();
                addLog('update', 'Job Card Issue Items', 'job_card_entries', $id, $oldData, $newData);
                DB::commit();
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true, 
                        'message' => 'Item updated and stock deducted successfully',
                        'updated_items' => $updatedItems ?? [],
                        'total_price' => $totalPrice ?? $jobCard->price_fs
                    ]);
                }
                return redirect('job_card_entries')->with('success', 'Issue Items saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
                }
                return back()->with('danger', 'Error: ' . $e->getMessage());
            }
        }
    }

    public function getSizeRatioDetails($id) {
        $sizeRatio = SizeRatio::find($id);
        return response()->json($sizeRatio);
    }

    public function getPoDetails($id) {
        $po = PurchaseOrder::with(['items'])->find($id);
        if (!$po) return response()->json(['error' => 'PO not found'], 404);
        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $po->id)->pluck('id');
        $grns = GrnEntry::whereIn('purchase_invoice_id', $invoiceIds)->get();
        $grnIds = $grns->pluck('id');
        $artData = GrnEntryItem::whereIn('grn_entry_id', $grnIds)->with(['purchaseInvoiceItem.uom', 'purchaseInvoiceItem.rawMaterial.uom', 'grnEntry', 'stockEntryItems'])
            ->get()->groupBy('art_no')
            ->map(function ($items, $artNo) {
                $firstItem = $items->first();
                $uom = $firstItem->purchaseInvoiceItem->uom->uom_code 
                    ?? ($firstItem->purchaseInvoiceItem->rawMaterial->uom->uom_code ?? null);
                
                $rawMaterial = $firstItem->purchaseInvoiceItem->rawMaterial ?? null;
                $catId = $rawMaterial ? $rawMaterial->store_category_id : 1;

                return [
                    'art_no' => $artNo,
                    'art_name' => $firstItem->purchaseInvoiceItem->rawMaterial->name ?? ($firstItem->fabricType->name ?? null),
                    'mtr' => $items->filter(function($item) {
                        return $item->stockEntryItems->isNotEmpty();
                    })->sum('qty_accepted'),
                    'uom_code' => $uom,
                    'store_category_id' => $catId,
                    'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                    'grn_no' => $firstItem->grnEntry->grn_number ?? null
                ];
            })->values();

        if ($artData->isEmpty()) {
            $po->load(['items.uom', 'items.rawMaterial.uom']); 
            $artData = $po->items->map(function($item) {
                $uom = null;
                if ($item->uom) {
                    $uom = $item->uom->uom_code;
                } elseif ($item->rawMaterial && $item->rawMaterial->uom) {
                    $uom = $item->rawMaterial->uom->uom_code;
                }
                
                $catId = ($item->rawMaterial) ? $item->rawMaterial->store_category_id : 1;
                
                $fallbackStock = 0;
                if ($item->rawMaterial) {
                    $fallbackStock = \App\Models\StockEntryItem::where('raw_material_id', $item->raw_material_id)
                        ->sum(\Illuminate\Support\Facades\DB::raw('qty_in - qty_out'));
                }

                return [
                    'art_no' => $item->art_no,
                    'art_name' => $item->rawMaterial->name ?? ($item->fabricType->name ?? null),
                    'mtr' => $fallbackStock > 0 ? $fallbackStock : $item->quantity, 
                    'uom_code' => $uom,
                    'store_category_id' => $catId,
                    'raw_material_id' => $item->raw_material_id,
                    'grn_no' => $item->grn_no
                ];
            })->unique('art_no')->values();
        }

        return response()->json([
            'po' => $po,
            'art_data' => $artData, 
            'art_numbers' => $artData->pluck('art_no'), 
            'linked_grns' => $grnIds
        ]);
    }

    public function checkStock($id) {
        $po = PurchaseOrder::with(['items.rawMaterial'])->find($id);
        if (!$po) return response()->json(['error' => 'PO not found'], 404);

        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $po->id)->pluck('id');
        $grnIds = GrnEntry::whereIn('purchase_invoice_id', $invoiceIds)->pluck('id');

        $artData = GrnEntryItem::whereIn('grn_entry_id', $grnIds)
            ->with(['purchaseInvoiceItem.rawMaterial', 'grnEntry', 'stockEntryItems'])
            ->get()
            ->groupBy('art_no')
            ->map(function ($items, $artNo) {
                $firstItem = $items->first();
                $rawMaterial = $firstItem->purchaseInvoiceItem->rawMaterial ?? null;
                $catId = $rawMaterial ? $rawMaterial->store_category_id : 1;
                
                return [
                    'art_no' => $artNo,
                    'art_name' => $rawMaterial->name ?? ($firstItem->fabricType->name ?? null),
                    'mtr' => (float)$items->filter(function($item) {
                        return $item->stockEntryItems->isNotEmpty();
                    })->sum('qty_accepted'),
                    'store_category_id' => $catId,
                    'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                    'grn_no' => $firstItem->grnEntry->grn_number ?? null,
                    'uom_code' => $rawMaterial->uom->uom_code ?? null
                ];
            })->values();

        if ($artData->isEmpty()) {
            $artData = $po->items->map(function($item) {
                $acceptedQty = \App\Models\GrnEntryItem::where('art_no', $item->art_no)
                    ->whereHas('grnEntry.purchaseInvoice', function($q) use ($item) {
                        $q->where('purchase_order_id', $item->purchase_order_id);
                    })
                    ->whereHas('stockEntryItems')
                    ->sum('qty_accepted');
                
                return [
                    'art_no' => $item->art_no,
                    'art_name' => $item->rawMaterial->name ?? ($item->fabricType->name ?? null),
                    'mtr' => (float)$acceptedQty,
                    'store_category_id' => $item->rawMaterial ? $item->rawMaterial->store_category_id : 1,
                    'raw_material_id' => $item->raw_material_id,
                    'grn_no' => $item->grn_no,
                    'uom_code' => $item->rawMaterial->uom->uom_code ?? null
                ];
            })->unique('art_no')->values();
        }

        return response()->json(['art_data' => $artData]);
    }

    public function deleteImage($id) {
        $image = JobCardImage::findOrFail($id);
        $filePath = public_path($image->image);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $image->delete();
        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }
    
    public function fabricConsumptionPdf($id)
    {
        $jobCard = JobCardEntry::with([
            'brand', 
            'fabricDetails.quantities', 
            'purchaseOrder.items.rawMaterial.uom', 
            'purchaseOrder.supplier', 
            'purchaseOrder.items.uom', 
            'purchaseOrder.items.brand', 
            'issueItems.fabricDetail'
        ])->findOrFail($id);

        $artTotalMap = [];
        foreach ($jobCard->fabricDetails as $detail) {
            $total = $detail->quantities->sum('total_qty');
            
            if (!isset($artTotalMap[$detail->art_no])) {
                $artTotalMap[$detail->art_no] = 0;
            }
            $artTotalMap[$detail->art_no] += $total;
        }

        $issueItems = $jobCard->issueItems->groupBy(function($item) {
            return $item->fabricDetail->art_no ?? 'N/A';
        })->map(function($items, $artNo) use ($artTotalMap) {
            return (object)[
                'art_no' => $artNo,
                'produced_qty' => $artTotalMap[$artNo] ?? $items->max('produced_qty'),
                'qty_issue' => $items->sum('qty_issue'),
                'qty_wastage' => $items->sum('qty_wastage'),
                'qty_used' => $items->sum('qty_used'),
                'qty_adjusted' => $items->sum('qty_adjusted'),
                'balance' => $items->sum('balance'),
                'unit_price' => $items->average('unit_price'), 
                'total_cost' => $items->sum('total_cost'),
            ];
        })->values();
        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
        $grnItems = GrnEntryItem::whereIn('grn_entry_id', function($query) use ($invoiceIds) {
            $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
        })->with(['purchaseInvoiceItem.rawMaterial.uom', 'purchaseInvoiceItem.uom', 'fabricType', 'storeLocation'])->get();

        $artMaterialMap = [];
        $artUomMap = [];
        $artLocationMap = [];

        foreach ($grnItems as $item) {
            $name = $item->purchaseInvoiceItem->rawMaterial->name ?? ($item->fabricType->name ?? null);
            if ($name && !isset($artMaterialMap[$item->art_no])) {
                $artMaterialMap[$item->art_no] = $name;
            }
            if ($item->storeLocation && !isset($artLocationMap[$item->art_no])) {
                $artLocationMap[$item->art_no] = $item->storeLocation->store_location;
            }
            $uom = $item->purchaseInvoiceItem->uom->uom_code ?? ($item->purchaseInvoiceItem->rawMaterial->uom->uom_code ?? null);
            if ($uom && !isset($artUomMap[$item->art_no])) {
                $artUomMap[$item->art_no] = $uom;
            }
        }

        $pdf = Pdf::loadView('job_card_entry.fabric_consumption_pdf', compact('jobCard', 'issueItems', 'artMaterialMap', 'artUomMap', 'artLocationMap'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'Fabric_Consumption_' . str_replace(['/', '\\'], '_', $jobCard->job_card_no) . '.pdf';
        return $pdf->stream($filename);
    }
    public function workOrderPdf($id)
    {
        $jobCard = JobCardEntry::with([
            'brand',
            'item',
            'serviceProvider',
            'fabricDetails.quantities',
            'purchaseOrder.items.brand',
            'purchaseOrder.items.uom',
            'purchaseOrder.items.style',
            'season',
            'processGroup'
        ])->findOrFail($id);

        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
        $grnItems = GrnEntryItem::whereIn('grn_entry_id', function($query) use ($invoiceIds) {
            $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
        })->with(['purchaseInvoiceItem.rawMaterial.uom', 'purchaseInvoiceItem.uom', 'fabricType'])->get();

        $artUomMap = [];
        foreach ($grnItems as $item) {
            $uom = $item->purchaseInvoiceItem->uom->uom_code ?? ($item->purchaseInvoiceItem->rawMaterial->uom->uom_code ?? null);
            if ($uom && !isset($artUomMap[$item->art_no])) {
                $artUomMap[$item->art_no] = $uom;
            }
        }

        $pdf = Pdf::loadView('job_card_entry.work_order_pdf', compact('jobCard', 'artUomMap'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'Work_Order_' . str_replace(['/', '\\'], '_', $jobCard->job_card_no) . '.pdf';
        return $pdf->stream($filename);
    }

    public function viewDetailsPdf($id)
    {
        $jobCard = JobCardEntry::with([
            'purchaseOrder', 
            'brand', 
            'season', 
            'processGroup', 
            'cuttingSizeRatios', 
            'fabricDetails.quantities', 
            'images', 
            'cuttingMaster',
            'sizeRatio',
            'sleeveMeters',
            'fit',
            'pattiType',
            'collarType',
            'cuffType',
            'pocketType',
            'bottomCut'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('job_card_entry.view_details_pdf', compact('jobCard'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Job_Card_Details_' . str_replace(['/', '\\'], '_', $jobCard->job_card_no) . '.pdf';
        return $pdf->stream($filename);
    }

    public function getItemsByBrandCategory(Request $request)
    {
        $brandCategoryId = $request->input('brand_category_id');
        if (!$brandCategoryId) {
            return response()->json(['items' => []]);
        }
        $items = Item::where('brand_category_id', $brandCategoryId)->where('status', 'Active')->get(['id', 'name', 'code']);
        return response()->json(['items' => $items]);
    }
}
