<?php

namespace App\Http\Controllers;

use App\Models\JobCardEntry;
use App\Models\JobCardCuttingSizeRatio;
use App\Models\JobCardMatrixQuantity;
use App\Models\BarcodeMaster;
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
use Illuminate\Support\Collection;
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
use App\Models\StandardConsumption;
use App\Models\Task;
use App\Models\Setting;
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
            $query = JobCardEntry::with(['brand', 'season', 'processGroup'])->orderBy('id', 'desc');

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('job_card_no', 'like', "%{$search}%")
                        ->orWhereHas('brand', function ($q2) use ($search) {
                            $q2->where('brand_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('season', function ($q3) use ($search) {
                            $q3->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('processGroup', function ($q4) use ($search) {
                            $q4->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $jobCards = $query->get();

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

                if (auth()->id() == 1 || auth()->user()->can('view_details job-card')) {
                    $action .= '<a href="' . url('job_card_entries/view/' . $jc->id) . '" class="dropdown-item"><i class="icon-base ri ri-eye-line me-2"></i>View</a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('issue-item job-card')) {
                    $action .= '<a href="' . url('job_card_entries/view-item/' . $jc->id) . '" class="dropdown-item"><i class="icon-base ri ri-list-check-2 me-2"></i>Issue Item</a>';
                }

                $action .= '</div></div>';

                $data[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'job_card_no' => $jc->job_card_no,
                    'job_card_date' => date('d-m-Y', strtotime($jc->job_card_date)),
                    'brand' => $jc->brand->brand_name ?? '-',
                    'season' => $jc->season->name ?? '-',
                    'process_group' => $jc->processGroup->name ?? '-',
                    'total_qty' => $jc->grand_total_qty,
                    'job_card_type' => $jc->job_card_type ?? 'Regular',
                    'status' => $status,
                    'action' => $action
                ];
            }
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
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
                'job_card_type' => 'nullable|string|in:Regular,Urgent,Sample,Special Order',
                'remarks' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,csv|max:5120',
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
                'production_stages.*.rate' => 'required|numeric|min:0',
                'stages' => 'nullable|array|min:1',
                'fabrics.*.mtr' => 'nullable|numeric|min:0.01',
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
                'fabrics.*.mtr.min' => 'The Issued Meters must be greater than 0 for fabric material.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            $validator->after(function ($validator) use ($request) {
                $totalFs = (float) ($request->total_qty_fs ?? 0);
                $totalHs = (float) ($request->total_qty_hs ?? 0);
                if (($totalFs + $totalHs) <= 0) {
                    $validator->errors()->add('article_matrix', 'Please enter at least one quantity in the Article Quantity Matrix.');
                }

                $fabrics = $request->input('fabrics', []);
                $articleMatrix = collect($request->input('article_matrix', []));

                foreach ($fabrics as $index => $fabric) {
                    $artNo = isset($fabric['art_no']) ? trim($fabric['art_no']) : null;
                    if ($artNo) {
                        $matrixRow = $articleMatrix->where('art_no', $artNo)->first();
                        if ($matrixRow) {
                            $catId = $fabric['store_category_id'] ?? null;
                            $rm = RawMaterial::where('code', $artNo)->orWhere('name', $artNo)->first();
                            if (!$catId && $rm) {
                                $catId = $rm->store_category_id ?? ($rm->category_id ?? null);
                            }
                            $isFabric = ($catId == 1);
                            $artName = $rm->name ?? '';
                            $isVisible = $isFabric || (stripos($artName, 'BUTTONS') !== false) || (stripos($artName, 'LABEL') !== false);

                            if ($isVisible && (empty($fabric['mtr']) || (float) $fabric['mtr'] <= 0)) {
                                $validator->errors()->add("fabrics.$index.mtr", "The Issued Meters field is required for material: $artNo ($artName).");
                            }

                            $totalNeeded = 0;

                            if ($isFabric) {
                                $layMarks = $fabric['lay_marks'] ?? [];
                                if (count($layMarks) > 0) {
                                    foreach ($layMarks as $lm) {
                                        $mkMeter = (float) ($lm['meter'] ?? 0);
                                        $mkLay = (float) ($lm['no_of_lay'] ?? 0);
                                        $totalNeeded += ($mkMeter * $mkLay);
                                    }
                                } else {
                                    $consumptions = $fabric['consumptions'] ?? [];
                                    foreach ($matrixRow as $key => $val) {
                                        if (str_starts_with($key, 'fs_')) {
                                            $size = substr($key, 3);
                                            $cons = (float) ($consumptions[$size]['fs_cons'] ?? 0);
                                            $totalNeeded += (float) ($val ?? 0) * $cons;
                                        } elseif (str_starts_with($key, 'hs_')) {
                                            $size = substr($key, 3);
                                            $cons = (float) ($consumptions[$size]['hs_cons'] ?? 0);
                                            $totalNeeded += (float) ($val ?? 0) * $cons;
                                        }
                                    }
                                }
                            } else {
                                foreach ($matrixRow as $key => $val) {
                                    if (str_starts_with($key, 'fs_') || str_starts_with($key, 'hs_')) {
                                        $totalNeeded += (float) ($val ?? 0);
                                    }
                                }
                            }

                            $used = (float) ($fabric['mtr'] ?? 0);

                            if (($totalNeeded - $used) > 0.001) {
                                $unit = $isFabric ? ' MTR' : '';
                                $totalNeededFormatted = (fmod($totalNeeded, 1) == 0) ? (int) $totalNeeded : number_format($totalNeeded, 2, '.', '');
                                $validator->errors()->add("fabrics.$index.mtr", "Shortage for $artNo! Matrix Needs: $totalNeededFormatted$unit, but only $used was entered.");
                            }
                        }
                    }
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
                    'no_of_days' => $request->no_of_days,
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
                    'sleeve_instances' => $request->sleeve_instances ? json_decode($request->sleeve_instances, true) : null,
                    'job_card_type' => $request->job_card_type ?? 'Regular',
                ];

                if ($request->hasFile('attachment')) {
                    $attachmentPath = public_path('uploads/job_card/attachments');
                    if (!file_exists($attachmentPath)) {
                        mkdir($attachmentPath, 0755, true);
                    }
                    if ($id) {
                        $existingJc = JobCardEntry::find($id);
                        if ($existingJc && $existingJc->attachment) {
                            $oldFile = public_path($existingJc->attachment);
                            if (file_exists($oldFile)) {
                                unlink($oldFile);
                            }
                        }
                    }
                    $file = $request->file('attachment');
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $file->move($attachmentPath, $fileName);
                    $data['attachment'] = 'uploads/job_card/attachments/' . $fileName;
                }
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

                if ($request->fabrics) {
                    foreach ($request->fabrics as $index => $fabric) {
                        $artNo = $fabric['art_no'] ?? null;
                        if (!$artNo)
                            continue;
                        $grnImage = $fabric['grn_image'] ?? null;
                        if (!$grnImage) {
                            $grnImage = $this->getLatestGrnImageForArtNo($artNo);
                        }

                        $matrix = collect($request->article_matrix ?? [])->where('art_no', $artNo)->first();

                        $fdMatch = ['art_no' => $artNo];
                        $fdVal = [
                            'width' => $fabric['width'] ?? null,
                            'mtr' => $fabric['mtr'] ?? null,
                            'in_out' => $fabric['in_out'] ?? null,
                            'n_patti' => $fabric['n_patti'] ?? null,
                            'fs_qty' => $fabric['fs_qty'] ?? null,
                            'hs_qty' => $fabric['hs_qty'] ?? null,
                            'total_qty' => $fabric['total_qty'] ?? null,
                            'used_qty' => $fabric['used_qty'] ?? null,
                            'remaining_qty' => $fabric['remaining_qty'] ?? null,
                            'grn_image' => $grnImage,
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
                        } else {
                            $rm = RawMaterial::where('code', $artNo)->orWhere('name', $artNo)->first();
                            if ($rm) {
                                $std = StandardConsumption::where('raw_material_id', $rm->id)->active()->first();
                                if ($std) {
                                    $allMatrixSizes = collect($request->matrix_items ?? [])->pluck('size')->unique();
                                    foreach ($allMatrixSizes as $sz) {
                                        if (!$sz)
                                            continue;
                                        $fabricDetail->consumptions()->create([
                                            'size' => $sz,
                                            'fs_cons' => $std->fs_qty,
                                            'hs_cons' => $std->hs_qty,
                                        ]);
                                    }
                                }
                            }
                        }

                        if (isset($fabric['lay_marks']) && is_array($fabric['lay_marks'])) {
                            $existingMarkNos = [];
                            foreach ($fabric['lay_marks'] as $index => $layMark) {
                                if (!empty($layMark['sizes'])) {
                                    $markNo = $index + 1;
                                    $existingMarkNos[] = $markNo;
                                    $lmMatch = ['mark_no' => $markNo];
                                    $lmVal = [
                                        'sizes' => $layMark['sizes'],
                                        'sleeve_type' => $layMark['sleeve'] ?? null,
                                        'lay_mark_meter' => $layMark['meter'] ?? null,
                                        'no_of_lay' => $layMark['no_of_lay'] ?? null,
                                    ];

                                    if ($hasTasks) {
                                        $fabricDetail->layMarks()->updateOrCreate($lmMatch, $lmVal);
                                    } else {
                                        $fabricDetail->layMarks()->updateOrCreate($lmMatch, $lmVal);
                                    }
                                }
                            }
                            $fabricDetail->layMarks()->whereNotIn('mark_no', $existingMarkNos)->delete();
                        }

                        $rowTotal = 0;
                        if ($matrix) {
                            $sizeQtys = [];
                            $rowColorId = null;

                            $seIds = [];
                            if ($request->stock_entry_ids) {
                                foreach ($request->stock_entry_ids as $cid) {
                                    $seIds[] = strpos($cid, '::') !== false ? explode('::', $cid)[0] : $cid;
                                }
                            }

                            if (!empty($seIds)) {
                                $stockItem = StockEntryItem::whereIn('stock_entry_id', $seIds)
                                    ->whereHas('rawMaterial', function ($q) {
                                        $q->where('store_category_id', 1);
                                    })
                                    ->with('grnEntryItem')
                                    ->first();
                                if ($stockItem && $stockItem->grnEntryItem) {
                                    $rowColorId = $stockItem->grnEntryItem->color_id;
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
                        }
                        $fabricDetail->update(['row_total' => $rowTotal]);

                        /*  if (isset($issueBackup[$artNo]) && !$hasTasks) {
                            JobCardIssueItem::create(array_merge($issueBackup[$artNo], [
                                'job_card_entry_id' => $jobCard->id,
                                'job_card_article_matrix_id' => $fabricDetail->id,
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
                            ]));
                        } */
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
                                'rate' => $stageData['rate'] ?? 0,
                                'total_cost' => ($stageData['rate'] ?? 0) * $jobCard->grand_total_qty,
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
                // $this->syncStockDeduction($jobCard, $request->fabrics ?? []);

                $this->generateProductionConsumables($jobCard);

                DB::commit();
                return redirect('job_card_entries')->with('success', 'Job Card saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('danger', 'Error: ' . $e->getMessage());
            }
        }

        $jobCard = $id ? JobCardEntry::with(['cuttingSizeRatios', 'images', 'sizeRatio', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'fabricDetails.layMarks', 'issueItems', 'sleeveMeters', 'operations'])->findOrFail($id) : null;

        $grnImageMap = [];
        if ($jobCard && $jobCard->fabricDetails->isNotEmpty()) {
            $grnImageMap = $this->getGrnImageMapForArtNos($jobCard->fabricDetails->pluck('art_no')->all());
        }
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

        $plants = ServiceProvider::active()->orderBy('id', 'desc')->get();
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
        $hasIssuedItems = $jobCard ? $jobCard->issueItems()->exists() : false;
        return view('job_card_entry/add', compact(
            'jobCard',
            'grnImageMap',
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
            'hasIssuedItems',
            'colors'
        ));
    }
    public function view_details($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details job-card')) {
            return unauthorizedRedirect();
        }
        $jobCard = JobCardEntry::with(['purchaseOrder.items.rawMaterial', 'brand', 'sizeRatio', 'season', 'processGroup', 'cuttingSizeRatios', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'images', 'sleeveMeters', 'fit', 'pattiType', 'collarType', 'cuffType', 'pocketType', 'bottomCut', 'fabricType', 'issueItems.stockEntryItem', 'issueItems.rawMaterial'])->findOrFail($id);

        $maps = $this->getJobCardMaps($jobCard);
        $artMaterialMap = $maps['artMaterialMap'];
        $artCategoryMap = $maps['artCategoryMap'];

        $this->hydrateJobCardGrnImages($jobCard);

        return view('job_card_entry/view_details', compact('jobCard', 'artMaterialMap', 'artCategoryMap'));
    }

    private function getJobCardMaps($jobCard)
    {
        $artMaterialMap = [];
        $artCategoryMap = [];

        $fabricArtNos = $jobCard->fabricDetails->pluck('art_no')->map(fn($a) => trim($a))->unique()->toArray();

        if ($jobCard->purchase_order_id) {
            $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
            $grnItems = GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
                $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
            })->with(['purchaseInvoiceItem.rawMaterial', 'fabricType', 'purchaseInvoiceItem.purchaseOrderItem'])->get();

            foreach ($grnItems as $item) {
                $trimmedArtNo = trim($item->art_no);
                $name = $item->purchaseInvoiceItem->rawMaterial->name ?? ($item->fabricType->operation_stage_name ?? ($item->fabricType->name ?? null));
                if ($name && !isset($artMaterialMap[$trimmedArtNo])) {
                    $artMaterialMap[$trimmedArtNo] = $name;
                }
                if (!isset($artCategoryMap[$trimmedArtNo])) {
                    $artCategoryMap[$trimmedArtNo] = $item->purchaseInvoiceItem->purchaseOrderItem->store_category_id
                        ?? ($item->purchaseInvoiceItem->rawMaterial->store_category_id
                            ?? ($item->purchaseInvoiceItem->store_category_id ?? 1));
                }
            }

            if ($jobCard->purchaseOrder && $jobCard->purchaseOrder->items) {
                foreach ($jobCard->purchaseOrder->items as $poItem) {
                    $artNo = trim($poItem->rawMaterial->code ?? '');
                    if (!$artNo)
                        continue;

                    if (!isset($artMaterialMap[$artNo])) {
                        $artMaterialMap[$artNo] = $poItem->rawMaterial->name ?? null;
                    }
                    if (!isset($artCategoryMap[$artNo])) {
                        $artCategoryMap[$artNo] = $poItem->store_category_id ?? ($poItem->rawMaterial->store_category_id ?? 1);
                    }
                }
            }
        }

        $missingArtNos = array_diff($fabricArtNos, array_keys($artCategoryMap));
        if (!empty($missingArtNos)) {
            $otherGrnItems = GrnEntryItem::whereIn('art_no', $missingArtNos)->with(['purchaseInvoiceItem.rawMaterial', 'purchaseInvoiceItem.purchaseOrderItem'])->orderBy('id', 'desc')->get();
            foreach ($otherGrnItems as $item) {
                $trimmedArtNo = trim($item->art_no);
                if (!isset($artCategoryMap[$trimmedArtNo])) {
                    $artCategoryMap[$trimmedArtNo] = $item->purchaseInvoiceItem->purchaseOrderItem->store_category_id
                        ?? ($item->purchaseInvoiceItem->rawMaterial->store_category_id
                            ?? ($item->purchaseInvoiceItem->store_category_id ?? 1));
                }
            }

            foreach ($missingArtNos as $mArtNo) {
                if (!isset($artCategoryMap[$mArtNo])) {
                    $rm = RawMaterial::where('code', $mArtNo)->orWhere('name', $mArtNo)->first();
                    if ($rm) {
                        $artCategoryMap[$mArtNo] = $rm->store_category_id ?? 1;
                        if (!isset($artMaterialMap[$mArtNo]))
                            $artMaterialMap[$mArtNo] = $rm->name;
                    }
                }
            }
        }

        return ['artMaterialMap' => $artMaterialMap, 'artCategoryMap' => $artCategoryMap];
    }

    public function view_jc_item($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('issue-item job-card')) {
            return unauthorizedRedirect();
        }
        $jobCard = JobCardEntry::with(['brand', 'issueStore', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'purchaseOrder.items.rawMaterial.uom', 'purchaseOrder.supplier', 'purchaseOrder.items.uom', 'purchaseOrder.items.brand', 'purchaseOrder.items.style', 'issueItems', 'sleeveMeters', 'operations'])->findOrFail($id);
        $issueItemMap = $jobCard->issueItems->keyBy('job_card_article_matrix_id');
        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
        $grnItems = GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
            $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
        })->with(['purchaseInvoiceItem.rawMaterial.uom', 'purchaseInvoiceItem.uom', 'purchaseInvoiceItem.purchaseOrderItem.fabricWidth', 'fabricType', 'storeLocation'])->get();

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

                if ($si->rawMaterial) {
                    $artCategoryMap[$si->art_no] = $si->rawMaterial->store_category_id;
                    $artRawMaterialIdMap[$si->art_no] = $si->rawMaterial->id;
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

            $rm = $item->purchaseInvoiceItem->rawMaterial ?? null;
            if ($rm) {
                $artCategoryMap[$item->art_no] = $rm->store_category_id;
                $artRawMaterialIdMap[$item->art_no] = $rm->id;
            }
        }

        $allArtNos = array_unique(array_merge(
            array_keys($artMaterialMap),
            $jobCard->fabricDetails->pluck('art_no')->toArray()
        ));

        $missingArtNos = array_diff($allArtNos, array_keys($artCategoryMap));
        if (!empty($missingArtNos)) {
            $fallbackRms = RawMaterial::whereIn('code', $missingArtNos)->orWhereIn('name', $missingArtNos)->get();
            foreach ($fallbackRms as $rm) {
                if (!isset($artCategoryMap[$rm->code]))
                    $artCategoryMap[$rm->code] = $rm->store_category_id;
                if (!isset($artRawMaterialIdMap[$rm->code]))
                    $artRawMaterialIdMap[$rm->code] = $rm->id;
                if (!isset($artCategoryMap[$rm->name]))
                    $artCategoryMap[$rm->name] = $rm->store_category_id;
                if (!isset($artRawMaterialIdMap[$rm->name]))
                    $artRawMaterialIdMap[$rm->name] = $rm->id;
            }
        }

        $standardConsumptions = StandardConsumption::whereIn('raw_material_id', array_values($artRawMaterialIdMap))->active()->get()->keyBy('raw_material_id');

        return view('job_card_entry/view_jc_item', compact('jobCard', 'artMaterialMap', 'artLocationMap', 'artUomMap', 'artPriceMap', 'issueItemMap', 'artCategoryMap', 'artRawMaterialIdMap', 'standardConsumptions'));
    }

    public function issue_items(Request $request, $id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('issue-item job-card')) {
            return unauthorizedRedirect();
        }
        $jobCard = JobCardEntry::with(['purchaseOrder.items.style', 'purchaseOrder.items.rawMaterial', 'item.style', 'brand'])->findOrFail($id);
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
                    foreach ($request->items as $matrixId => $itemData) {
                        $matrix = JobCardFabricDetail::find($matrixId);
                        if (!$matrix)
                            continue;
                        $artNo = trim($matrix->art_no ?? '');

                        $qtyIssue = floatval($itemData['qty_issue'] ?? 0);
                        $qtyUsed = floatval($itemData['qty_used'] ?? 0);
                        $qtyAdjusted = floatval($itemData['qty_adjusted'] ?? 0);
                        $qtyWastage = floatval($itemData['qty_wastage'] ?? 0);
                        $totalToDeduct = $qtyUsed + $qtyAdjusted + $qtyWastage;
                        $totalIssuedFabric += $qtyIssue;

                        $existingItem = $existingItems->first(function ($item) use ($matrixId) {
                            return $item->job_card_article_matrix_id == $matrixId;
                        });

                        // 1. Revert previous deduction if updating
                        if ($existingItem) {
                            $oldQtyToDeduct = floatval($existingItem->qty_used ?? 0) + floatval($existingItem->qty_adjusted ?? 0);
                            if ($oldQtyToDeduct > 0) {
                                $qtyToRevert = $oldQtyToDeduct;

                                // Try to revert from the exact StockEntryItem if stored
                                if ($existingItem->stock_entry_item_id) {
                                    $stockItem = StockEntryItem::find($existingItem->stock_entry_item_id);
                                    if ($stockItem) {
                                        $revert = min($stockItem->qty_out, $qtyToRevert);
                                        $stockItem->qty_out = (float) ($stockItem->qty_out ?? 0) - $revert;
                                        $stockItem->save();
                                        $qtyToRevert -= $revert;
                                    }
                                }

                                if ($qtyToRevert > 0 && $artNo) {
                                    // Fallback: revert remaining from other items of same art_no
                                    $revQuery = StockEntryItem::whereRaw('qty_out > 0')->orderBy('id', 'desc');
                                    if ($jobCard->purchase_order_id)
                                        $revQuery->whereIn('grn_entry_item_id', $grnItemIds);
                                    $revQuery->whereHas('grnEntryItem', function ($q) use ($artNo) {
                                        $q->where('art_no', $artNo); });

                                    $revItems = $revQuery->get();
                                    foreach ($revItems as $ri) {
                                        if ($qtyToRevert <= 0)
                                            break;
                                        $take = min($ri->qty_out, $qtyToRevert);
                                        $ri->qty_out -= $take;
                                        $ri->save();
                                        $qtyToRevert -= $take;
                                    }
                                }
                            }
                        }

                        // 2. Perform new deduction
                        $unitPrice = 0;
                        $firstStockItemId = null;
                        if ($artNo && $totalToDeduct > 0) {
                            $query = StockEntryItem::whereRaw('(qty_in - COALESCE(qty_out, 0)) > 0')->orderBy('id', 'asc');
                            if ($jobCard->purchase_order_id) {
                                $query->whereIn('grn_entry_item_id', $grnItemIds);
                            }

                            $query->where(function ($q) use ($artNo) {
                                $q->whereHas('grnEntryItem', function ($q2) use ($artNo) {
                                    $q2->where('art_no', $artNo); })
                                    ->orWhereHas('rawMaterial', function ($q2) use ($artNo) {
                                        $q2->where('code', $artNo)->orWhere('name', $artNo); })
                                    ->orWhereHas('grnEntryItem.purchaseInvoiceItem.purchaseOrderItem', function ($q2) use ($artNo) {
                                        $q2->where('art_no', $artNo); });
                            });

                            $stockCandidates = $query->get();
                            $remainingToDeduct = $totalToDeduct;
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

                                if (!$firstStockItemId)
                                    $firstStockItemId = $stockItem->id;
                                $weightedCost += ($take * $stockItem->price);
                                $remainingToDeduct = round($remainingToDeduct - $take, 4);
                            }

                            /* 
                            if ($remainingToDeduct > 0.001) {
                                throw new \Exception("Insufficient stock for Art No: $artNo. Shortage: " . $remainingToDeduct);
                            }
                            */
                            $actualDeducted = round($totalToDeduct - $remainingToDeduct, 4);
                            $unitPrice = ($actualDeducted > 0) ? ($weightedCost / $actualDeducted) : 0;
                        }

                        if (isset($itemData['is_manual_price']) && $itemData['is_manual_price'] == 1 && isset($itemData['unit_price'])) {
                            $unitPrice = floatval($itemData['unit_price']);
                        }

                        $producedQty = floatval($itemData['produced_qty'] ?? 0);
                        $totalCost = $totalToDeduct * $unitPrice;
                        $costPerPc = ($producedQty > 0) ? ($totalCost / $producedQty) : 0;

                        // 3. Generate Barcode and QR Data
                        preg_match('/([a-zA-Z]*)(\d+)(?:-(\d+))?/', $artNo, $matches);
                        $numericBase = $matches[2] ?? '';
                        $suffix = $matches[3] ?? '1';
                        $formattedSuffix = str_pad($suffix, 2, '0', STR_PAD_LEFT);
                        $formattedSize = "00"; // Raw materials usually don't have size-wise barcodes here
                        $sleeveCode = "00";

                        $barcodeNo = 'BC' . $numericBase . $formattedSuffix . $formattedSize . $sleeveCode;

                        $qrData = [
                            'sku' => $barcodeNo,
                            'jc_no' => $jobCard->job_card_no,
                            'art_no' => $artNo,
                            'material' => $artMaterialMap[$artNo] ?? $artNo,
                            'qty' => $qtyUsed,
                            'uom' => $artUomMap[$artNo] ?? '',
                            'brand' => $jobCard->brand->brand_name ?? '',
                            'date' => date('Y-m-d')
                        ];

                        $data = [
                            'job_card_entry_id' => $jobCard->id,
                            'job_card_article_matrix_id' => $matrixId,
                            'qty_issue' => $qtyIssue,
                            'qty_adjusted' => $qtyAdjusted,
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
                            'barcode_no' => $barcodeNo,
                            'qrcode_data' => json_encode($qrData),
                        ];

                        if ($existingItem) {
                            $existingItem->update($data);
                        } else {
                            $data['created_by'] = auth()->id();
                            JobCardIssueItem::create($data);
                        }

                        // 4. Automatically create/update BarcodeMaster records for all planned sizes/sleeves
                        $matrixQuantities = JobCardMatrixQuantity::where('job_card_fabric_detail_id', $matrixId)->get();
                        $brand = $jobCard->brand;

                        // Get style from the stock deduction chain (Stock -> GRN -> PI -> PO)
                        $style = null;
                        if ($firstStockItemId) {
                            $stockItem = StockEntryItem::with(['grnEntryItem.purchaseInvoiceItem.purchaseOrderItem.style'])->find($firstStockItemId);
                            $style = $stockItem->grnEntryItem->purchaseInvoiceItem->purchaseOrderItem->style ?? null;
                        }

                        if (!$style) {
                            $style = $jobCard->item->style ?? null;
                        }

                        foreach ($matrixQuantities as $mq) {
                            $sizeCode = is_numeric($mq->size) ? str_pad($mq->size, 2, '0', STR_PAD_LEFT) : '00';

                            // Create/Update for Full Sleeve (F/S)
                            if ($mq->qty_fs > 0) {
                                $barcodeFS = 'BC' . $numericBase . $formattedSuffix . $sizeCode . '01';
                                BarcodeMaster::updateOrCreate(
                                    ['barcode_no' => $barcodeFS],
                                    [
                                        'item_code' => implode('-', array_filter([$brand->code ?? '', $style->code ?? '', 'F/S'])),
                                        'art_no' => $artNo,
                                        'item_name' => trim(($brand->brand_name ?? '') . ' ' . ($style->style_name ?? '') . ' F/S'),
                                        'sleeve_type' => 'F/S',
                                        'size' => $mq->size,
                                        'quantity' => $mq->qty_fs,
                                        'brand_id' => $jobCard->brand_id,
                                        'style_id' => $style->id ?? null,
                                        'lot_no' => $jobCard->job_card_no,
                                        'color_id' => $mq->color_id,
                                        'fabric_type_id' => $jobCard->fabric_type_id
                                    ]
                                );
                            }

                            // Create/Update for Half Sleeve (H/S)
                            if ($mq->qty_hs > 0) {
                                $barcodeHS = 'BC' . $numericBase . $formattedSuffix . $sizeCode . '02';
                                BarcodeMaster::updateOrCreate(
                                    ['barcode_no' => $barcodeHS],
                                    [
                                        'item_code' => implode('-', array_filter([$brand->code ?? '', $style->code ?? '', 'H/S'])),
                                        'art_no' => $artNo,
                                        'item_name' => trim(($brand->brand_name ?? '') . ' ' . ($style->style_name ?? '') . ' H/S'),
                                        'sleeve_type' => 'H/S',
                                        'size' => $mq->size,
                                        'quantity' => $mq->qty_hs,
                                        'brand_id' => $jobCard->brand_id,
                                        'style_id' => $style->id ?? null,
                                        'lot_no' => $jobCard->job_card_no,
                                        'color_id' => $mq->color_id,
                                        'fabric_type_id' => $jobCard->fabric_type_id
                                    ]
                                );
                            }
                        }

                        $updatedItems[$matrixId] = [
                            'id' => $existingItem ? $existingItem->id : JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->where('job_card_article_matrix_id', $matrixId)->first()->id,
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

                $groupKey = 'art|' . $item->art_no;

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

    private function syncStockDeduction($jobCard, $fabrics)
    {
        $seIds = [];
        if (request()->stock_entry_ids) {
            foreach (request()->stock_entry_ids as $cid) {
                $seIds[] = strpos($cid, '::') !== false ? explode('::', $cid)[0] : $cid;
            }
        }

        foreach ($fabrics as $index => $fabric) {
            $artNo = $fabric['art_no'] ?? null;
            $qtyUsed = floatval($fabric['mtr'] ?? 0);
            if (!$artNo)
                continue;

            $fabricDetail = $jobCard->fabricDetails()->where('art_no', $artNo)->first();
            if (!$fabricDetail)
                continue;

            $issueItem = JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->where('job_card_article_matrix_id', $fabricDetail->id)->first();

            $oldQtyUsed = $issueItem ? $issueItem->qty_used : 0;
            if ($issueItem && $issueItem->stock_entry_item_id) {
                $oldStockItem = StockEntryItem::find($issueItem->stock_entry_item_id);
                if ($oldStockItem) {
                    $oldStockItem->qty_out = (float) ($oldStockItem->qty_out ?? 0) - $oldQtyUsed;
                    $oldStockItem->save();
                }
            }

            $firstStockItemId = null;
            $weightedCost = 0;
            $remainingToDeduct = $qtyUsed;

            if ($remainingToDeduct > 0 && !empty($seIds)) {
                $stockCandidates = StockEntryItem::whereIn('stock_entry_id', $seIds)->where('art_no', $artNo)->get();

                foreach ($stockCandidates as $stockItem) {
                    if ($remainingToDeduct <= 0)
                        break;

                    $available = $stockItem->qty_in - (float) ($stockItem->qty_out ?? 0);
                    if ($available <= 0)
                        continue;

                    $take = min($available, $remainingToDeduct);
                    $stockItem->qty_out = (float) ($stockItem->qty_out ?? 0) + $take;
                    $stockItem->save();

                    if (!$firstStockItemId)
                        $firstStockItemId = $stockItem->id;
                    $weightedCost += ($take * $stockItem->price);
                    $remainingToDeduct = round($remainingToDeduct - $take, 4);
                }

                if ($remainingToDeduct > 0.001) {
                    throw new \Exception("Insufficient stock for $artNo! Shortage: $remainingToDeduct. Please adjust your Stock Entry selection.");
                }
            }

            $unitPrice = ($qtyUsed > 0) ? ($weightedCost / $qtyUsed) : 0;
            $producedQty = $jobCard->grand_total_qty ?? 0;
            $totalCost = $qtyUsed * $unitPrice;
            $costPerPc = ($producedQty > 0) ? ($totalCost / $producedQty) : 0;

            $issueData = [
                'job_card_entry_id' => $jobCard->id,
                'job_card_article_matrix_id' => $fabricDetail->id,
                'qty_issue' => $qtyUsed,
                'qty_used' => $qtyUsed,
                'unit_price' => $unitPrice,
                'total_cost' => $totalCost,
                'produced_qty' => $producedQty,
                'cost_per_pc' => $costPerPc,
                'stock_entry_item_id' => $firstStockItemId,
                'raw_material_id' => ($firstStockItemId ? StockEntryItem::find($firstStockItemId)->raw_material_id : null),
                'updated_by' => auth()->id()
            ];

            if ($issueItem) {
                $issueItem->update($issueData);
            } else {
                $issueData['created_by'] = auth()->id();
                JobCardIssueItem::create($issueData);
            }
        }

        $this->updateJobCardOverallPricing($jobCard);
    }

    private function updateJobCardOverallPricing($jobCard)
    {
        $totalFabricMtr = $jobCard->fabricDetails()->sum('mtr');
        $grandTotalQty = $jobCard->grand_total_qty ?? 0;
        $overallAverage = ($grandTotalQty > 0) ? ($totalFabricMtr / $grandTotalQty) : 0;

        $totalPrice = $jobCard->issueItems()->sum('cost_per_pc');

        $jobCard->update([
            'average' => $overallAverage,
            'price_fs' => $totalPrice,
            'price_hs' => 0
        ]);
    }

    public function getStockEntryDetails(Request $request)
    {
        $ids = $request->input('ids', []);
        $jobCardId = $request->input('job_card_id');

        if (empty($ids)) {
            return response()->json(['art_data' => [], 'art_numbers' => []]);
        }

        $issuedQtys = collect();
        if ($jobCardId) {
            $issuedQtys = JobCardIssueItem::where('job_card_entry_id', $jobCardId)
                ->with(['fabricDetail', 'rawMaterial'])
                ->get()
                ->groupBy(function ($item) {
                    return trim($item->fabricDetail->art_no ?? ($item->rawMaterial->code ?? ''));
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

        $stockItems = StockEntryItem::whereIn('stock_entry_id', $stockEntryIds)->with(['rawMaterial.uom', 'storeCategory', 'uom', 'grnEntryItem.purchaseInvoiceItem.fabricWidth'])->get();

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
        $filteredItems = $filteredItems->sortBy(function ($item) use ($idOrderMap, $filters) {
            $seId = (string) $item->stock_entry_id;
            if (isset($filters[$seId])) {
                foreach ($filters[$seId] as $f) {
                    if (
                        ($f['type'] === 'rm' && $item->raw_material_id == $f['val']) ||
                        ($f['type'] === 'item' && $item->item_id == $f['val']) ||
                        ($f['type'] === 'art' && $item->art_no == $f['val'])
                    ) {
                        $combinedId = $seId . '::' . $f['type'] . '|' . $f['val'];
                        if (isset($idOrderMap[$combinedId]))
                            return $idOrderMap[$combinedId];
                    }
                }
            }

            return $idOrderMap[$seId] ?? 999;
        });

        $allArtNos = $filteredItems->pluck('art_no')->unique()->toArray();
        $savedArtNos = $issuedQtys->keys()->toArray();
        $finalArtNos = array_values(array_unique(array_merge($allArtNos, $savedArtNos)));

        $groupedItems = $filteredItems->groupBy('art_no');

        $artData = collect($finalArtNos)->map(function ($artNo) use ($groupedItems, $issuedQtys) {
            $items = $groupedItems->get($artNo) ?? collect();
            $firstItem = $items->first();

            $rawMaterial = $firstItem ? $firstItem->rawMaterial : RawMaterial::where('code', $artNo)->first();

            $uomCode = null;
            if ($firstItem && $firstItem->uom) {
                $uomCode = $firstItem->uom->uom_code;
            } elseif ($rawMaterial && $rawMaterial->uom) {
                $uomCode = $rawMaterial->uom->uom_code;
            }

            $isEdit = request()->job_card_id ? true : false;
            $netQty = $items->sum(function ($item) use ($isEdit) {
                return $isEdit ? ($item->qty_in ?? 0) : (($item->qty_in ?? 0) - ($item->qty_out ?? 0));
            });

            $alreadyIssued = (float) ($issuedQtys[$artNo] ?? 0);

            return [
                'art_no' => $artNo,
                'art_name' => $rawMaterial ? $rawMaterial->name : null,
                'mtr' => $netQty,
                'already_issued' => $alreadyIssued,
                'total_available' => $isEdit ? $netQty : ($netQty + $alreadyIssued),
                'uom_code' => $uomCode,
                'store_category_id' => $rawMaterial ? $rawMaterial->store_category_id : 1,
                'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                'fabric_type_id' => $firstItem ? ($firstItem->fabric_type_id ?? ($firstItem->grnEntryItem->fabric_type_id ?? null)) : null,
                'width' => $firstItem && $firstItem->grnEntryItem && $firstItem->grnEntryItem->purchaseInvoiceItem && $firstItem->grnEntryItem->purchaseInvoiceItem->fabricWidth ? $firstItem->grnEntryItem->purchaseInvoiceItem->fabricWidth->width : null,
                'fs_cons' => $rawMaterial && $rawMaterial->standardConsumption ? $rawMaterial->standardConsumption->fs_qty : null,
                'hs_cons' => $rawMaterial && $rawMaterial->standardConsumption ? $rawMaterial->standardConsumption->hs_qty : null,
            ];
        })->values();
        $artData = $this->attachGrnImagesToArtData($artData);
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
        $po = PurchaseOrder::with(['items.fabricWidth'])->find($id);
        if (!$po)
            return response()->json(['error' => 'PO not found'], 404);
        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $po->id)->pluck('id');
        $grns = GrnEntry::whereIn('purchase_invoice_id', $invoiceIds)->get();
        $grnIds = $grns->pluck('id');
        $artData = GrnEntryItem::whereIn('grn_entry_id', $grnIds)->with(['purchaseInvoiceItem.uom', 'purchaseInvoiceItem.rawMaterial.uom', 'purchaseInvoiceItem.fabricWidth', 'grnEntry', 'stockEntryItems'])
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
                    'grn_no' => $firstItem->grnEntry->grn_number ?? null,
                    'width' => $firstItem->purchaseInvoiceItem->fabricWidth->width ?? null
                ];
            })->values();

        if ($artData->isEmpty()) {
            $po->load(['items.uom', 'items.rawMaterial.uom']);
            $artData = collect($po->items)->map(function ($item) use ($grnIds) {
                $item->load('fabricWidth');
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
                    'grn_no' => $item->grn_no,
                    'width' => $item->fabricWidth->width ?? null
                ];
            })->unique('art_no')->values();
        }
        $artData = $this->attachGrnImagesToArtData($artData);
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
                ->with(['fabricDetail', 'rawMaterial'])
                ->get()
                ->groupBy(function ($item) {
                    return trim($item->fabricDetail->art_no ?? ($item->rawMaterial->code ?? ''));
                })
                ->map(function ($items) {
                    return $items->sum('qty_issue');
                });
        }

        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $po->id)->pluck('id');
        $grnIds = GrnEntry::whereIn('purchase_invoice_id', $invoiceIds)->pluck('id');

        $artData = GrnEntryItem::whereIn('grn_entry_id', $grnIds)->with(['purchaseInvoiceItem.rawMaterial', 'purchaseInvoiceItem.fabricWidth', 'grnEntry', 'stockEntryItems'])->get()->groupBy('art_no')->map(function ($items, $artNo) use ($issuedQtys, $jobCardId) {
            $firstItem = $items->first();
            $rawMaterial = $firstItem->purchaseInvoiceItem->rawMaterial ?? null;
            $catId = $rawMaterial ? $rawMaterial->store_category_id : 1;

            $netStock = $items->flatMap(function ($item) {
                return $item->stockEntryItems;
            })->sum(function ($stockItem) use ($jobCardId) {
                return $jobCardId ? ($stockItem->qty_in ?? 0) : (($stockItem->qty_in ?? 0) - ($stockItem->qty_out ?? 0));
            });

            $alreadyIssued = (float) ($issuedQtys[$artNo] ?? 0);

            return [
                'art_no' => $artNo,
                'art_name' => $rawMaterial->name ?? ($firstItem->fabricType->name ?? null),
                'mtr' => (float) $netStock,
                'already_issued' => $alreadyIssued,
                'total_available' => $jobCardId ? (float) $netStock : ((float) $netStock + $alreadyIssued),
                'store_category_id' => $catId,
                'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                'grn_no' => $firstItem->grnEntry->grn_number ?? null,
                'uom_code' => $rawMaterial->uom->uom_code ?? null,
                'width' => $firstItem->purchaseInvoiceItem->fabricWidth->width ?? null
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
                        })->with('stockEntryItems')->get()
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
                    'uom_code' => $item->rawMaterial->uom->uom_code ?? null,
                    'width' => $item->fabricWidth->width ?? null
                ];
            })->unique('art_no')->values();
        }
        $artData = $this->attachGrnImagesToArtData($artData);
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
            'issueItems.fabricDetail.quantities',
            'issueItems.rawMaterial',
            'issueItems.stockEntryItem.grnEntryItem.purchaseInvoiceItem.purchaseOrderItem.fabricWidth'
        ])->findOrFail($id);

        $maps = $this->getJobCardMaps($jobCard);
        $artCategoryMap = $maps['artCategoryMap'];

        $artTotalMap = [];
        foreach ($jobCard->fabricDetails as $detail) {
            $trimmedArtNo = trim($detail->art_no ?? '');

            if (($artCategoryMap[$trimmedArtNo] ?? 1) != 1) {
                continue;
            }

            $total = $detail->quantities->sum('total_qty');
            if (!isset($artTotalMap[$trimmedArtNo])) {
                $artTotalMap[$trimmedArtNo] = 0;
            }
            $artTotalMap[$trimmedArtNo] += $total;
        }

        $issueItems = $jobCard->issueItems->filter(function ($item) use ($artCategoryMap) {
            $artNo = trim($item->fabricDetail->art_no ?? '');
            return ($item->rawMaterial?->store_category_id == 1) || (($artCategoryMap[$artNo] ?? 1) == 1);
        })->groupBy(function ($item) {
            return trim($item->fabricDetail->art_no ?? ($item->rawMaterial?->code ?? 'N/A'));
        })->map(function ($items, $artNo) use ($artTotalMap) {
            $stockUnitPrice = $items->map(function ($item) {
                return $item->stockEntryItem->price ?? null;
            })->filter(function ($price) {
                return $price !== null && $price > 0;
            })->avg();

            $sizeLabel = $items->map(function ($item) {
                return $item->stockEntryItem?->grnEntryItem?->purchaseInvoiceItem?->purchaseOrderItem?->fabricWidth?->width;
            })->filter(function ($width) {
                return !is_null($width) && $width !== '';
            })->first();

            return (object) [
                'art_no' => $artNo,
                'raw_material_id' => $items->pluck('raw_material_id')->filter()->first(),
                'produced_qty' => $artTotalMap[trim($artNo)] ?? $items->max('produced_qty'),
                'qty_issue' => $items->sum('qty_issue'),
                'qty_wastage' => $items->sum('qty_wastage'),
                'qty_used' => $items->sum('qty_used'),
                'qty_adjusted' => $items->sum('qty_adjusted'),
                'balance' => $items->sum('balance'),
                'unit_price' => $stockUnitPrice > 0 ? $stockUnitPrice : $items->average('unit_price'),
                'size_label' => $sizeLabel,
            ];
        })->values();
        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
        $grnItems = GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
            $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
        })->with(['purchaseInvoiceItem.rawMaterial.uom', 'purchaseInvoiceItem.uom', 'purchaseInvoiceItem.fabricWidth', 'purchaseInvoiceItem.purchaseOrderItem.fabricWidth', 'fabricType', 'storeLocation'])->get();

        $artMaterialMap = [];
        $artUomMap = [];
        $artLocationMap = [];

        foreach ($grnItems as $item) {
            $trimmedArtNo = trim($item->art_no ?? '');
            $purchaseOrderItem = $item->purchaseInvoiceItem->purchaseOrderItem ?? null;
            $name = $item->purchaseInvoiceItem->rawMaterial->name ?? ($item->fabricType->name ?? null);
            if ($name && !isset($artMaterialMap[$trimmedArtNo])) {
                $artMaterialMap[$trimmedArtNo] = $name;
            }
            if ($item->storeLocation && !isset($artLocationMap[$trimmedArtNo])) {
                $artLocationMap[$trimmedArtNo] = $item->storeLocation->store_location;
            }
            $uom = $item->purchaseInvoiceItem->uom->uom_code ?? ($item->purchaseInvoiceItem->rawMaterial->uom->uom_code ?? null);
            if ($uom && !isset($artUomMap[$trimmedArtNo])) {
                $artUomMap[$trimmedArtNo] = $uom;
            }
        }

        $pdf = Pdf::loadView('job_card_entry.fabric_consumption_pdf', compact('jobCard', 'issueItems', 'artMaterialMap', 'artUomMap', 'artLocationMap'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'Fabric_Consumption_' . str_replace(['/', '\\'], '_', $jobCard->job_card_no) . '.pdf';
        return $pdf->stream($filename);
    }

    public function accessoriesConsumptionPdf($id)
    {
        $jobCard = JobCardEntry::with([
            'brand',
            'fabricDetails.quantities',
            'purchaseOrder.items.rawMaterial.uom',
            'purchaseOrder.supplier',
            'purchaseOrder.items.uom',
            'purchaseOrder.items.brand',
            'issueItems.fabricDetail.quantities',
            'issueItems.rawMaterial',
            'issueItems.stockEntryItem.grnEntryItem.purchaseInvoiceItem.purchaseOrderItem.fabricWidth'
        ])->findOrFail($id);

        $maps = $this->getJobCardMaps($jobCard);
        $artCategoryMap = $maps['artCategoryMap'];

        $artTotalMap = [];
        foreach ($jobCard->fabricDetails as $detail) {
            $trimmedArtNo = trim($detail->art_no ?? '');

            if (($artCategoryMap[$trimmedArtNo] ?? 1) == 1) {
                continue;
            }

            $total = $detail->quantities->sum('total_qty');
            if (!isset($artTotalMap[$trimmedArtNo])) {
                $artTotalMap[$trimmedArtNo] = 0;
            }
            $artTotalMap[$trimmedArtNo] += $total;
        }

        $issueItems = $jobCard->issueItems->filter(function ($item) use ($artCategoryMap) {
            $artNo = trim($item->fabricDetail->art_no ?? '');
            return ($item->rawMaterial?->store_category_id != 1) && (($artCategoryMap[$artNo] ?? 1) != 1);
        })->groupBy(function ($item) {
            return trim($item->fabricDetail->art_no ?? ($item->rawMaterial?->code ?? 'N/A'));
        })->map(function ($items, $artNo) use ($jobCard) {
            $stockUnitPrice = $items->map(function ($item) {
                return $item->stockEntryItem->price ?? null;
            })->filter(function ($price) {
                return $price !== null && $price > 0;
            })->avg();

            return (object) [
                'art_no' => $artNo,
                'raw_material_id' => $items->pluck('raw_material_id')->filter()->first(),
                'produced_qty' => $jobCard->grand_total_qty,
                'qty_issue' => $items->sum('qty_issue'),
                'qty_wastage' => $items->sum('qty_wastage'),
                'qty_used' => $items->sum('qty_used'),
                'qty_adjusted' => $items->sum('qty_adjusted'),
                'balance' => $items->sum('balance'),
                'unit_price' => $stockUnitPrice > 0 ? $stockUnitPrice : $items->average('unit_price'),
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
            $trimmedArtNo = trim($item->art_no ?? '');
            $name = $item->purchaseInvoiceItem->rawMaterial->name ?? ($item->fabricType->name ?? null);
            if ($name && !isset($artMaterialMap[$trimmedArtNo])) {
                $artMaterialMap[$trimmedArtNo] = $name;
            }
            if ($item->storeLocation && !isset($artLocationMap[$trimmedArtNo])) {
                $artLocationMap[$trimmedArtNo] = $item->storeLocation->store_location;
            }
            $uom = $item->purchaseInvoiceItem->uom->uom_code ?? ($item->purchaseInvoiceItem->rawMaterial->uom->uom_code ?? null);
            if ($uom && !isset($artUomMap[$trimmedArtNo])) {
                $artUomMap[$trimmedArtNo] = $uom;
            }
        }

        $pdf = Pdf::loadView('job_card_entry.accessories_consumption_pdf', compact('jobCard', 'issueItems', 'artMaterialMap', 'artUomMap', 'artLocationMap'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'Accessories_Consumption_' . str_replace(['/', '\\'], '_', $jobCard->job_card_no) . '.pdf';
        return $pdf->stream($filename);
    }

    public function workOrderPdf($id)
    {
        $jobCard = JobCardEntry::with([
            'brand',
            'item',
            'serviceProvider', 'receiptStore', 
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

        $maps = $this->getJobCardMaps($jobCard);
        $artCategoryMap = $maps['artCategoryMap'];

        $pdf = Pdf::loadView('job_card_entry.work_order_pdf', compact('jobCard', 'artUomMap', 'artCategoryMap'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'Work_Order_' . str_replace(['/', '\\'], '_', $jobCard->job_card_no) . '.pdf';
        return $pdf->stream($filename);
    }

    public function viewDetailsPdf($id, $is_print = false)
    {
        $jobCard = JobCardEntry::with([
            'purchaseOrder.items.rawMaterial',
            'brand',
            'season',
            'processGroup',
            'cuttingSizeRatios',
            'fabricDetails.quantities',
            'fabricDetails.layMarks',
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
            'fabricType',
            'operations.stage',
            'operations.serviceProvider'
        ])->findOrFail($id);

        $maps = $this->getJobCardMaps($jobCard);
        $artMaterialMap = $maps['artMaterialMap'];
        $artCategoryMap = $maps['artCategoryMap'];
        $this->hydrateJobCardGrnImages($jobCard);

        if ($is_print) {
            return view('job_card_entry.view_details_pdf', compact('jobCard', 'is_print', 'artCategoryMap', 'artMaterialMap'));
        }

        $pdf = Pdf::loadView('job_card_entry.view_details_pdf', compact('jobCard', 'artCategoryMap', 'artMaterialMap'));
        $pdf->setPaper('A4', 'landscape');

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
                    $stockItem = \DB::table('stock_entry_items')->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')->where('grn_entry_items.art_no', $artNo)->select('stock_entry_items.raw_material_id', 'stock_entry_items.uom_id')->first();

                    $rawMaterialId = $stockItem->raw_material_id ?? null;
                    $uomId = $stockItem->uom_id ?? null;

                    if (!$rawMaterialId && $artNo) {
                        $rm = RawMaterial::where('code', $artNo)->orWhere('name', $artNo)->first();
                        if ($rm) {
                            $rawMaterialId = $rm->id;
                            $uomId = $uomId ?? $rm->uom_id;
                        }
                    }

                    $sleeveType = 'All';
                    if ($totalFsCons > 0 && $totalHsCons == 0)
                        $sleeveType = 'F/S';
                    elseif ($totalHsCons > 0 && $totalFsCons == 0)
                        $sleeveType = 'H/S';

                    if ($rawMaterialId) {
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

                    $issueArtNo = $stockInfo->art_no ?? null;
                    $issueRawMaterialId = $stockInfo->raw_material_id ?? ($issueItem->raw_material_id ?? null);
                    $issueUomId = $stockInfo->uom_id ?? null;

                    if (!$issueRawMaterialId && $issueArtNo) {
                        $rm = \App\Models\RawMaterial::where('code', $issueArtNo)->orWhere('name', $issueArtNo)->first();
                        if ($rm) {
                            $issueRawMaterialId = $rm->id;
                            $issueUomId = $issueUomId ?? $rm->uom_id;
                        }
                    }

                    $sleeveType = 'All';
                    if ($fsCons > 0 && $hsCons == 0)
                        $sleeveType = 'F/S';
                    elseif ($hsCons > 0 && $fsCons == 0)
                        $sleeveType = 'H/S';

                    if ($issueRawMaterialId) {
                        ProductionStageConsumable::create([
                            'job_card_id' => $jobCard->id,
                            'production_stage_id' => $stageId,
                            'stage' => $stageName,
                            'art_no' => $issueArtNo,
                            'item_type' => 'Consumable',
                            'raw_material_id' => $issueRawMaterialId,
                            'planned_qty' => $jobCard->grand_total_qty,
                            'fs_qty' => $fsCons,
                            'hs_qty' => $hsCons,
                            'total_qty' => $totalConsumption,
                            'actual_qty' => $totalConsumption,
                            'uom_id' => $issueUomId,
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
    public function costing_analysis($id)
    {
        $jobCard = JobCardEntry::with([
            'brand',
            'fabricDetails.quantities',
            'issueItems.stockEntryItem',
            'issueItems.rawMaterial',
            'operations.operationStage'
        ])->findOrFail($id);

        $totalProduced = (float) ($jobCard->grand_total_qty ?? 0);
        $totalFabricCost = 0;
        $fabricIssueItems = $jobCard->issueItems->filter(fn($i) => ($i->rawMaterial?->store_category_id == 1));

        if ($fabricIssueItems->count() > 0) {
            $totalFabricCost = $fabricIssueItems->sum(fn($i) => ($i->qty_used + $i->qty_wastage) * ($i->stockEntryItem->price ?? 0));
        } else {
            foreach ($jobCard->fabricDetails as $fd) {
                if ($fd->store_category_id == 1) {
                    $latestPrice = StockEntryItem::where('art_no', $fd->art_no)
                        ->where('price', '>', 0)
                        ->latest()
                        ->value('price') ?? 0;
                    $totalFabricCost += ($fd->mtr * $latestPrice);
                }
            }
        }

        $totalAccessoryCost = 0;
        $accessoryIssueItems = $jobCard->issueItems->filter(fn($i) => ($i->rawMaterial?->store_category_id != 1));

        if ($accessoryIssueItems->count() > 0) {
            $totalAccessoryCost = $accessoryIssueItems->sum(fn($i) => ($i->qty_used + $i->qty_wastage) * ($i->stockEntryItem->price ?? 0));
        }
        $totalProcessCost = $jobCard->operations->sum(function ($op) use ($totalProduced) {
            if ($op->total_cost > 0)
                return $op->total_cost;
            return ($op->rate ?? 0) * $totalProduced;
        });

        $grandTotalCost = $totalFabricCost + $totalAccessoryCost + $totalProcessCost;

        $analysis = [
            'total_produced' => $totalProduced,
            'fabric' => [
                'total' => $totalFabricCost,
                'avg' => $totalProduced > 0 ? $totalFabricCost / $totalProduced : 0
            ],
            'accessories' => [
                'total' => $totalAccessoryCost,
                'avg' => $totalProduced > 0 ? $totalAccessoryCost / $totalProduced : 0
            ],
            'process' => [
                'total' => $totalProcessCost,
                'avg' => $totalProduced > 0 ? $totalProcessCost / $totalProduced : 0
            ],
            'grand_total' => [
                'total' => $grandTotalCost,
                'avg' => $totalProduced > 0 ? $grandTotalCost / $totalProduced : 0
            ]
        ];

        return view('job_card_entry.costing_analysis', compact('jobCard', 'analysis'));
    }

    public function printLabel($id, Request $request)
    {
        $issueItem = JobCardIssueItem::with([
            'jobCard.brand',
            'jobCard.item.style',
            'stockEntryItem.grnEntryItem.purchaseInvoiceItem.purchaseOrderItem.style',
            'rawMaterial.uom',
            'stockEntryItem.grnEntryItem.color'
        ])->findOrFail($id);
        $jobCard = $issueItem->jobCard;
        $colorName = $issueItem->stockEntryItem->grnEntryItem->color->color_name ?? '-';
        $selectedSize = $request->size ?? 'Bulk';
        $selectedSleeve = $request->sleeve;

        $orientation = $request->orientation ?? 'portrait';
        $width = $request->width ?? 60;
        $height = $request->height ?? 135;
        $margin = $request->margin ?? 2;
        $bg_color = $request->bg_color ?? '#ffffff';
        $v_align = $request->v_align ?? 'top';
        $order = $request->order ?? 'header,product,brand,art,color,fabric,size,mrp,mfg,footer';

        $settings = Setting::first();
        $artNo = $issueItem->job_card_article_matrix_id ? JobCardFabricDetail::find($issueItem->job_card_article_matrix_id)->art_no : ($issueItem->rawMaterial->code ?? '');
        preg_match('/([a-zA-Z]*)(\d+)(?:-(\d+))?/', $artNo, $matches);
        $numericBase = $matches[2] ?? '';
        $suffix = $matches[3] ?? '1';
        $formattedSuffix = str_pad($suffix, 2, '0', STR_PAD_LEFT);
        $formattedSize = is_numeric($selectedSize) ? str_pad($selectedSize, 2, '0', STR_PAD_LEFT) : '00';

        $sleeveCode = '00';
        $sleeveText = '-';

        if ($selectedSleeve) {
            $sleeveText = $selectedSleeve;
            $sleeveCode = ($selectedSleeve == 'F/S') ? '01' : (($selectedSleeve == 'H/S') ? '02' : '00');
        } else {
            $sleeveTypes = [];
            if ($jobCard->total_qty_fs > 0) {
                $sleeveTypes[] = 'F/S';
                $sleeveCode = '01';
            }
            if ($jobCard->total_qty_hs > 0) {
                $sleeveTypes[] = 'H/S';
                if ($sleeveCode == '01')
                    $sleeveCode = '03';
                else
                    $sleeveCode = '02';
            }
            $sleeveText = implode(' & ', $sleeveTypes);
        }

        $barcodeNo = 'BC' . $numericBase . $formattedSuffix . $formattedSize . $sleeveCode;

        $labelData = [
            'company_name' => $settings->company_name ?? 'NACHIAS',
            'company_email' => $settings->email ?? 'info@nachias.com',
            'company_gstin' => $settings->gst_no ?? '',
            'company_address' => $settings->address ?? '-',
            'toll_free' => $settings->toll_free_no ?? '-',
            'phone_number' => $settings->phone_number ?? '',
            'working_days' => $settings->working_days ?? 'MONDAY - SATURDAY',
            'opening_time' => $settings->opening_time ? date('h A', strtotime($settings->opening_time)) : '10 AM',
            'closing_time' => $settings->closing_time ? date('h A', strtotime($settings->closing_time)) : '6 PM',
            'product_name' => $jobCard->item->name ?? 'SHIRTS',
            'brand_name' => $jobCard->brand->brand_name ?? '-',
            'design' => $artNo,
            'color' => $colorName,
            'fabric' => $issueItem->rawMaterial->name ?? '-',
            'size' => $selectedSize,
            'sleeve' => $sleeveText,
            'price' => number_format(($jobCard->mrp > 0 ? $jobCard->mrp : $issueItem->unit_price), 2),
            'mfg_date' => date('F Y'),
            'lot_no' => $jobCard->job_card_no,
            'sku' => $barcodeNo,
            'quantity' => $issueItem->qty_used . ' ' . ($issueItem->rawMaterial->uom->uom_code ?? '')
        ];

        $artNo = $issueItem->fabricDetail->art_no ?? ($issueItem->rawMaterial->code ?? '');

        $style = $issueItem->stockEntryItem->grnEntryItem->purchaseInvoiceItem->purchaseOrderItem->style ?? null;

        if (!$style) {
            $style = $jobCard->item->style ?? null;
        }

        $brandName = $jobCard->brand->brand_name ?? '';
        $styleName = $style->style_name ?? '';
        $brandCode = $jobCard->brand->code ?? '';
        $styleCode = $style->code ?? '';

        $sleeveShort = ($selectedSleeve == 'F/S' || $selectedSleeve == 'Full Sleeve') ? 'F/S' : (($selectedSleeve == 'H/S' || $selectedSleeve == 'Half Sleeve') ? 'H/S' : '');

        $customItemName = trim("$brandName $styleName $sleeveShort");
        $customItemCode = implode('-', array_filter([$brandCode, $styleCode, $sleeveShort]));

        BarcodeMaster::updateOrCreate(
            ['barcode_no' => $barcodeNo],
            [
                'item_code' => $customItemCode,
                'art_no' => $artNo,
                'item_name' => $customItemName,
                'sleeve_type' => $sleeveText,
                'size' => $selectedSize,
                'quantity' => $issueItem->qty_used,
                'brand_id' => $jobCard->brand_id,
                'style_id' => $style->id ?? null,
                'lot_no' => $jobCard->job_card_no,
                'color_id' => $issueItem->stockEntryItem->grnEntryItem->color_id ?? null,
                'fabric_type_id' => $jobCard->fabric_type_id
            ]
        );

        return view('labels.print_barcode', compact('labelData', 'orientation', 'width', 'height', 'margin', 'bg_color', 'v_align', 'order'));
    }

    public function barcodePreview($id, Request $request)
    {
        $issueItem = JobCardIssueItem::with(['jobCard.brand', 'jobCard.item.style', 'jobCard.purchaseOrder.items.style', 'jobCard.purchaseOrder.items.rawMaterial', 'rawMaterial.uom', 'stockEntryItem.grnEntryItem.color'])->findOrFail($id);
        $jobCard = $issueItem->jobCard;
        $selectedSize = $request->size ?? 'Bulk';
        $selectedSleeve = $request->sleeve;

        $artNo = $issueItem->job_card_article_matrix_id ? JobCardFabricDetail::find($issueItem->job_card_article_matrix_id)->art_no : ($issueItem->rawMaterial->code ?? '');

        $labelData = [
            'id' => $id,
            'product_name' => $jobCard->item->name ?? 'SHIRTS',
            'brand_name' => $jobCard->brand->brand_name ?? '-',
            'design' => $artNo,
            'color' => $issueItem->stockEntryItem->grnEntryItem->color->color_name ?? '-',
            'fabric' => $issueItem->rawMaterial->name ?? '-',
            'size' => $selectedSize,
            'sleeve' => $selectedSleeve ?? '-',
            'price' => number_format(($jobCard->mrp > 0 ? $jobCard->mrp : $issueItem->unit_price), 2),
            'mfg_date' => date('F Y'),
            'lot_no' => $jobCard->job_card_no,
            'sku' => ($issueItem->rawMaterial->code ?? '') . '-' . $selectedSize . '-' . ($selectedSleeve ?? ''),
            'company_name' => Setting::first()->company_name ?? 'NACHIAS',
            'company_email' => Setting::first()->email ?? 'info@nachias.com',
            'company_gstin' => Setting::first()->gst_no ?? '',
            'company_address' => Setting::first()->address ?? '-',
            'toll_free' => Setting::first()->toll_free_no ?? '-',
            'working_days' => Setting::first()->working_days ?? 'MONDAY - SATURDAY',
            'opening_time' => Setting::first()->opening_time ? date('h A', strtotime(Setting::first()->opening_time)) : '10 AM',
            'closing_time' => Setting::first()->closing_time ? date('h A', strtotime(Setting::first()->closing_time)) : '6 PM',
        ];

        return view('labels.barcode_preview', compact('labelData', 'jobCard'));
    }

    public function getSizes($id)
    {
        $issueItem = JobCardIssueItem::findOrFail($id);
        $jobCard = $issueItem->jobCard;
        $sizes = JobCardMatrixQuantity::whereIn('job_card_fabric_detail_id', $jobCard->fabricDetails->pluck('id'))->where('total_qty', '>', 0)->pluck('size')->unique()->sort()->values();
        return response()->json(['sizes' => $sizes]);
    }

    public function barcodeMatrix($id)
    {
        $currentIssueItem = JobCardIssueItem::findOrFail($id);
        $jobCard = $currentIssueItem->jobCard;

        $allIssueItems = JobCardIssueItem::with(['rawMaterial', 'stockEntryItem.grnEntryItem.color', 'fabricDetail.quantities'])
            ->where('job_card_entry_id', $jobCard->id)
            ->whereHas('rawMaterial', function ($query) {
                $query->where('store_category_id', 1);
            })
            ->get();

        $fabrics = [];
        foreach ($allIssueItems as $issueItem) {
            $fabricRecords = [];

            if ($issueItem->fabricDetail && $issueItem->fabricDetail->quantities->count() > 0) {
                foreach ($issueItem->fabricDetail->quantities as $mq) {
                    if ($mq->qty_fs > 0) {
                        $fabricRecords[] = [
                            'size' => $mq->size,
                            'sleeve' => 'F/S',
                            'qty' => $mq->qty_fs
                        ];
                    }
                    if ($mq->qty_hs > 0) {
                        $fabricRecords[] = [
                            'size' => $mq->size,
                            'sleeve' => 'H/S',
                            'qty' => $mq->qty_hs
                        ];
                    }
                }
            }

            $fabricRecords[] = [
                'size' => 'Bulk',
                'sleeve' => 'Common',
                'qty' => $issueItem->qty_used
            ];

            $fabrics[] = [
                'issueItem' => $issueItem,
                'records' => $fabricRecords,
                'is_current' => ($issueItem->id == $id)
            ];
        }

        return view('labels.barcode_matrix', compact('jobCard', 'fabrics'));
    }

    private function calculateNoOfDays(?string $issueDate, ?string $deliveryDate): ?int
    {
        if (!$issueDate || !$deliveryDate) {
            return null;
        }

        try {
            $issue = Carbon::createFromFormat('d-m-Y', $issueDate)->startOfDay();
            $delivery = Carbon::createFromFormat('d-m-Y', $deliveryDate)->startOfDay();

            if ($delivery->lessThan($issue)) {
                return null;
            }

            return $issue->diffInDays($delivery);
        } catch (\Exception $e) {
            return null;
        }
    }
    private function getGrnImageMapForArtNos(array $artNos): array
    {
        $normalizedArtNos = collect($artNos)->filter()->map(fn($artNo) => trim((string) $artNo))->unique()->values();
        if ($normalizedArtNos->isEmpty()) {
            return [];
        }

        $items = GrnEntryItem::whereNotNull('image')->where('image', '!=', '')->whereIn(DB::raw('TRIM(art_no)'), $normalizedArtNos->all())->orderByDesc('id')->get(['art_no', 'image']);

        $imageMap = [];
        foreach ($items as $item) {
            $artNo = trim((string) $item->art_no);
            if (!$artNo || isset($imageMap[$artNo])) {
                continue;
            }
            $imageMap[$artNo] = ['art_no' => $artNo, 'image' => $item->image, 'url' => url('uploads/grn_items/' . $item->image),];
        }

        return $imageMap;
    }

    private function hydrateJobCardGrnImages(JobCardEntry $jobCard): void
    {
        // Ensure GRN images are available even if job_card_fabric_details.grn_image is empty.
        $grnImageMap = $this->getGrnImageMapForArtNos($jobCard->fabricDetails->pluck('art_no')->all());
        foreach ($jobCard->fabricDetails as $detail) {
            $artNo = trim((string) ($detail->art_no ?? ''));
            $grnImage = $grnImageMap[$artNo] ?? null;
            if (!$grnImage) {
                continue;
            }

            if (empty($detail->grn_image)) {
                $detail->grn_image = $grnImage['image'] ?? null;
            }
            $detail->grn_image_url = $grnImage['url'] ?? null;
        }
    }

    private function getLatestGrnImageForArtNo(?string $artNo): ?string
    {
        $artNo = trim((string) $artNo);
        if ($artNo === '') {
            return null;
        }

        return GrnEntryItem::whereRaw('TRIM(art_no) = ?', [$artNo])->whereNotNull('image')->where('image', '!=', '')->orderByDesc('id')->value('image');
    }


    private function attachGrnImagesToArtData(Collection $artData): Collection
    {
        $imageMap = $this->getGrnImageMapForArtNos($artData->pluck('art_no')->all());

        return $artData->map(function ($row) use ($imageMap) {
            $artNo = trim((string) ($row['art_no'] ?? ''));
            $grnImage = $imageMap[$artNo] ?? null;
            $row['grn_image'] = $grnImage['image'] ?? null;
            $row['grn_image_url'] = $grnImage['url'] ?? null;
            return $row;
        });
    }

}
