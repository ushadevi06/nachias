<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view brands')) {
            return unauthorizedRedirect();
        }
        $canAdd    = true;
        $canEdit   = true;
        $canDelete = true;

        if ($request->ajax()) {

            $brands = Brand::latest()->get();
            $data = [];
            $count = 1;

            foreach ($brands as $brand) {

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input brand-status-toggle"
                        data-id="' . $brand->id . '" ' . ($brand->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider"></span>
                </label>
                <div class="status_msg_' . $brand->id . ' mt-1"></div>
            ';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit brands')) {
                    $action .= '
                    <a href="' . url('brands/add/' . $brand->id) . '" class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete brands')) {
                    $action .= '
                    <button class="btn btn-delete"
                        onclick="delete_data(`' . url('brands/delete/' . $brand->id) . '`)">
                        <i class="icon-base ri ri-delete-bin-line"></i>
                    </button>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'brand_name' => $brand->brand_name,
                    'created_by' => createdByName($brand->created_by),
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('brands.view', compact('canAdd'));
    }


    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit brands')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create brands')) {
                return unauthorizedRedirect();
            }
        }
        $brand = $id ? Brand::findOrFail($id) : null;

        if ($request->isMethod('post')) {

            $rules = [
                'brand_name' => 'required|string|max:255|unique:brands,brand_name,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'status' => 'required|in:Active,Inactive',
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];

            $validated = $request->validate($rules, $messages);

            $data = [
                'brand_name' => $request->brand_name,
                'status' => $request->status,
                'created_by' => auth()->id() ?? 1,
            ];

            if ($id) {
                $data['updated_by'] = auth()->id();
                $oldData = $brand->toArray();
                $brand->update($data);
                $newData = $brand->fresh()->toArray();

                addLog('update', 'Brand', 'brands', $id, $oldData, $newData);

                return redirect('brands')->with('success', 'Brand updated successfully');
            } else {
                $data['created_by'] = auth()->id();
                $created = Brand::create($data);

                addLog('create', 'Brand', 'brands', $created->id, null, $created->toArray());

                return redirect('brands')->with('success', 'Brand added successfully');
            }
        }

        return view('brands.add', compact('brand'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete brands')) {
            return unauthorizedRedirect();
        }
        $brand = Brand::findOrFail($id);
        $oldData = $brand->toArray();

        $brand->delete();

        addLog('delete', 'Brand', 'brands', $id, $oldData, null);

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $oldData = $brand->toArray();

        $brand->status = $request->status;
        $brand->save();

        $newData = $brand->toArray();

        addLog('update_status', 'Brand Status', 'brands', $id, $oldData, $newData);

        return response()->json(['success' => true]);
    }
}
