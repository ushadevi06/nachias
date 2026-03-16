<?php

namespace App\Http\Controllers;

use App\Models\TransportMode;
use Illuminate\Http\Request;

class TransportModeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view transport-modes')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $transportModes = TransportMode::latest()->get();

            $data = [];
            $i = 1;

            foreach ($transportModes as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input transport-mode-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit transport-modes')) {
                    $action .= '<a href="' . url('transport_modes/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete transport-modes')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('transport_modes/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'name'        => $row->name,
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('logistics_master.transport_mode.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit transport-modes')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create transport-modes')) {
                return unauthorizedRedirect();
            }
        }

        $transportMode = $id ? TransportMode::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'name'   => 'required|string|min:2|max:50|unique:transport_modes,name,' . $id . ',id,deleted_at,NULL',
                'status' => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                TransportMode::where('id', $id)->update($data);
                addLog('update', 'Transport Mode', 'transport_modes', $id, null, $data);
                $msg = 'Transport Mode updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newMode = TransportMode::create($data);
                addLog('create', 'Transport Mode', 'transport_modes', $newMode->id, null, $data);
                $msg = 'Transport Mode added successfully';
            }

            return redirect('transport_modes')->with('success', $msg);
        }

        return view('logistics_master.transport_mode.add', compact('transportMode'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete transport-modes')) {
            return unauthorizedRedirect();
        }
        $transportMode = TransportMode::findOrFail($id);

        $oldData = $transportMode->toArray();
        $transportMode->delete();
        addLog('delete', 'Transport Mode', 'transport_modes', $id, $oldData, null);
        return redirect('transport_modes')->with('success', 'Transport Mode deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $mode = TransportMode::findOrFail($id);
        $oldData = $mode->toArray();
        $mode->status = $request->status;
        $mode->save();
        $newData = $mode->toArray();
        addLog('update_status', 'Transport Mode Status', 'transport_modes', $mode->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $mode->status
        ]);
    }
}
