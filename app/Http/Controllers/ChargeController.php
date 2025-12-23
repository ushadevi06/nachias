<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
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
                <div class="status_msg_' . $charge->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit charges')) {
                    $action .= '
                        <a href="' . url('charges/add/' . $charge->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete charges')) {
                    $action .= '
                        <a href="javascript:;" class="btn btn-delete"
                           onclick="delete_data(\'' . url('charges/delete/' . $charge->id) . '\')">
                           <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
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
        $charge = null;
        if ($id) {
            $charge = Charge::findOrFail($id);
            $oldData = $charge->toArray();
        }

        if (request()->isMethod('post')) {

            $request = request();

            $rules = [
                'charge_name' => ['required','string','max:255',
                    Rule::unique('charges', 'charge_name')->ignore($id)->whereNull('deleted_at')],
                'status' => 'required|in:Active,Inactive'
            ];
            $messages =  [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
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
                Charge::create($data);
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
        addLog('update', 'Charge Status', 'charges', $id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $charge->status
        ]);
    }

    public function destroy($id)
    {
        $charge = Charge::findOrFail($id);
        $oldData = $charge->toArray();
        $charge->delete();
        addLog('delete', 'Charge', 'charges', $id, $oldData, null);
        return redirect('charges')->with('success', 'Charge deleted successfully');
    }

}
