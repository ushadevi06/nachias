<?php

namespace App\Http\Controllers;

use App\Models\JobCardEntry;
use App\Models\JobCardCuttingSizeRatio;
use App\Models\JobCardImage;
use App\Models\JobCardFabricDetail;
use App\Models\StockEntryItem;
use App\Models\StockEntry;
use App\Models\JobCardOperation;
use App\Models\PurchaseOrder;
use App\Models\Brand;
use App\Models\Season;
use App\Models\ProcessGroup;
use App\Models\SizeRatio;
use App\Models\User;
use App\Models\Production;
use App\Models\ProcessSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductionStageConsumable;
use App\Models\Color;
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
use App\Models\RawMaterial;
use App\Models\Item;
use App\Models\OperationStage;
use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class JobCardEntryController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view job-card')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $jobCards = JobCardEntry::with(['brand', 'season', 'processGroup'])->orderBy('id', 'desc')->get();

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

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit job-card')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create job-card')) {
                return unauthorizedRedirect();
            }
        }
        if ($request->isMethod('post')) {
            $rules = [
                'job_card_no' => 'required|string|min:5|max:50|unique:job_card_entries,job_card_no' . ($id ? ',' . $id : ''),
                'purchase_order_id' => 'nullable|exists:purchase_orders,id',
                'stock_entry_ids' => 'nullable|array',
                'service_provider_id' => 'required|exists:service_providers,id',
                'issue_store_id' => 'required|exists:store_types,id',
                'issue_date' => 'required|date_format:d-m-Y',
                'delivery_date' => 'required|date_format:d-m-Y',
                'washing' => 'nullable|in:Yes,No',
                'width' => 'nullable|string|min:1|max:50',
                'mrp' => 'nullable|numeric',
                'total_qty_fs' => 'nullable|numeric',
                'total_qty_hs' => 'nullable|numeric',
                'season_id' => 'nullable|exists:seasons,id',
                'brand_id' => 'required|exists:brands,id',
                'receipt_store_id' => 'required|exists:store_types,id',
                'process_group_id' => 'required|exists:process_groups,id',
                'reference_no' => 'required|string|max:255|same:job_card_no',
                'status' => 'required|string',
                'remarks' => 'nullable|string',
                'fit_id' => 'nullable|exists:fits,id',
                'patti_type_id' => 'nullable|exists:patti_types,id',
                'collar_type_id' => 'nullable|exists:collar_types,id',
                'cuff_type_id' => 'nullable|exists:cuff_types,id',
                'pocket_type_id' => 'nullable|exists:pocket_types,id',
                'bottom_cut_id' => 'nullable|exists:bottom_cuts,id',
                'production_stages' => 'nullable|array',
                'production_stages.*.stage_id' => 'required|exists:operation_stages,id',
                'production_stages.*.service_provider_id' => 'required|exists:service_providers,id',
                'production_stages.*.issue_date' => 'required|date_format:d-m-Y',
                'production_stages.*.deadline_date' => 'required|date_format:d-m-Y',
                'stages' => 'nullable|array|min:1',
                'size_ratio_id' => 'required|exists:size_ratios,id',
            ];

            $messages = [
                '*.required' => 'This field is required',
                'job_card_no.unique' => 'This field already exists.',
                'reference_no.same' => 'Reference No must be the same as Job Card No.',
                'production_stages.*.stage_id.required' => 'This field is required',
                'production_stages.*.service_provider_id.required' => 'This field is required',
                'production_stages.*.issue_date.required' => 'This field is required',
                'production_stages.*.deadline_date.required' => 'This field is required',
                '*.min' => 'This field must be at least :min characters.',
                '*.max' => 'This field should not be more than :max characters.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            $validator->after(function ($validator) use ($request) {
                $totalFs = (float) ($request->total_qty_fs ?? 0);
                $totalHs = (float) ($request->total_qty_hs ?? 0);
                if (($totalFs + $totalHs) <= 0) {
                    $validator->errors()->add('article_matrix', 'Please enter at least one quantity in the Article Quantity Matrix.');
                }

                $fabrics = $request->input('fabrics', []);
                $missingFabricArtNos = [];
                foreach ($fabrics as $index => $fabric) {
                    $artNo = $fabric['art_no'] ?? ('Art No ' . ($index + 1));
                    $rowQty = 0;
                    if (isset($fabric['consumptions']) && is_array($fabric['consumptions'])) {
                        foreach ($fabric['consumptions'] as $cons) {
                            $rowQty += (float) ($cons['fs_cons'] ?? 0);
                            $rowQty += (float) ($cons['hs_cons'] ?? 0);
                        }
                    } else {
                        $rowQty += (float) ($fabric['fs_qty'] ?? 0);
                        $rowQty += (float) ($fabric['hs_qty'] ?? 0);
                    }
                    if ($rowQty <= 0) {
                        $missingFabricArtNos[] = $artNo;
                    }
                }
                if (!empty($missingFabricArtNos)) {
                    $validator->errors()->add('fabric_details', 'Please enter Sleeve Wise Qty for Art No: ' . implode(', ', $missingFabricArtNos));
                }

                $productionStages = $request->input('production_stages', []);
                if (is_array($productionStages)) {
                    foreach ($productionStages as $index => $stageData) {
                        $stageId = $stageData['stage_id'] ?? null;
                        $issueDateStr = $stageData['issue_date'] ?? null;
                        $deadlineDateStr = $stageData['deadline_date'] ?? null;

                        if ($stageId && $issueDateStr && $deadlineDateStr) {
                            try {
                                $issueDate = Carbon::createFromFormat('d-m-Y', $issueDateStr);
                                $deadlineDate = Carbon::createFromFormat('d-m-Y', $deadlineDateStr);

                                if ($deadlineDate->lessThan($issueDate)) {
                                    $stage = OperationStage::find($stageId);
                                    $stageName = $stage ? $stage->name : ('Stage ' . ($index + 1));
                                    $validator->errors()->add("production_stages.$index.deadline_date", "$stageName deadline date must be date after or equal to $stageName issue date");
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $data = [
                    'job_card_no' => $request->job_card_no,
                    'reference_no' => $request->reference_no,
                    'purchase_order_id' => $request->purchase_order_id,
                    'stock_entry_ids' => $request->stock_entry_ids ? json_encode($request->stock_entry_ids) : null,
                    'service_provider_id' => $request->service_provider_id,
                    'issue_store_id' => $request->issue_store_id,
                    'receipt_store_id' => $request->receipt_store_id,
                    'job_card_date' => date('Y-m-d', strtotime($request->issue_date)),
                    'delivery_date' => $request->delivery_date ? date('Y-m-d', strtotime($request->delivery_date)) : null,
                    'washing' => $request->washing,
                    'width' => $request->width,
                    'season_id' => $request->season_id,
                    'brand_id' => $request->brand_id,
                    'fs_qty' => $request->fs,
                    'hs_qty' => $request->hs,
                    'remarks' => $request->remarks,
                    'status' => $request->status ?? '',
                    'fit_id' => $request->fit_id,
                    'patti_type_id' => $request->patti_type_id,
                    'collar_type_id' => $request->collar_type_id,
                    'cuff_type_id' => $request->cuff_type_id,
                    'pocket_type_id' => $request->pocket_type_id,
                    'bottom_cut_id' => $request->bottom_cut_id,
                    'total_qty_fs' => $request->total_qty_fs ?? 0,
                    'total_qty_hs' => $request->total_qty_hs ?? 0,
                    'grand_total_qty' => ($request->total_qty_fs ?? 0) + ($request->total_qty_hs ?? 0),
                    'process_group_id' => $request->process_group_id,
                    'size_ratio_id' => $request->size_ratio_id,
                    'ex_1_label' => $request->ex_1_label,
                    'ex_2_label' => $request->ex_2_label,
                    'fabric_type_id' => $request->fabric_type_id,
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
                                'raw_material_id' => $issue->raw_material_id,
                            ];
                        }
                    }
                }
                $hasTasks = $id ? $jobCard->tasks()->exists() : false;
                if ($id) {
                    $jobCard->update($data);
                    if (!$hasTasks) {
                        $jobCard->cuttingSizeRatios()->forceDelete();
                        $jobCard->fabricDetails()->forceDelete();
                    }
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
                        $match = ['size' => $item['size']];
                        $val = [
                            'ratio' => $item['ratio'] ?? 0,
                            'qty_fs' => $item['qty_fs'] ?? 0,
                            'qty_hs' => $item['qty_hs'] ?? 0,
                            'total_qty' => ($item['qty_fs'] ?? 0) + ($item['qty_hs'] ?? 0),
                        ];
                        if ($hasTasks) {
                            $jobCard->cuttingSizeRatios()->updateOrCreate($match, $val);
                        } else {
                            $jobCard->cuttingSizeRatios()->create(array_merge($match, $val));
                        }
                    }
                }

                if ($request->article_matrix) {
                    foreach ($request->article_matrix as $index => $matrix) {
                        $fabric = collect($request->fabrics)->where('art_no', $matrix['art_no'])->first() ?? ($request->fabrics[$index] ?? null);

                        $fdMatch = ['art_no' => $matrix['art_no']];
                        $fdVal = [
                            'width' => $fabric['width'] ?? null,
                            'mtr' => $fabric['mtr'] ?? null,
                            'in_out' => $fabric['in_out'] ?? null,
                            'n_patti' => $fabric['n_patti'] ?? null,
                            'fs_qty' => $fabric['fs_qty'] ?? null,
                            'hs_qty' => $fabric['hs_qty'] ?? null,
                            'row_total' => 0
                        ];

                        if ($hasTasks) {
                            $fabricDetail = $jobCard->fabricDetails()->updateOrCreate($fdMatch, $fdVal);
                        } else {
                            $fabricDetail = $jobCard->fabricDetails()->create(array_merge($fdMatch, $fdVal));
                        }

                        if (isset($fabric['consumptions']) && is_array($fabric['consumptions'])) {
                            foreach ($fabric['consumptions'] as $sz => $cons) {
                                $cMatch = ['size' => $sz];
                                $cVal = [
                                    'fs_cons' => $cons['fs_cons'] ?? null,
                                    'hs_cons' => $cons['hs_cons'] ?? null,
                                ];
                                if ($hasTasks) {
                                    $fabricDetail->consumptions()->updateOrCreate($cMatch, $cVal);
                                } else {
                                    $fabricDetail->consumptions()->create(array_merge($cMatch, $cVal));
                                }
                            }
                        }

                        $rowTotal = 0;
                        $sizeQtys = [];

                        $rowColorId = null;

                        $seIds = [];
                        if ($request->stock_entry_ids) {
                            foreach ($request->stock_entry_ids as $cid) {
                                $seIds[] = strpos($cid, '::') !== false ? explode('::', $cid)[0] : $cid;
                            }
                        }

                        if (!empty($seIds)) {
                            $stockItem = \App\Models\StockEntryItem::whereIn('stock_entry_id', $seIds)
                                ->whereHas('rawMaterial', function ($q) {
                                    $q->where('store_category_id', 1);
                                })
                                ->with('grnEntryItem.purchaseInvoiceItem.purchaseOrderItem')
                                ->first();
                            if ($stockItem && $stockItem->grnEntryItem && $stockItem->grnEntryItem->purchaseInvoiceItem && $stockItem->grnEntryItem->purchaseInvoiceItem->purchaseOrderItem) {
                                $rowColorId = $stockItem->grnEntryItem->purchaseInvoiceItem->purchaseOrderItem->color_id;
                            }
                        }
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
                                $mqMatch = ['size' => $size];
                                $mqVal = [
                                    'qty_fs' => $qFs,
                                    'qty_hs' => $qHs,
                                    'total_qty' => $qFs + $qHs,
                                    'color_id' => $rowColorId
                                ];
                                if ($hasTasks) {
                                    $fabricDetail->quantities()->updateOrCreate($mqMatch, $mqVal);
                                } else {
                                    $fabricDetail->quantities()->create(array_merge($mqMatch, $mqVal));
                                }
                                $rowTotal += ($qFs + $qHs);
                            }
                        }
                        $fabricDetail->update(['row_total' => $rowTotal]);

                        if (isset($issueBackup[$matrix['art_no']]) && !$hasTasks) {
                            JobCardIssueItem::create(array_merge($issueBackup[$matrix['art_no']], [
                                'job_card_entry_id' => $jobCard->id,
                                'job_card_article_matrix_id' => $fabricDetail->id,
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
                            ]));
                        }
                    }
                }

                if ($request->has('production_stages')) {
                    $jobCard->operations()->delete();
                    foreach ($request->production_stages as $stageData) {
                        if (!empty($stageData['stage_id'])) {
                            $jobCard->operations()->create([
                                'operation_stage_id' => $stageData['stage_id'],
                                'service_provider_id' => $stageData['service_provider_id'] ?? null,
                                'employee_id' => $stageData['employee_id'] ?? null,
                                'assigned_date' => !empty($stageData['issue_date']) ? date('Y-m-d', strtotime($stageData['issue_date'])) : null,
                                'deadline_date' => !empty($stageData['deadline_date']) ? date('Y-m-d', strtotime($stageData['deadline_date'])) : null,
                                'remarks' => $stageData['remarks'] ?? null,
                            ]);
                        }
                    }
                }

                $this->syncSchedulesFromJobCard($jobCard, $request->production_stages ?? []);

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
                $this->generateProductionConsumables($jobCard);

                DB::commit();
                return redirect('job_card_entries')->with('success', 'Job Card saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('danger', 'Error: ' . $e->getMessage());
            }
        }

        $jobCard = $id ? JobCardEntry::with(['cuttingSizeRatios', 'images', 'sizeRatio', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'issueItems', 'sleeveMeters', 'operations'])->findOrFail($id) : null;
        $allPurchaseOrders = PurchaseOrder::with(['items'])->orderBy('id', 'desc')->get();
        $purchaseOrders = $allPurchaseOrders->filter(function ($po) use ($jobCard) {
            if ($jobCard && $po->id == $jobCard->purchase_order_id) {
                return true;
            }
            if (JobCardEntry::where('purchase_order_id', $po->id)->exists()) {
                return false;
            }
            $hasStock = StockEntry::whereHas('grnEntry.purchaseInvoice', function ($q) use ($po) {
                $q->where('purchase_order_id', $po->id);
            })->exists();

            if (!$hasStock) {
                return false;
            }

            return true;
        });

        $brands = Brand::active()->orderBy('id', 'desc')->get();
        $seasons = Season::active()->orderBy('id', 'desc')->get();
        $processGroups = ProcessGroup::active()->orderBy('id', 'desc')->get();
        $sizeRatios = SizeRatio::active()->orderBy('id', 'desc')->get();
        $employees = User::active()->where('id', '!=', 1)->orderBy('id', 'desc')->get();
        $colors = Color::active()->orderBy('id', 'desc')->get();

        $fits = Fit::active()->orderBy('id', 'desc')->get();
        $pattiTypes = PattiType::active()->orderBy('id', 'desc')->get();
        $collarTypes = CollarType::active()->orderBy('id', 'desc')->get();
        $cuffTypes = CuffType::active()->orderBy('id', 'desc')->get();
        $pocketTypes = PocketType::active()->orderBy('id', 'desc')->get();
        $bottomCuts = BottomCut::active()->orderBy('id', 'desc')->get();
        $cuttingMasters = User::active()->where('id', '!=', 1)->orderBy('id', 'desc')->get();

        $plants = ServiceProvider::where('is_plant', 1)->where('status', 'Active')->orderBy('id', 'desc')->get();
        $storeTypes = StoreType::where('status', 'Active')->orderBy('id', 'desc')->get();
        $operationStages = OperationStage::active()->orderBy('id', 'desc')->get();

        $stageTaskStatus = [];
        if ($jobCard) {
            $tasks = Task::with('stage')->where('job_card_entry_id', $jobCard->id)->get();
            foreach ($tasks as $task) {
                $osId = ($task->stage && $task->stage->operation_stage_id) ? $task->stage->operation_stage_id : $task->stage_id;
                if ($osId) {
                    $stageTaskStatus[$osId] = [
                        'status' => $task->status,
                        'task_no' => $task->task_no,
                    ];
                }
            }
        }
        $hasTasks = $jobCard ? $jobCard->tasks()->exists() : false;
        return view('job_card_entry/add', compact(
            'jobCard',
            'purchaseOrders',
            'brands',
            'seasons',
            'processGroups',
            'sizeRatios',
            'employees',
            'fits',
            'pattiTypes',
            'collarTypes',
            'cuffTypes',
            'pocketTypes',
            'bottomCuts',
            'cuttingMasters',
            'plants',
            'storeTypes',
            'operationStages',
            'stageTaskStatus',
            'hasTasks',
            'colors'
        ));
    }

    public function view_details($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view job-card')) {
            return unauthorizedRedirect();
        }
        $jobCard = JobCardEntry::with(['purchaseOrder.items.rawMaterial', 'brand', 'season', 'processGroup', 'cuttingSizeRatios', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'images', 'sleeveMeters', 'fit', 'pattiType', 'collarType', 'cuffType', 'pocketType', 'bottomCut', 'fabricType'])->findOrFail($id);

        $artMaterialMap = [];
        if ($jobCard->purchase_order_id) {
            $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
            $grnItems = GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
                $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
            })->with(['purchaseInvoiceItem.rawMaterial', 'fabricType'])->get();

            foreach ($grnItems as $item) {
                $name = $item->purchaseInvoiceItem->rawMaterial->name ?? ($item->fabricType->operation_stage_name ?? ($item->fabricType->name ?? null));
                if ($name && !isset($artMaterialMap[$item->art_no])) {
                    $artMaterialMap[$item->art_no] = $name;
                }
            }

            if ($jobCard->purchaseOrder && $jobCard->purchaseOrder->items) {
                foreach ($jobCard->purchaseOrder->items as $poItem) {
                    if (!isset($artMaterialMap[$poItem->art_no])) {
                        $artMaterialMap[$poItem->art_no] = $poItem->rawMaterial->name ?? null;
                    }
                }
            }
        }

        return view('job_card_entry/view_details', compact('jobCard', 'artMaterialMap'));
    }

    public function view_jc_item($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('issue-item job-card')) {
            return unauthorizedRedirect();
        }
        $jobCard = JobCardEntry::with(['brand', 'issueStore', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'purchaseOrder.items.rawMaterial.uom', 'purchaseOrder.supplier', 'purchaseOrder.items.uom', 'purchaseOrder.items.brand', 'purchaseOrder.items.style', 'issueItems', 'sleeveMeters'])->findOrFail($id);
        $issueItemMap = $jobCard->issueItems->keyBy('job_card_article_matrix_id');
        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
        $grnItems = GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
            $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
        })->with(['purchaseInvoiceItem.rawMaterial.uom', 'purchaseInvoiceItem.uom', 'fabricType', 'storeLocation'])->get();

        $seIds = [];
        if ($jobCard->stock_entry_ids) {
            $ids = json_decode($jobCard->stock_entry_ids, true);
            if ($ids) {
                foreach ($ids as $idStr) {
                    $seIds[] = strpos($idStr, '::') !== false ? explode('::', $idStr)[0] : $idStr;
                }
            }
        }

        $artMaterialMap = [];
        $artLocationMap = [];
        $artUomMap = [];
        $artPriceMap = [];

        if (!empty($seIds)) {
            $stockItems = StockEntryItem::whereIn('stock_entry_id', $seIds)->with(['rawMaterial', 'item', 'storeLocation', 'uom'])->get();

            foreach ($stockItems as $si) {
                if (!isset($artMaterialMap[$si->art_no])) {
                    $artMaterialMap[$si->art_no] = $si->rawMaterial->name ?? ($si->item->name ?? $si->art_no);
                }
                if ($si->storeLocation && !isset($artLocationMap[$si->art_no])) {
                    $artLocationMap[$si->art_no] = $si->storeLocation->store_location;
                }
                if ($si->uom && !isset($artUomMap[$si->art_no])) {
                    $artUomMap[$si->art_no] = $si->uom->uom_code;
                }
                if ($si->price > 0 && !isset($artPriceMap[$si->art_no])) {
                    $artPriceMap[$si->art_no] = $si->price;
                }
            }
        }

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
                $rm = RawMaterial::where('code', $item->art_no)->first();
                $uom = $rm->uom->uom_code ?? null;
            }
            if ($uom && !isset($artUomMap[$item->art_no])) {
                $artUomMap[$item->art_no] = $uom;
            }
        }

        return view('job_card_entry/view_jc_item', compact('jobCard', 'artMaterialMap', 'artLocationMap', 'artUomMap', 'artPriceMap', 'issueItemMap'));
    }

    public function issue_items(Request $request, $id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('issue-item job-card')) {
            return unauthorizedRedirect();
        }
        $jobCard = JobCardEntry::with(['purchaseOrder.items'])->findOrFail($id);
        $oldData = $jobCard->load('issueItems')->toArray();

        if ($request->isMethod('post')) {
            $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
            $grnItemIds = GrnEntryItem::whereIn('grn_entry_id', function ($q) use ($invoiceIds) {
                $q->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
            })->pluck('id');

            DB::beginTransaction();
            try {
                if ($request->items) {
                    $existingItems = JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->get();
                    $updatedItems = [];
                    $totalIssuedFabric = 0;

                    $artNoReversions = [];
                    foreach ($request->items as $matrixId => $itemData) {
                        $matrix = JobCardFabricDetail::find($matrixId);
                        $artNo = $matrix->art_no ?? '';

                        $existingItem = $existingItems->first(function ($item) use ($matrixId) {
                            return $item->job_card_article_matrix_id == $matrixId;
                        });

                        if ($existingItem && $existingItem->qty_used > 0 && $artNo) {
                            if (!isset($artNoReversions[$artNo])) {
                                $artNoReversions[$artNo] = 0;
                            }
                            $artNoReversions[$artNo] += $existingItem->qty_used;
                        }
                    }

                    foreach ($artNoReversions as $artNo => $qtyToRevert) {
                        $query = StockEntryItem::whereRaw('(qty_out > 0)')->orderBy('id', 'asc');

                        if ($jobCard->purchase_order_id) {
                            $query->whereIn('grn_entry_item_id', $grnItemIds);
                        }

                        if ($jobCard->purchaseOrder) {
                            $poItems = $jobCard->purchaseOrder->items;
                            $poItem = $poItems->where('art_no', $artNo)->first();
                            if ($poItem && $poItem->raw_material_id) {
                                $query->where('raw_material_id', $poItem->raw_material_id);
                            } else {
                                $artNo = trim($artNo);
                                $grnDirectQuery = clone $query;
                                $grnDirectQuery->whereHas('grnEntryItem', function ($q) use ($artNo) {
                                    $q->where('art_no', $artNo);
                                });

                                if ($grnDirectQuery->count() > 0) {
                                    $query = $grnDirectQuery;
                                } else {
                                    $rawMaterialByName = RawMaterial::where('name', 'LIKE', "%$artNo%")->first();
                                    $rawMaterialByCode = RawMaterial::where('code', 'LIKE', "%$artNo%")->first();
                                    $targetRmId = $rawMaterialByName->id ?? ($rawMaterialByCode->id ?? null);

                                    if ($targetRmId) {
                                        $query->where('raw_material_id', $targetRmId);
                                    } else {
                                        $query->whereHas('grnEntryItem.purchaseInvoiceItem.purchaseOrderItem', function ($q) use ($artNo) {
                                            $q->where('art_no', $artNo);
                                        });
                                    }
                                }
                            }
                        }

                        $stockItemsToRevert = $query->get();
                        $remainingToRevert = $qtyToRevert;

                        foreach ($stockItemsToRevert as $stockItem) {
                            if ($remainingToRevert <= 0)
                                break;

                            $currentOut = (float) ($stockItem->qty_out ?? 0);
                            if ($currentOut <= 0)
                                continue;

                            $revertAmount = min($currentOut, $remainingToRevert);
                            $stockItem->qty_out = $currentOut - $revertAmount;
                            $stockItem->save();

                            $remainingToRevert -= $revertAmount;
                        }
                    }

                    foreach ($request->items as $matrixId => $itemData) {
                        $qtyIssue = floatval($itemData['qty_issue'] ?? 0);
                        $qtyUsed = floatval($itemData['qty_used'] ?? 0);
                        $totalIssuedFabric += $qtyIssue;
                        $matrix = JobCardFabricDetail::find($matrixId);
                        $artNo = $matrix->art_no ?? '';

                        $existingItem = $existingItems->first(function ($item) use ($matrixId) {
                            return $item->job_card_article_matrix_id == $matrixId;
                        });

                        $unitPrice = 0;
                        $firstStockItemId = null;

                        if ($artNo && $qtyUsed > 0) {
                            $query = StockEntryItem::whereRaw('(qty_in - COALESCE(qty_out, 0)) > 0')->orderBy('id', 'asc');
                            $poFiltered = false;

                            if ($jobCard->purchase_order_id) {
                                $query->whereIn('grn_entry_item_id', $grnItemIds);
                            }

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
                                $grnDirectQuery->whereHas('grnEntryItem', function ($q) use ($artNo) {
                                    $q->where('art_no', $artNo);
                                });

                                $grnDirectCount = $grnDirectQuery->count();

                                if ($grnDirectCount > 0) {
                                    $query = $grnDirectQuery;
                                } else {
                                    $rawMaterialByName = RawMaterial::where('name', 'LIKE', "%$artNo%")->first();
                                    $rawMaterialByCode = RawMaterial::where('code', 'LIKE', "%$artNo%")->first();
                                    $targetRmId = $rawMaterialByName->id ?? ($rawMaterialByCode->id ?? null);

                                    if ($targetRmId) {
                                        $rmQuery = clone $query;
                                        $rmQuery->where('raw_material_id', $targetRmId);
                                        if ($rmQuery->count() > 0) {
                                            $query = $rmQuery;
                                        } else {
                                            $query->whereHas('grnEntryItem.purchaseInvoiceItem', function ($q) use ($targetRmId) {
                                                $q->where('raw_material_id', $targetRmId);
                                            });
                                        }
                                    } else {
                                        $query->whereHas('grnEntryItem.purchaseInvoiceItem.purchaseOrderItem', function ($q) use ($artNo) {
                                            $q->where('art_no', $artNo);
                                        });
                                    }
                                }
                            }

                            $stockCandidates = $query->get();

                            $remainingToDeduct = $qtyUsed;
                            $weightedCost = 0;

                            foreach ($stockCandidates as $stockItem) {
                                if ($remainingToDeduct <= 0)
                                    break;

                                $available = $stockItem->qty_in - (float) ($stockItem->qty_out ?? 0);
                                if ($available <= 0)
                                    continue;

                                $take = min($available, $remainingToDeduct);

                                $stockItem->qty_out = (float) ($stockItem->qty_out ?? 0) + $take;
                                $stockItem->save();

                                if (!isset($firstStockItemId))
                                    $firstStockItemId = $stockItem->id;
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
                            'stock_entry_item_id' => $firstStockItemId ?? null,
                            'raw_material_id' => ($firstStockItemId ? StockEntryItem::find($firstStockItemId)->raw_material_id : null),
                            'updated_by' => auth()->id(),
                        ];

                        if ($existingItem) {
                            $issueItem = $existingItem;
                            $issueItem->update($data);
                        } else {
                            $data['created_by'] = auth()->id();
                            $issueItem = JobCardIssueItem::create($data);
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

    public function searchStockEntries(Request $request)
    {
        $term = $request->input('q', '');

        $entries = StockEntry::with(['stockEntryItems.rawMaterial', 'stockEntryItems.uom'])
            ->where(function ($q) use ($term) {
                $q->where('stock_entry_no', 'like', "%{$term}%")
                    ->orWhereHas('stockEntryItems', function ($q2) use ($term) {
                        $q2->where('art_no', 'like', "%{$term}%")
                            ->orWhereHas('rawMaterial', function ($q3) use ($term) {
                                $q3->where('name', 'like', "%{$term}%");
                            });
                    });
            })->orderBy('id', 'desc')->limit(30)->get();

        $results = [];
        foreach ($entries as $entry) {
            $materialGroups = [];
            foreach ($entry->stockEntryItems as $item) {
                if (!$item->raw_material_id || !$item->rawMaterial) {
                    continue;
                }

                $name = $item->rawMaterial->name;

                $matchSe = stripos($entry->stock_entry_no, $term) !== false;
                $matchName = stripos($name, $term) !== false;
                $matchArtNo = stripos($item->art_no, $term) !== false;

                if ($term && !$matchSe && !$matchName && !$matchArtNo) {
                    continue;
                }

                $groupKey = 'rm|' . $item->raw_material_id;

                if (!isset($materialGroups[$groupKey])) {
                    $materialGroups[$groupKey] = [
                        'name' => $name,
                        'art_no' => $item->art_no,
                        'net_qty' => 0,
                        'uom' => $item->uom->uom_code ?? '',
                    ];
                }
                $materialGroups[$groupKey]['net_qty'] += ($item->qty_in - ($item->qty_out ?? 0));
            }

            foreach ($materialGroups as $key => $group) {
                $netQty = round($group['net_qty'], 3);

                if ($netQty <= 0) {
                    continue;
                }

                $combinedId = $entry->id . '::' . $key;
                $results[] = [
                    'id' => $combinedId,
                    'text' => $entry->stock_entry_no . ' | ' . $group['art_no'] . ' | ' . $group['name'] . ' | Qty: ' . $netQty . ' ' . $group['uom'],
                    'se_no' => $entry->stock_entry_no,
                    'art_no' => $group['art_no'],
                    'name' => $group['name'],
                    'qty' => $netQty . ' ' . $group['uom'],
                ];
            }
        }

        $seen = [];
        $unique = [];
        foreach ($results as $r) {
            if (!in_array($r['id'], $seen)) {
                $seen[] = $r['id'];
                $unique[] = $r;
            }
        }

        return response()->json(['results' => $unique]);
    }

    public function getStockEntryDetails(Request $request)
    {
        $ids = $request->input('ids', []);
        $jobCardId = $request->input('job_card_id');

        if (empty($ids)) {
            return response()->json(['art_data' => [], 'art_numbers' => []]);
        }

        $issuedQtys = [];
        if ($jobCardId) {
            $issuedQtys = JobCardIssueItem::where('job_card_entry_id', $jobCardId)
                ->with('fabricDetail')
                ->get()
                ->groupBy(function ($item) {
                    return $item->fabricDetail->art_no ?? '';
                })
                ->map(function ($items) {
                    return $items->sum('qty_issue');
                });
        }

        $stockEntryIds = [];
        $filters = [];
        foreach ($ids as $combinedId) {
            if (strpos($combinedId, '::') !== false) {
                list($seId, $target) = explode('::', $combinedId, 2);
                $stockEntryIds[] = $seId;
                if (strpos($target, '|') !== false) {
                    list($type, $val) = explode('|', $target, 2);
                    $filters[$seId][] = ['type' => $type, 'val' => $val];
                }
            } else {
                $stockEntryIds[] = $combinedId;
            }
        }

        $stockEntryIds = array_unique($stockEntryIds);

        $stockItems = StockEntryItem::whereIn('stock_entry_id', $stockEntryIds)->with(['rawMaterial.uom', 'storeCategory', 'uom', 'grnEntryItem'])->get();

        $filteredItems = $stockItems->filter(function ($item) use ($filters) {
            $seId = $item->stock_entry_id;
            if (!isset($filters[$seId])) {
                return true;
            }

            foreach ($filters[$seId] as $f) {
                if ($f['type'] === 'rm' && $item->raw_material_id == $f['val'])
                    return true;
                if ($f['type'] === 'item' && $item->item_id == $f['val'])
                    return true;
                if ($f['type'] === 'art' && $item->art_no == $f['val'])
                    return true;
            }

            return false;
        });

        $idOrderMap = array_flip($ids);
        $filteredItems = $filteredItems->sortBy(function($item) use ($idOrderMap, $filters) {
            $seId = (string) $item->stock_entry_id;
            if (isset($filters[$seId])) {
                foreach($filters[$seId] as $f) {
                    if (($f['type'] === 'rm' && $item->raw_material_id == $f['val']) ||
                        ($f['type'] === 'item' && $item->item_id == $f['val']) ||
                        ($f['type'] === 'art' && $item->art_no == $f['val'])) {
                        $combinedId = $seId . '::' . $f['type'] . '|' . $f['val'];
                        if (isset($idOrderMap[$combinedId])) return $idOrderMap[$combinedId];
                    }
                }
            }
            
            return $idOrderMap[$seId] ?? 999;
        });

        $grouped = $filteredItems->groupBy('art_no');

        $artData = $grouped->map(function ($items, $artNo) use ($issuedQtys) {
            $firstItem = $items->first();
            $rawMaterial = $firstItem->rawMaterial ?? null;

            $uomCode = null;
            if ($firstItem->uom) {
                $uomCode = $firstItem->uom->uom_code;
            } elseif ($rawMaterial && $rawMaterial->uom) {
                $uomCode = $rawMaterial->uom->uom_code;
            }

            $netQty = $items->sum(function ($item) {
                return ($item->qty_in ?? 0) - ($item->qty_out ?? 0);
            });

            $alreadyIssued = (float) ($issuedQtys[$artNo] ?? 0);

            return [
                'art_no' => $artNo,
                'art_name' => $rawMaterial->name ?? null,
                'mtr' => $netQty + $alreadyIssued,
                'already_issued' => $alreadyIssued,
                'uom_code' => $uomCode,
                'store_category_id' => $rawMaterial ? $rawMaterial->store_category_id : 1,
                'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                'fabric_type_id' => $firstItem->fabric_type_id ?? ($firstItem->grnEntryItem->fabric_type_id ?? null),
            ];
        })->values();

        return response()->json([
            'art_data' => $artData,
            'art_numbers' => $artData->pluck('art_no'),
        ]);
    }

    public function getSizeRatioDetails($id)
    {
        $sizeRatio = SizeRatio::find($id);
        return response()->json($sizeRatio);
    }

    public function getPoDetails($id)
    {
        $po = PurchaseOrder::with(['items'])->find($id);
        if (!$po)
            return response()->json(['error' => 'PO not found'], 404);
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
                    'mtr' => $items->filter(function ($item) {
                        return $item->stockEntryItems->isNotEmpty();
                    })->flatMap(function ($item) {
                        return $item->stockEntryItems;
                    })->sum(function ($stockItem) {
                        return $stockItem->qty_in - ($stockItem->qty_out ?? 0);
                    }),
                    'uom_code' => $uom,
                    'store_category_id' => $catId,
                    'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                    'grn_no' => $firstItem->grnEntry->grn_number ?? null
                ];
            })->values();

        if ($artData->isEmpty()) {
            $po->load(['items.uom', 'items.rawMaterial.uom']);
            $artData = $po->items->map(function ($item) {
                $uom = null;
                if ($item->uom) {
                    $uom = $item->uom->uom_code;
                } elseif ($item->rawMaterial && $item->rawMaterial->uom) {
                    $uom = $item->rawMaterial->uom->uom_code;
                }

                $catId = ($item->rawMaterial) ? $item->rawMaterial->store_category_id : 1;

                $fallbackStock = 0;
                if ($item->raw_material_id) {
                    $fallbackStock = StockEntryItem::whereIn('grn_entry_item_id', function ($q) use ($grnIds) {
                        $q->select('id')->from('grn_entry_items')->whereIn('grn_entry_id', $grnIds);
                    })->where('raw_material_id', $item->raw_material_id)->sum(DB::raw('qty_in - COALESCE(qty_out, 0)'));
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

    public function checkStock(Request $request, $id)
    {
        $po = PurchaseOrder::with(['items.rawMaterial'])->find($id);
        if (!$po)
            return response()->json(['error' => 'PO not found'], 404);

        $jobCardId = $request->query('job_card_id');
        $issuedQtys = [];
        if ($jobCardId) {
            $issuedQtys = JobCardIssueItem::where('job_card_entry_id', $jobCardId)
                ->with('fabricDetail')
                ->get()
                ->groupBy(function ($item) {
                    return $item->fabricDetail->art_no ?? '';
                })
                ->map(function ($items) {
                    return $items->sum('qty_issue');
                });
        }

        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $po->id)->pluck('id');
        $grnIds = GrnEntry::whereIn('purchase_invoice_id', $invoiceIds)->pluck('id');

        $artData = GrnEntryItem::whereIn('grn_entry_id', $grnIds)->with(['purchaseInvoiceItem.rawMaterial', 'grnEntry', 'stockEntryItems'])->get()->groupBy('art_no')->map(function ($items, $artNo) use ($issuedQtys) {
            $firstItem = $items->first();
            $rawMaterial = $firstItem->purchaseInvoiceItem->rawMaterial ?? null;
            $catId = $rawMaterial ? $rawMaterial->store_category_id : 1;

            $netStock = $items->flatMap(function ($item) {
                return $item->stockEntryItems;
            })->sum(function ($stockItem) {
                return $stockItem->qty_in - ($stockItem->qty_out ?? 0);
            });

            $alreadyIssued = (float) ($issuedQtys[$artNo] ?? 0);

            return [
                'art_no' => $artNo,
                'art_name' => $rawMaterial->name ?? ($firstItem->fabricType->name ?? null),
                'mtr' => (float) $netStock + $alreadyIssued,
                'already_issued' => $alreadyIssued,
                'store_category_id' => $catId,
                'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                'grn_no' => $firstItem->grnEntry->grn_number ?? null,
                'uom_code' => $rawMaterial->uom->uom_code ?? null
            ];
        })->values();

        if ($artData->isEmpty()) {
            $artData = $po->items->map(function ($item) use ($grnIds, $issuedQtys) {
                $netStock = 0;
                if ($item->raw_material_id) {
                    $netStock = StockEntryItem::whereIn('grn_entry_item_id', function ($q) use ($grnIds) {
                        $q->select('id')->from('grn_entry_items')->whereIn('grn_entry_id', $grnIds);
                    })
                        ->where('raw_material_id', $item->raw_material_id)
                        ->sum(DB::raw('qty_in - COALESCE(qty_out, 0)'));
                } else {
                    $netStock = GrnEntryItem::where('art_no', $item->art_no)
                        ->whereHas('grnEntry.purchaseInvoice', function ($q) use ($item) {
                            $q->where('purchase_order_id', $item->purchase_order_id);
                        })
                        ->with('stockEntryItems')
                        ->get()
                        ->flatMap(function ($item) {
                            return $item->stockEntryItems;
                        })
                        ->sum(function ($si) {
                            return $si->qty_in - ($si->qty_out ?? 0);
                        });
                }

                $alreadyIssued = (float) ($issuedQtys[$item->art_no] ?? 0);

                return [
                    'art_no' => $item->art_no,
                    'art_name' => $item->rawMaterial->name ?? ($item->fabricType->name ?? null),
                    'mtr' => (float) $netStock + $alreadyIssued,
                    'already_issued' => $alreadyIssued,
                    'store_category_id' => $item->rawMaterial ? $item->rawMaterial->store_category_id : 1,
                    'raw_material_id' => $item->raw_material_id,
                    'grn_no' => $item->grn_no,
                    'uom_code' => $item->rawMaterial->uom->uom_code ?? null
                ];
            })->unique('art_no')->values();
        }

        return response()->json(['art_data' => $artData]);
    }

    public function deleteImage($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('edit job-card')) {
            return unauthorizedRedirect();
        }
        $image = JobCardImage::findOrFail($id);
        $oldData = $image->toArray();
        $filePath = public_path($image->image);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $image->delete();
        addLog('delete', 'Job Card Image', 'job_card_images', $id, $oldData, null);
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

        $issueItems = $jobCard->issueItems->groupBy(function ($item) {
            return $item->fabricDetail->art_no ?? 'N/A';
        })->map(function ($items, $artNo) use ($artTotalMap) {
            return (object) [
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
        $grnItems = GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
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
        $grnItems = GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
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

    public function viewDetailsPdf($id, $is_print = false)
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
            'bottomCut',
            'fabricType'
        ])->findOrFail($id);

        if ($is_print) {
            return view('job_card_entry.view_details_pdf', compact('jobCard', 'is_print'));
        }

        $pdf = Pdf::loadView('job_card_entry.view_details_pdf', compact('jobCard'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Job_Card_Details_' . str_replace(['/', '\\'], '_', $jobCard->job_card_no) . '.pdf';
        return $pdf->stream($filename);
    }

    public function print_details($id)
    {
        $is_print = true;
        return $this->viewDetailsPdf($id, $is_print);
    }

    public function download_details($id)
    {
        return $this->viewDetailsPdf($id);
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

    private function syncSchedulesFromJobCard($jobCard, $stagesData)
    {
        ProcessSchedule::where('job_card_entry_id', $jobCard->id)->delete();

        foreach ($stagesData as $stage) {
            if (empty($stage['stage_id']))
                continue;

            $os = OperationStage::find($stage['stage_id']);
            if (!$os)
                continue;

            ProcessSchedule::create([
                'job_card_entry_id' => $jobCard->id,
                'production_id' => null,
                'operation_stage_id' => $os->id,
                'stage' => $os->operation_stage_name,
                'planned_qty' => $jobCard->grand_total_qty ?? 0,
                'uom' => 'PCS',
                'scheduled_to' => $stage['service_provider_id'] ?? null,
                'start_date' => !empty($stage['issue_date']) ? date('Y-m-d', strtotime($stage['issue_date'])) : null,
                'due_date' => !empty($stage['deadline_date']) ? date('Y-m-d', strtotime($stage['deadline_date'])) : null,
                'status' => 'Planned',
                'created_by' => auth()->id(),
            ]);
        }
    }

    private function generateProductionConsumables($jobCard)
    {
        ProductionStageConsumable::where('job_card_id', $jobCard->id)->forceDelete();

        $stages = $jobCard->operations;
        if ($stages->isEmpty())
            return;

        $matrixItems = $jobCard->cuttingSizeRatios;

        $fabricDetails = $jobCard->fabricDetails()->with('consumptions')->get();

        $issueItems = $jobCard->issueItems;

        foreach ($stages as $stage) {
            $stageId = $stage->operation_stage_id;
            $stageName = $stage->operationStage->operation_stage_name ?? 'Unknown';

            foreach ($fabricDetails as $fabricDetail) {
                $artNo = trim($fabricDetail->art_no);

                $totalFsCons = 0;
                $totalHsCons = 0;

                if ($fabricDetail->consumptions->isNotEmpty()) {
                    foreach ($matrixItems as $mx) {
                        $cons = $fabricDetail->consumptions->where('size', $mx->size)->first();
                        if ($cons) {
                            $totalFsCons += ($mx->qty_fs * ($cons->fs_cons ?? 0));
                            $totalHsCons += ($mx->qty_hs * ($cons->hs_cons ?? 0));
                        }
                    }
                } else {
                    $totalFsCons = ($jobCard->total_qty_fs ?? 0) * ($fabricDetail->fs_qty ?? 0);
                    $totalHsCons = ($jobCard->total_qty_hs ?? 0) * ($fabricDetail->hs_qty ?? 0);
                }

                $totalActual = $totalFsCons + $totalHsCons;

                if ($totalActual > 0) {
                    $stockItem = \DB::table('stock_entry_items')
                        ->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')
                        ->where('grn_entry_items.art_no', $artNo)
                        ->select('stock_entry_items.raw_material_id', 'stock_entry_items.uom_id')
                        ->first();

                    $rawMaterialId = $stockItem->raw_material_id ?? null;
                    $uomId = $stockItem->uom_id ?? null;

                    $sleeveType = 'All';
                    if ($totalFsCons > 0 && $totalHsCons == 0)
                        $sleeveType = 'F/S';
                    elseif ($totalHsCons > 0 && $totalFsCons == 0)
                        $sleeveType = 'H/S';

                    ProductionStageConsumable::create([
                        'job_card_id' => $jobCard->id,
                        'production_stage_id' => $stageId,
                        'stage' => $stageName,
                        'art_no' => $artNo,
                        'item_type' => 'Consumable',
                        'raw_material_id' => $rawMaterialId,
                        'planned_qty' => $jobCard->grand_total_qty,
                        'fs_qty' => $totalFsCons,
                        'hs_qty' => $totalHsCons,
                        'total_qty' => $totalActual,
                        'actual_qty' => $totalActual,
                        'uom_id' => $uomId,
                        'sleeve_type' => $sleeveType,
                        'status' => 'Active',
                        'remarks' => "Derived from Job Card Fabric Details. Article: {$artNo}",
                        'created_by' => auth()->id()
                    ]);
                }
            }

            foreach ($issueItems as $issueItem) {
                $rate = $issueItem->average ?? 0;
                $fsCons = ($jobCard->total_qty_fs ?? 0) * $rate;
                $hsCons = ($jobCard->total_qty_hs ?? 0) * $rate;
                $totalConsumption = $fsCons + $hsCons;

                if ($totalConsumption > 0) {
                    $stockInfo = null;
                    if ($issueItem->stock_entry_item_id) {
                        $stockInfo = \DB::table('stock_entry_items')->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')->where('stock_entry_items.id', $issueItem->stock_entry_item_id)->select('grn_entry_items.art_no', 'stock_entry_items.raw_material_id', 'stock_entry_items.uom_id')->first();
                    }

                    $sleeveType = 'All';
                    if ($fsCons > 0 && $hsCons == 0)
                        $sleeveType = 'F/S';
                    elseif ($hsCons > 0 && $fsCons == 0)
                        $sleeveType = 'H/S';

                    ProductionStageConsumable::create([
                        'job_card_id' => $jobCard->id,
                        'production_stage_id' => $stageId,
                        'stage' => $stageName,
                        'art_no' => $stockInfo->art_no ?? null,
                        'item_type' => 'Consumable',
                        'raw_material_id' => $stockInfo->raw_material_id ?? null,
                        'planned_qty' => $jobCard->grand_total_qty,
                        'fs_qty' => $fsCons,
                        'hs_qty' => $hsCons,
                        'total_qty' => $totalConsumption,
                        'actual_qty' => $totalConsumption,
                        'uom_id' => $stockInfo->uom_id ?? null,
                        'sleeve_type' => $sleeveType,
                        'status' => 'Active',
                        'remarks' => "Derived from Job Card Issue Items.",
                        'created_by' => auth()->id()
                    ]);
                }
            }
        }
    }
}
