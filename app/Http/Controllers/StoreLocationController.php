<?php

namespace App\Http\Controllers;

use App\Models\StoreLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorelocationController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view store-location')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {

            $locations = StoreLocation::orderBy('id','desc')->get();
            $data = [];
            $count = 1;

            foreach ($locations as $loc) {

                $checked = $loc->status === 'Active' ? 'checked' : '';

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input store-status-toggle"
                        data-id="' . $loc->id . '" ' . $checked . '>
                    <span class="switch-toggle-slider"></span>
                </label>
                <div class="status_msg_' . $loc->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit store-location')) {
                    $action .= '<a href="' . url('store_location/add/' . $loc->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete store-location')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete"
                                onclick="delete_data(\'' . url('store_location/delete/' . $loc->id) . '\')">
                                <i class="icon-base ri ri-delete-bin-line"></i>
                            </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'store_location' => $loc->store_location,
                    'status' => $status,
                    'action' => $action
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('store_location.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit store-location')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create store-location')) {
                return unauthorizedRedirect();
            }
        }
        $storeLocation = $id ? StoreLocation::findOrFail($id) : null;
        $oldData = $storeLocation ? $storeLocation->toArray() : null;

        if ($request->isMethod('post')) {

            $request->validate([
                'store_location' => 'required|string|max:255|unique:store_locations,store_location,' . $id . ',id,deleted_at,NULL',
                'status' => 'required|in:Active,Inactive'
            ],[
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ]);

            $data = $request->only(['store_location', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                StoreLocation::where('id', $id)->update($data);
                $newData = StoreLocation::find($id)->toArray();

                addLog('update', 'Store Location', 'store_locations', $id, $oldData, $newData);

                $msg = 'Store Location updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $storeLocation = StoreLocation::create($data);
                $newData = $storeLocation->toArray();

                addLog('create', 'Store Location', 'store_locations', $storeLocation->id, null, $newData);

                $msg = 'Store Location added successfully';
            }

            return redirect('store_location')->with('success', $msg);
        }

        return view('store_location.add', compact('storeLocation'));
    }


    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete store-location')) {
            return unauthorizedRedirect();
        }
        $storeLocation = StoreLocation::findOrFail($id);

        $checks = [
            ['table' => 'stock_entry_items', 'column' => 'store_location_id', 'label' => 'Stock Entry Items'],
            ['table' => 'grn_entry_items', 'column' => 'store_location_id', 'label' => 'GRN Entry Items'],
        ];

        foreach ($checks as $check) {
            $table = $check['table'];
            $column = $check['column'];
            $label = $check['label'];

            $query = \Illuminate\Support\Facades\DB::table($table)->where($column, $id);
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'deleted_at')) {
                $query->whereNull('deleted_at');
            }

            if ($query->exists()) {
                return redirect('store_location')->with('danger', "This store location is currently referenced in {$label} and cannot be deleted.");
            }
        }

        $oldData = $storeLocation->toArray();
        $storeLocation->delete();
        addLog('delete', 'Store Location', 'store_locations', $id, $oldData, null);
        return redirect('store_location')->with('success', 'Store Location deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $storeLocation = StoreLocation::findOrFail($id);
        $oldData = $storeLocation->toArray();
        $storeLocation->status = $request->status;
        $storeLocation->save();
        $newData = $storeLocation->toArray();
        addLog('update_status', 'Store Location Status', 'store_locations', $id, $oldData, $newData);
        return response()->json(['success' => true]);
    }
}
