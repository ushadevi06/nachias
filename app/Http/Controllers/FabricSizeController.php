<?php

namespace App\Http\Controllers;

use App\Models\FabricSize;
use Illuminate\Http\Request;

class FabricSizeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view fabric-sizes')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $fabricSizes = FabricSize::latest()->get();

            $data = [];
            $i = 1;

            foreach ($fabricSizes as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input fabric-size-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit fabric-sizes')) {
                    $action .= '<a href="' . url('fabric-sizes/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete fabric-sizes')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('fabric-sizes/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'width'       => $row->width,
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('fabric_sizes.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit fabric-sizes')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create fabric-sizes')) {
                return unauthorizedRedirect();
            }
        }

        $fabricSize = $id ? FabricSize::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'width'  => 'required|string|min:1|max:50|unique:fabric_sizes,width,' . $id . ',id,deleted_at,NULL',
                'status' => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['width', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                FabricSize::where('id', $id)->update($data);
                addLog('update', 'Fabric Size', 'fabric_sizes', $id, null, $data);
                $msg = 'Fabric Size updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newFabricSize = FabricSize::create($data);
                addLog('create', 'Fabric Size', 'fabric_sizes', $newFabricSize->id, null, $data);
                $msg = 'Fabric Size added successfully';
            }

            return redirect('fabric-sizes')->with('success', $msg);
        }

        return view('fabric_sizes.add', compact('fabricSize'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete fabric-sizes')) {
            return unauthorizedRedirect();
        }
        $fabricSize = FabricSize::findOrFail($id);
        $oldData = $fabricSize->toArray();
        $fabricSize->delete();
        addLog('delete', 'Fabric Size', 'fabric_sizes', $id, $oldData, null);
        return redirect('fabric-sizes')->with('success', 'Fabric Size deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $fabricSize = FabricSize::findOrFail($id);
        $oldData = $fabricSize->toArray();
        $fabricSize->status = $request->status;
        $fabricSize->save();
        $newData = $fabricSize->toArray();
        addLog('update_status', 'Fabric Size Status', 'fabric_sizes', $fabricSize->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $fabricSize->status
        ]);
    }
}
