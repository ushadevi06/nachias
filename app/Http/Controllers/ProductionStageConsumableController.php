<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionStageConsumable;
use App\Models\OperationStage;
use App\Models\RawMaterial;
use App\Models\Uom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionStageConsumableController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductionStageConsumable::with(['rawMaterial', 'uom'])->latest()->get();
            
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('raw_material_name', function($row){
                    return $row->rawMaterial->name ?? '-';
                })
                ->addColumn('uom_name', function($row){
                    return $row->uom->uom_code ?? '-';
                })
                ->addColumn('status', function($row){
                    $checked = $row->status == 'Active' ? 'checked' : '';
                    return '<label class="switch switch-success switch-lg">
                                <input type="checkbox" class="switch-input status-toggle" data-id="'.$row->id.'" '.$checked.'>
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                            </label>';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="d-flex gap-2">';
                    $btn .= '<a href="'.url('production_stage_consumables/add/'.$row->id).'" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>';
                    $btn .= '<a href="'.url('production_stage_consumables/delete/'.$row->id).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')"><i class="ri-delete-bin-line"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('production_stage_consumables.view');
    }

    public function add($id = null)
    {
        $consumable = null;
        if ($id) {
            $consumable = ProductionStageConsumable::findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();
            $request->validate([
                'stage' => 'required',
                'raw_material_id' => 'required|exists:raw_materials,id',
                'quantity_per_unit' => 'required|numeric|min:0.0001',
                'uom_id' => 'required|exists:uoms,id',
                'status' => 'required|in:Active,Inactive'
            ]);

            DB::beginTransaction();
            try {
                $data = $request->except('_token');
                
                if ($id) {
                    $data['updated_by'] = Auth::id();
                    $consumable->update($data);
                    $msg = 'Updated successfully';
                } else {
                    $data['created_by'] = Auth::id();
                    ProductionStageConsumable::create($data);
                    $msg = 'Created successfully';
                }

                DB::commit();
                return redirect('production_stage_consumables')->with('success', $msg);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', $e->getMessage())->withInput();
            }
        }

        $stages = OperationStage::where('status', 'Active')->get();
        $rawMaterials = RawMaterial::where('status', 'Active')->get();
        $uoms = Uom::where('status', 'Active')->get();

        return view('production_stage_consumables.add', compact('consumable', 'stages', 'rawMaterials', 'uoms'));
    }

    public function delete($id)
    {
        $consumable = ProductionStageConsumable::findOrFail($id);
        $consumable->delete();
        return redirect('production_stage_consumables')->with('success', 'Deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $consumable = ProductionStageConsumable::findOrFail($id);
        $consumable->status = $request->status;
        $consumable->save();
        return response()->json(['success' => true]);
    }
}
