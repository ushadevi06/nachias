<?php

namespace App\Http\Controllers;

use App\Models\PattiType;
use Illuminate\Http\Request;

class PattiTypeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view patti types')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $pattiTypes = PattiType::latest()->get();

            $data = [];
            $i = 1;

            foreach ($pattiTypes as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input patti-type-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit patti types')) {
                    $action .= '<a href="' . url('patti_types/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete patti types')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('patti_types/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'      => $i++,
                    'patti_type_name'  => $row->patti_type_name,
                    'status'           => $status,
                    'action'           => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('patti_types.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit patti types')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create patti types')) {
                return unauthorizedRedirect();
            }
        }

        $pattiType = $id ? PattiType::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'patti_type_name' => 'required|string|max:255|unique:patti_types,patti_type_name,' . $id . ',id,deleted_at,NULL',
                'status'         => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['patti_type_name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                PattiType::where('id', $id)->update($data);
                addLog('update', 'Patti Type', 'patti_types', $id, null, $data);
                $msg = 'Patti Type updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newPattiType = PattiType::create($data);
                addLog('create', 'Patti Type', 'patti_types', $newPattiType->id, null, $data);
                $msg = 'Patti Type added successfully';
            }

            return redirect('patti_types')->with('success', $msg);
        }

        return view('patti_types.add', compact('pattiType'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete patti types')) {
            return unauthorizedRedirect();
        }

        $pattiType = PattiType::findOrFail($id);
        $oldData = $pattiType->toArray();
        $pattiType->delete();
        addLog('delete', 'Patti Type', 'patti_types', $id, $oldData, null);
        return redirect('patti_types')->with('success', 'Patti Type deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $pattiType = PattiType::findOrFail($id);
        $oldData = $pattiType->toArray();
        $pattiType->status = $request->status;
        $pattiType->save();
        $newData = $pattiType->toArray();
        addLog('update_status', 'Patti Type Status', 'patti_types', $pattiType->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $pattiType->status
        ]);
    }
}
