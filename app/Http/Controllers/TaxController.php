<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $taxes = Tax::latest()->get();
            $data = [];
            $i = 1;

            foreach ($taxes as $tax) {

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input tax-status-toggle"
                        data-id="' . $tax->id . '" ' . ($tax->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $tax->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit taxes')) {
                    $action .= '<a href="' . url('taxes/add/' . $tax->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>
                                ';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete taxes')) {
                    $action .= '<button class="btn btn-delete"
                                onclick="delete_data(\'' . url('tax/delete/' . $tax->id) . '\')">
                                <i class="icon-base ri ri-delete-bin-line"></i>
                              </button>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'item_name'   => $tax->item_name,
                    'tax_rate'    => $tax->tax_rate . '%',
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('taxes.view');
    }

    public function add(Request $request, $id = null)
    {
        $tax = $id ? Tax::findOrFail($id) : null;
        $oldData = $tax ? $tax->toArray() : null;

        if ($request->isMethod('post')) {

            $rules = [
                'item_name' => 'required|string|max:255|unique:taxes,item_name,' . $id . ',id,deleted_at,NULL',
                'tax_rate'  => 'required|numeric|min:0|max:100',
                'status'    => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];

            $request->validate($rules,$messages);

            $data = $request->only(['item_name', 'tax_rate', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                Tax::where('id', $id)->update($data);
                $newData = Tax::find($id)->toArray();

                addLog('update', 'Tax', 'taxes', $id, $oldData, $newData);

                $msg = 'Tax updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $tax = Tax::create($data);
                $newData = $tax->toArray();

                addLog('create', 'Tax', 'taxes', $tax->id, null, $newData);

                $msg = 'Tax added successfully';
            }

            return redirect('taxes')->with('success', $msg);
        }

        return view('taxes.add', compact('tax'));
    }

    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);
        $oldData = $tax->toArray();

        $tax->delete();

        addLog('delete', 'Tax', 'taxes', $id, $oldData, null);

        return redirect('taxes')->with('success', 'Tax deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $tax = Tax::findOrFail($id);
        $oldData = $tax->toArray();

        $tax->status = $request->status;
        $tax->save();

        $newData = $tax->toArray();

        addLog('update_status', 'Tax Status', 'taxes', $tax->id, $oldData, $newData);

        return response()->json([
            'success' => true,
            'status'  => $tax->status
        ]);
    }
}
