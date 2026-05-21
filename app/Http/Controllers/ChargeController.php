<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\PurchaseInvoiceCharge;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view charges')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $charges = Charge::latest()->get();
            $data = [];
            $count = 1;
            foreach ($charges as $charge) {
                $checked = $charge->status === 'Active' ? 'checked' : '';
                $status = '
                    <label class="switch switch-success switch-lg">
                        <input type="checkbox"
                            class="switch-input charge-status-toggle"
                            data-id="' . $charge->id . '" ' . $checked . '>
                        <span class="switch-toggle-slider">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                    </label>
                    <div class="status_msg_' . $charge->id . '"></div>
                ';
                $action = '<div class="button-box">';
                if (strtoupper(trim($charge->charge_name)) !== 'BROKERAGE') {
                    if (auth()->id() == 1 || auth()->user()->can('edit charges')) {
                        $action .= '<a href="' . url('charges/add/' . $charge->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                    }
                    if (auth()->id() == 1 || auth()->user()->can('delete charges')) {
                        $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('charges/delete/' . $charge->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                    }
                } else {
                    $action .= '<span class="badge bg-label-secondary"><i class="ri-lock-line me-1"></i> System Charge</span>';
                }
                $action .= '</div>';
                $data[] = [
                    'DT_RowIndex' => $count++,
                    'charge_name' => $charge->charge_name,
                    'status' => $status,
                    'action' => $action,
                ];
            }
            return response()->json(['data' => $data]);
        }

        return view('charges.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit charges')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create charges')) {
                return unauthorizedRedirect();
            }
        }
        $charge = null;
        if ($id) {
            $charge = Charge::findOrFail($id);
            if (strtoupper(trim($charge->charge_name)) === 'BROKERAGE') {
                return redirect('charges')->with('danger', 'The BROKERAGE charge is a system-required record and cannot be modified.');
            }
            $oldData = $charge->toArray();
        }
        if (request()->isMethod('post')) {
            $request = request();
            $rules = [
                'charge_name' => ['required','string','min:3','max:50', Rule::unique('charges', 'charge_name')->ignore($id)->whereNull('deleted_at')],
                'status' => 'required|in:Active,Inactive'
            ];
            $messages =  [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
            ];
            $request->validate($rules,$messages);
            $data = [
                'charge_name' => $request->charge_name,
                'status' => $request->status
            ];
            if ($id) {
                $data['updated_by'] = auth()->id();
                Charge::where('id', $id)->update($data);
                $newData = Charge::find($id)->toArray();
                addLog('update', 'Charge', 'charges', $id, $oldData, $newData);
                $message = 'Charge updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $charge = Charge::create($data);
                $newData = $charge->toArray();
                addLog('create', 'Charge', 'charges', $charge->id, null, $newData);
                $message = 'Charge added successfully';
            }
            return redirect('charges')->with('success', $message);
        }
        return view('charges.add', compact('charge'));
    }
    
    public function updateStatus(Request $request, $id)
    {
        $charge = Charge::findOrFail($id);
        $oldData = $charge->toArray();
        $charge->status = $request->status;
        $charge->save();
        $newData = $charge->toArray();
        addLog('update_status', 'Charge Status', 'charges', $id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $charge->status
        ]);
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete charges')) {
            return unauthorizedRedirect();
        }
        $charge = Charge::findOrFail($id);
        if (strtoupper(trim($charge->charge_name)) === 'BROKERAGE') {
            return redirect('charges')->with('danger', 'The BROKERAGE charge is a system-required record and cannot be deleted.');
        }
        $references = [
            [PurchaseInvoiceCharge::class, 'charge_id', 'Purchase Invoice Charges'],
        ];
        foreach ($references as $reference) {
            $model = $reference[0];
            $column = $reference[1];
            $label = $reference[2];
            if ($model::where($column, $id)->exists()) {
                return redirect('charges')->with('danger', "This charge is currently referenced in {$label} and cannot be deleted.");
            }
        }
        $oldData = $charge->toArray();
        $charge->delete();
        addLog('delete', 'Charge', 'charges', $id, $oldData, null);
        return redirect('charges')->with('success', 'Charge deleted successfully');
    }
}
