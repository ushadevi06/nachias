<?php

namespace App\Http\Controllers;

use App\Models\ProcessGroup;
use App\Models\JobCardEntry;
use App\Models\Production;
use Illuminate\Http\Request;

class ProcessGroupController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view process-groups')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $processGroups = ProcessGroup::orderBy('id','desc')->get();

            $data = [];
            $i = 1;

            foreach ($processGroups as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input process-group-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit process-groups')) {
                    $action .= '<a href="' . url('process_groups/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete process-groups')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('process_groups/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
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

        return view('process_groups.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit process-groups')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create process-groups')) {
                return unauthorizedRedirect();
            }
        }
        $processGroup = $id ? ProcessGroup::findOrFail($id) : null;
        if ($request->isMethod('post')) {
            $rules = [
                'name'   => 'required|string|max:255|unique:process_groups,name,' . $id . ',id,deleted_at,NULL',
                'status' => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                ProcessGroup::where('id', $id)->update($data);
                addLog('update', 'Process Group', 'process_groups', $id, null, $data);
                $msg = 'Process Group updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newProcessGroup = ProcessGroup::create($data);
                addLog('create', 'Process Group', 'process_groups', $newProcessGroup->id, null, $data);
                $msg = 'Process Group added successfully';
            }
            return redirect('process_groups')->with('success', $msg);
        }

        return view('process_groups.add', compact('processGroup'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete process-groups')) {
            return unauthorizedRedirect();
        }

        if (JobCardEntry::where('process_group_id', $id)->exists()) {
            return redirect('process_groups')->with('danger', "This Process Group is currently referenced in Job Cards and cannot be deleted.");
        }

        if (Production::where('process_group_id', $id)->exists()) {
            return redirect('process_groups')->with('danger', "This Process Group is currently referenced in Production records and cannot be deleted.");
        }

        $processGroup = ProcessGroup::findOrFail($id);
        $oldData = $processGroup->toArray();
        $processGroup->delete();
        addLog('delete', 'Process Group', 'process_groups', $id, $oldData, null);
        return redirect('process_groups')->with('success', 'Process Group deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $processGroup = ProcessGroup::findOrFail($id);
        $oldData = $processGroup->toArray();
        $processGroup->status = $request->status;
        $processGroup->save();
        $newData = $processGroup->toArray();
        addLog('update_status', 'Process Group Status', 'process_groups', $processGroup->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $processGroup->status
        ]);
    }
}
