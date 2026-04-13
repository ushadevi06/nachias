<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionStageConsumable;
use App\Models\OperationStage;
use Illuminate\Support\Facades\DB;

class StockConsumableReturnController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductionStageConsumable::with(['jobCard', 'rawMaterial', 'uom'])->where('status', 'Active')->where('item_type', 'Consumable');

            if ($request->production && $request->production != '') {
                $query->where('stage', $request->production);
            }

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('art_no', 'like', "%{$search}%")
                        ->orWhere('stage', 'like', "%{$search}%")
                        ->orWhereHas('jobCard', function ($q2) use ($search) {
                            $q2->where('job_card_no', 'like', "%{$search}%");
                        })
                        ->orWhereHas('rawMaterial', function ($q3) use ($search) {
                            $q3->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $consumables = $query->latest()->get();
            $data = [];
            $i = $start + 1;

            foreach ($consumables as $row) {
                $data[] = [
                    'DT_RowIndex' => $i++,
                    'production_no' => $row->production ? 'PROD-' . str_pad($row->production_id, 4, '0', STR_PAD_LEFT) : '-',
                    'job_card_no' => $row->jobCard ? $row->jobCard->job_card_no : '-',
                    'production_stage' => $row->stage,
                    'art_no' => $row->art_no,
                    'material_name' => $row->rawMaterial ? $row->rawMaterial->name : '-',
                    'uom' => $row->uom ? $row->uom->uom_code : '-',
                    'fs_qty' => number_format($row->fs_qty, 2),
                    'hs_qty' => number_format($row->hs_qty, 2),
                    'total_issue_qty' => number_format($row->actual_qty, 2),
                    'issued_date' => $row->created_at->format('d-m-Y'),
                    'action' => '<a href="' . url('stock_consumables_returns/view/' . $row->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>',
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }
        $stages = OperationStage::orderBy('id','desc')->get();
        return view('stock_consumable_returns/view', compact('stages'));
    }
    public function add(){
        return view('stock_consumable_returns/add');
    }
    public function view($id){
        $consumable = ProductionStageConsumable::with(['jobCard', 'rawMaterial', 'uom'])->findOrFail($id);
        return view('stock_consumable_returns/view_details', compact('consumable'));
    }
}
