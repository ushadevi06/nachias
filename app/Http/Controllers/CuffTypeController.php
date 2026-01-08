<?php

namespace App\Http\Controllers;

use App\Models\CuffType;
use Illuminate\Http\Request;

class CuffTypeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view cuff types')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $cuffTypes = CuffType::latest()->get();

            $data = [];
            $i = 1;

            foreach ($cuffTypes as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input cuff-type-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit cuff types')) {
                    $action .= '<a href="' . url('cuff_types/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete cuff types')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('cuff_types/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'     => $i++,
                    'cuff_type_name'  => $row->cuff_type_name,
                    'status'          => $status,
                    'action'          => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('cuff_types.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit cuff types')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create cuff types')) {
                return unauthorizedRedirect();
            }
        }

        $cuffType = $id ? CuffType::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'cuff_type_name' => 'required|string|max:255|unique:cuff_types,cuff_type_name,' . $id . ',id,deleted_at,NULL',
                'status'        => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['cuff_type_name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                CuffType::where('id', $id)->update($data);
                addLog('update', 'Cuff Type', 'cuff_types', $id, null, $data);
                $msg = 'Cuff Type updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newCuffType = CuffType::create($data);
                addLog('create', 'Cuff Type', 'cuff_types', $newCuffType->id, null, $data);
                $msg = 'Cuff Type added successfully';
            }

            return redirect('cuff_types')->with('success', $msg);
        }

        return view('cuff_types.add', compact('cuffType'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete cuff types')) {
            return unauthorizedRedirect();
        }

        $cuffType = CuffType::findOrFail($id);
        $oldData = $cuffType->toArray();
        $cuffType->delete();
        addLog('delete', 'Cuff Type', 'cuff_types', $id, $oldData, null);
        return redirect('cuff_types')->with('success', 'Cuff Type deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $cuffType = CuffType::findOrFail($id);
        $oldData = $cuffType->toArray();
        $cuffType->status = $request->status;
        $cuffType->save();
        $newData = $cuffType->toArray();
        addLog('update_status', 'Cuff Type Status', 'cuff_types', $cuffType->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $cuffType->status
        ]);
    }
}
