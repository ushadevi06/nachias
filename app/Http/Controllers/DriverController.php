<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\TransportBooking;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view drivers')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $drivers = Driver::latest()->get();

            $data = [];
            $i = 1;

            foreach ($drivers as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input driver-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == 'Active' ? 'checked' : '') . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit drivers')) {
                    $action .= '<a href="' . url('drivers/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete drivers')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('drivers/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'driver_name' => $row->driver_name,
                    'loading_unloading_amount' => '₹' . number_format((float) $row->loading_unloading_amount, 2),
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('logistics_master.driver.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit drivers')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create drivers')) {
                return unauthorizedRedirect();
            }
        }

        $driver = $id ? Driver::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'driver_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:150',
                    'not_regex:/^0+$/',
                    'unique:drivers,driver_name,' . $id . ',id,deleted_at,NULL',
                ],
                'loading_unloading_amount' => 'required|numeric|min:0',
                'status' => 'required|in:Active,Inactive',
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique' => 'This driver already exists.',
                'driver_name.not_regex' => 'This field is an invalid format.',
                '*.min' => 'This field must be at least :min.',
                '*.max' => 'This field should not be more than :max characters.',
            ];
            $request->validate($rules, $messages);

            $data = [
                'driver_name' => trim($request->input('driver_name')),
                'loading_unloading_amount' => round((float) $request->input('loading_unloading_amount'), 2),
                'status' => $request->input('status'),
            ];

            if ($id) {
                $oldData = $driver->toArray();
                $data['updated_by'] = auth()->id();
                $driver->update($data);
                if (function_exists('addLog')) {
                    addLog('update', 'Drivers', 'drivers', $id, $oldData, $data);
                }
                $msg = 'Driver updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newDriver = Driver::create($data);
                if (function_exists('addLog')) {
                    addLog('create', 'Drivers', 'drivers', $newDriver->id, null, $data);
                }
                $msg = 'Driver added successfully';
            }

            return redirect('drivers')->with('success', $msg);
        }

        return view('logistics_master.driver.add', compact('driver'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete drivers')) {
            return unauthorizedRedirect();
        }

        $driver = Driver::findOrFail($id);

        if (TransportBooking::where('driver_id', $id)->exists()) {
            return redirect('drivers')->with('danger', 'This driver is currently referenced in Transport Handover bookings.');
        }

        $oldData = $driver->toArray();
        $driver->delete();
        if (function_exists('addLog')) {
            addLog('delete', 'Drivers', 'drivers', $id, $oldData, null);
        }

        return redirect('drivers')->with('success', 'Driver deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('edit drivers')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $driver = Driver::findOrFail($id);
        $oldData = $driver->toArray();
        $driver->status = $request->input('status') === 'Inactive' ? 'Inactive' : 'Active';
        $driver->updated_by = auth()->id();
        $driver->save();
        if (function_exists('addLog')) {
            addLog('update_status', 'Driver Status', 'drivers', $driver->id, $oldData, $driver->toArray());
        }

        return response()->json([
            'success' => true,
            'status' => $driver->status,
        ]);
    }
}

