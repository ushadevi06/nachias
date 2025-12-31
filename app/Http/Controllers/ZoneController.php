<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view zones')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {

            $zones = Zone::with('state')->orderBy('id','desc')->get();
            $data = [];
            $count = 1;

            foreach ($zones as $zone) {
                $checked = $zone->status === 'Active' ? 'checked' : '';
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input zone-status-toggle" data-id="' . $zone->id . '" ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $zone->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit zones')) {
                    $action .= '
                        <a href="' . url('zones/add/' . $zone->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete zones')) {
                    $action .= '
                        <a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('zones/delete/' . $zone->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'zone_name' => $zone->zone_name,
                    'state_name' => $zone->state ? $zone->state->state_name : 'N/A',
                    'city_names' => $zone->city_names,
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('zones.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit zones')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create zones')) {
                return unauthorizedRedirect();
            }
        }
        $zone = null;
        $oldData = null;

        if ($id) {
            $zone = Zone::findOrFail($id);
            $oldData = $zone->toArray();
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'state_id' => 'required|exists:states,id',
                'city_ids' => 'required|array',
                'city_ids.*' => 'exists:cities,id',
                'zone_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('zones', 'zone_name')
                        ->ignore($id)
                        ->whereNull('deleted_at')
                ],
                'status' => 'required|in:Active,Inactive'
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];

            $validated = $request->validate($rules, $messages);

            $data = [
                'zone_name' => $request->zone_name,
                'state_id' => $request->state_id,
                'city_ids' => implode(',', $request->city_ids),
                'status' => $request->status
            ];

            if ($id) {
                $data['updated_by'] = auth()->id();
                Zone::where('id', $id)->update($data);
                $newData = Zone::find($id)->toArray();
                addLog('update', 'Zone', 'zones', $id, $oldData, $newData);
                $message = 'Zone updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $zone = Zone::create($data);
                $newData = $zone->toArray();
                addLog('create', 'Zone', 'zones', $zone->id, null, $newData);
                $message = 'Zone added successfully';
            }

            return redirect('zones')->with('success', $message);
        }

        $states = State::active()->get();
        $cities = [];
        $stateId = old('state_id') ?? ($zone->state_id ?? null);
        if ($stateId) {
            $cities = City::where('state_id', $stateId)->get();
        }

        return view('zones.add', compact('zone', 'states', 'cities'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete zones')) {
            return unauthorizedRedirect();
        }
        $zone = Zone::findOrFail($id);
        $oldData = $zone->toArray();

        $zone->delete();

        addLog('delete', 'Zone', 'zones', $id, $oldData, null);

        return redirect('zones')->with('success', 'Zone deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);
        $oldData = $zone->toArray();

        $zone->status = $request->status;
        $zone->save();

        $newData = $zone->toArray();
        addLog('update_status', 'Zone Status', 'zones', $zone->id, $oldData, $newData);

        return response()->json(['success' => true, 'status' => $zone->status]);
    }
}
