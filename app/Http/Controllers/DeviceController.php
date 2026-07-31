<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view devices')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $devices = Device::orderBy('id','desc')->get();
            $data = [];
            $count = 1;
            foreach ($devices as $device) {
                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('edit devices')) {
                    $action .= '
                        <a href="' . url('devices/add/' . $device->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }
                $action .= '</div>';
                $data[] = [
                    'DT_RowIndex' => $count++,
                    'device_name' => $device->device_name,
                    'serial_number' => $device->serial_number,
                    'action' => $action,
                ];
            }
            return response()->json(['data' => $data]);
        }

        return view('device.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit devices')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create devices')) {
                return unauthorizedRedirect();
            }
        }
        $device = null;
        $oldData = null;
        if ($id) {
            $device = Device::findOrFail($id);
            $oldData = $device->toArray();
        }
        if (request()->isMethod('post')) {
            $request = request();
            $rules = [
                'device_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:50',
                    'regex:/^(?!0+$).*$/',
                    Rule::unique('devices', 'device_name')->ignore($id)->whereNull('deleted_at')
                ],
                'serial_number' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                    'regex:/^(?!0+$).*$/',
                    Rule::unique('devices', 'serial_number')->ignore($id)->whereNull('deleted_at')
                ],
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.regex' => 'This field is an invalid format.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
            ];
            $request->validate($rules, $messages);
            $data = [
                'device_name' => strtoupper($request->device_name),
                'serial_number' => $request->serial_number
            ];
            if ($id) {
                $data['updated_by'] = auth()->id();
                Device::where('id', $id)->update($data);
                $newData = Device::find($id)->toArray();
                addLog('update', 'Device', 'devices', $id, $oldData, $newData);
                $message = 'Device updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $device = Device::create($data);
                $newData = $device->toArray();
                addLog('create', 'Device', 'devices', $device->id, null, $newData);
                $message = 'Device added successfully';
            }
            return redirect('devices')->with('success', $message);
        }
        return view('device.add', compact('device'));
    }
}
