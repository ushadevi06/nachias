<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\FabricType;
use App\Models\Uom;
use App\Models\SizeRatio;
use App\Models\Color;
use App\Models\StoreCategory;
use App\Models\RawMaterial;
use App\Models\ServiceProvider;
use App\Models\OperationStage;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view items')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {

            $query = Item::with(['brand', 'brandCategory', 'uom'])->latest();

            if ($request->brand_category_id) {
                $query->where('brand_category_id', $request->brand_category_id);
            }

            if ($request->brand_id) {
                $query->where('brand_id', $request->brand_id);
            }

            $items = $query->get();
            $data = [];
            $count = 1;
            $colorMap = Color::pluck('color_name', 'id');

            foreach ($items as $item) {

                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input item-status-toggle"
                        data-id="' . $item->id . '" ' . ($item->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider"></span>
                </label>
                <div class="status_msg_' . $item->id . ' mt-1"></div>
            ';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('view_details items')) {
                $action .= '
                    <a href="' . url("items/view/$item->id") . '" class="btn btn-view">
                            <i class="icon-base ri ri-eye-line"></i>
                        </a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('edit items')) {
                    $action .= 
                    '<a href="' . url("items/add/$item->id") . '" class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete items')) {
                    $action .= '
                    <button class="btn btn-delete"
                        onclick="delete_data(`' . url("items/delete/$item->id") . '`)">
                        <i class="icon-base ri ri-delete-bin-line"></i>
                    </button>';
                }

                $action .= '</div>';

                $colors = collect($item->color_id)
                    ->map(fn($id) => $colorMap[$id] ?? null)
                    ->filter()
                    ->implode(', ');

                $pricing =
                    number_format($item->wholesale_price ?? 0) . ' / ' .
                    number_format($item->retail_price ?? 0) . ' / ' .
                    number_format($item->export_price ?? 0);

                $data[] = [
                    'DT_RowIndex'   => $count++,
                    'brand_category' => $item->brandCategory
                        ? $item->brandCategory->name . ' (' . $item->brandCategory->code . ')'
                        : '-',
                    'name'          => $item->name,
                    'brand'         => $item->brand->brand_name ?? '-',
                    'color'         => $colors,
                    'uom'           => $item->uom->uom_code ?? '-',
                    'pricing'       => $pricing,
                    'created_by'    => createdByName($item->created_by),
                    'status'        => $status,
                    'action'        => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        $brandCategories = BrandCategory::where('status', 'Active')->get();
        $brands = Brand::where('status', 'Active')->get();

        return view('items.view', compact('brandCategories', 'brands'));
    }


    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit items')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create items')) {
                return unauthorizedRedirect();
            }
        }
        $item = $id ? Item::findOrFail($id) : null;

        if (request()->isMethod('post')) {

            $request = request();
            // dd($request->color_id);

            $rules = [
                'brand_id'            => 'required|exists:brands,id',
                'brand_category_id'   => 'required|exists:brand_categories,id',
                // 'entry_type'          => 'required|in:raw_material,items',
                'name'                => 'required|string|max:255|unique:items,name,' . ($id ?? '0'),
                'code'                => 'required|string|max:50|unique:items,code,' . ($id ?? '0'),
                'style'               => 'nullable|string|max:100',
                'fabric_type_id'      => 'nullable|exists:fabric_types,id',
                'design_art_no'       => 'nullable|string|max:100',
                'uom_id'              => 'required|exists:uoms,id',
                'size_ratio_id'       => 'nullable|exists:size_ratios,id',
                'color_id'            => 'nullable|array',
                'color_id.*'          => 'exists:colors,id',
                'standard_costing'    => 'nullable|numeric|min:0',
                'store_category_id'   => 'nullable|exists:store_categories,id',
                'related_materials'   => 'nullable',
                'operation_stages'    => 'nullable|array',
                'service_providers'   => 'nullable|array',
                'service_providers.*' => 'nullable|exists:service_providers,id',
                'wholesale_price'     => 'nullable|numeric|min:0',
                'retail_price'        => 'nullable|numeric|min:0',
                'export_price'        => 'nullable|numeric|min:0',
                'status'              => 'required|in:Active,Inactive',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This value already exists.',
                '*.exists'   => 'The selected value is invalid.',
                '*.numeric'  => 'This field must be a valid number.',
                '*.array'    => 'This field must be an array.',
            ];

            $validated = $request->validate($rules, $messages);

            DB::beginTransaction();

            try {

                $data = [
                    'brand_id'          => $request->brand_id,
                    'brand_category_id' => $request->brand_category_id,
                    'entry_type'        => $request->entry_type,
                    'name'              => $request->name,
                    'code'              => $request->code,
                    'style'             => $request->style,
                    'fabric_type_id'    => $request->fabric_type_id,
                    'design_art_no'     => $request->design_art_no,
                    'uom_id'            => $request->uom_id,
                    'size_ratio_id'     => $request->size_ratio_id,
                    'color_id' => $request->has('color_id')
                        ? array_values(array_unique(array_map('intval', $request->color_id)))
                        : [],

                    'standard_costing'  => $request->standard_costing,
                    'store_category_id' => $request->store_category_id,
                    // 'related_materials' => $request->related_materials ? json_encode($request->related_materials) : null,
                    'related_materials' => $request->related_materials,
                    'operation_stages' => $request->operation_stages,
                    'service_providers' => $request->service_providers,
                    'wholesale_price'   => $request->wholesale_price,
                    'retail_price'      => $request->retail_price,
                    'export_price'      => $request->export_price,
                    'status'            => $request->status,
                ];

                if ($id) {
                    $oldData = $item->toArray();
                    $data['updated_by'] = auth()->id();
                    $item->update($data);
                    $newData = $item->fresh()->toArray();
                    addLog('update', 'Item', 'items', $id, $oldData, $newData);
                    $message = 'Item updated successfully';
                } else {
                    $data['created_by'] = auth()->id();
                    $created = Item::create($data);
                    addLog('create', 'Item', 'items', $created->id, null, $created->toArray());
                    $message = 'Item added successfully';
                }

                DB::commit();

                return redirect('items')->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'error' => 'An error occurred: ' . $e->getMessage()
                ]);
            }
        }

        return view('items.add', [
            'item'            => $item,
            'brands'          => Brand::where('status', 'Active')->get(),
            'brandCategories' => BrandCategory::where('status', 'Active')->get(),
            'fabricTypes'     => FabricType::where('status', 'Active')->get(),
            'uoms'            => Uom::where('status', 'Active')->get(),
            'sizeRatios'      => SizeRatio::where('status', 'Active')->get(),
            'colors'          => Color::get(),
            'storeCategories' => StoreCategory::where('status', 'Active')->get(),
            'materials'       => RawMaterial::where('status', 'Active')->get(),
            'serviceProviders' => ServiceProvider::where('status', 'Active')->get(),
            'operationStages'  => OperationStage::where('status', 'Active')->get(),
        ]);
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details items')) {
            return unauthorizedRedirect();
        }
        $item = Item::with([
            'brand',
            'brandCategory',
            'fabricType',
            'uom',
            'sizeRatio',
            'storeCategory'
        ])->findOrFail($id);
        $colors = collect([]);

        if (!empty($item->color_id)) {
            if (is_string($item->color_id)) {
                $colorIds = json_decode($item->color_id, true);
            } else {
                $colorIds = $item->color_id;
            }

            if (is_array($colorIds)) {
                $colors = \App\Models\Color::whereIn('id', $colorIds)->get();
            }
        }
        $operationStages = OperationStage::where('status', 'Active')->get();
        $serviceProviders = ServiceProvider::where('status', 'Active')->get();
        return view('items.view_details', compact(
            'item',
            'colors',
            'operationStages',
            'serviceProviders'
        ));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete items')) {
            return unauthorizedRedirect();
        }
        $item = Item::findOrFail($id);
        $oldData = $item->toArray();
        $item->delete();
        addLog(
            'delete', 'Item', 'items', $id, $oldData, null         
        );
        return redirect('items')->with('success', 'Item deleted successfully');
    }

    public function updateStatus($id)
    {
        $item = Item::findOrFail($id);
        $oldData = $item->toArray();
        $item->status = request('status');
        $item->save();
        $newData = $item->fresh()->toArray();
        addLog('update_status', 'Item Status', 'items', $id, $oldData, $newData);
        return redirect('items')->with('success', 'Status updated successfully');
    }
}