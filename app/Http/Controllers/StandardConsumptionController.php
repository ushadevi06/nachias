<?php

namespace App\Http\Controllers;

use App\Models\StandardConsumption;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class StandardConsumptionController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view standard-consumption')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = StandardConsumption::with('rawMaterial')->latest()->get();

            $data = [];
            $i = 1;

            foreach ($query as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input sc-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit standard-consumption')) {
                    $action .= '<a href="' . url('standard_consumptions/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete standard-consumption')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('standard_consumptions/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'raw_material' => ($row->rawMaterial->name ?? '-') . ' (' . ($row->rawMaterial->code ?? '-') . ')',
                    'fs_qty'       => $row->fs_qty,
                    'hs_qty'       => $row->hs_qty,
                    'status'       => $status,
                    'action'       => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('standard_consumptions.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit standard-consumption')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create standard-consumption')) {
                return unauthorizedRedirect();
            }
        }

        $standardConsumption = $id ? StandardConsumption::with('rawMaterial')->findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'raw_material_id' => 'required|exists:raw_materials,id|unique:standard_consumptions,raw_material_id,' . $id . ',id,deleted_at,NULL',
                'fs_qty' => ['required', 'numeric', 'min:0', 'not_regex:/^0+(\\.0+)?$/'],
                'hs_qty' => ['required', 'numeric', 'min:0', 'not_regex:/^0+(\\.0+)?$/'],
                'status'   => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.min'      => 'This field must be at least :min.',
                '*.not_regex' => 'This field cannot be zero.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['raw_material_id', 'fs_qty', 'hs_qty', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                StandardConsumption::where('id', $id)->update($data);
                addLog('update', 'Standard Consumption', 'standard_consumptions', $id, null, $data);
                $msg = 'Standard Consumption updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newSc = StandardConsumption::create($data);
                addLog('create', 'Standard Consumption', 'standard_consumptions', $newSc->id, null, $data);
                $msg = 'Standard Consumption added successfully';
            }

            return redirect('standard_consumptions')->with('success', $msg);
        }

        return view('standard_consumptions.add', compact('standardConsumption'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete standard-consumption')) {
            return unauthorizedRedirect();
        }

        $sc = StandardConsumption::findOrFail($id);
        $oldData = $sc->toArray();
        $sc->delete();
        addLog('delete', 'Standard Consumption', 'standard_consumptions', $id, $oldData, null);

        return redirect('standard_consumptions')->with('success', 'Standard Consumption deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $sc = StandardConsumption::findOrFail($id);
        $oldData = $sc->toArray();
        $sc->status = $request->status;
        $sc->save();
        $newData = $sc->toArray();
        addLog('update_status', 'Standard Consumption Status', 'standard_consumptions', $sc->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $sc->status
        ]);
    }
}
