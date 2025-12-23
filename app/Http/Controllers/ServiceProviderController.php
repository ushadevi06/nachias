<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use App\Models\ServiceType;
use App\Models\State;
use App\Models\City;
use App\Models\Place;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index(Request $request)
    {
        $service_types = ServiceType::get();
        if ($request->ajax()) {
            $query = ServiceProvider::with(['serviceType', 'state', 'city', 'place']);
            if (!empty($request->service_type)) {
                $query->where('service_type_id', $request->service_type);
            }

            if (!empty($request->service_rate)) {
                $query->where('service_rate', $request->service_rate);
            }

            $providers = $query->orderBy('id', 'desc')->get();
            $data = [];
            $count = 1;
            

            foreach ($providers as $provider) {

                $checked = $provider->status === 'Active' ? 'checked' : '';

                $status = '
                    <label class="switch switch-success switch-lg">
                        <input type="checkbox"
                            class="switch-input service-provider-status-toggle"
                            data-id="' . $provider->id . '" ' . $checked . '>
                        <span class="switch-toggle-slider"></span>
                    </label>
                    <div class="status_msg_' . $provider->id . '"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('edit service-providers')) {
                    $action .= '<a href="' . url('service_providers/add/' . $provider->id) . '" class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';

                }
                if (auth()->id() == 1 || auth()->user()->can('delete service-providers')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete"
                    onclick="delete_data(\'' . url('service_provider/delete/' . $provider->id) . '\')">
                    <i class="icon-base ri ri-delete-bin-line"></i>
                    </a>';

                }
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'name'   => $provider->name . ' (' . $provider->code . ')',
                    'mobile' => $provider->mobile_no,
                    'email'  => $provider->email ?? '-',
                    'service_type' => $provider->serviceType->service_type_name ?? '-',
                    'service_rate' => $provider->service_rate,
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('service_providers.view',compact('service_types'));
    }

    public function add($id = null)
    {
        $serviceProvider = null;
        if ($id) {
            $serviceProvider = ServiceProvider::with(['serviceType',  'state', 'city', 'place'])->findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'service_type_id' => 'required|exists:service_types,id',
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:service_providers,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'email' => 'nullable|email|max:255|unique:service_providers,email,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'mobile_no' => 'required|string|max:20|unique:service_providers,mobile_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'zip_code' => 'nullable|string|max:10',
                'website_url' => 'nullable|url|max:255',
                'service_rate' => 'required|in:Per Agent,Job Type',
                'status' => 'required|in:Active,Inactive',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'place_id' => 'required|exists:places,id',
                'address_line_1' => 'required|string|max:500',
                'address_line_2' => 'nullable|string|max:500',
                'contact_person_name' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'contact_email' => 'nullable|email|max:255',
                'pan_no' => 'nullable|string|max:20',
                'gst_no' => 'nullable|string|max:20',
                'remarks' => 'nullable|string|max:1000',
                'bank_name' => 'nullable|string|max:255',
                'bank_acc_no' => 'nullable|string|max:50|unique:service_providers,bank_acc_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'ifsc_code' => 'nullable|string|max:20|unique:service_providers,ifsc_code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'payment_terms' => 'nullable|string|max:1000',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $validated = $request->validate($rules, $messages);
            
            $data = [
                'service_type_id' => $request->service_type_id,
                'name' => $request->name,
                'code' => $request->code,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'zip_code' => $request->zip_code,
                'website_url' => $request->website_url,
                'service_rate' => $request->service_rate,
                'status' => $request->status,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'place_id' => $request->place_id,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'contact_person_name' => $request->contact_person_name,
                'designation' => $request->designation,
                'phone_number' => $request->phone_number,
                'contact_email' => $request->contact_email,
                'pan_no' => $request->pan_no,
                'gst_no' => $request->gst_no,
                'remarks' => $request->remarks,
                'bank_name' => $request->bank_name,
                'bank_acc_no' => $request->bank_acc_no,
                'ifsc_code' => $request->ifsc_code,
                'payment_terms' => $request->payment_terms,
            ];

            if ($id) {
                $data['updated_by'] = auth()->id();
                $oldData = ServiceProvider::find($id)->toArray();

                ServiceProvider::where('id', $id)->update($data);

                $newData = ServiceProvider::find($id)->toArray();

                addLog('update', 'Service Provider', 'service_providers', $id, $oldData, $newData);

                $message = 'Service Provider updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $provider = ServiceProvider::create($data);
                $newData = $provider->toArray();

                addLog('create', 'Service Provider', 'service_providers', $provider->id, null, $newData);

                $message = 'Service Provider added successfully';
            }

            return redirect('service_providers')->with('success', $message);
        }

        $service_types = ServiceType::get();
        $states = State::where('status', 'Active')->get();
        $cities = [];
        $places = [];

        $stateId = old('state_id', $serviceProvider->state_id ?? null);
        $cityId = old('city_id', $serviceProvider->city_id ?? null);
        if ($stateId) {
            $cities = City::where('state_id', $stateId)->where('status', 'Active')->get();
        }
        if ($cityId) {
            $places = Place::where('city_id', $cityId)->where('status', 'Active')->get();
        }

        return view('service_providers.add', compact('serviceProvider', 'service_types', 'states', 'cities', 'places'));
    }

    public function destroy($id)
    {
        $provider = ServiceProvider::findOrFail($id);
        $oldData = $provider->toArray();

        $provider->delete();

        addLog('delete', 'Service Provider', 'service_providers', $id, $oldData, null);

        return redirect('service_providers')->with('success', 'Service Provider deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $provider = ServiceProvider::findOrFail($id);
        $oldData = $provider->toArray();

        $provider->status = $request->status;
        $provider->save();

        $newData = $provider->toArray();

        addLog('update', 'Service Provider Status', 'service_providers', $id, $oldData, $newData);

        return response()->json(['success' => true]);
    }
}
