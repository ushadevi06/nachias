<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view leave-types')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $leaveTypes = LeaveType::latest()->get();
            $data = [];
            $count = 1;

            foreach ($leaveTypes as $item) {
                $checked = $item->status === 'Active' ? 'checked' : '';
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input leave-type-status-toggle"
                        data-id="' . $item->id . '"
                        ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $item->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit leave-types')) {
                    $action .= '
                        <a href="' . url('leave_types/add/' . $item->id) . '" class="btn btn-edit" title="Edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete leave-types')) {
                    $action .= '
                        <a href="javascript:;" class="btn btn-delete" title="Delete" onclick="delete_data(\'' . url('leave_types/delete/' . $item->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'leave_type'  => $item->leave_type,
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('leave_types.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit leave-types')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create leave-types')) {
                return unauthorizedRedirect();
            }
        }

        $leaveType = $id ? LeaveType::findOrFail($id) : new LeaveType();
        $oldData = $id ? $leaveType->toArray() : null;

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'leave_type' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                    'regex:/^(?!0+$).*$/',
                    Rule::unique('leave_types', 'leave_type')->ignore($id)->whereNull('deleted_at')
                ],
                'status' => 'required|in:Active,Inactive'
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This leave type already exists.',
                '*.regex'    => 'This field has an invalid format.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
            ];

            $request->validate($rules, $messages);

            if ($id) {
                $leaveType->leave_type = trim($request->leave_type);
                $leaveType->status     = $request->status;
                $leaveType->updated_by = auth()->id();
                $leaveType->save();

                $newData = LeaveType::find($id)->toArray();
                if (function_exists('addLog')) {
                    addLog('update', 'Leave Type', 'leave_types', $leaveType->id, $oldData, $newData);
                }
                $message = 'Leave Type updated successfully';
            } else {
                $leaveType->leave_type = trim($request->leave_type);
                $leaveType->status     = $request->status;
                $leaveType->created_by = auth()->id();
                $leaveType->save();

                $newData = LeaveType::find($leaveType->id)->toArray();
                if (function_exists('addLog')) {
                    addLog('create', 'Leave Type', 'leave_types', $leaveType->id, null, $newData);
                }
                $message = 'Leave Type added successfully';
            }

            return redirect('leave_types')->with('success', $message);
        }

        return view('leave_types.add', compact('leaveType'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete leave-types')) {
            return unauthorizedRedirect();
        }

        $leaveType = LeaveType::findOrFail($id);

        if (\Illuminate\Support\Facades\Schema::hasTable('leaves')) {
            $hasLeaves = DB::table('leaves')->where('leave_type', $leaveType->leave_type)->exists();

            if ($hasLeaves) {
                return redirect('leave_types')->with('danger', 'This leave type is currently referenced in Leaves and cannot be deleted.');
            }
        }

        $oldData = $leaveType->toArray();
        $leaveType->delete();

        if (function_exists('addLog')) {
            addLog('delete', 'Leave Type', 'leave_types', $id, $oldData, null);
        }

        return redirect('leave_types')->with('success', 'Leave Type deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $leaveType = LeaveType::findOrFail($id);
        $oldData = $leaveType->toArray();

        $leaveType->status = $request->status;
        $leaveType->save();

        $newData = $leaveType->toArray();

        if (function_exists('addLog')) {
            addLog('update_status', 'Leave Type Status', 'leave_types', $leaveType->id, $oldData, $newData);
        }

        return response()->json([
            'success' => true,
            'status'  => $leaveType->status
        ]);
    }
}
