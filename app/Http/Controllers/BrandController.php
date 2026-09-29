<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\StoreCategory;
use App\Models\PurchaseOrderItem;
use App\Models\JobCardEntry;
use App\Models\Item;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view brands')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $brands = Brand::with('storeCategories')->latest()->get();
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

                $catBadges = $brand->storeCategories->map(function ($cat) {
                    return '<span class="badge bg-label-primary me-1 mb-1">' . e($cat->category_name . ($cat->code ? ' (' . $cat->code . ')' : '')) . '</span>';
                })->implode('');

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'brand_name' => $brand->brand_name,
                    'code' => $brand->code,
                    'store_category' => $catBadges ?: '-',
                    'created_by' => createdByName($brand->created_by),
                    'status' => $status,
                    'action' => $action,
                ];
            }
            return response()->json(['data' => $data]);
        }
        return view('brands.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit brands')) {
                return unauthorizedRedirect();
            }
        }
        else {
            if (auth()->id() != 1 && !auth()->user()->can('create brands')) {
                return unauthorizedRedirect();
            }
        }
        $brand = $id ? Brand::with('storeCategories')->findOrFail($id) : null;
        $hasInvoices = false;
        if ($brand) {
            $hasInvoices = \App\Models\SalesInvoice::where('brand_id', $brand->id)->exists();
        }

        $storeCategories = StoreCategory::active()->orderBy('id','desc')->get();
        $selectedCategoryIds = $brand ? $brand->storeCategories->pluck('id')->toArray() : [];

        if ($request->isMethod('post')) {
            if ($hasInvoices && $brand) {
                $request->merge(['code' => $brand->code]);
            }

            $rules = [
                'brand_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^(?!0+$).*$/',
                    'unique:brands,brand_name,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'code' => [
                    'required',
                    'string',
                    'min:2',
                    'max:50',
                    'regex:/^(?!0+$).*$/',
                    'unique:brands,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'store_category_ids' => 'required|array|min:1',
                'store_category_ids.*' => 'exists:store_categories,id',
                'status' => 'required|in:Active,Inactive',
            ];
            $messages = [
                '*.required' => 'This field is required.',
                'store_category_ids.required' => 'Please select at least one Store Category.',
                '*.unique' => 'This field already exists.',
                '*.regex' => 'This field is an invalid format.',
                'code.regex' => 'Code cannot be 0',
                '*.min' => 'This field must be at least :min characters.',
                '*.max' => 'This field should not be more than :max characters.',
            ];
            $validated = $request->validate($rules, $messages);
            $data = [
                'brand_name' => $request->brand_name,
                'code' => $hasInvoices && $brand ? $brand->code : $request->code,
                'status' => $request->status,
            ];
            if ($id) {
                $data['updated_by'] = auth()->id();
                $oldData = $brand->toArray();
                $brand->update($data);
                $brand->storeCategories()->sync($request->store_category_ids);
                $newData = $brand->fresh()->toArray();
                addLog('update', 'Brand', 'brands', $id, $oldData, $newData);
                return redirect('brands')->with('success', 'Brand updated successfully');
            }
            else {
                $data['created_by'] = auth()->id() ?? 1;
                $created = Brand::create($data);
                $created->storeCategories()->sync($request->store_category_ids);
                addLog('create', 'Brand', 'brands', $created->id, null, $created->toArray());
                return redirect('brands')->with('success', 'Brand added successfully');
            }
        }
        return view('brands.add', compact('brand', 'hasInvoices', 'storeCategories', 'selectedCategoryIds'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete brands')) {
            return unauthorizedRedirect();
        }
        $brand = Brand::findOrFail($id);
        if (PurchaseOrderItem::where('brand_id', $id)->exists()) {
            return redirect('brands')->with('danger', 'This brand is currently referenced in Purchase Order Items and cannot be deleted.');
        }
        if (JobCardEntry::where('brand_id', $id)->exists()) {
            return redirect('brands')->with('danger', 'This brand is currently referenced in Job Card Entries and cannot be deleted.');
        }
        if (Item::where('brand_id', $id)->exists()) {
            return redirect('brands')->with('danger', 'This brand is currently referenced in Items and cannot be deleted.');
        }
        $oldData = $brand->toArray();
        $brand->delete();
        addLog('delete', 'Brand', 'brands', $id, $oldData, null);
        return redirect('brands')->with('success', 'Brand deleted successfully');
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
