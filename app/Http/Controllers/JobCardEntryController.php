<?php

namespace App\Http\Controllers;

use App\Models\JobCardEntry;
use App\Models\JobCardItem;
use App\Models\JobCardImage;
use App\Models\JobCardOperation;
use App\Models\PurchaseOrder;
use App\Models\Brand;
use App\Models\Season;
use App\Models\ProcessGroup;
use App\Models\SizeRatio;
use App\Models\OperationStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Fit;
use App\Models\PattiType;
use App\Models\CollarType;
use App\Models\CuffType;
use App\Models\PocketType;
use App\Models\BottomCut;
use App\Models\GrnEntry;
use App\Models\GrnEntryItem;
use App\Models\PurchaseInvoice;

class JobCardEntryController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $jobCards = JobCardEntry::with(['purchaseOrder', 'brand', 'season', 'processGroup'])
                ->orderBy('id', 'desc')
                ->get();
            
            $data = [];
            foreach ($jobCards as $index => $jc) {
                $action = '<div class="button-box">
                    <a href="' . url('job_card_entries/view/' . $jc->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                    <a href="' . url('job_card_entries/view-item/' . $jc->id) . '" class="btn btn-item" title="Issue Item"><i class="icon-base ri ri-list-check-2"></i></a>
                    <a href="' . url('job_card_entries/add/' . $jc->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                </div>';
                
                $data[] = [
                    'DT_RowIndex' => $index + 1,
                    'job_card_no' => $jc->job_card_no,
                    'job_card_date' => date('d-m-Y', strtotime($jc->job_card_date)),
                    'po_number' => $jc->purchaseOrder->po_number ?? '-',
                    'brand' => $jc->brand->brand_name ?? '-',
                    'season' => $jc->season->name ?? '-',
                    'process_group' => $jc->processGroup->name ?? '-',
                    'total_qty' => $jc->grand_total_qty,
                    'status' => '<span class="badge bg-label-primary">' . $jc->status . '</span>',
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
                'fit' => 'nullable|string',
                'patti_type' => 'nullable|string',
                'collar_type' => 'nullable|string',
                'cuff_type' => 'nullable|string',
                'pocket_type' => 'nullable|string',
                'bottom_cut' => 'nullable|string',
                'cutting_master_id' => 'required|exists:users,id',
                'cutting_date' => 'required|date_format:d-m-Y',
                'cutting_issue_unit' => 'required|string',
                'size_ratio_id' => 'required|exists:size_ratios,id',
                'stages' => 'required|array|min:1',
            ];

            $messages = [
                '*.required' => 'This field is required',
                'job_card_no.unique' => 'This Job Card Number already exists.',
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
                    'job_card_date' => date('Y-m-d', strtotime($request->issue_date)),
                    'delivery_date' => $request->delivery_date ? date('Y-m-d', strtotime($request->delivery_date)) : null,
                    'washing' => $request->washing,
                    'width' => $request->width,
                    'mrp' => $request->mrp,
                    'fs_qty' => $request->fs,
                    'hs_qty' => $request->hs,
                    'receipt_store' => $request->receipt_store,
                    'remarks' => $request->remarks,
                    'status' => $request->status ?? 'Draft',
                    'fit' => $request->fit,
                    'patti_type' => $request->patti_type,
                    'collar_type' => $request->collar_type,
                    'cuff_type' => $request->cuff_type,
                    'pocket_type' => $request->pocket_type,
                    'bottom_cut' => $request->bottom_cut,
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
                    $jobCard = JobCardEntry::findOrFail($id);
                    $jobCard->update($data);
                    $jobCard->items()->forceDelete();
                    $jobCard->articleMatrices()->forceDelete();
                    $jobCard->operations()->delete();
                } else {
                    $data['created_by'] = auth()->id();
                    $jobCard = JobCardEntry::create($data);
                }

                if ($request->matrix_items) {
                    foreach ($request->matrix_items as $item) {
                        $jobCard->items()->create([
                            'article_no' => $item['article_no'],
                            'size' => $item['size'],
                            'ratio' => $item['ratio'] ?? 0,
                            'qty_fs' => $item['qty_fs'] ?? 0,
                            'qty_hs' => $item['qty_hs'] ?? 0,
                            'total_qty' => ($item['qty_fs'] ?? 0) + ($item['qty_hs'] ?? 0),
                        ]);
                    }
                }

                if ($request->article_matrix) {
                    foreach ($request->article_matrix as $index => $matrix) {
                        $fabric = collect($request->fabrics)->where('art_no', $matrix['art_no'])->first() ?? ($request->fabrics[$index] ?? null);
                        
                        $jobCard->articleMatrices()->create([
                            'art_no'  => $matrix['art_no'],
                            'width'   => $fabric['width'] ?? null,
                            'mtr'     => $fabric['mtr'] ?? null,
                            'in_out'  => $fabric['in_out'] ?? null,
                            'n_patti' => $fabric['n_patti'] ?? null,
                            'fs_36'   => $matrix['fs_36'] ?? 0,
                            'fs_38'   => $matrix['fs_38'] ?? 0,
                            'fs_40'   => $matrix['fs_40'] ?? 0,
                            'fs_42'   => $matrix['fs_42'] ?? 0,
                            'fs_44'   => $matrix['fs_44'] ?? 0,
                            'hs_38'   => $matrix['hs_38'] ?? 0,
                            'hs_40'   => $matrix['hs_40'] ?? 0,
                            'hs_42'   => $matrix['hs_42'] ?? 0,
                            'hs_44'   => $matrix['hs_44'] ?? 0,
                            'hs_46'   => $matrix['hs_46'] ?? 0,
                            'ex_1'    => $matrix['ex_1'] ?? 0,
                            'ex_2'    => $matrix['ex_2'] ?? 0,
                            'row_total' => array_sum([
                                $matrix['fs_36'] ?? 0, $matrix['fs_38'] ?? 0, $matrix['fs_40'] ?? 0, $matrix['fs_42'] ?? 0, $matrix['fs_44'] ?? 0,
                                $matrix['hs_38'] ?? 0, $matrix['hs_40'] ?? 0, $matrix['hs_42'] ?? 0, $matrix['hs_44'] ?? 0, $matrix['hs_46'] ?? 0,
                                $matrix['ex_1'] ?? 0, $matrix['ex_2'] ?? 0
                            ])
                        ]);
                    }
                }

                if ($request->stages) {
                    foreach ($request->stages as $stage) {
                        $jobCard->operations()->create([
                            'operation_stage_id' => $stage['stage_id'] ?? null,
                            'employee_id' => $stage['employee_id'] ?? null,
                            'assigned_date' => $stage['issue_date'] ? date('Y-m-d', strtotime($stage['issue_date'])) : date('Y-m-d'),
                            'received_by' => $stage['received_by'] ?? null,
                        ]);
                    }
                }

                if ($request->has('fabric_images')) {
                    $uploadPath = public_path('uploads/job_cards');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    foreach ($request->fabric_images as $index => $files) {
                        if (is_array($files)) {
                            foreach ($files as $file) {
                                if ($file && $file->isValid()) {
                                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                                    $file->move($uploadPath, $fileName);
                                    $jobCard->images()->create([
                                        'image' => 'uploads/job_cards/' . $fileName,
                                        'art_no' => $request->fabrics[$index]['art_no'] ?? null
                                    ]);
                                }
                            }
                        }
                    }
                }

                DB::commit();
                return redirect('job_card_entries')->with('success', 'Job Card saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('danger', 'Error: ' . $e->getMessage());
            }
        }

        $jobCard = $id ? JobCardEntry::with(['items', 'images', 'operations.operationStage', 'sizeRatio'])->findOrFail($id) : null;
        
        $allPurchaseOrders = PurchaseOrder::with(['items'])->orderBy('id', 'desc')->get();
        
        $purchaseOrders = $allPurchaseOrders->filter(function($po) use ($jobCard) {
            if ($jobCard && $po->id == $jobCard->purchase_order_id) {
                return true;
            }
            return !JobCardEntry::where('purchase_order_id', $po->id)->exists();
        });

        $brands = Brand::where('status', 'Active')->orderBy('brand_name')->get();
        $seasons = Season::where('status', 'Active')->orderBy('name')->get();
        $processGroups = ProcessGroup::where('status', 'Active')->orderBy('name')->get();
        $sizeRatios = SizeRatio::where('status', 'Active')->orderBy('id')->get();
        $operationStages = OperationStage::where('status', 'Active')->orderBy('operation_stage_name')->get();
        $employees = User::where('status', 'Active')->orderBy('name')->get();
        
        $fits = Fit::active()->orderBy('fit_name')->get();
        $pattiTypes = PattiType::active()->orderBy('patti_type_name')->get();
        $collarTypes = CollarType::active()->orderBy('collar_type_name')->get();
        $cuffTypes = CuffType::active()->orderBy('cuff_type_name')->get();
        $pocketTypes = PocketType::active()->orderBy('pocket_type_name')->get();
        $bottomCuts = BottomCut::active()->orderBy('bottom_cut_name')->get();
        $cuttingMasters = User::active()->orderBy('name')->get();

        return view('job_card_entry/add', compact(
            'jobCard', 'purchaseOrders', 'brands', 'seasons', 
            'processGroups', 'sizeRatios', 'operationStages', 'employees',
            'fits', 'pattiTypes', 'collarTypes', 'cuffTypes', 'pocketTypes', 
            'bottomCuts', 'cuttingMasters'
        ));
    }

    public function view_details($id) {
        $jobCard = JobCardEntry::with(['purchaseOrder', 'brand', 'season', 'processGroup', 'items', 'images', 'operations.operationStage', 'operations.employee'])->findOrFail($id);
        return view('job_card_entry/view_details', compact('jobCard'));
    }

    public function destroy($id) {
        $jobCard = JobCardEntry::findOrFail($id);
        $jobCard->delete();
        return response()->json(['success' => true, 'message' => 'Job Card deleted successfully']);
    }

    public function view_jc_item($id) {
        $jobCard = JobCardEntry::with(['articleMatrices', 'purchaseOrder.items.rawMaterial', 'purchaseOrder.supplier', 'purchaseOrder.items.uom'])->findOrFail($id);
        
        // Fetch GRN material names and locations for each article number
        $invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
        $grnItems = GrnEntryItem::whereIn('grn_entry_id', function($query) use ($invoiceIds) {
            $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
        })->with(['purchaseInvoiceItem.rawMaterial', 'fabricType', 'storeLocation'])->get();

        $artMaterialMap = [];
        $artLocationMap = [];
        foreach ($grnItems as $item) {
            $name = $item->purchaseInvoiceItem->rawMaterial->name ?? ($item->fabricType->name ?? null);
            if ($name && !isset($artMaterialMap[$item->art_no])) {
                $artMaterialMap[$item->art_no] = $name;
            }
            if ($item->storeLocation && !isset($artLocationMap[$item->art_no])) {
                $artLocationMap[$item->art_no] = $item->storeLocation->store_location;
            }
        }

        return view('job_card_entry/view_jc_item', compact('jobCard', 'artMaterialMap', 'artLocationMap'));
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

        $artData = GrnEntryItem::whereIn('grn_entry_id', $grnIds)
            ->select('art_no', DB::raw('SUM(qty_accepted) as mtr'))
            ->groupBy('art_no')
            ->get();

        if ($artData->isEmpty()) {
            $artData = $po->items->map(function($item) {
                return [
                    'art_no' => $item->art_no,
                    'mtr' => $item->quantity
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

    public function deleteImage($id) {
        $image = JobCardImage::findOrFail($id);
        $filePath = public_path($image->image);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $image->delete();
        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }
}
