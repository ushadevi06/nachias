<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $departments = Department::latest()->get();

            $data = [];
            $i = 1;

            foreach ($departments as $row) {

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input department-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit departments')) {
                    $action .= '<a href="' . url('departments/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete departments')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('department/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'department' => $row->department,
                    'status'     => $status,
                    'action'     => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('departments.view');
    }

    public function add(Request $request, $id = null)
    {
        $department = $id ? Department::findOrFail($id) : null;
        if ($request->isMethod('post')) {
            $rules = [
                'department' => 'required|string|max:255|unique:departments,department,' . $id . ',id,deleted_at,NULL',
                'status'     => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);
            $data = $request->only(['department', 'status']);
            if ($id) {
                $data['updated_by'] = auth()->id();
                Department::where('id', $id)->update($data);
                addLog('update', 'Department', 'departments', $id, null, $data);

                $msg = 'Department updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $dept = Department::create($data);
                addLog('create', 'Department', 'departments', $dept->id, null, $data);
                $msg = 'Department added successfully';
            }

            return redirect('departments')->with('success', $msg);
        }
        return view('departments.add', compact('department'));
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $oldData = $department->toArray();   
        $department->delete();             
        addLog('delete', 'Department', 'departments', $id, $oldData, null);
        return redirect('departments')->with('success', 'Department deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        $oldData = $department->toArray();  
        $department->status = $request->status;
        $department->save();
        $newData = $department->toArray(); 
        addLog('update_status', 'Department Status', 'departments', $department->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $department->status
        ]);
    }
}
