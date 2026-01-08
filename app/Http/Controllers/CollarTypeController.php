<?php

namespace App\Http\Controllers;

use App\Models\CollarType;
use Illuminate\Http\Request;

class CollarTypeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view collar types')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $collarTypes = CollarType::latest()->get();

            $data = [];
            $i = 1;

            foreach ($collarTypes as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input collar-type-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit collar types')) {
                    $action .= '<a href="' . url('collar_types/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete collar types')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('collar_types/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'       => $i++,
                    'collar_type_name'  => $row->collar_type_name,
                    'status'            => $status,
                    'action'            => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('collar_types.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit collar types')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create collar types')) {
                return unauthorizedRedirect();
            }
        }

        $collarType = $id ? CollarType::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'collar_type_name' => 'required|string|max:255|unique:collar_types,collar_type_name,' . $id . ',id,deleted_at,NULL',
                'status'          => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['collar_type_name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                CollarType::where('id', $id)->update($data);
                addLog('update', 'Collar Type', 'collar_types', $id, null, $data);
                $msg = 'Collar Type updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newCollarType = CollarType::create($data);
                addLog('create', 'Collar Type', 'collar_types', $newCollarType->id, null, $data);
                $msg = 'Collar Type added successfully';
            }

            return redirect('collar_types')->with('success', $msg);
        }

        return view('collar_types.add', compact('collarType'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete collar types')) {
            return unauthorizedRedirect();
        }

        $collarType = CollarType::findOrFail($id);
        $oldData = $collarType->toArray();
        $collarType->delete();
        addLog('delete', 'Collar Type', 'collar_types', $id, $oldData, null);
        return redirect('collar_types')->with('success', 'Collar Type deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $collarType = CollarType::findOrFail($id);
        $oldData = $collarType->toArray();
        $collarType->status = $request->status;
        $collarType->save();
        $newData = $collarType->toArray();
        addLog('update_status', 'Collar Type Status', 'collar_types', $collarType->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $collarType->status
        ]);
    }
}
