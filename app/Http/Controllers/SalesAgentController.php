<?php

namespace App\Http\Controllers;

use App\Models\SalesAgent;
use App\Models\State;
use App\Models\City;
use App\Models\Place;
use Illuminate\Http\Request;

class SalesAgentController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view sales-agents')) {
            return unauthorizedRedirect();
        }
        $canViewDetail   = true;
        $canEdit   = true;
        $canDelete = true;
        $canAdd    = true;

        if ($request->ajax()) {

            $query = SalesAgent::with(['state', 'city']);

            if (!empty($request->agent_type)) {
                $query->where('agent_type', $request->agent_type);
            }

            $agents = $query->orderBy('id', 'desc')->get();

            $count = 1;
            $data = [];

            foreach ($agents as $agent) {

                $checked = $agent->status === 'Active' ? 'checked' : '';

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input sales-agent-status-toggle"
                        data-id="' . $agent->id . '" ' . $checked . '>
                    <span class="switch-toggle-slider"></span>
                </label>
                <div class="status_msg_' . $agent->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('view_details sales-agents')) {
                    $action .= '
                    <a href="' . url('sales_agent/' . $agent->id) . '" class="btn btn-view">
                        <i class="icon-base ri ri-eye-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('edit sales-agents')) {
                    $action .= '
                    <a href="' . url('sales_agents/add/' . $agent->id) . '" class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete sales-agents')) {

                    $action .= '
                    <a href="javascript:;" class="btn btn-delete"
                       onclick="delete_data(\'' . url('sales_agent/delete/' . $agent->id) . '\')">
                        <i class="icon-base ri ri-delete-bin-line"></i>
                    </a>';
                }

                $action .= '</div>';

                $contactInfo = '
                <div class="contact-info">
                    <div>
                        <i class="ri ri-mail-line icon-email"></i>
                        ' . ($agent->email ?? '-') . '
                    </div>
                    <div>
                        <i class="ri ri-phone-line icon-phone"></i>
                        ' . ($agent->mobile_no ?? '-') . '
                    </div>
                </div>';

                $data[] = [
                    'DT_RowIndex'   => $count++,
                    'name'          => $agent->name . ' (' . $agent->code . ')',
                    'agent_type'    => $agent->agent_type,
                    'contact_info' => $contactInfo,
                    'location'      => ($agent->city->city_name ?? '-') . ', ' . ($agent->state->state_name ?? '-'),
                    'status'        => $status,
                    'action'        => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('sales_agent.view', compact('canAdd'));
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit sales-agents')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create sales-agents')) {
                return unauthorizedRedirect();
            }
        }
        $salesAgent = null;
        if ($id) {
            $salesAgent = SalesAgent::with(['state', 'city', 'place'])->findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();
            $rules = [
                'agent_type' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:sales_agents,code,' . $id . ',id,deleted_at,NULL',
                'email' => 'nullable|email|unique:sales_agents,email,' . $id . ',id,deleted_at,NULL',
                'mobile_no' => 'required|digits:10|unique:sales_agents,mobile_no,' . $id . ',id,deleted_at,NULL',
                'status' => 'required|in:Active,Inactive',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'place_id' => 'required|exists:places,id',
                'address_line_1' => 'required|string|max:500',
                'address_line_2' => 'nullable|string|max:500',
                'zip_code' => 'nullable|digits:6',
                'contact_person_name' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'contact_phone_number' => 'nullable|string|digits:10',
                'contact_email' => 'nullable|email|max:255',
                'gst_no' => [
                    'nullable',
                    'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                    'unique:sales_agents,gst_no,' . $id . ',id,deleted_at,NULL'
                ],
                'pan_no' => [
                    'nullable',
                    'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                    'unique:sales_agents,pan_no,' . $id . ',id,deleted_at,NULL'
                ],
                'commission_value' => 'nullable|numeric',
                'sales_target' => 'nullable|numeric',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];

            $validated = $request->validate($rules, $messages);

            $data = [
                'agent_type' => $request->agent_type,
                'name' => $request->name,
                'code' => $request->code,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'status' => $request->status,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'place_id' => $request->place_id,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'zip_code' => $request->zip_code,
                'contact_person_name' => $request->contact_person_name,
                'designation' => $request->designation,
                'contact_phone_number' => $request->contact_phone_number,
                'contact_email' => $request->contact_email,
                'pan_no' => $request->pan_no,
                'gst_no' => $request->gst_no,
                'commission_value' => $request->commission_value,
                'sales_target' => $request->sales_target,
            ];

            if ($id) {
                $data['updated_by'] = auth()->id();
                $oldData = SalesAgent::find($id)->toArray();

                SalesAgent::where('id', $id)->update($data);

                $newData = SalesAgent::find($id)->toArray();

                addLog('update', 'Sales Agent', 'sales_agents', $id, $oldData, $newData);

                $message = 'Sales Agent updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $agent = SalesAgent::create($data);

                $newData = $agent->toArray();

                addLog('create', 'Sales Agent', 'sales_agents', $agent->id, null, $newData);

                $message = 'Sales Agent added successfully';
            }

            return redirect('sales_agents')->with('success', $message);
        }

        $states = State::where('status', 'Active')->get();
        $cities = [];
        $places = [];

        $stateId = old('state_id', $salesAgent->state_id ?? null);
        $cityId = old('city_id', $salesAgent->city_id ?? null);

        if ($stateId) {
            $cities = City::where('state_id', $stateId)->where('status', 'Active')->get();
        }
        if ($cityId) {
            $places = Place::where('city_id', $cityId)->where('status', 'Active')->get();
        }

        return view('sales_agent.add', compact('salesAgent', 'states', 'cities', 'places'));
    }
    public function show($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details sales-agents')) {
            return unauthorizedRedirect();
        }
        $salesAgent = SalesAgent::with(['state', 'city', 'place'])->findOrFail($id);
        return view('sales_agent.view_details', compact('salesAgent'));
    }
    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete sales-agents')) {
            return unauthorizedRedirect();
        }
        $agent = SalesAgent::findOrFail($id);
        $oldData = $agent->toArray();

        $agent->delete();

        addLog('delete', 'Sales Agent', 'sales_agents', $id, $oldData, null);

        return redirect('sales_agents')->with('success', 'Sales Agent deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $agent = SalesAgent::findOrFail($id);
        $oldData = $agent->toArray();

        $agent->status = $request->status;
        $agent->save();

        $newData = $agent->toArray();

        addLog('update_status', 'Sales Agent Status', 'sales_agents', $id, $oldData, $newData);

        return response()->json(['success' => true]);
    }
}
