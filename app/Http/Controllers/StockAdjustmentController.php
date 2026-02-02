<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskAdjustment;
use App\Models\RawMaterial;
use App\Models\StockEntryItem;
use App\Models\StockConsumableIssue;
use App\Models\StockConsumableIssueItem;
use App\Models\StockConsumableStockDetail;
use App\Models\TaskReceive;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view task')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $adjustments = TaskAdjustment::with(['task.jobCard', 'service', 'task.assignee'])->orderBy('created_at', 'desc')->get();

            $data = [];
            foreach ($adjustments as $index => $row) {
                $material = RawMaterial::find($row->raw_material_id);
                $materialText = $material ? $material->name . ($material->code ? " ({$material->code})" : "") : '-';
                
                $badgeClass = 'bg-label-info';
                if ($row->adjustment_type === 'Loss') $badgeClass = 'bg-label-danger';
                if ($row->adjustment_type === 'Excess') $badgeClass = 'bg-label-success';
                if ($row->adjustment_type === 'Rework') $badgeClass = 'bg-label-warning';
                
                $typeHtml = '<span class="badge ' . $badgeClass . '">' . $row->adjustment_type . '</span>';

                $btn = '<div class="d-flex gap-2">';
                $btn .= '<button type="button" onclick="editAdjustment(' . $row->id . ')" class="btn btn-edit"><i class="ri ri-edit-line"></i></button>';
                $btn .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $index + 1,
                    'adjustment_no' => $row->adjustment_no,
                    'job_card_no' => $row->task->jobCard->job_card_no ?? '-',
                    'task_no' => $row->task->task_no ?? '-',
                    'employee' => $row->task->assignee->name ?? '-',
                    'material' => $materialText,
                    'adjustment_type' => $typeHtml,
                    'qty' => $row->qty,
                    'reason' => $row->reason,
                    'action' => $btn
                ];
            }

            return response()->json(['data' => $data]);
        }

        $tasks = Task::with('jobCard')->orderBy('created_at', 'desc')->get();
        $nextAdjNo = 'ADJ-' . date('Y') . '-' . str_pad(TaskAdjustment::count() + 1, 3, '0', STR_PAD_LEFT);
        return view('stock_adjustments.index', compact('tasks', 'nextAdjNo'));
    }

    public function edit($id)
    {
        $adjustment = TaskAdjustment::with(['task.jobCard'])->find($id);
        if (!$adjustment) {
            return response()->json([
                'success' => false,
                'message' => 'Stock adjustment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $adjustment
        ]);
    }


    public function store(Request $request, $id = null)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'adjustment_type' => 'required',
            'qty' => 'required|numeric|min:0.01',
            'reason' => 'required',
            'raw_material_id' => 'required|exists:raw_materials,id'
        ], [
            'required' => 'This field is required.'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            if (!$id) {
                if (!$request->adjustment_no) {
                    $count = TaskAdjustment::count();
                    $data['adjustment_no'] = 'ADJ-' . date('Y') . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                }
                $data['created_by'] = auth()->id();
                $adjustment = TaskAdjustment::create($data);
                $message = 'Stock adjustment created successfully';
            } else {
                $adjustment = TaskAdjustment::find($id);
                if (!$adjustment) {
                    throw new \Exception("Stock adjustment not found.");
                }
                $data['updated_by'] = auth()->id();
                $adjustment->update($data);
                $message = 'Stock adjustment updated successfully';
            }

            $task = Task::find($request->task_id);
            if ($task) {
                if ($request->adjustment_type == 'Loss' || $request->adjustment_type == 'Rework') {
                    $this->deductStock($request->raw_material_id, $request->qty, "{$request->adjustment_type} Adjustment {$adjustment->adjustment_no}: {$request->reason}", $task, $request->adjustment_type);
                } elseif ($request->adjustment_type == 'Excess' || $request->adjustment_type == 'Return' || $request->adjustment_type == 'Material Return') {
                    $this->addStock($request->raw_material_id, $request->qty, "{$request->adjustment_type} Adjustment {$adjustment->adjustment_no}: {$request->reason}", $task, $request->adjustment_type);
                }
            }

            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return redirect()->route('stock_adjustments.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    private function deductStock($materialId, $qty, $remarks, $task, $type = 'Stock Adjustment')
    {
        $stageName = $task->stage->operationStage->operation_stage_name ?? ($task->stage->stage ?? '-');
        
        $issueCount = StockConsumableIssue::count();
        $issueNo = 'ISSUE-ADJ-' . date('Ymd') . '-' . str_pad($issueCount + 1, 4, '0', STR_PAD_LEFT);
        
        $issueType = 'Stock Adjustment';
        if ($type == 'Rework') $issueType = 'Rework';
        
        $issue = StockConsumableIssue::create([
            'issue_no' => $issueNo,
            'issue_date' => date('Y-m-d'),
            'issue_type' => $issueType,
            'production_stage' => $stageName,
            'remarks' => $remarks,
            'status' => 'Posted',
            'created_by' => auth()->id(),
        ]);

        $remainingToDeduct = $qty;
        $tempStockUsage = [];
        $weightedCost = 0;

        $mat = RawMaterial::find($materialId);
        $matName = $mat ? $mat->name : "Unknown Item";

        $stockCandidates = StockEntryItem::where('raw_material_id', $materialId)
            ->whereRaw('(qty_in - qty_out) > 0')
            ->orderBy('id', 'asc')
            ->get();

        $totalAvailable = $stockCandidates->sum(function($item) { return $item->qty_in - $item->qty_out; });

        if ($totalAvailable < $qty) {
            throw new \Exception("Insufficient stock for '{$matName}'. Available: {$totalAvailable}, Required: {$qty}");
        }

        foreach ($stockCandidates as $stockItem) {
            if ($remainingToDeduct <= 0) break;
            $available = $stockItem->qty_in - $stockItem->qty_out;
            if ($available <= 0) continue;

            $take = min($available, $remainingToDeduct);
            $stockItem->qty_out += $take;
            $stockItem->save();

            $tempStockUsage[] = ['stock_entry_item_id' => $stockItem->id, 'qty' => $take];
            $weightedCost += ($take * $stockItem->price);
            $remainingToDeduct -= $take;
        }

        $unitPrice = ($qty > 0) ? ($weightedCost / $qty) : 0;

        $issueItem = StockConsumableIssueItem::create([
            'stock_consumable_issue_id' => $issue->id,
            'raw_material_id' => $materialId,
            'stock_entry_item_id' => $tempStockUsage[0]['stock_entry_item_id'],
            'qty_issued' => $qty,
            'qty_returned' => 0,
            'net_consumption' => $qty,
            'uom_id' => $mat->uom_id ?? null,
            'unit_price' => $unitPrice,
            'total_value' => $qty * $unitPrice,
            'created_by' => auth()->id(),
        ]);

        foreach ($tempStockUsage as $usage) {
            StockConsumableStockDetail::create([
                'stock_consumable_issue_item_id' => $issueItem->id,
                'stock_entry_item_id' => $usage['stock_entry_item_id'],
                'qty' => $usage['qty']
            ]);
            
            $stockEntryItem = StockEntryItem::find($usage['stock_entry_item_id']);
            if ($stockEntryItem) {
                $stockEntryItem->qty_in += $usage['qty'];
                $stockEntryItem->save();
            }
        }
    }

    private function addStock($materialId, $qty, $remarks, $task, $type = 'Stock Adjustment')
    {
        $stockEntryItem = StockEntryItem::where('raw_material_id', $materialId)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($stockEntryItem) {
            $stockEntryItem->qty_in += $qty;
            $stockEntryItem->save();
        }
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete task')) {
            return unauthorizedRedirect();
        }

        $adjustment = TaskAdjustment::find($id);
        if (!$adjustment) {
            return response()->json([
                'success' => false,
                'message' => 'Stock adjustment not found'
            ], 404);
        }
        
        $adjustment->delete();

        return response()->json(['success' => true, 'message' => 'Adjustment deleted successfully']);
    }

    public function get_task_materials($taskId)
    {
        $task = Task::with(['jobCard.fabricDetails', 'stage.operationStage', 'assignee'])->find($taskId);
        if (!$task) return response()->json(['success' => false]);
        $stageName = $task->stage->operationStage->operation_stage_name ?? ($task->stage->stage ?? '');
        $artNumbers = $task->jobCard->fabricDetails->pluck('art_no')->filter()->toArray();
        $rmIdsFromArt = RawMaterial::whereIn('code', $artNumbers)->pluck('id')->toArray();

        $rmIdsFromIssues = \App\Models\StockEntryItem::whereIn('id', function($q) use ($task) {
            $q->select('stock_entry_item_id')->from('job_card_issue_items')->where('job_card_entry_id', $task->job_card_entry_id);
        })->pluck('raw_material_id')->toArray();

        $consumableConfigs = \App\Models\ProductionStageConsumable::where('stage', $stageName)->where('status', 'Active')->pluck('raw_material_id')->toArray();
        $allRelatedRmIds = array_unique(array_merge($rmIdsFromArt, $rmIdsFromIssues, $consumableConfigs));

        $query = RawMaterial::where('status', 'Active');
        if (!empty($allRelatedRmIds)) {
            $query->whereIn('id', $allRelatedRmIds);
        } else {
            $query->whereIn('id', $consumableConfigs);
        }

        $materials = $query->get();

        $formatted = $materials->map(function($m) use ($consumableConfigs) {
            $isConsumable = in_array($m->id, $consumableConfigs);
            return [
                'id' => $m->id,
                'text' => $m->name . ($m->code ? " ({$m->code})" : "") . ($isConsumable ? " [Consumable]" : "")
            ];
        });

        return response()->json([
            'success' => true,
            'materials' => $formatted,
            'job_card_no' => $task->jobCard->job_card_no ?? '-',
            'stage' => $stageName,
            'employee' => $task->assignee->name ?? '-'
        ]);
    }
}
