<?php

namespace App\Http\Controllers;

use App\Models\BrandCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandCategoryController extends Controller
{
    public function index(Request $request)
    {
        $canAdd    = true;
        $canEdit   = true;
        $canDelete = true;

        if ($request->ajax()) {

            $categories = BrandCategory::latest()->get();
            $data = [];
            $count = 1;

            foreach ($categories as $category) {
                $checked = $category->status === 'Active' ? 'checked' : '';

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input brand-category-status-toggle"
                        data-id="' . $category->id . '" ' . $checked . '>
                    <span class="switch-toggle-slider"></span>
                </label>
                <div class="status_msg_' . $category->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit brand-categories')) {
                    $action .= '
                    <a href="' . url('brand_categories/add/' . $category->id) . '" 
                       class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete brand-categories')) {
                    $action .= '
                    <button class="btn btn-delete"
                        onclick="delete_data(\'' . url('brand_categories/delete/' . $category->id) . '\')">
                        <i class="icon-base ri ri-delete-bin-line"></i>
                    </button>';
                }
                $action .= '</div>';
                $data[] = [
                    'DT_RowIndex' => $count++,
                    'code'        => '<span class="badge bg-light-primary">' . $category->code . '</span>',
                    'name'        => $category->name,
                    'created_by' => createdByName($category->created_by),
                    'status'     => $status,
                    'action'     => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('brand_categories.view', compact('canAdd'));
    }

    public function add($id = null)
    {
        $brandCategory = null;
        if ($id) {
            $brandCategory = BrandCategory::findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'code' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('brand_categories', 'code')
                        ->ignore($id)
                        ->whereNull('deleted_at')
                ],
                'name' => 'required|string|max:255',
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
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
            ];

            if ($id) {
                $oldData = $brandCategory->toArray();
                $data['updated_by'] = auth()->id();
                $brandCategory->update($data);
                $newData = $brandCategory->fresh()->toArray();
                BrandCategory::where('id', $id)->update($data);
                addLog('update', 'Brand Category', 'brands_categories', $id, $oldData, $newData);

                $message = 'Brand Category updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $created = BrandCategory::create($data);

                addLog('create', 'Brand Category', 'brands_categories', $created->id, null, $created->toArray());
                $message = 'Brand Category added successfully';
            }

            return redirect('brand_categories')->with('success', $message);
        }

        return view('brand_categories.add', compact('brandCategory'));
    }

    public function destroy($id)
    {
        $brandCategory = BrandCategory::findOrFail($id);

        $oldData = $brandCategory->toArray();

        $brandCategory->delete();

        addLog('delete','Brand Category','brand_categories', $id,$oldData,null);

        return response()->json([
            'success' => true,
            'message' => 'Brand Category deleted successfully'
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        $brandCategory = BrandCategory::findOrFail($id);

        $oldData = $brandCategory->toArray();

        $brandCategory->status = $request->status;
        $brandCategory->save();

        $newData = $brandCategory->toArray();

        addLog('update_status', 'Brand Category Status', 'brand_categories', $id, $oldData, $newData);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
