<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Zone;
use App\Models\State;
use App\Models\City;
use App\Models\Place;
use App\Models\Tax;
use App\Models\StoreType;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;

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
                    $action .= '<a href="' . url('customers/view/' . $cust->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('edit customers')) {
                    $action .= '<a href="' . url('customers/add/' . $cust->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete customers')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('customers/delete/' . $cust->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
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
                'name' => 'required|string|min:3|max:50|regex:/[a-zA-Z]/',
                'code' => ['required', 'string', 'min:3', 'max:20', 'not_regex:/^0+$/', 'regex:/^[a-zA-Z0-9\-_]+$/', 'unique:customers,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'],
                'mobile_no' => 'required|numeric|digits_between:10,15|regex:/[1-9]/',
                'email' => 'nullable|email:rfc,dns|max:128|unique:customers,email,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'website_url' => 'nullable|url|max:255',
                'transport_name' => 'nullable|string|min:3|max:50|regex:/[a-zA-Z]/',
                'booking_office' => 'nullable|string|min:3|max:50|regex:/[a-zA-Z]/',
                'zone_id' => 'required|exists:zones,id',
                'store_id' => 'nullable|exists:store_types,id',
                'status' => 'required|in:Active,Inactive',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'place_id' => 'required|exists:places,id',
                'address_line_1' => 'required|string|min:3|max:150|regex:/[a-zA-Z]/',
                'address_line_2' => 'nullable|string|min:3|max:150|regex:/[a-zA-Z]/',
                'address_line_3' => 'nullable|string|min:3|max:150|regex:/[a-zA-Z]/',
                'zip_code' => 'nullable|numeric|digits:6|regex:/[1-9]/',
                'contact_person_name' => 'nullable|string|min:3|max:50|regex:/[a-zA-Z]/',
                'designation' => 'nullable|string|min:3|max:50|regex:/[a-zA-Z]/',
                'contact_mobile_no' => 'nullable|numeric|digits_between:10,15|regex:/[1-9]/',
                'contact_email' => 'nullable|email:rfc,dns|max:128',
                'tax_type_id' => 'nullable|exists:taxes,id',
                'gst_no' => [
                    'nullable',
                    'string',
                    'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                ],
                'pan_no' => [
                    'nullable',
                    'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                    'unique:customers,pan_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'payment_terms' => 'nullable|string|min:3|max:255|regex:/^[^<>]*$/',
                'credit_limit' => 'nullable|numeric|min:0|max:100',
                'sales_discount' => 'nullable|numeric|min:0|max:100',
                'box_discount_amount' => 'nullable|numeric|min:0',
                'bank_name' => 'nullable|string|min:3|max:50|regex:/[a-zA-Z]/',
                'branch' => 'nullable|string|min:3|max:50|regex:/[a-zA-Z]/',
                'account_number' => [
                    'nullable',
                    'digits_between:9,20',
                    'regex:/[1-9]/',
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
                'code.not_in' => 'This field is an invalid format.',
                '*.regex' => 'This field is an invalid format.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
                '*.digits_between' => 'This field must be between :min and :max digits.',
                '*.digits' => 'This field must be exactly :digits digits.',
                '*.numeric' => 'This field must be a number.',
                '*.url' => 'This field is an invalid URL.',
                '*.email' => 'Please enter a valid email address.',
                'credit_limit.min' => 'Please enter a valid numeric value greater than or equal to 0.',
                'sales_discount.min' => 'Please enter a valid numeric value greater than or equal to 0.',
                'box_discount_amount.min' => 'Please enter a valid numeric value greater than or equal to 0.',
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
                'store_id' => $request->store_id,
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
                'box_discount_amount' => $request->box_discount_amount ?? 0,
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

        $zones = [];
        $states = State::active()->get();
        $cities = [];
        $places = [];
        $taxes = Tax::active()->get();
        $store_types = StoreType::active()->get();

        $stateId = old('state_id') ?? ($customer->state_id ?? null);
        $cityId  = old('city_id')  ?? ($customer->city_id ?? null);

        if ($stateId) {
            $cities = City::where('state_id', $stateId)->get();
        }

        if ($cityId) {
            $places = Place::where('city_id', $cityId)->get();
            $zones = Zone::active()->whereRaw("FIND_IN_SET(?, city_ids)", [$cityId])->get();
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
        $salesOrder = SalesOrder::where('customer_id', $id)->exists();
        if ($salesOrder) {
            return redirect('customers')->with('error', 'Customer cannot be deleted as it is used in sales orders');
        }
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

    public function import(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create customers')) {
            return unauthorizedRedirect();
        }

        $request->validate([
            'import_file' => 'required|mimes:csv,txt,xlsx,xls'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\CustomerImport, $request->file('import_file'));
            return redirect('customers')->with('success', 'Customers imported successfully.');
        } catch (\Exception $e) {
            return redirect('customers')->with('error', $e->getMessage());
        }
    }

    public function downloadSample()
    {
        $headers = ['Category','Code', 'Name', 'Mobile Number','Email','Website URL','State','City','Place','Address','Zip Code','Transport Name','Booking Office','Zone','Contact Person Name','Contact Mobile No','Contact Email','Status','GST No','Payment Terms', 'Sales Discount', 'Box Discount Amount(Per Pcs)'];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->streamDownload($callback, 'Customer_Sample_Format.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportExcel()
    {
        if (auth()->id() != 1 && !auth()->user()->can('view customers')) {
            return unauthorizedRedirect();
        }
        return Excel::download(new CustomersExport, 'customers_' . date('Ymd_His') . '.xlsx');
    }
}
