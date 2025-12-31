<?php

namespace App\Http\Controllers;

use App\Models\StoreCategory;
use Illuminate\Http\Request;

class StoreCategoryController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view store-categories')) {
            return unauthorizedRedirect();
        }
        $canAdd    = true;
        $canEdit   = true;
        $canDelete = true;

        if ($request->ajax()) {

            $categories = StoreCategory::latest()->get();
            $data = [];
            $count = 1;

            foreach ($categories as $category) {

                $checked = $category->status === 'Active' ? 'checked' : '';

                $status = '
                    <label class="switch switch-success switch-lg">
                        <input type="checkbox"
                            class="switch-input store-category-status-toggle"
                            data-id="' . $category->id . '" ' . $checked . '>
                        <span class="switch-toggle-slider"></span>
                    </label>
                    <div class="status_msg_' . $category->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit store-categories')) {
                    $action .= '
                <a href="' . url('store_categories/add/' . $category->id) . '" 
                   class="btn btn-edit">
                    <i class="icon-base ri ri-edit-box-line"></i>
                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete store-categories')) {
                    $action .= '
                    <button class="btn btn-delete"
                        onclick="delete_data(\'' . url('store_categories/delete/' . $category->id) . '\')">
                        <i class="icon-base ri ri-delete-bin-line"></i>
                    </button>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'  => $count++,
                    'code'         => $category->code,
                    'category'     => $category->category_name,
                    'created_by'   => createdByName($category->created_by),
                    'status'       => $status,
                    'action'       => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('store_categories.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit store-categories')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create store-categories')) {
                return unauthorizedRedirect();
            }
        }
        $storeCategory = null;
        if ($id) {
            $storeCategory = StoreCategory::findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'code' => 'required|string|max:50|unique:store_categories,code,' . ($id ? $id : 'NULL') . ',id',
                'category_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:Active,Inactive',
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];

            $validated = $request->validate($rules, $messages);

            $data = [
                'code' => $request->code,
                'category_name' => $request->category_name,
                'description' => $request->description,
                'status' => $request->status,
                'created_by' => auth()->id() ?? 1,
            ];

            if ($id) {
                // $data['updated_by'] = auth()->id();
                $oldData = StoreCategory::find($id)->toArray();

                StoreCategory::where('id', $id)->update($data);

                $newData = StoreCategory::find($id)->toArray();

                addLog('update', 'Store Category', 'store_categories', $id, $oldData, $newData);

                $message = 'Store Category updated successfully';
            } else {
                // $data['created_by'] = auth()->id();
                $category = StoreCategory::create($data);

                $newData = $category->toArray();

                addLog('create', 'Store Category', 'store_categories', $category->id, null, $newData);

                $message = 'Store Category added successfully';
            }

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect('store_categories')->with('success', $message);
        }

        return view('store_categories.add', compact('storeCategory'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete store-categories')) {
            return unauthorizedRedirect();
        }
        $storeCategory = StoreCategory::findOrFail($id);

        $oldData = $storeCategory->toArray();

        $storeCategory->delete();

        addLog('delete', 'Store Category', 'store_categories', $id, $oldData, null);

        return response()->json([
            'success' => true,
            'message' => 'Store Category deleted successfully'
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        $storeCategory = StoreCategory::findOrFail($id);

        $oldData = $storeCategory->toArray();

        $storeCategory->status = $request->status;
        $storeCategory->save();

        $newData = $storeCategory->toArray();

        addLog('update_status', 'Store Category Status', 'store_categories', $id, $oldData, $newData);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
