<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use App\Models\SalesOrder;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view shipping-methods')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $shippingMethods = ShippingMethod::latest()->get();

            $data = [];
            $i = 1;

            foreach ($shippingMethods as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input shipping-method-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit shipping-methods')) {
                    $action .= '<a href="' . url('shipping_methods/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete shipping-methods')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('shipping_methods/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'name'        => $row->name,
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('logistics_master.shipping_method.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit shipping-methods')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create shipping-methods')) {
                return unauthorizedRedirect();
            }
        }

        $shippingMethod = $id ? ShippingMethod::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'name'   => 'required|string|min:2|max:50|unique:shipping_methods,name,' . $id . ',id,deleted_at,NULL',
                'status' => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                ShippingMethod::where('id', $id)->update($data);
                addLog('update', 'Shipping Method', 'shipping_methods', $id, null, $data);
                $msg = 'Shipping Method updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newMethod = ShippingMethod::create($data);
                addLog('create', 'Shipping Method', 'shipping_methods', $newMethod->id, null, $data);
                $msg = 'Shipping Method added successfully';
            }

            return redirect('shipping_methods')->with('success', $msg);
        }

        return view('logistics_master.shipping_method.add', compact('shippingMethod'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete shipping-methods')) {
            return unauthorizedRedirect();
        }
        $shippingMethod = ShippingMethod::findOrFail($id);

        if (SalesOrder::where('shipping_method_id', $id)->exists()) {
            return redirect('shipping_methods')->with('danger', 'This method is currently referenced in Sales Orders.');
        }

        $oldData = $shippingMethod->toArray();
        $shippingMethod->delete();
        addLog('delete', 'Shipping Method', 'shipping_methods', $id, $oldData, null);
        return redirect('shipping_methods')->with('success', 'Shipping Method deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $method = ShippingMethod::findOrFail($id);
        $oldData = $method->toArray();
        $method->status = $request->status;
        $method->save();
        $newData = $method->toArray();
        addLog('update_status', 'Shipping Method Status', 'shipping_methods', $method->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $method->status
        ]);
    }
}
