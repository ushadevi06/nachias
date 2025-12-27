<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\State;
use App\Models\City;
use App\Models\Place;
use App\Models\Tax;
use Illuminate\Http\Request;
use App\Models\PurchaseCommissionAgent;


class SupplierController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $query = Supplier::with(['state', 'city', 'place']);
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
                    <a href="' . url('view_supplier/' . $supplier->id) . '" class="btn btn-view">
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
                    'name'   => $supplier->name . ' (' . $supplier->code . ')',
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
        $supplier = null;
        if ($id) {
            $supplier = Supplier::with(['state', 'city', 'place', 'tax','purchaseCommissionAgent'])->findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:suppliers,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'mobile_no' => 'required|string|max:15|unique:suppliers,mobile_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'email' => 'nullable|email|max:255|unique:suppliers,email,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'website_url' => 'nullable|url|max:255',
                'transport_name' => 'nullable|string|max:255',
                'booking_area' => 'nullable|string|max:255',
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
                'purchase_commission_agent' => 'nullable|string|max:255',
                'commission' => 'nullable|numeric|min:0|max:100',
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
                'ecc_no' => 'nullable|string|max:20',
                'credit_limit' => 'nullable|numeric|min:0',
                'payment_terms' => 'nullable|string|max:500',
                'bank_name' => 'nullable|string|max:255',
                'branch' => 'nullable|string|max:255',
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
                '*.unique'   => 'This field already exists.',
            ];
            $validated = $request->validate($rules,$messages);

            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'website_url' => $request->website_url,
                'transport_name' => $request->transport_name,
                'booking_area' => $request->booking_area,
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

        $states = State::active()->get();
        $cities = [];
        $places = [];
        $taxes = Tax::active()->get();
        $purchase_commission_agents = PurchaseCommissionAgent::active()->get();

        $stateId = old('state_id', $supplier->state_id ?? null);
        $cityId = old('city_id', $supplier->city_id ?? null);

        if ($stateId) {
            $cities = City::where('state_id', $stateId)->get();
        }

        if ($cityId) {
            $places = Place::where('city_id', $cityId)->get();
        }

        return view('suppliers.add', compact('supplier', 'states', 'cities', 'places', 'taxes', 'purchase_commission_agents'));
    }

    public function view($id)
    {
        $supplier = Supplier::with(['state', 'city', 'place', 'tax'])->findOrFail($id);
        return view('suppliers.view_details', compact('supplier'));
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
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
}
