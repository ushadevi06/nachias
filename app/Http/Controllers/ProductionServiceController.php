<?php

namespace App\Http\Controllers;

use App\Models\ProductionService;
use App\Models\OperationStage;
use Illuminate\Http\Request;

class ProductionServiceController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production services')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $services = ProductionService::with('operationStage')->latest()->get();

            $data = [];
            $i = 1;

            foreach ($services as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input service-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit production services')) {
                    $action .= '<a href="' . url('production_services/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete production services')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('production_services/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'  => $i++,
                    'service_code' => $row->service_code,
                    'service_name' => $row->service_name,
                    'operation_stage' => $row->operationStage ? $row->operationStage->operation_stage_name : '-',
                    'applies_to'   => $row->applies_to,
                    'multiplier'   => $row->multiplier,
                    'status'       => $status,
                    'action'       => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('production_services.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit production services')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create production services')) {
                return unauthorizedRedirect();
            }
        }

        $service = $id ? ProductionService::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'service_name' => 'required|string|max:255|unique:production_services,service_name,' . $id . ',id,deleted_at,NULL',
                'service_code' => 'required|string|max:50|unique:production_services,service_code,' . $id . ',id,deleted_at,NULL',
                'operation_stage_id' => 'required|exists:operation_stages,id',
                'status'       => 'required|in:Active,Inactive',
                'applies_to'   => 'required|in:ALL,Full Sleeve,Half Sleeve,Both',
                'base_quantity_source' => 'required|in:Total Qty,FS Qty,HS Qty',
                'multiplier'   => 'required|numeric|min:0',
                'uom'          => 'required|string|max:20',
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only([
                'service_name', 'service_code', 'operation_stage_id', 'status',
                'applies_to', 'base_quantity_source', 'multiplier', 'uom'
            ]);

            if ($id) {
                $data['updated_by'] = auth()->id();
                ProductionService::where('id', $id)->update($data);
                addLog('update', 'Production Service', 'production_services', $id, null, $data);
                $msg = 'Production Service updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newService = ProductionService::create($data);
                addLog('create', 'Production Service', 'production_services', $newService->id, null, $data);
                $msg = 'Production Service added successfully';
            }

            return redirect('production_services')->with('success', $msg);
        }

        $operationStages = OperationStage::active()->get();
        $uoms = \App\Models\Uom::active()->get();

        return view('production_services.add', compact('service', 'operationStages', 'uoms'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete production services')) {
            return unauthorizedRedirect();
        }

        $service = ProductionService::findOrFail($id);
        $oldData = $service->toArray();
        $service->delete();
        addLog('delete', 'Production Service', 'production_services', $id, $oldData, null);
        return redirect('production_services')->with('success', 'Production Service deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $service = ProductionService::findOrFail($id);
        $oldData = $service->toArray();
        $service->status = $request->status;
        $service->save();
        $newData = $service->toArray();
        addLog('update_status', 'Production Service Status', 'production_services', $service->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $service->status
        ]);
    }
}
