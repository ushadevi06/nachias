<?php

namespace App\Http\Controllers;

use App\Models\StoreType;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view stores')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $storeTypes = StoreType::latest()->get();

            $data = [];
            $i = 1;

            foreach ($storeTypes as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input store-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit stores')) {
                    $action .= '<a href="' . url('stores/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete stores')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('stores/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'store_type_name' => $row->store_type_name,
                    'status'      => $status,
                    'action'      => $action,
                ];
            }
            return response()->json(['data' => $data]);
        }

        return view('stores.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit stores')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create stores')) {
                return unauthorizedRedirect();
            }
        }

        $storeType = $id ? StoreType::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'store_type_name' => 'required|string|max:255|unique:store_types,store_type_name,' . $id,
                'status'   => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['store_type_name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                StoreType::where('id', $id)->update($data);
                addLog('update', 'Store Type', 'store_types', $id, null, $data);
                $msg = 'Store updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newStoreType = StoreType::create($data);
                addLog('create', 'Store Type', 'store_types', $newStoreType->id, null, $data);
                $msg = 'Store added successfully';
            }

            return redirect('stores')->with('success', $msg);
        }

        return view('stores.add', compact('storeType', 'id'));
    }

    public function updateStatus(Request $request, $id)
    {
        $storeType = StoreType::findOrFail($id);
        $oldData = $storeType->toArray();
        $storeType->status = $request->status;
        $storeType->save();
        $newData = $storeType->toArray();
        addLog('update_status', 'Store Type Status', 'store_types', $storeType->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $storeType->status
        ]);
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete stores')) {
            return unauthorizedRedirect();
        }
        $storeType = StoreType::findOrFail($id);
        $oldData = $storeType->toArray();
        $storeType->delete();
        addLog('delete', 'Store Type', 'store_types', $id, $oldData, null);
        return redirect('stores')->with('success', 'Store deleted successfully');
    }
}
