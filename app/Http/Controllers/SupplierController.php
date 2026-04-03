<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\State;
use App\Models\City;
use App\Models\Place;
use App\Models\Tax;
use App\Models\PurchaseOrder;
use App\Models\PurchaseInvoice;
use App\Models\GrnEntry;
use App\Models\DebitNote;
use Illuminate\Http\Request;
use App\Models\PurchaseCommissionAgent;
use App\Models\StoreType;


class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view suppliers')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = Supplier::with(['state', 'city', 'place', 'storeType']);
            $suppliers = $query->orderBy('id', 'desc')->get();

            $data = [];
            $count = 1;

            foreach ($suppliers as $supplier) {
                $checked = $supplier->status === 'Active' ? 'checked' : '';
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input supplier-status-toggle"
                        data-id="' . $supplier->id . '" ' . $checked . '>
                    <span class="switch-toggle-slider"></span>
                </label>
                <div class="status_msg_' . $supplier->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('view_details suppliers')) {
                    $action .= '
                    <a href="' . url('suppliers/view_details/' . $supplier->id) . '" class="btn btn-view">
                        <i class="icon-base ri ri-eye-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('edit suppliers')) {
                    $action .= '
                    <a href="' . url('suppliers/add/' . $supplier->id) . '" class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete suppliers')) {
                    $action .= '
                    <a href="javascript:;" class="btn btn-delete"
                       onclick="delete_data(\'' . url('supplier/delete/' . $supplier->id) . '\')">
                        <i class="icon-base ri ri-delete-bin-line"></i>
                    </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'name' => $supplier->name . ' (' . $supplier->code . ')',
                    'contact_info' => '
                        <div class="contact-info">
                            <div><i class="ri ri-mail-line icon-phone"></i> ' . ($supplier->mobile_no ?? '-') . '</div>
                            <div><i class="ri ri-mail-line icon-email"></i> ' . ($supplier->email ?? '-') . '</div>
                        </div>
                    ',
                    'location' => '
                        <div class="location-info">
                            <div><strong>State:</strong> ' . ($supplier->state->state_name ?? '-') . '</div>
                            <div><strong>City:</strong> ' . ($supplier->city->city_name ?? '-') . '</div>
                            <div><strong>Place:</strong> ' . ($supplier->place->place_name ?? '-') . '</div>
                        </div>
                    ',
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('suppliers.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit suppliers')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create suppliers')) {
                return unauthorizedRedirect();
            }
        }
        $supplier = null;
        if ($id) {
            $supplier = Supplier::with(['state', 'city', 'place', 'tax', 'purchaseCommissionAgent', 'storeType'])->findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'name' => 'required|string|min:3|max:50',
                'code' => 'required|string|min:3|max:25|unique:suppliers,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'mobile_no' => 'required|numeric|digits_between:10,15|unique:suppliers,mobile_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'email' => 'nullable|email|max:128|unique:suppliers,email,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'website_url' => 'nullable|url|max:255',
                'transport_name' => 'nullable|string|min:3|max:50',
                'booking_area' => 'nullable|string|min:3|max:50',
                'store_id' => 'nullable|exists:store_types,id',
                'status' => 'required|in:Active,Inactive',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'place_id' => 'required|exists:places,id',
                'address_line_1' => 'required|string|min:5|max:150',
                'address_line_2' => 'nullable|string|min:5|max:150',
                'address_line_3' => 'nullable|string|min:5|max:150',
                'zip_code' => 'nullable|string|min:6|max:10',
                'contact_person_name' => 'nullable|string|min:3|max:50',
                'designation' => 'nullable|string|min:3|max:50',
                'contact_mobile_no' => 'nullable|numeric|digits_between:10,15',
                'contact_email' => 'nullable|email|max:128',
                'commission' => 'nullable|numeric|min:0|max:100',
                'purchase_commission_agent_id' => 'nullable|exists:purchase_commission_agents,id',
                'commission_percentage' => 'nullable|numeric|min:0|max:100',
                'tax_id' => 'nullable|exists:taxes,id',
                'gst_no' => [
                    'nullable',
                    'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                    'unique:suppliers,gst_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'pan_no' => [
                    'nullable',
                    'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                    'unique:suppliers,pan_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'ecc_no' => 'nullable|string|max:15',
                'credit_limit' => 'nullable|numeric|min:0|max:100',
                'payment_terms' => 'nullable|string|max:255|regex:/^[^<>]*$/',
                'bank_name' => 'nullable|string|min:3|max:50',
                'branch' => 'nullable|string|min:3|max:50',
                'account_number' => [
                    'nullable',
                    'digits_between:9,20',
                    'unique:suppliers,account_number,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'ifsc_code' => [
                    'nullable',
                    'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
                    'unique:suppliers,ifsc_code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique' => 'This field already exists.',
                '*.regex' => 'This field is an invalid format',
                '*.min' => 'This field must be at least :min characters.',
                '*.max' => 'This field should not be more than :max characters.',
                '*.digits_between' => 'This field must be between :min and :max digits.',
                '*.numeric' => 'This field must be a number.',
                '*.url' => 'This field is an invalid URL.',
            ];
            $validated = $request->validate($rules, $messages);

            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'website_url' => $request->website_url,
                'transport_name' => $request->transport_name,
                'booking_area' => $request->booking_area,
                'stores' => $request->stores,
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
                'purchase_commission_agent_id' => $request->purchase_commission_agent_id,
                'commission_percentage' => $request->commission_percentage ?? 0,
                'tax_id' => $request->tax_id,
                'gst_no' => $request->gst_no,
                'pan_no' => $request->pan_no,
                'ecc_no' => $request->ecc_no,
                'credit_limit' => $request->credit_limit ?? 0,
                'payment_terms' => $request->payment_terms,
                'bank_name' => $request->bank_name,
                'branch' => $request->branch,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
            ];

            if ($id) {
                $data['updated_by'] = auth()->id();
                $oldData = Supplier::find($id)->toArray();
                Supplier::where('id', $id)->update($data);
                $newData = Supplier::find($id)->toArray();
                addLog('update', 'Supplier', 'suppliers', $id, $oldData, $newData);
                $message = 'Supplier updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $supplier = Supplier::create($data);
                $newData = $supplier->toArray();
                addLog('create', 'Supplier', 'suppliers', $supplier->id, null, $newData);
                $message = 'Supplier added successfully';
            }

            return redirect('suppliers')->with('success', $message);
        }

        $states = State::active()->orderBy('id', 'desc')->get();
        $cities = [];
        $places = [];
        $taxes = Tax::active()->orderBy('id', 'desc')->get();
        $purchase_commission_agents = PurchaseCommissionAgent::active()->orderBy('id', 'desc')->get();
        $store_types = StoreType::active()->orderBy('id', 'desc')->get();

        $stateId = old('state_id', $supplier->state_id ?? null);
        $cityId = old('city_id', $supplier->city_id ?? null);

        if ($stateId) {
            $cities = City::where('state_id', $stateId)->get();
        }

        if ($cityId) {
            $places = Place::where('city_id', $cityId)->get();
        }
        $latestSupplier = Supplier::orderByRaw('CAST(code AS UNSIGNED) DESC')->first();
        $nextCode = $latestSupplier && is_numeric($latestSupplier->code) ? ((int) $latestSupplier->code + 1) : 1000;

        return view('suppliers.add', compact('supplier', 'states', 'cities', 'places', 'taxes', 'purchase_commission_agents', 'store_types', 'nextCode'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details suppliers')) {
            return unauthorizedRedirect();
        }
        $supplier = Supplier::with(['state', 'city', 'place', 'tax'])->findOrFail($id);
        return view('suppliers.view_details', compact('supplier'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete suppliers')) {
            return unauthorizedRedirect();
        }

        $supplier = Supplier::findOrFail($id);

        if (PurchaseOrder::where('supplier_id', $id)->exists()) {
            return redirect('suppliers')->with('danger', 'This supplier is referenced in Purchase Orders and cannot be deleted.');
        }

        if (PurchaseInvoice::where('supplier_id', $id)->exists()) {
            return redirect('suppliers')->with('danger', 'This supplier is referenced in Purchase Invoices and cannot be deleted.');
        }

        if (GrnEntry::where('supplier_id', $id)->exists()) {
            return redirect('suppliers')->with('danger', 'This supplier is referenced in GRN Entries and cannot be deleted.');
        }

        if (DebitNote::where('supplier_id', $id)->exists()) {
            return redirect('suppliers')->with('danger', 'This supplier is referenced in Debit Notes and cannot be deleted.');
        }

        $oldData = $supplier->toArray();
        $supplier->delete();
        addLog('delete', 'Supplier', 'suppliers', $id, $oldData, null);
        return redirect('suppliers')->with('success', 'Supplier deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $oldData = $supplier->toArray();
        $supplier->status = $request->status;
        $supplier->save();
        $newData = $supplier->toArray();
        addLog('update_status', 'Supplier Status', 'suppliers', $id, $oldData, $newData);
        return response()->json(['success' => true]);
    }

    public function import(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create suppliers')) {
            return unauthorizedRedirect();
        }

        $request->validate([
            'import_file' => 'required|mimes:csv,txt,xlsx,xls'  
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SupplierImport, $request->file('import_file'));
            return redirect('suppliers')->with('success', 'Suppliers imported successfully.');
        } catch (\Exception $e) {
            return redirect('suppliers')->with('error', $e->getMessage());
        }
    }

    public function downloadSample()
    {
        $headers = [
            'Name',
            'Code',
            'Mobile Number',
            'Email',
            'Website URL',
            'Transport Name',
            'Booking Area',
            'Store',
            'Status (Active/Inactive)',
            'State',
            'City',
            'Place',
            'Address Line 1',
            'Address Line 2',
            'Address Line 3',
            'Zip Code',
            'Contact Person Name',
            'Designation',
            'Contact Mobile No',
            'Contact Email',
            'Commission Agent',
            'Commission Percentage',
            'Tax Type',
            'GST No',
            'PAN No',
            'ECC No',
            'Payment Terms',
            'Credit Limit',
            'Bank Name',
            'Branch',
            'Account Number',
            'IFSC Code'
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->streamDownload($callback, 'Supplier_Sample_Format.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
