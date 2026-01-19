<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Zone;
use App\Models\State;
use App\Models\City;
use App\Models\Place;
use App\Models\Tax;
use App\Models\StoreType;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view customers')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {

            $query = Customer::with(['zone', 'state', 'city'])->orderBy('id', 'desc');

            if (!empty($request->category)) {
                $query->where('category', $request->category);
            }

            $customers = $query->get();

            $data = [];
            $count = 1;

            foreach ($customers as $cust) {

                $checked = $cust->status === 'Active' ? 'checked' : '';

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input customer-status-toggle"
                        data-id="' . $cust->id . '" ' . $checked . '>
                    <span class="switch-toggle-slider"></span>
                </label>
                <div class="status_msg_' . $cust->id . '"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view_details customers')) {
                    $action .= '
                    <a href="' . url('view_customer/' . $cust->id) . '" class="btn btn-view">
                        <i class="icon-base ri ri-eye-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('edit customers')) {
                    $action .= '
                    <a href="' . url('customers/add/' . $cust->id) . '" class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete customers')) {
                    $action .= '
                    <a href="javascript:;" class="btn btn-delete"
                    onclick="delete_data(\'' . url('customers/delete/' . $cust->id) . '\')">
                        <i class="icon-base ri ri-delete-bin-line"></i>
                    </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'name'     => $cust->name . ' (' . $cust->code . ')',
                    'category' => $cust->category,
                    'contact_info' => '
                        <div class="contact-info">
                            <div><i class="ri ri-mail-line icon-email"></i> ' . ($cust->email ?? '-') . '</div>
                            <div><i class="ri ri-phone-line icon-phone"></i> ' . ($cust->mobile_no ?? '-') . '</div>
                        </div>
                    ',
                    'location' => '
                        <div class="location-info">
                            <div><strong>State:</strong> ' . ($cust->state->state_name ?? '-') . '</div>
                            <div><strong>City:</strong> ' . ($cust->city->city_name ?? '-') . '</div>
                            <div><strong>Place:</strong> ' . ($cust->place->place_name ?? '-') . '</div>
                        </div>
                    ',
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('customers.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit customers')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create customers')) {
                return unauthorizedRedirect();
            }
        }
        $customer = null;
        if ($id) {
            $customer = Customer::with(['zone', 'state', 'city', 'place', 'tax'])->findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'category' => 'required|in:Retailer,Wholesaler',
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:customers,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'mobile_no' => 'required|string|max:15',
                'email' => 'nullable|email|max:255|unique:customers,email,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'website_url' => 'nullable|url|max:255',
                'transport_name' => 'nullable|string|max:255',
                'booking_office' => 'nullable|string|max:255',
                'zone_id' => 'required|exists:zones,id',
                'stores' => 'nullable|string|max:255',
                'status' => 'required|in:Active,Inactive',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'place_id' => 'required|exists:places,id',
                'address_line_1' => 'required|string|max:500',
                'address_line_2' => 'nullable|string|max:500',
                'address_line_3' => 'nullable|string|max:500',
                'zip_code' => 'nullable|string|max:10',
                'contact_person_name' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'contact_mobile_no' => 'nullable|string|max:15',
                'contact_email' => 'nullable|email|max:255',
                'tax_type_id' => 'nullable|exists:taxes,id',
                'gst_no' => [
                    'nullable',
                    'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                    'unique:customers,gst_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'pan_no' => [
                    'nullable',
                    'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                    'unique:customers,pan_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'payment_terms' => 'nullable|string|max:500',
                'credit_limit' => 'nullable|numeric|min:0',
                'sales_discount' => 'nullable|numeric|min:0|max:100',
                'box_discount' => 'nullable|numeric|min:0|max:100',
                'bank_name' => 'nullable|string|max:255',
                'branch' => 'nullable|string|max:255',
                'account_number' => [
                    'nullable',
                    'digits_between:9,20',
                    'unique:customers,account_number,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'ifsc_code' => [
                    'nullable',
                    'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
                    'unique:customers,ifsc_code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.regex' => 'This field is an invalid format'
            ];

            $validated = $request->validate($rules,$messages);

            $data = [
                'category' => $request->category,
                'name' => $request->name,
                'code' => $request->code,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'website_url' => $request->website_url,
                'transport_name' => $request->transport_name,
                'booking_office' => $request->booking_office,
                'zone_id' => $request->zone_id,
                'stores' => $request->stores,
                'status' => $request->status,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'place_id' => $request->place_id,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'address_line_3' => $request->address_line_3,
                'zip_code' => $request->zip_code,
                'contact_person_name' => $request->contact_person_name,
                'designation' => $request->designation,
                'contact_mobile_no' => $request->contact_mobile_no,
                'contact_email' => $request->contact_email,
                'tax_type_id' => $request->tax_type_id,
                'gst_no' => $request->gst_no,
                'pan_no' => $request->pan_no,
                'payment_terms' => $request->payment_terms,
                'credit_limit' => $request->credit_limit ?? 0,
                'sales_discount' => $request->sales_discount ?? 0,
                'box_discount' => $request->box_discount ?? 0,
                'bank_name' => $request->bank_name,
                'branch' => $request->branch,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
            ];

            if ($id) {
                $data['updated_by'] = auth()->id();
                $oldData = Customer::find($id)->toArray();

                Customer::where('id', $id)->update($data);
                $newData = Customer::find($id)->toArray();
                addLog('update', 'Customer', 'customers', $id, $oldData, $newData);
                $message = 'Customer updated successfully';

            } else {
                $data['created_by'] = auth()->id();
                $customer = Customer::create($data);
                $newData = $customer->toArray();
                addLog('create', 'Customer', 'customers', $customer->id, null, $newData);
                $message = 'Customer added successfully';
            }


            return redirect('customers')->with('success', $message);
        }

        $zones = Zone::active()->get();
        $states = State::active()->get();
        $cities = [];
        $places = [];
        $taxes = Tax::active()->get();
        $store_types = StoreType::where('status', 'Active')->get();

        $stateId = old('state_id') ?? ($customer->state_id ?? null);
        $cityId  = old('city_id')  ?? ($customer->city_id ?? null);

        if ($stateId) {
            $cities = City::where('state_id', $stateId)->get();
        }

        if ($cityId) {
            $places = Place::where('city_id', $cityId)->get();
        }
        return view('customers.add', compact('customer', 'zones', 'states', 'cities', 'places', 'taxes', 'store_types'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details customers')) {
            return unauthorizedRedirect();
        }
        $customer = Customer::with(['zone', 'state', 'city', 'place', 'tax'])->findOrFail($id);
        return view('customers.view_details', compact('customer'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete customers')) {
            return unauthorizedRedirect();
        }
        $customer = Customer::findOrFail($id);
        $oldData = $customer->toArray();
        $customer->delete();
        addLog('delete', 'Customer', 'customers', $id, $oldData, null);
        return redirect('customers')->with('success', 'Customer deleted successfully');
    }


    public function updateStatus(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $oldData = $customer->toArray();
        $customer->status = $request->status;
        $customer->save();
        $newData = $customer->toArray();
        addLog('update_status', 'Customer Status', 'customers', $id, $oldData, $newData);
        return response()->json(['success' => true]);
    }
}
