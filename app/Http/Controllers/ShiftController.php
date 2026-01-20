<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view shifts')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $shifts = Shift::latest()->get();

            $data = [];
            $i = 1;

            foreach ($shifts as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input shift-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit shifts')) {
                    $action .= '<a href="' . url('shifts/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete shifts')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('shifts/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'shift_name'  => $row->shift_name,
                    'start_time'  => date('h:i A', strtotime($row->start_time)),
                    'end_time'    => date('h:i A', strtotime($row->end_time)),
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('shifts.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit shifts')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create shifts')) {
                return unauthorizedRedirect();
            }
        }

        $shift = $id ? Shift::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'shift_name' => 'required|string|max:255|unique:shifts,shift_name,' . $id . ',id,deleted_at,NULL',
                'start_time' => 'required',
                'end_time'   => 'required',
                'status'     => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['shift_name', 'start_time', 'end_time', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                Shift::where('id', $id)->update($data);
                addLog('update', 'Shift', 'shifts', $id, null, $data);
                $msg = 'Shift updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newShift = Shift::create($data);
                addLog('create', 'Shift', 'shifts', $newShift->id, null, $data);
                $msg = 'Shift added successfully';
            }

            return redirect('shifts')->with('success', $msg);
        }

        return view('shifts.add', compact('shift'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete shifts')) {
            return unauthorizedRedirect();
        }

        $shift = Shift::findOrFail($id);
        $oldData = $shift->toArray();
        $shift->delete();
        addLog('delete', 'Shift', 'shifts', $id, $oldData, null);
        return redirect('shifts')->with('success', 'Shift deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);
        $oldData = $shift->toArray();
        $shift->status = $request->status;
        $shift->save();
        $newData = $shift->toArray();
        addLog('update_status', 'Shift Status', 'shifts', $shift->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $shift->status
        ]);
    }
}
