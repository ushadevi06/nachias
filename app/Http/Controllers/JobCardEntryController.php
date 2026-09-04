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
            $query = JobCardEntry::with(['brand', 'season', 'processGroup', 'serviceProvider', 'fabricDetails.layMarks'])->orderBy('id', 'desc');

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
            
            if ($request->has('brand_id') && !empty($request->brand_id)) {
                $query->where('brand_id', $request->brand_id);
            }
            if ($request->has('season_id') && !empty($request->season_id)) {
                $query->where('season_id', $request->season_id);
            }
            if ($request->has('job_card_type') && !empty($request->job_card_type)) {
                $query->where('job_card_type', $request->job_card_type);
            }
            if ($request->has('status') && !empty($request->status)) {
                if ($request->status == 'In Progress') {
                    $query->where(function($q) {
                        $q->where('status', 'like', '%process%')->orWhere('status', 'like', '%production%');
                    });
                } elseif ($request->status == 'Hold') {
                    $query->where('status', 'like', '%hold%');
                } elseif ($request->status == 'Completed') {
                    $query->where('status', 'like', '%complet%');
                } else {
                    $query->where('status', $request->status);
                }
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
                $rawStatus = strtolower($jc->status);
                if (str_contains($rawStatus, 'hold')) {
                    $displayStatus = 'Hold';
                    $statusClass = 'bg-label-danger';
                } elseif (str_contains($rawStatus, 'complet')) {
                    $displayStatus = 'Completed';
                    $statusClass = 'bg-label-success';
                } else {
                    $displayStatus = 'Inprogress';
                    $statusClass = 'bg-label-warning';
                }
                
                $status = '<span class="badge ' . $statusClass . '">' . $displayStatus . '</span>';
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
                    $issueBadge = ($jc->additional_qty > 0) ? ' <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size: 10px;">+' . $jc->additional_qty . ' extra</span>' : '';
                    $action .= '<a href="' . url('job_card_entries/view-item/' . $jc->id) . '" class="dropdown-item d-flex align-items-center justify-content-between"><span class="d-flex align-items-center"><i class="icon-base ri ri-list-check-2 me-2"></i>Issue Item</span>' . $issueBadge . '</a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('issue-item job-card') || auth()->user()->can('edit job-card')) {
                    $addBadge = ($jc->additional_qty > 0) ? ' <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size: 10px;">+' . $jc->additional_qty . '</span>' : '';
                    $action .= '<a href="' . url('job_card_entries/additional-qty/' . $jc->id) . '" class="dropdown-item d-flex align-items-center justify-content-between"><span class="d-flex align-items-center"><i class="icon-base ri ri-add-circle-line me-2"></i>Additional Qty</span>' . $addBadge . '</a>';
                }

                $action .= '</div></div>';
                $jcTypeBadge = '<br><span class="badge bg-label-info mt-1" style="font-size: 10px;">' . ($jc->job_card_type ?? 'Regular') . '</span>';
                $totalMeter = 0;
                if ($jc->fabricDetails) {
                    foreach ($jc->fabricDetails as $fabric) {
                        if ($fabric->layMarks) {
                            foreach ($fabric->layMarks as $lm) {
                                $totalMeter += floatval($lm->lay_mark_meter);
                            }
                        }
                    }
                }


                $data[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'job_card_no' => $jc->job_card_no . $jcTypeBadge,
                    'plant' => $jc->serviceProvider->name ?? '-',
                    'job_card_date' => date('d-m-Y', strtotime($jc->job_card_date)),
                    'brand' => $jc->brand->brand_name ?? '-',
                    'season' => $jc->season->name ?? '-',
                    'process_group' => $jc->processGroup->name ?? '-',
                    'total_meter' => number_format($totalMeter, 2),
                    'total_qty' => ($jc->additional_qty > 0) ? ($jc->grand_total_qty . ' <br><span class="badge bg-label-warning text-dark" style="font-size: 10px;">(+' . $jc->additional_qty . ' extra)</span>') : $jc->grand_total_qty,
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
        $brands = \App\Models\Brand::active()->orderBy('brand_name', 'asc')->get();
        $seasons = \App\Models\Season::active()->orderBy('name', 'asc')->get();
        return view('job_card_entry/view', compact('brands', 'seasons'));
    }
    public function add(Request $request, $id = null)
    {
        $restrictedRoleIds = [4, 9, 12, 18, 28]; 
        $hasRestrictedRole = false;
        if (auth()->check() && auth()->id() != 1) {
            $hasRestrictedRole = in_array(auth()->user()->role_id, $restrictedRoleIds);
        }
        $canEdit = auth()->id() == 1 || auth()->user()->can('edit job-card');
        
        $isRestrictedEdit = false;
        
        if ($id) {
            if ($hasRestrictedRole) {
                $isRestrictedEdit = true;
            } elseif (!$canEdit) {
                return unauthorizedRedirect();
            }
        } else {
            if ($hasRestrictedRole) {
                return unauthorizedRedirect();
            }
            if (!$canEdit && !auth()->user()->can('create job-card')) {
                return unauthorizedRedirect();
            }
        }

        if ($request->isMethod('post')) {
            set_time_limit(600); 
            ini_set('memory_limit', '1024M'); 

            if ($isRestrictedEdit && $id) {
                $jobCard = JobCardEntry::with('issueItems.fabricDetail')->findOrFail($id);
                $oldData = $jobCard->toArray();
                
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
                
                $newData = $jobCard->fresh()->toArray();
                addLog('update', 'Job Card Entry', 'job_card_entries', $id, $oldData, $newData);
                
                return redirect('job_card_entries')->with('success', 'Production stages saved successfully');
            }
            $isCanvas = false;
            if ($request->filled('brand_id')) {
                $brand = \App\Models\Brand::find($request->input('brand_id'));
                if ($brand && in_array(strtoupper(trim($brand->brand_name)), ['CANVAS ACCESSORIES', 'CANVAS ACCESSORIES (CAS)'])) {
                    $isCanvas = true;
                }
            }

            $rules = [
                'job_card_no' => ['required', 'string', 'min:5', 'max:50', 'not_regex:/^0+$/', 'unique:job_card_entries,job_card_no' . ($id ? ',' . $id : '')],
                'purchase_order_id' => 'nullable|exists:purchase_orders,id',
                'service_provider_id' => 'required|exists:service_providers,id',
                'issue_store_id' => 'required|exists:store_types,id',
                'issue_date' => 'required|date_format:d-m-Y',
                'delivery_date' => 'required|date_format:d-m-Y',
                'washing' => 'nullable|in:Yes,No',
                'width' => 'nullable|exists:fabric_sizes,id',
                'mrp' => 'nullable|numeric',
                'total_qty_fs' => 'nullable|numeric',
                'total_qty_hs' => 'nullable|numeric',
                'season_id' => 'nullable|exists:seasons,id',
                'brand_id' => 'required|exists:brands,id',
                'receipt_store_id' => 'required|exists:store_types,id',
                'process_group_id' => 'required|exists:process_groups,id',
                'reference_no' => 'required|string|max:255|same:job_card_no',
                'status' => 'required|string',
                'job_card_type' => 'required|string|in:Regular,Urgent,Sample,Special Order',
                'remarks' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,csv|max:5120',
                'fit_id' => 'nullable|exists:fits,id',
                'patti_type_id' => 'nullable|exists:patti_types,id',
                'collar_type_id' => 'nullable|exists:collar_types,id',
                'cuff_type_id' => 'nullable|exists:cuff_types,id',
                'pocket_type_id' => 'nullable|exists:pocket_types,id',
                'bottom_cut_id' => 'nullable|exists:bottom_cuts,id',
                'stock_entry_ids' => 'nullable|array',
                'stock_entry_ids.*' => 'distinct',
                'sleeve_instances' => [
                    $isCanvas ? 'nullable' : 'required',
                    function ($attribute, $value, $fail) use ($isCanvas) {
                        if ($isCanvas) return;
                        $decoded = json_decode($value, true);
                        if (!$decoded || empty($decoded['instances'])) {
                            $fail('Please add at least one Sleeve Configuration.');
                        }
                    },
                ],
                'production_stages' => 'required|array|min:1',
                'production_stages.*.stage_id' => 'required|exists:operation_stages,id',
                'production_stages.*.service_provider_id' => $isCanvas ? 'nullable|exists:service_providers,id' : 'required|exists:service_providers,id',
                'production_stages.*.issue_date' => $isCanvas ? 'nullable|date_format:d-m-Y' : 'required|date_format:d-m-Y',
                'production_stages.*.deadline_date' => $isCanvas ? 'nullable|date_format:d-m-Y' : 'required|date_format:d-m-Y',
                'production_stages.*.rate' => $isCanvas ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
                'fabrics.*.mtr' => 'nullable|numeric|min:0.01',
                'fabrics.*.lay_marks.*.sizes' => 'required|array|min:1',
                'fabrics.*.lay_marks.*.sleeve' => 'required|string',
                'fabrics.*.lay_marks.*.meter' => 'required|numeric|gt:0',
                'fabrics.*.lay_marks.*.no_of_lay' => 'required|numeric|gt:0',
            ];

            $messages = [
                '*.required' => 'This field is required',
                '*.not_regex' => 'This field is an invalid format.',
                'job_card_no.unique' => 'This field already exists.',
                'reference_no.same' => 'Reference No must be the same as Job Card No.',
                'production_stages.*.stage_id.required' => 'This field is required',
                'production_stages.*.service_provider_id.required' => 'This field is required',
                'production_stages.*.issue_date.required' => 'This field is required',
                'production_stages.*.deadline_date.required' => 'This field is required',
                'production_stages.*.rate.required' => 'This field is required',
                'production_stages.required' => 'At least one production stage is required',
                'production_stages.min' => 'At least one production stage is required',
                'stock_entry_ids.*.distinct' => 'Duplicate Stock Entries are not allowed.',
                '*.min' => 'This field must be at least :min characters.',
                '*.max' => 'This field should not be more than :max characters.',
                'fabrics.*.mtr.min' => 'The Issued Meters must be greater than 0 for fabric material.',
                'fabrics.*.lay_marks.*.sizes.required' => 'Size is mandatory for all Lay Marks.',
                'fabrics.*.lay_marks.*.sizes.min' => 'Please select at least one Size for Lay Marks.',
                'fabrics.*.lay_marks.*.sleeve.required' => 'Sleeve is mandatory for all Lay Marks.',
                'fabrics.*.lay_marks.*.meter.required' => 'Lay Mark Meter is mandatory.',
                'fabrics.*.lay_marks.*.meter.gt' => 'Lay Mark Meter must be greater than 0.',
                'fabrics.*.lay_marks.*.no_of_lay.required' => 'No. of Lay is mandatory.',
                'fabrics.*.lay_marks.*.no_of_lay.gt' => 'No. of Lay must be greater than 0.',
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
                $hasIssuedItems = $id ? $jobCard->issueItems()->exists() : false;
                if ($id) {
                    $jobCard->update($data);
                    if (!$hasTasks && !$hasIssuedItems) {
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
                        if ($hasTasks || $hasIssuedItems) {
                            $jobCard->cuttingSizeRatios()->updateOrCreate($match, $val);
                        } else {
                            $jobCard->cuttingSizeRatios()->create(array_merge($match, $val));
                        }
                    }
                }

                $savedFabricDetailIds = [];
                if ($request->fabrics) {
                    foreach ($request->fabrics as $index => $fabric) {
                        $artNo = $fabric['art_no'] ?? null;
                        if (!$artNo)
                            continue;

                        $seId = $fabric['stock_entry_id'] ?? null;

                        $grnImage = $fabric['grn_image'] ?? null;
                        if (!$grnImage) {
                            $grnImage = $this->getLatestGrnImageForArtNo($artNo);
                        }

                        $matrix = collect($request->article_matrix ?? [])->where('art_no', $artNo)->first();

                        $fdMatch = ['art_no' => $artNo, 'stock_entry_id' => $seId, 'is_additional' => 0];
                        $fdVal = [
                            'is_additional' => 0,
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

                        $incomingStockTotalQty = isset($fabric['stock_total_qty']) && $fabric['stock_total_qty'] !== '' && $fabric['stock_total_qty'] !== null
                            ? (float) $fabric['stock_total_qty']
                            : null;

                        if ($hasTasks || $hasIssuedItems) {
                            $existing = $jobCard->fabricDetails()->where(function($q) { $q->where('is_additional', 0)->orWhereNull('is_additional'); })->where('art_no', $artNo)->where('stock_entry_id', $seId)->first();
                            if ($existing && $existing->stock_total_qty === null && $incomingStockTotalQty !== null) {
                                $fdVal['stock_total_qty'] = $incomingStockTotalQty;
                            }
                            $fabricDetail = $jobCard->fabricDetails()->updateOrCreate($fdMatch, $fdVal);
                        } else {
                            if ($incomingStockTotalQty !== null) {
                                $fdVal['stock_total_qty'] = $incomingStockTotalQty;
                            }
                            $fabricDetail = $jobCard->fabricDetails()->create(array_merge($fdMatch, $fdVal));
                        }

                        if (isset($fabric['consumptions']) && is_array($fabric['consumptions'])) {
                            foreach ($fabric['consumptions'] as $sz => $cons) {
                                $cMatch = ['size' => $sz];
                                $cVal = [
                                    'fs_cons' => $cons['fs_cons'] ?? null,
                                    'hs_cons' => $cons['hs_cons'] ?? null,
                                ];
                                if ($hasTasks || $hasIssuedItems) {
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

                                    if ($hasTasks || $hasIssuedItems) {
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

                            $searchSeIds = !empty($seId) ? [$seId] : $seIds;
                            if (!empty($searchSeIds) && $artNo) {
                                $stockItem = StockEntryItem::whereIn('stock_entry_id', $searchSeIds)
                                    ->where(function ($q) use ($artNo) {
                                        $q->where('art_no', $artNo)
                                          ->orWhereHas('rawMaterial', function ($q2) use ($artNo) {
                                              $q2->where('code', $artNo)->orWhere('name', $artNo);
                                          });
                                    })
                                    ->first();
                                if ($stockItem) {
                                    $rowColorId = $stockItem->color_id ?? ($stockItem->grnEntryItem->color_id ?? null);
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
                                    if ($hasTasks || $hasIssuedItems) {
                                        $fabricDetail->quantities()->updateOrCreate($mqMatch, $mqVal);
                                    } else {
                                        $fabricDetail->quantities()->create(array_merge($mqMatch, $mqVal));
                                    }
                                    $rowTotal += ($qFs + $qHs);
                                }
                            }
                        }
                        $savedFabricDetailIds[] = $fabricDetail->id;
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

                if ($id) {
                    $removedFabricDetails = $jobCard->fabricDetails()->where(function($q) {
                        $q->where('is_additional', 0)->orWhereNull('is_additional');
                    })->whereNotIn('id', $savedFabricDetailIds)->get();
                    foreach ($removedFabricDetails as $removedFd) {
                        JobCardIssueItem::where('job_card_article_matrix_id', $removedFd->id)->delete();
                        $removedFd->quantities()->delete();
                        $removedFd->consumptions()->delete();
                        $removedFd->layMarks()->delete();
                        $removedFd->delete();
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
        $fabricSizes = \App\Models\FabricSize::active()->orderBy('id','desc')->get();

        $stageTaskStatus = [];
        if ($jobCard) {
            $tasks = Task::with('stage')->where('job_card_entry_id', $jobCard->id)->where(function($q) {
                $q->where('is_additional', 0)->orWhereNull('is_additional');
            })->get();
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
        $hasTasks = $jobCard ? $jobCard->tasks()->where(function($q) { $q->where('is_additional', 0)->orWhereNull('is_additional'); })->exists() : false;
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
            'colors',
            'isRestrictedEdit',
            'fabricSizes'
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
                    $sei = \App\Models\StockEntryItem::where('art_no', $mArtNo)->orderBy('id', 'desc')->first();
                    if ($sei && $sei->store_category_id) {
                        $artCategoryMap[$mArtNo] = $sei->store_category_id;
                    }

                    $rm = RawMaterial::where('code', $mArtNo)->orWhere('name', $mArtNo)->first();
                    if ($rm) {
                        if (!isset($artCategoryMap[$mArtNo])) {
                            $artCategoryMap[$mArtNo] = $rm->store_category_id ?? 1;
                        }
                        if (!isset($artMaterialMap[$mArtNo]))
                            $artMaterialMap[$mArtNo] = $rm->name;
                    }
                }
            }
        }

        foreach ($jobCard->fabricDetails as $detail) {
            if ($detail->fs_qty > 0 || $detail->hs_qty > 0 || $detail->quantities->sum('qty_fs') > 0 || $detail->quantities->sum('qty_hs') > 0) {
                $artCategoryMap[trim($detail->art_no)] = 1;
            }
        }

        return ['artMaterialMap' => $artMaterialMap, 'artCategoryMap' => $artCategoryMap];
    }

    public function view_jc_item($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('issue-item job-card')) {
            return unauthorizedRedirect();
        }
        $jobCard = JobCardEntry::with(['brand', 'issueStore', 'fabricDetails.quantities', 'fabricDetails.consumptions', 'purchaseOrder.items.rawMaterial.uom', 'purchaseOrder.supplier', 'purchaseOrder.items.uom', 'purchaseOrder.items.brand', 'purchaseOrder.items.style', 'issueItems', 'sleeveMeters', 'operations', 'cuttingSizeRatios'])->findOrFail($id);
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

            $seIds = [];
            if ($jobCard->stock_entry_ids) {
                $decoded = json_decode($jobCard->stock_entry_ids, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $cid) {
                        $seIds[] = strpos($cid, '::') !== false ? explode('::', $cid)[0] : $cid;
                    }
                }
            }
            $seIds = array_unique($seIds);

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
                        $totalToDeduct = $qtyUsed + $qtyWastage;
                        $totalIssuedFabric += $totalToDeduct;

                        $existingItem = $existingItems->first(function ($item) use ($matrixId) {
                            return $item->job_card_article_matrix_id == $matrixId;
                        });

                        if ($existingItem) {
                            $oldQtyToDeduct = floatval($existingItem->qty_used ?? 0) + floatval($existingItem->qty_wastage ?? 0);
                            if ($oldQtyToDeduct > 0) {
                                $qtyToRevert = $oldQtyToDeduct;

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
                                    $revQuery = StockEntryItem::whereRaw('qty_out > 0')->orderBy('id', 'desc');
                                    if ($matrix->stock_entry_id) {
                                        $revQuery->where('stock_entry_id', $matrix->stock_entry_id);
                                    } elseif (!empty($seIds)) {
                                        $revQuery->whereIn('stock_entry_id', $seIds);
                                    } elseif ($jobCard->purchase_order_id) {
                                        $revQuery->whereIn('grn_entry_item_id', $grnItemIds);
                                    }
                                    $revQuery->where(function ($q) use ($artNo) {
                                        $q->where('art_no', $artNo)
                                          ->orWhereHas('rawMaterial', function ($q2) use ($artNo) {
                                              $q2->where('code', $artNo)->orWhere('name', $artNo);
                                          });
                                    });

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

                        $unitPrice = 0;
                        $firstStockItemId = null;
                        if ($artNo && $totalToDeduct > 0) {
                            $query = StockEntryItem::whereRaw('(qty_in - COALESCE(qty_out, 0)) > 0')->orderBy('id', 'asc');
                            if ($matrix->stock_entry_id) {
                                $query->where('stock_entry_id', $matrix->stock_entry_id);
                            } elseif (!empty($seIds)) {
                                $query->whereIn('stock_entry_id', $seIds);
                            } elseif ($jobCard->purchase_order_id) {
                                $query->whereIn('grn_entry_item_id', $grnItemIds);
                            }

                            $query->where(function ($q) use ($artNo) {
                                $q->where('art_no', $artNo)
                                    ->orWhereHas('grnEntryItem', function ($q2) use ($artNo) {
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

                            if ($remainingToDeduct > 0.001) {
                                throw new \Exception("Insufficient stock for Art No: $artNo. Shortage: " . $remainingToDeduct);
                            }
                            $actualDeducted = round($totalToDeduct - $remainingToDeduct, 4);
                            $unitPrice = ($actualDeducted > 0) ? ($weightedCost / $actualDeducted) : 0;
                        }
						$unitPrice = ($actualDeducted > 0) ? ($weightedCost / $actualDeducted) : 0;
                        if ($unitPrice <= 0 && !empty($itemData['unit_price'])) {
							$unitPrice = floatval($itemData['unit_price']);
						}
                        $producedQty = floatval($itemData['produced_qty'] ?? 0);
                        $totalCost = $totalToDeduct * $unitPrice;
                        $costPerPc = ($producedQty > 0) ? ($totalCost / $producedQty) : 0;

                        $isStringArtNo = false;
                        $cleanedArtNo = '';
                        if ($artNo) {
                            $hasAlpha = preg_match('/[a-zA-Z]/', $artNo);
                            $matchesExistingPattern = preg_match('/^([a-zA-Z]*)(\d+)(?:-(\d+))?$/', $artNo);
                            if ($hasAlpha && !$matchesExistingPattern) {
                                $isStringArtNo = true;
                                $cleanedArtNo = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $artNo));
                            }
                        }

                        if ($isStringArtNo) {
                            $barcodeNo = 'BC' . $cleanedArtNo . '0000';
                        } else {
                            preg_match('/([a-zA-Z]*)(\d+)(?:-(\d+))?/', $artNo, $matches);
                            $numericBase = $matches[2] ?? '';
                            $suffix = $matches[3] ?? '1';
                            $formattedSuffix = str_pad($suffix, 2, '0', STR_PAD_LEFT);

                            if ($numericBase === '') {
                                $noPrefix = preg_replace('/^[a-zA-Z]+/', '', $jobCard->job_card_no ?? '0000');
                                $numericBase = preg_replace('/[^A-Za-z0-9]/', '', $noPrefix);
                            }
                            $formattedSize = "00"; 
                            $sleeveCode = "00";

                            $barcodeNo = 'BC' . $numericBase . $formattedSuffix . $formattedSize . $sleeveCode;
                        }

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

                        $match = [
                            'job_card_entry_id' => $jobCard->id,
                            'job_card_article_matrix_id' => $matrixId,
                        ];
                        
                        $checkExisting = JobCardIssueItem::where($match)->first();
                        if (!$checkExisting) {
                            $data['created_by'] = auth()->id();
                        }

                        JobCardIssueItem::updateOrCreate($match, $data);

                        $matrixQuantities = JobCardMatrixQuantity::where('job_card_fabric_detail_id', $matrixId)->get();
                        $brand = $jobCard->brand;

                        $style = null;
                        if ($firstStockItemId) {
                            $stockItem = StockEntryItem::with(['style', 'grnEntryItem.purchaseInvoiceItem.purchaseOrderItem.style'])->find($firstStockItemId);
                            $style = $stockItem->style ?? ($stockItem->grnEntryItem->purchaseInvoiceItem->purchaseOrderItem->style ?? null);
                        }

                        if (!$style && $artNo) {
                            $otherStockItem = StockEntryItem::with('style')->where('art_no', $artNo)->whereNotNull('style_id')->first();
                            $style = $otherStockItem->style ?? null;
                        }

                        if (!$style) {
                            $allPOItems = $jobCard->purchaseOrder?->items;
                            if (!$allPOItems && $artNo) {
                                $grnItem = \App\Models\GrnEntryItem::where('art_no', $artNo)->whereHas('purchaseInvoiceItem.purchaseOrderItem')->first();
                                $allPOItems = $grnItem?->purchaseInvoiceItem?->purchaseOrderItem?->purchaseOrder?->items;
                            }
                            $matchingPOItem = $allPOItems ? (
                                $allPOItems->where('store_category_id', 1)->whereNotNull('style_id')->first() 
                                ?: $allPOItems->whereNotNull('style_id')->first() 
                                ?: $allPOItems->first()
                            ) : null;
                            $style = $matchingPOItem->style ?? ($allPOItems?->whereNotNull('style_id')->first()?->style ?? null);
                        }

                        if (!$style) {
                            $style = $jobCard->item->style ?? null;
                        }

                        $missingPrices = [];

                        foreach ($matrixQuantities as $mq) {
                            $sizeCode = is_numeric($mq->size) ? str_pad($mq->size, 2, '0', STR_PAD_LEFT) : '00';

                            if ($isStringArtNo) {
                                $cleanSize = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $mq->size));
                                $barcodeFS = 'BC' . $cleanedArtNo . $cleanSize . '01';
                                $barcodeHS = 'BC' . $cleanedArtNo . $cleanSize . '02';
                            } else {
                                $barcodeFS = 'BC' . $numericBase . $formattedSuffix . $sizeCode . '01';
                                $barcodeHS = 'BC' . $numericBase . $formattedSuffix . $sizeCode . '02';
                            }

                            if ($mq->qty_fs > 0) {
                                $itemCodeFS = implode('-', array_filter([trim($brand->code ?? ''), trim($style->code ?? ''), 'FS'], function($v) { return $v !== ''; }));
                                $itemNameFS = trim(($brand->brand_name ?? '') . ' ' . ($style->style_name ?? '') . ' F/S');
                                
                                $priceExistsFS = \App\Models\ItemPrice::where('status', 'Active')
                                    ->where('finished_item_code', $itemCodeFS)
                                    ->where('art_no', $artNo)
                                    ->where(function($q) use ($mq) {
                                        $q->where('size', $mq->size)->orWhereNull('size')->orWhere('size', '');
                                    })
                                    ->whereDate('effective_from', '<=', now())
                                    ->exists();
                                
                                if (!$priceExistsFS) {
                                    $missingPrices[] = [
                                        'item_name' => $itemNameFS,
                                        'finished_item_code' => $itemCodeFS,
                                        'art_no' => $artNo
                                    ];
                                }

                                BarcodeMaster::updateOrCreate(
                                    ['barcode_no' => $barcodeFS],
                                    [
                                        'job_card_entry_id' => $jobCard->id,
                                        'item_code' => $itemCodeFS,
                                        'art_no' => $artNo,
                                        'item_name' => $itemNameFS,
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
                                $itemCodeHS = implode('-', array_filter([trim($brand->code ?? ''), trim($style->code ?? ''), 'HS'], function($v) { return $v !== ''; }));
                                $itemNameHS = trim(($brand->brand_name ?? '') . ' ' . ($style->style_name ?? '') . ' H/S');

                                $priceExistsHS = \App\Models\ItemPrice::where('status', 'Active')
                                    ->where('finished_item_code', $itemCodeHS)
                                    ->where('art_no', $artNo)
                                    ->where(function($q) use ($mq) {
                                        $q->where('size', $mq->size)->orWhereNull('size')->orWhere('size', '');
                                    })
                                    ->whereDate('effective_from', '<=', now())
                                    ->exists();

                                if (!$priceExistsHS) {
                                    $missingPrices[] = [
                                        'item_name' => $itemNameHS,
                                        'finished_item_code' => $itemCodeHS,
                                        'art_no' => $artNo
                                    ];
                                }

                                BarcodeMaster::updateOrCreate(
                                    ['barcode_no' => $barcodeHS],
                                    [
                                        'job_card_entry_id' => $jobCard->id,
                                        'item_code' => $itemCodeHS,
                                        'art_no' => $artNo,
                                        'item_name' => $itemNameHS,
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

                        if (!empty($missingPrices)) {
                            DB::rollBack();
                            if ($request->ajax()) {
                                return response()->json([
                                    'success' => false,
                                    'error_type' => 'missing_prices',
                                    'missing_prices' => array_values(array_unique($missingPrices, SORT_REGULAR)),
                                    'message' => 'Missing pricing for generated items.'
                                ]);
                            }
                            return back()->with('danger', 'Missing pricing for generated items.');
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

    /**
     * Display Additional Quantity / Supplementary Cutting Page
     */
    public function additional_qty($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('issue-item job-card') && !auth()->user()->can('edit job-card')) {
            return unauthorizedRedirect();
        }

        $jobCard = JobCardEntry::with([
            'brand',
            'item',
            'serviceProvider',
            'sizeRatio',
            'cuttingSizeRatios',
            'fabricDetails.quantities',
            'fabricDetails.layMarks',
            'fabricDetails.consumptions',
            'fabricDetails.stockEntry',
            'operations.operationStage',
            'operations.serviceProvider',
            'images'
        ])->findOrFail($id);

        $plants = ServiceProvider::active()->orderBy('id', 'desc')->get();
        if ($plants->isEmpty()) {
            $plants = ServiceProvider::all();
        }
        $operationStages = OperationStage::active()->orderBy('id', 'desc')->get();
        if ($operationStages->isEmpty()) {
            $operationStages = OperationStage::orderBy('id', 'desc')->get();
        }
        $fabricSizes = \App\Models\FabricSize::active()->orderBy('id','desc')->get();
        if ($fabricSizes->isEmpty()) {
            $fabricSizes = \App\Models\FabricSize::all();
        }

        $sizes = [];
        if ($jobCard->sizeRatio && $jobCard->sizeRatio->size) {
            $sizes = array_values(array_filter(array_map('trim', explode(',', $jobCard->sizeRatio->size))));
        }
        if (empty($sizes) && $jobCard->cuttingSizeRatios && $jobCard->cuttingSizeRatios->count() > 0) {
            $sizes = $jobCard->cuttingSizeRatios->pluck('size')->unique()->toArray();
        }
        if (empty($sizes)) {
            $sizes = ['36', '38', '40', '42', '44', '46'];
        }

        $additionalBatches = $jobCard->fabricDetails->where('is_additional', 1)->values();

        $editingBatch = null;
        $editingBatchGroup = collect();
        $batchIndex = null;
        if (request()->has('batch_id')) {
            $editingBatch = $additionalBatches->where('id', request('batch_id'))->first();
            if ($editingBatch) {
                $batchNo = $editingBatch->additional_batch_no;
                $editingBatchGroup = $additionalBatches->filter(function($f) use ($batchNo, $editingBatch) {
                    return $batchNo ? ($f->additional_batch_no == $batchNo) : ($f->id == $editingBatch->id);
                })->values();

                if ($editingBatchGroup->contains(fn($b) => $b->isPostedToWarehouse())) {
                    return redirect()->route('job_card_entries.additional_qty_view', ['id' => $jobCard->id, 'batch_id' => $editingBatch->id])
                        ->with('info', 'This batch has already been posted to the warehouse and is opened in View Details mode.');
                }
                $batchIndex = $batchNo ?? ($additionalBatches->search(fn($b) => $b->id == $editingBatch->id) + 1);
            }
        }

        $stageTaskStatus = [];
        $taskQuery = Task::with('stage')->where('job_card_entry_id', $jobCard->id);
        if ($editingBatch) {
            $targetBatchIds = $editingBatchGroup->isNotEmpty() ? $editingBatchGroup->pluck('id')->toArray() : [$editingBatch->id];
            $taskQuery->whereIn('job_card_fabric_detail_id', $targetBatchIds);
        } else {
            $taskQuery->where('is_additional', 1);
        }
        $tasks = $taskQuery->get();
        foreach ($tasks as $task) {
            $osId = ($task->stage && $task->stage->operation_stage_id) ? $task->stage->operation_stage_id : $task->stage_id;
            if ($osId) {
                $stageTaskStatus[$osId] = [
                    'status' => $task->status,
                    'task_no' => $task->task_no,
                ];
            }
        }
        $hasIssuedItems = $jobCard->issueItems()->exists();

        return view('job_card_entry.additional_qty', compact(
            'jobCard', 
            'plants', 
            'operationStages', 
            'fabricSizes', 
            'sizes',
            'additionalBatches',
            'editingBatch',
            'editingBatchGroup',
            'batchIndex',
            'stageTaskStatus',
            'hasIssuedItems'
        ));
    }

    /**
     * Dedicated page for Job Card Additional Quantity History & Batch Logs
     */
    public function additionalQtyHistory($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view job-card') && !auth()->user()->can('edit job-card')) {
            return unauthorizedRedirect();
        }

        $jobCard = JobCardEntry::with([
            'brand',
            'item',
            'serviceProvider',
            'sizeRatio',
            'cuttingSizeRatios',
            'fabricDetails.quantities',
            'fabricDetails.stockEntry',
            'fabricDetails.productionReceipts'
        ])->findOrFail($id);

        $firstFabric = $jobCard->fabricDetails->where('is_additional', 0)->first() ?? $jobCard->fabricDetails->first();
        $additionalBatches = $jobCard->fabricDetails->where('is_additional', 1)
            ->groupBy(function($item) {
                return $item->additional_batch_no ?? ($item->created_at ? $item->created_at->format('Y-m-d H:i') : $item->id);
            })
            ->values();

        return view('job_card_entry.additional_qty_history', compact('jobCard', 'firstFabric', 'additionalBatches'));
    }

    /**
     * Dedicated View Details page for an Additional Quantity Batch
     */
    public function additionalQtyView($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view job-card') && !auth()->user()->can('edit job-card')) {
            return unauthorizedRedirect();
        }

        $jobCard = JobCardEntry::with([
            'brand',
            'item',
            'serviceProvider',
            'sizeRatio',
            'cuttingSizeRatios',
            'fabricDetails.quantities',
            'fabricDetails.layMarks',
            'fabricDetails.consumptions',
            'fabricDetails.stockEntry',
            'operations.operationStage',
            'operations.serviceProvider',
            'images'
        ])->findOrFail($id);

        $allAdditionalFabrics = $jobCard->fabricDetails->where('is_additional', 1)->values();
        $batchId = request('batch_id');
        $targetFabric = $allAdditionalFabrics->where('id', $batchId)->first() ?? $allAdditionalFabrics->first();

        if (!$targetFabric) {
            return redirect()->route('job_card_entries.additional_qty_history', $jobCard->id)
                ->with('danger', 'Additional batch not found.');
        }

        $batchNo = $targetFabric->additional_batch_no;
        $batchGroup = $allAdditionalFabrics->filter(function($f) use ($batchNo, $targetFabric) {
            return $batchNo ? ($f->additional_batch_no == $batchNo) : ($f->id == $targetFabric->id);
        })->values();

        if ($batchGroup->isEmpty()) {
            $batchGroup = collect([$targetFabric]);
        }

        $batch = $targetFabric;
        $batchIndex = $batchNo ?? 1;

        $batchOps = $jobCard->operations;

        $tasks = Task::with('stage')
            ->where('job_card_entry_id', $jobCard->id)
            ->whereIn('job_card_fabric_detail_id', $batchGroup->pluck('id')->toArray())
            ->get();

        $stageTaskStatus = [];
        foreach ($tasks as $task) {
            $osId = ($task->stage && $task->stage->operation_stage_id) ? $task->stage->operation_stage_id : $task->stage_id;
            if ($osId) {
                $stageTaskStatus[$osId] = [
                    'status' => $task->status,
                    'task_no' => $task->task_no,
                    'issued_qty' => $task->issue_qty,
                    'employee' => $task->employee?->name,
                ];
            }
        }

        $productionReceipts = \App\Models\ProductionReceipt::with(['storeType', 'storeLocation', 'warehouse', 'employee'])
            ->whereIn('job_card_fabric_detail_id', $batchGroup->pluck('id')->toArray())
            ->get();

        $isPosted = $batchGroup->contains(fn($b) => $b->isPostedToWarehouse());

        $sizes = [];
        if ($jobCard->sizeRatio && $jobCard->sizeRatio->size) {
            $sizes = array_values(array_filter(array_map('trim', explode(',', $jobCard->sizeRatio->size))));
        }
        if (empty($sizes)) {
            $sizes = $batchGroup->flatMap->quantities->pluck('size')->unique()->toArray();
        }
        if (empty($sizes)) {
            $sizes = ['36', '38', '40', '42', '44', '46'];
        }

        return view('job_card_entry.additional_qty_view', compact(
            'jobCard',
            'batch',
            'batchGroup',
            'batchIndex',
            'batchOps',
            'stageTaskStatus',
            'productionReceipts',
            'isPosted',
            'sizes'
        ));
    }

    /**
     * Store Additional Quantity / Supplementary Cutting for a Job Card (Supports Multi-Art)
     */
    public function storeAdditionalQty(Request $request, $id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('issue-item job-card') && !auth()->user()->can('edit job-card')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            return unauthorizedRedirect();
        }

        $jobCard = JobCardEntry::with(['fabricDetails', 'cuttingSizeRatios', 'operations'])->findOrFail($id);

        $fabricsData = $request->input('fabrics', []);
        if (empty($fabricsData) && $request->has('art_no')) {
            $fabricsData = [
                0 => [
                    'art_no' => $request->art_no,
                    'stock_entry_id' => $request->stock_entry_id,
                    'width' => $request->width,
                    'in_out' => $request->in_out,
                    'n_patti' => $request->n_patti,
                    'total_fabric_meters' => $request->total_fabric_meters,
                    'sizes' => $request->sizes,
                    'lay_marks' => $request->lay_marks,
                ]
            ];
        }

        $totalGrandExtraFs = 0;
        $totalGrandExtraHs = 0;
        $totalGrandExtraQty = 0;
        $totalGrandFabricMeters = 0;

        $validFabrics = [];
        foreach ($fabricsData as $idx => $fData) {
            $artNo = trim($fData['art_no'] ?? '');
            if (!$artNo) continue;

            $fabricFs = 0;
            $fabricHs = 0;
            if (isset($fData['sizes']) && is_array($fData['sizes'])) {
                foreach ($fData['sizes'] as $s) {
                    $fabricFs += intval($s['qty_fs'] ?? 0);
                    $fabricHs += intval($s['qty_hs'] ?? 0);
                }
            }
            $fabricQty = $fabricFs + $fabricHs;
            $fabricMtr = floatval($fData['total_fabric_meters'] ?? 0);

            if ($fabricQty > 0 || $fabricMtr > 0) {
                $fData['calc_fs'] = $fabricFs;
                $fData['calc_hs'] = $fabricHs;
                $fData['calc_total_qty'] = $fabricQty;
                $fData['calc_mtr'] = $fabricMtr;
                $validFabrics[$idx] = $fData;

                $totalGrandExtraFs += $fabricFs;
                $totalGrandExtraHs += $fabricHs;
                $totalGrandExtraQty += $fabricQty;
                $totalGrandFabricMeters += $fabricMtr;
            }
        }

        if ($totalGrandExtraQty <= 0 && $totalGrandFabricMeters <= 0) {
            $msg = 'Please enter additional pieces in the Cutting Size Ratio matrix or fabric meters.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withInput()->with('danger', $msg);
        }

        DB::beginTransaction();
        try {
            $oldData = $jobCard->toArray();

            // Calculate next additional batch number for this job card
            $maxBatchNo = JobCardFabricDetail::where('job_card_entry_id', $jobCard->id)
                ->where('is_additional', 1)
                ->max('additional_batch_no');
            $newBatchNo = ($maxBatchNo ? intval($maxBatchNo) : 0) + 1;

            // 1. Process Cutting Size Ratios for all fabrics
            foreach ($validFabrics as $fData) {
                if (isset($fData['sizes']) && is_array($fData['sizes'])) {
                    foreach ($fData['sizes'] as $s) {
                        $sizeName = trim($s['size'] ?? '');
                        $fs = intval($s['qty_fs'] ?? 0);
                        $hs = intval($s['qty_hs'] ?? 0);
                        $tot = $fs + $hs;

                        if ($sizeName !== '') {
                            $ratio = JobCardCuttingSizeRatio::where('job_card_entry_id', $jobCard->id)
                                ->where('size', $sizeName)
                                ->first();

                            if ($ratio) {
                                $ratio->qty_fs = intval($ratio->qty_fs) + $fs;
                                $ratio->qty_hs = intval($ratio->qty_hs) + $hs;
                                $ratio->total_qty = intval($ratio->total_qty) + $tot;
                                $ratio->save();
                            } elseif ($tot > 0) {
                                JobCardCuttingSizeRatio::create([
                                    'job_card_entry_id' => $jobCard->id,
                                    'size' => $sizeName,
                                    'ratio' => $s['ratio'] ?? 0,
                                    'qty_fs' => $fs,
                                    'qty_hs' => $hs,
                                    'total_qty' => $tot,
                                ]);
                            }
                        }
                    }
                }
            }

            // 2. Create JobCardFabricDetail for each valid fabric
            $createdFabricDetails = [];
            foreach ($validFabrics as $idx => $fData) {
                $imagePath = null;
                if ($request->hasFile("fabrics.$idx.fabric_image")) {
                    $destinationPath = public_path('uploads/job_card/fabric');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $file = $request->file("fabrics.$idx.fabric_image");
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $file->move($destinationPath, $fileName);
                    $imagePath = 'uploads/job_card/fabric/' . $fileName;
                }

                $baseFabric = $jobCard->fabricDetails->where('art_no', $fData['art_no'])->first() ?? $jobCard->fabricDetails->first();
                $stockEntryId = !empty($fData['stock_entry_id']) ? $fData['stock_entry_id'] : ($baseFabric ? $baseFabric->stock_entry_id : null);
                $width = !empty($fData['width']) ? $fData['width'] : ($baseFabric ? $baseFabric->width : null);

                $newFabricDetail = JobCardFabricDetail::create([
                    'job_card_entry_id' => $jobCard->id,
                    'is_additional' => 1,
                    'additional_batch_no' => $newBatchNo,
                    'art_no' => $fData['art_no'],
                    'stock_entry_id' => $stockEntryId,
                    'width' => $width,
                    'mtr' => $fData['calc_mtr'],
                    'in_out' => $fData['in_out'] ?? 'NO',
                    'n_patti' => $fData['n_patti'] ?? 'WHITE',
                    'fs_qty' => $fData['calc_fs'],
                    'hs_qty' => $fData['calc_hs'],
                    'total_qty' => $fData['calc_total_qty'],
                    'used_qty' => $fData['calc_mtr'],
                    'remaining_qty' => 0,
                    'grn_image' => $imagePath,
                ]);

                $createdFabricDetails[] = $newFabricDetail;

                // Save individual size breakdown for this batch in JobCardMatrixQuantity
                if (isset($fData['sizes']) && is_array($fData['sizes'])) {
                    foreach ($fData['sizes'] as $s) {
                        $sizeName = trim($s['size'] ?? '');
                        $fs = intval($s['qty_fs'] ?? 0);
                        $hs = intval($s['qty_hs'] ?? 0);
                        $tot = $fs + $hs;
                        if ($sizeName !== '' && $tot > 0) {
                            JobCardMatrixQuantity::create([
                                'job_card_fabric_detail_id' => $newFabricDetail->id,
                                'size' => $sizeName,
                                'qty_fs' => $fs,
                                'qty_hs' => $hs,
                                'total_qty' => $tot,
                            ]);
                        }
                    }
                }

                // Save Lay Marks for this batch
                if (isset($fData['lay_marks']) && is_array($fData['lay_marks'])) {
                    foreach ($fData['lay_marks'] as $lmIdx => $lm) {
                        if (!empty($lm['sizes'])) {
                            $newFabricDetail->layMarks()->create([
                                'mark_no' => $lmIdx + 1,
                                'sizes' => is_array($lm['sizes']) ? $lm['sizes'] : explode(',', $lm['sizes']),
                                'sleeve_type' => $lm['sleeve'] ?? null,
                                'lay_mark_meter' => $lm['meter'] ?? null,
                                'no_of_lay' => $lm['no_of_lay'] ?? null,
                            ]);
                        }
                    }
                }

                // Deduct from warehouse stock item if available
                if ($stockEntryId && $fData['calc_mtr'] > 0) {
                    $stockItem = StockEntryItem::find($stockEntryId);
                    if ($stockItem) {
                        $stockItem->increment('qty_out', $fData['calc_mtr']);
                    }
                }
            }

            // 3. Update JobCardEntry grand totals and additional_qty
            $newGrandTotal = intval($jobCard->grand_total_qty) + $totalGrandExtraQty;
            $newFs = intval($jobCard->total_qty_fs ?? 0) + $totalGrandExtraFs;
            $newHs = intval($jobCard->total_qty_hs ?? 0) + $totalGrandExtraHs;
            $newAdditionalQty = intval($jobCard->additional_qty ?? 0) + $totalGrandExtraQty;

            $totalMtrAll = $jobCard->fabricDetails()->sum('mtr');
            $newAverage = ($newGrandTotal > 0) ? round($totalMtrAll / $newGrandTotal, 3) : ($jobCard->average ?? 0);

            $jobCard->update([
                'grand_total_qty' => $newGrandTotal,
                'total_qty_fs' => $newFs,
                'total_qty_hs' => $newHs,
                'additional_qty' => $newAdditionalQty,
                'average' => $newAverage,
            ]);

            // 4. Update/Sync Production Stages
            if ($request->has('production_stages') && is_array($request->production_stages)) {
                $validStages = array_values(array_filter($request->production_stages, function($s) {
                    return !empty($s['stage_id']);
                }));
                if (!empty($validStages)) {
                    $jobCard->operations()->delete();
                    foreach ($validStages as $stageData) {
                        $jobCard->operations()->create([
                            'operation_stage_id' => $stageData['stage_id'],
                            'service_provider_id' => $stageData['service_provider_id'] ?? null,
                            'employee_id' => $stageData['employee_id'] ?? null,
                            'assigned_date' => !empty($stageData['issue_date']) ? date('Y-m-d', strtotime($stageData['issue_date'])) : null,
                            'deadline_date' => !empty($stageData['deadline_date']) ? date('Y-m-d', strtotime($stageData['deadline_date'])) : null,
                            'remarks' => $stageData['remarks'] ?? null,
                            'rate' => $stageData['rate'] ?? 0,
                            'total_cost' => ($stageData['rate'] ?? 0) * $newGrandTotal,
                        ]);
                    }
                    $this->syncSchedulesFromJobCard($jobCard, $validStages);
                }
            }

            // 5. Log activity
            $newData = $jobCard->fresh()->toArray();
            $artList = implode(', ', array_column($validFabrics, 'art_no'));
            addLog('update', 'Job Card Additional Qty Added (+ ' . $totalGrandExtraQty . ' pcs for ' . $artList . ')', 'job_card_entries', $jobCard->id, $oldData, $newData);

            DB::commit();

            $successMsg = "Job Card additional quantity updated successfully (+$totalGrandExtraQty pcs)";
            session()->flash('success', $successMsg);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMsg,
                    'redirect' => url('job_card_entries'),
                    'new_grand_total' => $newGrandTotal,
                    'additional_qty' => $newAdditionalQty,
                ]);
            }

            return redirect('job_card_entries')->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding additional quantity: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('danger', 'Error adding additional quantity: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing Additional Quantity Batch (Supports Multi-Art)
     */
    public function updateAdditionalBatch(Request $request, $id, $batchId)
    {
        if (auth()->id() != 1 && !auth()->user()->can('issue-item job-card') && !auth()->user()->can('edit job-card')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            return unauthorizedRedirect();
        }

        $jobCard = JobCardEntry::with(['fabricDetails.quantities', 'cuttingSizeRatios', 'operations'])->findOrFail($id);
        $batch = JobCardFabricDetail::with('quantities')->where('job_card_entry_id', $jobCard->id)->where('is_additional', 1)->findOrFail($batchId);

        if ($batch->isPostedToWarehouse()) {
            $msg = 'This additional quantity batch has already been posted to the warehouse and cannot be edited.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withInput()->with('danger', $msg);
        }

        $fabricsData = $request->input('fabrics', []);
        if (empty($fabricsData) && $request->has('art_no')) {
            $fabricsData = [
                0 => [
                    'art_no' => $request->art_no,
                    'stock_entry_id' => $request->stock_entry_id,
                    'width' => $request->width,
                    'in_out' => $request->in_out,
                    'n_patti' => $request->n_patti,
                    'total_fabric_meters' => $request->total_fabric_meters,
                    'sizes' => $request->sizes,
                    'lay_marks' => $request->lay_marks,
                ]
            ];
        }

        $totalGrandExtraFs = 0;
        $totalGrandExtraHs = 0;
        $totalGrandExtraQty = 0;
        $totalGrandFabricMeters = 0;

        $validFabrics = [];
        foreach ($fabricsData as $idx => $fData) {
            $artNo = trim($fData['art_no'] ?? '');
            if (!$artNo) continue;

            $fabricFs = 0;
            $fabricHs = 0;
            if (isset($fData['sizes']) && is_array($fData['sizes'])) {
                foreach ($fData['sizes'] as $s) {
                    $fabricFs += intval($s['qty_fs'] ?? 0);
                    $fabricHs += intval($s['qty_hs'] ?? 0);
                }
            }
            $fabricQty = $fabricFs + $fabricHs;
            $fabricMtr = floatval($fData['total_fabric_meters'] ?? 0);

            if ($fabricQty > 0 || $fabricMtr > 0) {
                $fData['calc_fs'] = $fabricFs;
                $fData['calc_hs'] = $fabricHs;
                $fData['calc_total_qty'] = $fabricQty;
                $fData['calc_mtr'] = $fabricMtr;
                $validFabrics[$idx] = $fData;

                $totalGrandExtraFs += $fabricFs;
                $totalGrandExtraHs += $fabricHs;
                $totalGrandExtraQty += $fabricQty;
                $totalGrandFabricMeters += $fabricMtr;
            }
        }

        if ($totalGrandExtraQty <= 0 && $totalGrandFabricMeters <= 0) {
            $msg = 'Please enter additional pieces in the Cutting Size Ratio matrix or fabric meters.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withInput()->with('danger', $msg);
        }

        DB::beginTransaction();
        try {
            $oldData = $jobCard->toArray();

            // Find all fabrics in this batch group
            $batchGroup = collect([$batch]);
            if (!empty($batch->additional_batch_no)) {
                $batchGroup = JobCardFabricDetail::with(['quantities', 'layMarks'])
                    ->where('job_card_entry_id', $jobCard->id)
                    ->where('is_additional', 1)
                    ->where('additional_batch_no', $batch->additional_batch_no)
                    ->get();
            }

            $oldBatchQty = $batchGroup->sum('total_qty');
            $oldBatchFs = $batchGroup->sum('fs_qty');
            $oldBatchHs = $batchGroup->sum('hs_qty');
            $oldBatchMtr = $batchGroup->sum('mtr');
            $additionalBatchNo = $batch->additional_batch_no ?? ('BATCH-' . time() . '-' . $batch->id);

            // 1. Subtract old batch sizes from Cutting Size Ratios
            foreach ($batchGroup as $oldFab) {
                foreach ($oldFab->quantities as $oldMq) {
                    $ratio = JobCardCuttingSizeRatio::where('job_card_entry_id', $jobCard->id)
                        ->where('size', $oldMq->size)
                        ->first();
                    if ($ratio) {
                        $ratio->qty_fs = max(0, intval($ratio->qty_fs) - intval($oldMq->qty_fs));
                        $ratio->qty_hs = max(0, intval($ratio->qty_hs) - intval($oldMq->qty_hs));
                        $ratio->total_qty = max(0, intval($ratio->total_qty) - intval($oldMq->total_qty));
                        $ratio->save();
                    }
                }
            }

            // 2. Aggregate new sizes across all fabrics and add to Cutting Size Ratios
            $aggregatedSizes = [];
            foreach ($validFabrics as $fData) {
                if (isset($fData['sizes']) && is_array($fData['sizes'])) {
                    foreach ($fData['sizes'] as $s) {
                        $szName = trim($s['size'] ?? '');
                        if ($szName === '') continue;
                        if (!isset($aggregatedSizes[$szName])) {
                            $aggregatedSizes[$szName] = ['fs' => 0, 'hs' => 0, 'ratio' => $s['ratio'] ?? 0];
                        }
                        $aggregatedSizes[$szName]['fs'] += intval($s['qty_fs'] ?? 0);
                        $aggregatedSizes[$szName]['hs'] += intval($s['qty_hs'] ?? 0);
                    }
                }
            }

            foreach ($aggregatedSizes as $szName => $szData) {
                $fs = $szData['fs'];
                $hs = $szData['hs'];
                $tot = $fs + $hs;
                $ratio = JobCardCuttingSizeRatio::where('job_card_entry_id', $jobCard->id)
                    ->where('size', $szName)
                    ->first();

                if ($ratio) {
                    $ratio->qty_fs = intval($ratio->qty_fs) + $fs;
                    $ratio->qty_hs = intval($ratio->qty_hs) + $hs;
                    $ratio->total_qty = intval($ratio->total_qty) + $tot;
                    $ratio->save();
                } elseif ($tot > 0) {
                    JobCardCuttingSizeRatio::create([
                        'job_card_entry_id' => $jobCard->id,
                        'size' => $szName,
                        'ratio' => $szData['ratio'] ?? 0,
                        'qty_fs' => $fs,
                        'qty_hs' => $hs,
                        'total_qty' => $tot,
                    ]);
                }
            }

            // 3. Update / Create fabric details for each fabric in the batch
            foreach ($validFabrics as $idx => $fData) {
                $artNo = $fData['art_no'];
                $stockEntryId = $fData['stock_entry_id'] ?? null;
                $width = $fData['width'] ?? null;
                $inOut = $fData['in_out'] ?? 'NO';
                $nPatti = $fData['n_patti'] ?? 'WHITE';

                $fabDetail = $batchGroup->firstWhere('art_no', $artNo);

                $imagePath = $fabDetail ? $fabDetail->grn_image : null;
                if ($request->hasFile("fabrics.{$idx}.fabric_image")) {
                    $destinationPath = public_path('uploads/job_card/fabric');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $file = $request->file("fabrics.{$idx}.fabric_image");
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $file->move($destinationPath, $fileName);
                    $imagePath = 'uploads/job_card/fabric/' . $fileName;
                }

                $oldFabMtr = $fabDetail ? floatval($fabDetail->mtr) : 0;
                $diffFabMtr = $fData['calc_mtr'] - $oldFabMtr;
                $diffFabQty = $fData['calc_total_qty'] - ($fabDetail ? intval($fabDetail->total_qty) : 0);

                if ($fabDetail) {
                    $fabDetail->update([
                        'stock_entry_id' => $stockEntryId ?: $fabDetail->stock_entry_id,
                        'width' => $width ?: $fabDetail->width,
                        'mtr' => $fData['calc_mtr'],
                        'in_out' => $inOut,
                        'n_patti' => $nPatti,
                        'fs_qty' => $fData['calc_fs'],
                        'hs_qty' => $fData['calc_hs'],
                        'total_qty' => $fData['calc_total_qty'],
                        'used_qty' => $fData['calc_mtr'],
                        'grn_image' => $imagePath,
                        'additional_batch_no' => $additionalBatchNo,
                    ]);
                } else {
                    $fabDetail = JobCardFabricDetail::create([
                        'job_card_entry_id' => $jobCard->id,
                        'is_additional' => 1,
                        'additional_batch_no' => $additionalBatchNo,
                        'art_no' => $artNo,
                        'stock_entry_id' => $stockEntryId,
                        'width' => $width,
                        'mtr' => $fData['calc_mtr'],
                        'in_out' => $inOut,
                        'n_patti' => $nPatti,
                        'fs_qty' => $fData['calc_fs'],
                        'hs_qty' => $fData['calc_hs'],
                        'total_qty' => $fData['calc_total_qty'],
                        'used_qty' => $fData['calc_mtr'],
                        'remaining_qty' => 0,
                        'grn_image' => $imagePath,
                    ]);
                }

                // Matrix quantities
                $fabDetail->quantities()->delete();
                if (isset($fData['sizes']) && is_array($fData['sizes'])) {
                    foreach ($fData['sizes'] as $s) {
                        $szName = trim($s['size'] ?? '');
                        $fs = intval($s['qty_fs'] ?? 0);
                        $hs = intval($s['qty_hs'] ?? 0);
                        $tot = $fs + $hs;
                        if ($szName !== '' && $tot > 0) {
                            JobCardMatrixQuantity::create([
                                'job_card_fabric_detail_id' => $fabDetail->id,
                                'size' => $szName,
                                'qty_fs' => $fs,
                                'qty_hs' => $hs,
                                'total_qty' => $tot,
                            ]);
                        }
                    }
                }

                // Lay marks
                $fabDetail->layMarks()->delete();
                if (isset($fData['lay_marks']) && is_array($fData['lay_marks'])) {
                    foreach ($fData['lay_marks'] as $lmIdx => $lm) {
                        if (!empty($lm['sizes'])) {
                            $fabDetail->layMarks()->create([
                                'mark_no' => $lmIdx + 1,
                                'sizes' => is_array($lm['sizes']) ? $lm['sizes'] : explode(',', $lm['sizes']),
                                'sleeve_type' => $lm['sleeve'] ?? null,
                                'lay_mark_meter' => $lm['meter'] ?? null,
                                'no_of_lay' => $lm['no_of_lay'] ?? null,
                            ]);
                        }
                    }
                }

                // Update Batch Task Targets by difference if assigned
                if ($diffFabQty != 0) {
                    Task::where('job_card_fabric_detail_id', $fabDetail->id)->increment('issue_qty', $diffFabQty);
                }

                // Adjust warehouse stock item by difference
                if ($stockEntryId && $diffFabMtr != 0) {
                    $stockItem = StockEntryItem::find($stockEntryId);
                    if ($stockItem) {
                        $stockItem->increment('qty_out', $diffFabMtr);
                    }
                }
            }

            // 4. Update JobCardEntry grand totals and additional_qty
            $diffQty = $totalGrandExtraQty - $oldBatchQty;
            $diffFs = $totalGrandExtraFs - $oldBatchFs;
            $diffHs = $totalGrandExtraHs - $oldBatchHs;

            $newGrandTotal = max(0, intval($jobCard->grand_total_qty) + $diffQty);
            $newFs = max(0, intval($jobCard->total_qty_fs ?? 0) + $diffFs);
            $newHs = max(0, intval($jobCard->total_qty_hs ?? 0) + $diffHs);
            $newAdditionalQty = max(0, intval($jobCard->additional_qty ?? 0) + $diffQty);

            $totalMtrAll = $jobCard->fabricDetails()->sum('mtr');
            $newAverage = ($newGrandTotal > 0) ? round($totalMtrAll / $newGrandTotal, 3) : ($jobCard->average ?? 0);

            $jobCard->update([
                'grand_total_qty' => $newGrandTotal,
                'total_qty_fs' => $newFs,
                'total_qty_hs' => $newHs,
                'additional_qty' => $newAdditionalQty,
                'average' => $newAverage,
            ]);

            // 5. Update/Sync Production Stages if provided
            if ($request->has('production_stages') && is_array($request->production_stages)) {
                $validStages = array_values(array_filter($request->production_stages, function($s) {
                    return !empty($s['stage_id']);
                }));
                if (!empty($validStages)) {
                    $jobCard->operations()->delete();
                    foreach ($validStages as $stageData) {
                        $jobCard->operations()->create([
                            'operation_stage_id' => $stageData['stage_id'],
                            'service_provider_id' => $stageData['service_provider_id'] ?? null,
                            'employee_id' => $stageData['employee_id'] ?? null,
                            'assigned_date' => !empty($stageData['issue_date']) ? date('Y-m-d', strtotime($stageData['issue_date'])) : null,
                            'deadline_date' => !empty($stageData['deadline_date']) ? date('Y-m-d', strtotime($stageData['deadline_date'])) : null,
                            'remarks' => $stageData['remarks'] ?? null,
                            'rate' => $stageData['rate'] ?? 0,
                            'total_cost' => ($stageData['rate'] ?? 0) * $newGrandTotal,
                        ]);
                    }
                    $this->syncSchedulesFromJobCard($jobCard, $validStages);
                }
            }

            // 6. Log activity
            $newData = $jobCard->fresh()->toArray();
            addLog('update', 'Job Card Additional Batch #' . $batchId . ' Updated (Diff: ' . ($diffQty >= 0 ? '+' : '') . $diffQty . ' pcs)', 'job_card_entries', $jobCard->id, $oldData, $newData);

            DB::commit();

            $successMsg = "Job Card additional quantity batch updated successfully (" . ($diffQty >= 0 ? '+' : '') . "$diffQty pcs)";
            session()->flash('success', $successMsg);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMsg,
                    'redirect' => url('job_card_entries'),
                    'new_grand_total' => $newGrandTotal,
                    'additional_qty' => $newAdditionalQty,
                ]);
            }

            return redirect('job_card_entries')->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating batch: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('danger', 'Error updating batch: ' . $e->getMessage());
        }
    }

    /**
     * Delete an Additional Quantity Batch and revert quantities and stock
     */
    public function deleteAdditionalBatch(Request $request, $id, $batchId)
    {
        if (auth()->id() != 1 && !auth()->user()->can('issue-item job-card') && !auth()->user()->can('edit job-card')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            return unauthorizedRedirect();
        }

        $jobCard = JobCardEntry::with(['fabricDetails.quantities', 'cuttingSizeRatios'])->findOrFail($id);
        $batch = JobCardFabricDetail::with('quantities')->where('job_card_entry_id', $jobCard->id)->where('is_additional', 1)->findOrFail($batchId);

        if ($batch->isPostedToWarehouse()) {
            $msg = 'This additional quantity batch has already been posted to the warehouse and cannot be deleted.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('danger', $msg);
        }

        DB::beginTransaction();
        try {
            $oldData = $jobCard->toArray();
            $batchQty = intval($batch->total_qty);
            $batchFs = intval($batch->fs_qty);
            $batchHs = intval($batch->hs_qty);
            $batchMtr = floatval($batch->mtr);

            // 1. Revert size pieces from Cutting Size Ratios
            foreach ($batch->quantities as $mq) {
                $ratio = JobCardCuttingSizeRatio::where('job_card_entry_id', $jobCard->id)
                    ->where('size', $mq->size)
                    ->first();
                if ($ratio) {
                    $ratio->qty_fs = max(0, intval($ratio->qty_fs) - intval($mq->qty_fs));
                    $ratio->qty_hs = max(0, intval($ratio->qty_hs) - intval($mq->qty_hs));
                    $ratio->total_qty = max(0, intval($ratio->total_qty) - intval($mq->total_qty));
                    $ratio->save();
                }
            }
            $batch->quantities()->delete();

            // 2. Delete Associated Batch Tasks if any
            Task::where('job_card_fabric_detail_id', $batch->id)->delete();

            // 3. Revert warehouse stock deduction
            if ($batchMtr > 0 && $batch->stock_entry_id) {
                $stockItem = StockEntryItem::find($batch->stock_entry_id);
                if ($stockItem) {
                    $stockItem->decrement('qty_out', min($stockItem->qty_out, $batchMtr));
                }
            }

            // 4. Delete the batch record
            $batch->delete();

            // 5. Revert JobCardEntry grand totals and additional_qty
            $newGrandTotal = max(0, intval($jobCard->grand_total_qty) - $batchQty);
            $newFs = max(0, intval($jobCard->total_qty_fs ?? 0) - $batchFs);
            $newHs = max(0, intval($jobCard->total_qty_hs ?? 0) - $batchHs);
            $newAdditionalQty = max(0, intval($jobCard->additional_qty ?? 0) - $batchQty);

            $totalMtrAll = $jobCard->fabricDetails()->sum('mtr');
            $newAverage = ($newGrandTotal > 0) ? round($totalMtrAll / $newGrandTotal, 3) : ($jobCard->average ?? 0);

            $jobCard->update([
                'grand_total_qty' => $newGrandTotal,
                'total_qty_fs' => $newFs,
                'total_qty_hs' => $newHs,
                'additional_qty' => $newAdditionalQty,
                'average' => $newAverage,
            ]);

            // 6. Log activity
            $newData = $jobCard->fresh()->toArray();
            addLog('delete', 'Job Card Additional Batch #' . $batchId . ' Deleted (- ' . $batchQty . ' pcs)', 'job_card_entries', $jobCard->id, $oldData, $newData);

            DB::commit();

            $successMsg = "Addition Batch deleted and quantities reverted successfully!";
            session()->flash('success', $successMsg);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMsg,
                    'redirect' => url('job_card_entries/additional-qty/' . $jobCard->id),
                    'new_grand_total' => $newGrandTotal,
                    'additional_qty' => $newAdditionalQty,
                ]);
            }

            return redirect('job_card_entries/additional-qty/' . $jobCard->id)->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting batch: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('danger', 'Error deleting batch: ' . $e->getMessage());
        }
    }

    public function searchStockEntries(Request $request)
    {
        $term = $request->input('q', '');
        $isCanvas = false;
        
        if ($request->has('brand_id')) {
            $brand = \App\Models\Brand::find($request->input('brand_id'));
            if ($brand && strtoupper(trim($brand->brand_name)) === 'CANVAS ACCESSORIES') {
                $isCanvas = true;
            }
        }

        $entries = StockEntry::with(['stockEntryItems.rawMaterial', 'stockEntryItems.uom'])
            ->where(function ($q) use ($term) {
                $q->where('stock_entry_no', 'like', "%{$term}%")
                    ->orWhereHas('stockEntryItems', function ($q2) use ($term) {
                        $q2->where('art_no', 'like', "%{$term}%")
                            ->orWhereHas('rawMaterial', function ($q3) use ($term) {
                                $q3->where('name', 'like', "%{$term}%");
                            });
                    });
            });

        $entries = $entries->orderBy('id', 'desc')->limit(30)->get();

        $results = [];
        foreach ($entries as $entry) {
            $materialGroups = [];
            foreach ($entry->stockEntryItems as $item) {
                if (!$item->raw_material_id || !$item->rawMaterial) {
                    continue;
                }

                if ($isCanvas && $item->rawMaterial->store_category_id != 2) {
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

            $oldQtyUsed = $issueItem ? $issueItem->qty_issue : 0;
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

            $match = [
                'job_card_entry_id' => $jobCard->id,
                'job_card_article_matrix_id' => $fabricDetail->id,
            ];
            
            $checkExisting = JobCardIssueItem::where($match)->first();
            if (!$checkExisting) {
                $issueData['created_by'] = auth()->id();
            }
            
            JobCardIssueItem::updateOrCreate($match, $issueData);
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

        $savedFabricDetails = collect();
        if ($jobCardId) {
            $savedFabricDetails = JobCardFabricDetail::where('job_card_entry_id', $jobCardId)
                ->get()
                ->keyBy(function ($item) {
                    return trim($item->art_no);
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

        $stockItems = StockEntryItem::whereIn('stock_entry_id', $stockEntryIds)->with(['stockEntry', 'rawMaterial.uom', 'storeCategory', 'uom', 'grnEntryItem.purchaseInvoiceItem.fabricWidth'])->get();

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

        $filteredItems = $filteredItems->map(function ($item) {
            $item->composite_key = trim($item->art_no);
            return $item;
        });

        $allKeys = $filteredItems->pluck('composite_key')->unique()->toArray();
        $savedKeys = $issuedQtys->keys()->toArray();
        $finalKeys = array_values(array_unique(array_merge($allKeys, $savedKeys)));

        $groupedItems = $filteredItems->groupBy('composite_key');

        $artData = collect($finalKeys)->map(function ($artNo) use ($groupedItems, $issuedQtys, $savedFabricDetails) {
            $items = $groupedItems->get($artNo) ?? collect();
            $firstItem = $items->first();

            $seId = $firstItem ? $firstItem->stock_entry_id : null;
            $rawMaterial = $firstItem ? $firstItem->rawMaterial : RawMaterial::where('code', $artNo)->first();

            $uomCode = null;
            if ($firstItem && $firstItem->uom) {
                $uomCode = $firstItem->uom->uom_code;
            } elseif ($rawMaterial && $rawMaterial->uom) {
                $uomCode = $rawMaterial->uom->uom_code;
            }
            $netQty = $items->sum(function ($item) {
                return ($item->qty_in ?? 0) - ($item->qty_out ?? 0);
            });

            $alreadyIssued = (float) ($issuedQtys[$artNo] ?? 0);

            $savedDetail = $savedFabricDetails->get($artNo);
            $savedStockTotalQty = $savedDetail ? $savedDetail->stock_total_qty : null;
            $savedMtr = $savedDetail ? $savedDetail->mtr : null;

            $seNos = $items->map(function($item) {
                return $item->stockEntry ? $item->stockEntry->stock_entry_no : null;
            })->filter()->unique()->implode(', ');

            return [
                'art_no' => $artNo,
                'actual_art_no' => $artNo,
                'stock_entry_id' => $seId != '0' ? $seId : null,
                'art_name' => $rawMaterial ? $rawMaterial->name : null,
                'stock_entry_nos' => $seNos,
                'mtr' => $netQty,
                'already_issued' => $alreadyIssued,
                'total_available' => $netQty + $alreadyIssued,
                'saved_stock_total_qty' => $savedStockTotalQty,
                'saved_mtr' => $savedMtr,
                'uom_code' => $uomCode,
                'store_category_id' => $rawMaterial ? $rawMaterial->store_category_id : 1,
                'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                'fabric_type_id' => $firstItem ? ($firstItem->fabric_type_id ?? ($firstItem->grnEntryItem->fabric_type_id ?? null)) : null,
                'width' => $firstItem && $firstItem->grnEntryItem && $firstItem->grnEntryItem->purchaseInvoiceItem && $firstItem->grnEntryItem->purchaseInvoiceItem->fabricWidth ? $firstItem->grnEntryItem->purchaseInvoiceItem->fabricWidth->id : null,
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
                    'width' => $firstItem->purchaseInvoiceItem->fabricWidth->id ?? null
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
                    'width' => $item->fabricWidth->id ?? null
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
                'width' => $firstItem->purchaseInvoiceItem->fabricWidth->id ?? null
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
                    'width' => $item->fabricWidth->id ?? null
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
        })->map(function ($items, $artNo) use ($artTotalMap, $jobCard) {
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

            $finalPrice = $stockUnitPrice > 0 ? $stockUnitPrice : $items->average('unit_price');
            if ($finalPrice <= 0) {
                $seIds = [];
                if ($jobCard->stock_entry_ids) {
                    $ids = json_decode($jobCard->stock_entry_ids, true);
                    if ($ids) {
                        foreach ($ids as $idStr) {
                            $seIds[] = strpos($idStr, '::') !== false ? explode('::', $idStr)[0] : $idStr;
                        }
                    }
                }
                if (!empty($seIds)) {
                    $finalPrice = \App\Models\StockEntryItem::whereIn('stock_entry_id', $seIds)->where('art_no', $artNo)->where('price', '>', 0)->value('price') ?? 0;
                }
                if ($finalPrice <= 0 && $jobCard->purchase_order_id) {
                    $invoiceIds = \App\Models\PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
                    $grnItem = \App\Models\GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
                        $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
                    })->where('art_no', $artNo)
                      ->with('purchaseInvoiceItem')
                      ->first();
                    if ($grnItem) {
                        $finalPrice = $grnItem->purchaseInvoiceItem->rate ?? ($grnItem->rate ?? 0);
                    }
                }
            }

            $width = $items->map(function ($item) {
                return $item->fabricDetail && $item->fabricDetail->fabricSize ? $item->fabricDetail->fabricSize->width : null;
            })->filter()->first();

            return (object) [
                'art_no' => $artNo,
                'raw_material_id' => $items->pluck('raw_material_id')->filter()->first(),
                'produced_qty' => $artTotalMap[trim($artNo)] ?? $items->max('produced_qty'),
                'qty_issue' => $items->sum('qty_issue'),
                'qty_wastage' => $items->sum('qty_wastage'),
                'qty_used' => $items->sum('qty_used'),
                'qty_adjusted' => $items->sum('qty_adjusted'),
                'balance' => $items->sum('balance'),
                'unit_price' => $finalPrice,
                'size_label' => $sizeLabel,
                'width' => $width,
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

            $finalPrice = $stockUnitPrice > 0 ? $stockUnitPrice : $items->average('unit_price');
            if ($finalPrice <= 0) {
                $seIds = [];
                if ($jobCard->stock_entry_ids) {
                    $ids = json_decode($jobCard->stock_entry_ids, true);
                    if ($ids) {
                        foreach ($ids as $idStr) {
                            $seIds[] = strpos($idStr, '::') !== false ? explode('::', $idStr)[0] : $idStr;
                        }
                    }
                }
                if (!empty($seIds)) {
                    $finalPrice = \App\Models\StockEntryItem::whereIn('stock_entry_id', $seIds)->where('art_no', $artNo)->where('price', '>', 0)->value('price') ?? 0;
                }
                if ($finalPrice <= 0 && $jobCard->purchase_order_id) {
                    $invoiceIds = \App\Models\PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
                    $grnItem = \App\Models\GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
                        $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
                    })->where('art_no', $artNo)
                      ->with('purchaseInvoiceItem')
                      ->first();
                    if ($grnItem) {
                        $finalPrice = $grnItem->purchaseInvoiceItem->rate ?? ($grnItem->rate ?? 0);
                    }
                }
            }

            return (object) [
                'art_no' => $artNo,
                'raw_material_id' => $items->pluck('raw_material_id')->filter()->first(),
                'produced_qty' => $jobCard->grand_total_qty,
                'qty_issue' => $items->sum('qty_issue'),
                'qty_wastage' => $items->sum('qty_wastage'),
                'qty_used' => $items->sum('qty_used'),
                'qty_adjusted' => $items->sum('qty_adjusted'),
                'balance' => $items->sum('balance'),
                'unit_price' => $finalPrice,
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

        foreach ($jobCard->issueItems as $issueItem) {
            $artNo = trim($issueItem->fabricDetail->art_no ?? ($issueItem->rawMaterial?->code ?? ''));
            if ($artNo && !isset($artMaterialMap[$artNo]) && $issueItem->rawMaterial) {
                $artMaterialMap[$artNo] = $issueItem->rawMaterial->name;
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
            'processGroup',
            'issueItems.rawMaterial'
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
        $artMaterialMap = $maps['artMaterialMap'];
        
        foreach ($jobCard->issueItems as $issueItem) {
            $artNo = trim($issueItem->fabricDetail->art_no ?? ($issueItem->rawMaterial?->code ?? ''));
            if ($artNo && $issueItem->rawMaterial) {
                $artMaterialMap[$artNo] = $issueItem->rawMaterial->name;
            }
        }

        $seIds = [];
        if ($jobCard->stock_entry_ids) {
            $ids = json_decode($jobCard->stock_entry_ids, true);
            if ($ids) {
                foreach ($ids as $idStr) {
                    $seIds[] = strpos($idStr, '::') !== false ? explode('::', $idStr)[0] : $idStr;
                }
            }
        }

        if (!empty($seIds)) {
            $stockItems = \App\Models\StockEntryItem::whereIn('stock_entry_id', $seIds)->with(['rawMaterial', 'item'])->get();
            foreach ($stockItems as $si) {
                if ($si->rawMaterial && $si->rawMaterial->name) {
                    $artMaterialMap[$si->art_no] = $si->rawMaterial->name;
                } elseif ($si->item && $si->item->name) {
                    $artMaterialMap[$si->art_no] = $si->item->name;
                }
            }
        }

        $pdf = Pdf::loadView('job_card_entry.work_order_pdf', compact('jobCard', 'artUomMap', 'artCategoryMap', 'artMaterialMap'));
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
                    $stockItem = \DB::table('stock_entry_items')->where('art_no', $artNo)->select('raw_material_id', 'uom_id')->first();

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
                        $stockInfo = \DB::table('stock_entry_items')->where('id', $issueItem->stock_entry_item_id)->select('art_no', 'raw_material_id', 'uom_id')->first();
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
            $totalFabricCost = $fabricIssueItems->sum(function($i) use ($totalProduced) {
                $qtyPerPc = $totalProduced > 0 ? ($i->qty_used + $i->qty_wastage) / $totalProduced : 0;
                $costPerPc = round($qtyPerPc * ($i->stockEntryItem->price ?? 0), 2);
                return $costPerPc * $totalProduced;
            });
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
            $totalAccessoryCost = $accessoryIssueItems->sum(function($i) use ($totalProduced) {
                $qtyPerPc = $totalProduced > 0 ? ($i->qty_used + $i->qty_wastage) / $totalProduced : 0;
                $costPerPc = round($qtyPerPc * ($i->stockEntryItem->price ?? 0), 2);
                return $costPerPc * $totalProduced;
            });
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

    public function printLabelTag($id, Request $request)
    {
        $request->merge(['format' => 'tag']);
        return $this->printLabel($id, $request);
    }

    public function printLabelSticker($id, Request $request)
    {
        $request->merge(['format' => 'sticker']);
        return $this->printLabel($id, $request);
    }
    public function printLabelSquare($id, Request $request)
    {
        $request->merge(['format' => 'square']);
        return $this->printLabel($id, $request);
    }
    public function printLabel($id, Request $request)
    {
        $issueItem = JobCardIssueItem::with([
            'jobCard.brand',
            'jobCard.item.style',
            'stockEntryItem.color',
            'stockEntryItem.style',
            'stockEntryItem.grnEntryItem.purchaseInvoiceItem.purchaseOrderItem.style',
            'rawMaterial.uom',
            'stockEntryItem.grnEntryItem.color'
        ])->findOrFail($id);
        $jobCard = $issueItem->jobCard;
        $colorName = $request->custom_color ?: ($issueItem->stockEntryItem->color->color_name ?? ($issueItem->stockEntryItem->grnEntryItem->color->color_name ?? '-'));
        $fabricName = $request->custom_fabric ?: ($issueItem->rawMaterial->name ?? '-');
        $format = $request->format ?? 'tag';
        
        if ($format === 'sticker') {
            $width = $request->width ?? 70;
            $height = $request->height ?? 50;
		} elseif ($format === 'square') {
            $width = $request->width ?? 45;
            $height = $request->height ?? 45;
        } else {
            $width = $request->width ?? 45;
            $height = $request->height ?? 85;
        }
        
        $orientation = $request->orientation ?? 'portrait';
        $margin = $request->margin ?? 2;
        $bg_color = $request->bg_color ?? '#ffffff';
        $v_align = $request->v_align ?? 'top';
        $order = $request->order ?? 'header,product,brand,art,color,fabric,size,mrp,mfg,footer';

        $settings = Setting::first();
        $artNo = $issueItem->job_card_article_matrix_id ? JobCardFabricDetail::find($issueItem->job_card_article_matrix_id)->art_no : ($issueItem->rawMaterial->code ?? '');
        $isStringArtNo = false;
        $cleanedArtNo = '';
        if ($artNo) {
            $hasAlpha = preg_match('/[a-zA-Z]/', $artNo);
            $matchesExistingPattern = preg_match('/^([a-zA-Z]*)(\d+)(?:-(\d+))?$/', $artNo);
            if ($hasAlpha && !$matchesExistingPattern) {
                $isStringArtNo = true;
                $cleanedArtNo = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $artNo));
            }
        }

        if (!$isStringArtNo) {
            preg_match('/([a-zA-Z]*)(\d+)(?:-(\d+))?/', $artNo, $matches);
            $numericBase = $matches[2] ?? '';
            $suffix = $matches[3] ?? '1';
            $formattedSuffix = str_pad($suffix, 2, '0', STR_PAD_LEFT);

            if ($numericBase === '') {
                $noPrefix = preg_replace('/^[a-zA-Z]+/', '', $jobCard->job_card_no ?? '0000');
                $numericBase = preg_replace('/[^A-Za-z0-9]/', '', $noPrefix);
            }
        }

        $style = $issueItem->stockEntryItem->style ?? ($issueItem->stockEntryItem->grnEntryItem->purchaseInvoiceItem->purchaseOrderItem->style ?? null);
        if (!$style && $artNo) {
            $otherStockItem = StockEntryItem::with('style')->where('art_no', $artNo)->whereNotNull('style_id')->first();
            $style = $otherStockItem->style ?? null;
        }
        if (!$style) {
            $allPOItems = $jobCard->purchaseOrder?->items;
            if (!$allPOItems && $artNo) {
                $grnItem = \App\Models\GrnEntryItem::where('art_no', $artNo)->whereHas('purchaseInvoiceItem.purchaseOrderItem')->first();
                $allPOItems = $grnItem?->purchaseInvoiceItem?->purchaseOrderItem?->purchaseOrder?->items;
            }
            $matchingPOItem = $allPOItems ? (
                $allPOItems->where('store_category_id', 1)->whereNotNull('style_id')->first() 
                ?: $allPOItems->whereNotNull('style_id')->first() 
                ?: $allPOItems->first()
            ) : null;
            $style = $matchingPOItem->style ?? ($allPOItems?->whereNotNull('style_id')->first()?->style ?? null);
        }
        if (!$style) {
            $style = $jobCard->item->style ?? null;
        }

        $brandName = $jobCard->brand->brand_name ?? '';
        $styleName = $style->style_name ?? '';
        $brandCode = $jobCard->brand->code ?? '';
        $styleCode = $style->code ?? '';

        $records = [];
        if ($request->bulk_print == 1 && $issueItem->fabricDetail && $issueItem->fabricDetail->quantities->count() > 0) {
            foreach ($issueItem->fabricDetail->quantities as $mq) {
                if ($mq->qty_fs > 0) {
                    $records[] = ['size' => $mq->size, 'sleeve' => 'F/S', 'qty' => $mq->qty_fs];
                }
                if ($mq->qty_hs > 0) {
                    $records[] = ['size' => $mq->size, 'sleeve' => 'H/S', 'qty' => $mq->qty_hs];
                }
            }
        } else {
            $qty = $issueItem->qty_used;
            if ($request->size && $request->sleeve && $issueItem->fabricDetail) {
                $matrixQty = $issueItem->fabricDetail->quantities->where('size', $request->size)->first();
                if ($matrixQty) {
                    if ($request->sleeve == 'F/S') {
                        $qty = $matrixQty->qty_fs > 0 ? $matrixQty->qty_fs : $qty;
                    } elseif ($request->sleeve == 'H/S') {
                        $qty = $matrixQty->qty_hs > 0 ? $matrixQty->qty_hs : $qty;
                    }
                }
            }
            $records[] = ['size' => $request->size ?? 'Bulk', 'sleeve' => $request->sleeve, 'qty' => $qty];
        }

        $labels = [];

        foreach ($records as $record) {
            $selectedSize = $record['size'];
            $selectedSleeve = $record['sleeve'];
            
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

            if ($isStringArtNo) {
                $cleanSize = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $selectedSize));
                $barcodeNo = 'BC' . $cleanedArtNo . $cleanSize . $sleeveCode;
            } else {
                $barcodeNo = 'BC' . $numericBase . $formattedSuffix . $formattedSize . $sleeveCode;
            }

            $priceRecord = \App\Models\ItemPrice::where('status', 'Active')
                ->where('art_no', $artNo)
                ->where('size', $selectedSize)
                ->where(function($q) use ($jobCard, $selectedSleeve) {
                    if ($jobCard->item) {
                        $q->where('finished_item_code', $jobCard->item->code)
                          ->orWhereIn('finished_item_code', function($subQuery) use ($jobCard) {
                              $subQuery->select('finished_item_code')
                                       ->from('stock_entry_items')
                                       ->where('item_id', $jobCard->item->id)
                                       ->whereNull('deleted_at');
                          });
                    } else if ($selectedSleeve && $selectedSleeve !== 'All Sleeves') {
                        if ($selectedSleeve == 'F/S') {
                            $q->where('finished_item_code', 'like', '%-FS%');
                        } elseif ($selectedSleeve == 'H/S') {
                            $q->where('finished_item_code', 'like', '%-HS%');
                        }
                    }
                })
                ->whereDate('effective_from', '<=', now())
                ->orderBy('effective_from', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if (!$priceRecord) {
                $priceRecord = \App\Models\ItemPrice::where('status', 'Active')
                    ->where('art_no', $artNo)
                    ->where('size', $selectedSize)
                    ->whereDate('effective_from', '<=', now())
                    ->orderBy('effective_from', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
            }

            if (!$priceRecord) {
                $priceRecord = \App\Models\ItemPrice::where('status', 'Active')
                    ->where('art_no', $artNo)
                    ->whereDate('effective_from', '<=', now())
                    ->orderBy('effective_from', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
            }
            $mrpPrice = $priceRecord ? $priceRecord->selling_price : ($jobCard->mrp > 0 ? $jobCard->mrp : $issueItem->unit_price);

            $sleeveShort = ($selectedSleeve == 'F/S' || $selectedSleeve == 'Full Sleeve') ? 'F/S' : (($selectedSleeve == 'H/S' || $selectedSleeve == 'Half Sleeve') ? 'H/S' : '');
            $customItemName = trim("$brandName $styleName $sleeveShort");
            $sleeveCodeClean = str_replace('/', '', $sleeveShort);
            $customItemCode = implode('-', array_filter([$brandCode, $styleCode, $sleeveCodeClean]));

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
                'item_name_full' => $customItemName,
                'item_code' => $customItemCode,
                'brand_name' => $jobCard->brand->brand_name ?? '-',
                'design' => $artNo,
                'color' => $colorName,
                'fabric' => $fabricName,
                'size' => $selectedSize,
                'sleeve' => $sleeveText,
                'fit' => $jobCard->fit->fit_name ?? 'Tailor Fit',
                'price' => number_format($mrpPrice, 2),
                'raw_price' => $mrpPrice,
                'mfg_date' => date('F Y'),
                'lot_no' => $jobCard->job_card_no,
                'sku' => $barcodeNo,
                'barcode_no' => $barcodeNo,
                'quantity' => '1 ' . ($record['size'] !== 'Bulk' ? 'Number' : ($issueItem->rawMaterial->uom->uom_code ?? ''))
            ];

            BarcodeMaster::updateOrCreate(
                ['barcode_no' => $barcodeNo],
                [
                    'job_card_entry_id' => $jobCard->id,
                    'item_code' => $customItemCode,
                    'art_no' => $artNo,
                    'item_name' => $customItemName,
                    'sleeve_type' => $sleeveText,
                    'size' => $selectedSize,
                    'quantity' => $record['qty'],
                    'brand_id' => $jobCard->brand_id,
                    'style_id' => $style->id ?? null,
                    'lot_no' => $jobCard->job_card_no,
                    'color_id' => $issueItem->stockEntryItem->grnEntryItem->color_id ?? null,
                    'fabric_type_id' => $jobCard->fabric_type_id
                ]
            );

            $labels[] = $labelData;
        }

        $labelData = count($labels) > 0 ? $labels[0] : [];

        $viewName = ($format === 'square') ? 'labels.print_square_sticker' : (($format === 'tag') ? 'labels.print_tag' : 'labels.print_sticker');
        return view($viewName, compact('labelData', 'labels', 'orientation', 'width', 'height', 'margin', 'bg_color', 'v_align', 'order', 'format'));
    }

    public function barcodePreview($id, Request $request)
    {
        $issueItem = JobCardIssueItem::with(['jobCard.brand', 'jobCard.item.style', 'jobCard.purchaseOrder.items.style', 'jobCard.purchaseOrder.items.rawMaterial', 'rawMaterial.uom', 'stockEntryItem.color', 'stockEntryItem.grnEntryItem.color'])->findOrFail($id);
        $jobCard = $issueItem->jobCard;
        $selectedSize = $request->bulk_print ? 'All Sizes' : ($request->size ?? 'Bulk');
        $selectedSleeve = $request->bulk_print ? 'All Sleeves' : $request->sleeve;

        $artNo = $issueItem->job_card_article_matrix_id ? JobCardFabricDetail::find($issueItem->job_card_article_matrix_id)->art_no : ($issueItem->rawMaterial->code ?? '');

        $priceRecord = \App\Models\ItemPrice::where('status', 'Active')
            ->where('art_no', $artNo)
            ->when(is_numeric($selectedSize), function($q) use ($selectedSize) {
                $q->where('size', $selectedSize);
            })
            ->where(function($q) use ($jobCard, $selectedSleeve) {
                if ($jobCard->item) {
                    $q->where('finished_item_code', $jobCard->item->code)
                      ->orWhereIn('finished_item_code', function($subQuery) use ($jobCard) {
                          $subQuery->select('finished_item_code')
                                   ->from('stock_entry_items')
                                   ->where('item_id', $jobCard->item->id)
                                   ->whereNull('deleted_at');
                      });
                } else if ($selectedSleeve && $selectedSleeve !== 'All Sleeves') {
                    if ($selectedSleeve == 'F/S') {
                        $q->where('finished_item_code', 'like', '%-FS%');
                    } elseif ($selectedSleeve == 'H/S') {
                        $q->where('finished_item_code', 'like', '%-HS%');
                    }
                }
            })
            ->whereDate('effective_from', '<=', now())
            ->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $mrpPrice = $priceRecord ? $priceRecord->selling_price : ($jobCard->mrp > 0 ? $jobCard->mrp : $issueItem->unit_price);

        $labelData = [
            'id' => $id,
            'product_name' => $jobCard->item->name ?? 'SHIRTS',
            'brand_name' => $jobCard->brand->brand_name ?? '-',
            'design' => $artNo,
            'color' => $issueItem->stockEntryItem->color->color_name ?? ($issueItem->stockEntryItem->grnEntryItem->color->color_name ?? '-'),
            'fabric' => $issueItem->rawMaterial->name ?? '-',
            'size' => $selectedSize,
            'sleeve' => $selectedSleeve ?? '-',
            'fit' => $jobCard->fit->fit_name ?? 'Tailor Fit',
            'price' => number_format($mrpPrice, 2),
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

        $allIssueItems = JobCardIssueItem::with(['rawMaterial', 'stockEntryItem.color', 'stockEntryItem.grnEntryItem.color', 'fabricDetail.quantities'])->where('id', $id)->get();

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

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete job-card')) {
            return unauthorizedRedirect();
        }

        $jobCard = JobCardEntry::findOrFail($id);

        DB::beginTransaction();
        try {
            // Revert all stock deductions from all issue items
            $issueItems = JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->get();
            foreach ($issueItems as $existingItem) {
                $oldQtyToDeduct = floatval($existingItem->qty_issue ?? 0);
                if ($oldQtyToDeduct > 0) {
                    $qtyToRevert = $oldQtyToDeduct;
                    if ($existingItem->stock_entry_item_id) {
                        $stockItem = StockEntryItem::find($existingItem->stock_entry_item_id);
                        if ($stockItem) {
                            $revert = min($stockItem->qty_out, $qtyToRevert);
                            $stockItem->qty_out = (float) ($stockItem->qty_out ?? 0) - $revert;
                            $stockItem->save();
                            $qtyToRevert -= $revert;
                        }
                    }
                    if ($qtyToRevert > 0) {
                        $matrix = JobCardFabricDetail::find($existingItem->job_card_article_matrix_id);
                        $artNo = $matrix ? trim($matrix->art_no ?? '') : null;
                        if ($artNo) {
                            $revQuery = StockEntryItem::whereRaw('qty_out > 0')->orderBy('id', 'desc');
                            if ($matrix->stock_entry_id) {
                                $revQuery->where('stock_entry_id', $matrix->stock_entry_id);
                            } else {
                                $seIds = [];
                                if ($jobCard->stock_entry_ids) {
                                    $decoded = json_decode($jobCard->stock_entry_ids, true);
                                    if (is_array($decoded)) {
                                        foreach ($decoded as $cid) {
                                            $seIds[] = strpos($cid, '::') !== false ? explode('::', $cid)[0] : $cid;
                                        }
                                    }
                                }
                                if (!empty($seIds)) {
                                    $revQuery->whereIn('stock_entry_id', array_unique($seIds));
                                }
                            }
                            $revQuery->where(function ($q) use ($artNo) {
                                $q->where('art_no', $artNo)
                                  ->orWhereHas('rawMaterial', function ($q2) use ($artNo) {
                                      $q2->where('code', $artNo)->orWhere('name', $artNo);
                                  });
                            });
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
                $existingItem->forceDelete();
            }

            $oldData = $jobCard->toArray();
            $jobCard->delete();

            addLog('delete', 'Job Card Entry', 'job_card_entries', $id, $oldData, null);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Job Card deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}



