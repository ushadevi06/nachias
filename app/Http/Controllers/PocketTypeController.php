<?php

namespace App\Http\Controllers;

use App\Models\PocketType;
use Illuminate\Http\Request;

class PocketTypeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view pocket types')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $pocketTypes = PocketType::latest()->get();

            $data = [];
            $i = 1;

            foreach ($pocketTypes as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input pocket-type-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit pocket types')) {
                    $action .= '<a href="' . url('pocket_types/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete pocket types')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('pocket_types/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'       => $i++,
                    'pocket_type_name'  => $row->pocket_type_name,
                    'status'            => $status,
                    'action'            => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('pocket_types.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit pocket types')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create pocket types')) {
                return unauthorizedRedirect();
            }
        }

        $pocketType = $id ? PocketType::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'pocket_type_name' => 'required|string|max:255|unique:pocket_types,pocket_type_name,' . $id . ',id,deleted_at,NULL',
                'status'           => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['pocket_type_name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                PocketType::where('id', $id)->update($data);
                addLog('update', 'Pocket Type', 'pocket_types', $id, null, $data);
                $msg = 'Pocket Type updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newPocketType = PocketType::create($data);
                addLog('create', 'Pocket Type', 'pocket_types', $newPocketType->id, null, $data);
                $msg = 'Pocket Type added successfully';
            }

            return redirect('pocket_types')->with('success', $msg);
        }

        return view('pocket_types.add', compact('pocketType'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete pocket types')) {
            return unauthorizedRedirect();
        }

        $pocketType = PocketType::findOrFail($id);
        $oldData = $pocketType->toArray();
        $pocketType->delete();
        addLog('delete', 'Pocket Type', 'pocket_types', $id, $oldData, null);
        return redirect('pocket_types')->with('success', 'Pocket Type deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $pocketType = PocketType::findOrFail($id);
        $oldData = $pocketType->toArray();
        $pocketType->status = $request->status;
        $pocketType->save();
        $newData = $pocketType->toArray();
        addLog('update_status', 'Pocket Type Status', 'pocket_types', $pocketType->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $pocketType->status
        ]);
    }
}
