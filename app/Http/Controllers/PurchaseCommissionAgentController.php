<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseCommissionAgent;
use App\Models\State;
use App\Models\City;
use App\Models\Place;

class PurchaseCommissionAgentController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view purchase-commission-agent')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {

            $query = PurchaseCommissionAgent::with(['state', 'city', 'servicePoint']);
            $agents = $query->orderBy('id', 'desc')->get();

            $count = 1;
            $data = [];

            foreach ($agents as $agent) {

                $checked = $agent->status === 'Active' ? 'checked' : '';

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input purchase-agent-status-toggle"
                        data-id="' . $agent->id . '" ' . $checked . '>
                    <span class="switch-toggle-slider"></span>
                </label>
                <div class="status_msg_' . $agent->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('view_details purchase-commission-agent')) {
                    $action .= '
                    <a href="' . url('view_purchase_commission_agent/' . $agent->id) . '" class="btn btn-view">
                        <i class="icon-base ri ri-eye-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('edit purchase-commission-agent')) {
                    $action .= '
                    <a href="' . url('purchase_commission_agent/add/' . $agent->id) . '" class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete purchase-commission-agent')) {
                    $action .= '
                    <a href="javascript:;" class="btn btn-delete"
                        onclick="delete_data(\'' . url('purchase_commission_agent/delete/' . $agent->id) . '\')">
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

                $location = '
                <div class="location-info">
                    <div><strong>State:</strong> ' . ($agent->state->state_name ?? '-') . '</div>
                    <div><strong>City:</strong> ' . ($agent->city->city_name ?? '-') . '</div>
                    <div><strong>Place:</strong> ' . ($agent->servicePoint->place_name ?? '-') . '</div>
                </div>';

                $data[] = [
                    'DT_RowIndex'  => $count++,
                    'name'         => $agent->name,
                    'code'         => $agent->code,
                    'contact_info' => $contactInfo,
                    'location'     => $location,
                    'status'       => $status,
                    'action'       => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('purchase_commission_agent.view');
    }


    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit purchase-commission-agent')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create purchase-commission-agent')) {
                return unauthorizedRedirect();
            }
        }
        $agent = null;

        if ($id) {
            $agent = PurchaseCommissionAgent::with(['state', 'city', 'servicePoint'])->findOrFail($id);
        }

        if (request()->isMethod('post')) {

            $request = request();

            $rules = [
                'name'        => 'required|string|max:255',
                'code'        => 'required|string|max:50|unique:purchase_commission_agents,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'email'       => 'nullable|email|max:255|unique:purchase_commission_agents,email,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'mobile_no'   => 'nullable|string|max:20|unique:purchase_commission_agents,mobile_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'status'      => 'required|in:Active,Inactive',

                'state_id'    => 'required|exists:states,id',
                'city_id'     => 'required|exists:cities,id',
                'place_id'    => 'required|exists:places,id',

                'address_line_1'      => 'nullable|string|max:255',
                'address_line_2'      => 'nullable|string|max:255',
                'zipcode'             => 'nullable|string|max:20',

                'contact_person_name' => 'nullable|string|max:255',
                'designation'         => 'nullable|string|max:255',
                'phone_number'        => 'nullable|string|max:20',
                'contact_email'       => 'nullable|email|max:255',

                'pan_no' => [
                    'nullable',
                    'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                    'unique:purchase_commission_agents,pan_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],

                'gst_no' => [
                    'nullable',
                    'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                    'unique:purchase_commission_agents,gst_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],

                'remarks' => 'nullable|string',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];

            $validated = $request->validate($rules, $messages);

            $data = [
                'name'                => $request->name,
                'code'                => $request->code,
                'email'               => $request->email,
                'mobile_no'           => $request->mobile_no,
                'status'              => $request->status,

                'state_id'            => $request->state_id,
                'city_id'             => $request->city_id,
                'place_id'            => $request->place_id,

                'address_line_1'      => $request->address_line_1,
                'address_line_2'      => $request->address_line_2,
                'zipcode'             => $request->zipcode,

                'contact_person_name' => $request->contact_person_name,
                'designation'         => $request->designation,
                'phone_number'        => $request->phone_number,
                'contact_email'       => $request->contact_email,

                'pan_no'              => $request->pan_no,
                'gst_no'              => $request->gst_no,

                'remarks'             => $request->remarks,
            ];

            if ($id) {
                $data['updated_by'] = auth()->id();
                $oldData = PurchaseCommissionAgent::find($id)->toArray();

                PurchaseCommissionAgent::where('id', $id)->update($data);

                $newData = PurchaseCommissionAgent::find($id)->toArray();

                addLog('update', 'Purchase Commission Agent', 'purchase_commission_agents', $id, $oldData, $newData);

                $message = 'Purchase Commission Agent updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $agent = PurchaseCommissionAgent::create($data);

                $newData = $agent->toArray();

                addLog('create', 'Purchase Commission Agent', 'purchase_commission_agents', $agent->id, null, $newData);

                $message = 'Purchase Commission Agent added successfully';
            }

            return redirect('purchase_commission_agent')->with('success', $message);
        }

        $states = State::where('status', 'Active')->get();
        $cities = [];
        $servicePoints = [];

        if ($agent) {
            $cities = City::where('state_id', $agent->state_id)->get();
            $servicePoints = Place::where('city_id', $agent->city_id)->get();
        }

        return view('purchase_commission_agent.add', compact('agent', 'states', 'cities', 'servicePoints'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details purchase-commission-agent')) {
            return unauthorizedRedirect();
        }
        $agent = PurchaseCommissionAgent::with(['state', 'city', 'servicePoint'])->findOrFail($id);
        return view('purchase_commission_agent.view_details', compact('agent'));
    }

    public function updateStatus(Request $request, $id)
    {
        $agent = PurchaseCommissionAgent::findOrFail($id);

        $oldData = $agent->toArray();

        $agent->status = $request->status;
        $agent->save();

        $newData = $agent->toArray();

        addLog('update_status', 'Purchase Commission Agent Status', 'purchase_commission_agents', $id, $oldData, $newData);

        return response()->json([
            'status'  => true,
            'message' => 'Status updated successfully'
        ]);
    }


    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete purchase-commission-agent')) {
            return unauthorizedRedirect();
        }
        $agent = PurchaseCommissionAgent::findOrFail($id);

        $oldData = $agent->toArray();

        $agent->delete(); 

        addLog('delete', 'Purchase Commission Agent', 'purchase_commission_agents', $id, $oldData, null);

        if (request()->ajax()) {
            return response()->json([
                'status'  => true,
                'message' => 'Purchase Commission Agent deleted successfully'
            ]);
        }

        return redirect('purchase_commission_agent')
            ->with('success', 'Purchase Commission Agent deleted successfully');
    }
}
