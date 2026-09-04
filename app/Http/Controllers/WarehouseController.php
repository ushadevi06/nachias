<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseBrandCapacity;
use App\Models\Brand;
use App\Models\Style;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view warehouses')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $warehouses = Warehouse::with(['brandCapacities.brand', 'brandCapacities.style'])->latest()->get();
            $data = [];
            $i = 1;

            foreach ($warehouses as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input warehouse-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit warehouses')) {
                    $action .= '<a href="' . url('warehouses/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete warehouses')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('warehouses/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $totalCap = 0;
                $brandBadges = [];
                $grouped = $row->brandCapacities->where('status', 'Active')->groupBy('brand_id');
                foreach ($grouped as $bId => $caps) {
                    $firstCap = $caps->first();
                    $bName = $firstCap && $firstCap->brand ? $firstCap->brand->brand_name : 'Brand #' . $bId;
                    $bTotal = $caps->sum('capacity_pcs');
                    $totalCap += $bTotal;

                    $styleList = [];
                    foreach ($caps as $cap) {
                        if ($cap->style) {
                            $styleList[] = e($cap->style->style_name) . ': ' . number_format($cap->capacity_pcs);
                        }
                    }

                    if (!empty($styleList)) {
                        $brandBadges[] = '<span class="badge bg-label-info me-1 mb-1">' . e($bName) . ' (' . implode(', ', $styleList) . ')</span>';
                    } else {
                        $brandBadges[] = '<span class="badge bg-label-info me-1 mb-1">' . e($bName) . ': ' . number_format($bTotal) . '</span>';
                    }
                }

                $brandsSummary = !empty($brandBadges) ? implode(' ', $brandBadges) : '<span class="text-muted small">No brands configured</span>';

                $data[] = [
                    'DT_RowIndex'    => $i++,
                    'warehouse_name' => $row->warehouse_name,
                    'brands_summary' => $brandsSummary,
                    'total_capacity' => '<strong>' . number_format($totalCap) . ' Pcs</strong>',
                    'status'         => $status,
                    'action'         => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('warehouse.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit warehouses')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create warehouses')) {
                return unauthorizedRedirect();
            }
        }

        $warehouse = $id ? Warehouse::with(['brandCapacities.brand', 'brandCapacities.style'])->findOrFail($id) : null;
        $brands = Brand::where('status', 'Active')->orderBy('id','desc')->get();
        $styles = Style::where('status', 'Active')->orderBy('id','desc')->get();

        if ($request->isMethod('post')) {
            $hasBrandBlocks = $request->has('brand_blocks');

            $rules = [
                'warehouse_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:50',
                    'not_regex:/^0+$/',
                    'unique:warehouses,warehouse_name,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'status' => 'required|in:Active,Inactive',
            ];

            if ($hasBrandBlocks) {
                $rules['brand_blocks'] = 'required|array|min:1';
                $rules['brand_blocks.*.brand_id'] = 'required|exists:brands,id';
                $rules['brand_blocks.*.styles'] = 'required|array|min:1';
                $rules['brand_blocks.*.styles.*.style_id'] = 'required|exists:styles,id';
                $rules['brand_blocks.*.styles.*.capacity_pcs'] = 'required|numeric|min:0';
            } else {
                $rules['brand_ids'] = 'required|array|min:1';
                $rules['brand_ids.*'] = 'required|exists:brands,id';
                $rules['capacities'] = 'required|array|min:1';
                $rules['capacities.*'] = 'required|numeric|min:0';
            }

            $messages = [
                'warehouse_name.required' => 'Warehouse name is required.',
                'status.required' => 'Status is required.',
                'brand_blocks.required' => 'At least one brand must be configured.',
                'brand_blocks.min' => 'At least one brand must be configured.',
                'brand_blocks.*.brand_id.required' => 'Brand selection is required.',
                'brand_blocks.*.brand_id.exists' => 'Selected brand is invalid.',
                'brand_blocks.*.styles.required' => 'At least one style is required per brand.',
                'brand_blocks.*.styles.min' => 'At least one style is required per brand.',
                'brand_blocks.*.styles.*.style_id.required' => 'Style selection is required.',
                'brand_blocks.*.styles.*.style_id.exists' => 'Selected style is invalid.',
                'brand_blocks.*.styles.*.capacity_pcs.required' => 'Capacity is required.',
                'brand_blocks.*.styles.*.capacity_pcs.numeric' => 'Capacity must be a number.',
                'brand_blocks.*.styles.*.capacity_pcs.min' => 'Capacity must be at least 0.',
                'brand_ids.required' => 'At least one brand must be selected.',
                'brand_ids.*.required' => 'Brand selection is required.',
                'capacities.*.required' => 'Capacity is required.',
                'capacities.*.numeric' => 'Capacity must be a number.',
                'capacities.*.min' => 'Capacity must be at least 0.',
                '*.unique'   => 'This field already exists.',
                'warehouse_name.not_regex' => 'This field is an invalid format.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
            ];

            $request->validate($rules, $messages);

            DB::beginTransaction();
            try {
                $oldData = $warehouse ? $warehouse->toArray() : null;

                $data = [
                    'warehouse_name' => trim($request->warehouse_name),
                    'status' => $request->status,
                ];

                if ($id) {
                    $data['updated_by'] = auth()->id();
                    $warehouse->update($data);
                    $msg = 'Warehouse updated successfully';
                } else {
                    $data['created_by'] = auth()->id();
                    $warehouse = Warehouse::create($data);
                    $msg = 'Warehouse added successfully';
                }

                // Sync brand capacities
                WarehouseBrandCapacity::where('warehouse_id', $warehouse->id)->forceDelete();

                if ($hasBrandBlocks && is_array($request->brand_blocks)) {
                    foreach ($request->brand_blocks as $bBlock) {
                        $bId = $bBlock['brand_id'] ?? null;
                        $stylesList = $bBlock['styles'] ?? [];
                        if (!$bId || !is_array($stylesList)) continue;

                        foreach ($stylesList as $sItem) {
                            $sId = !empty($sItem['style_id']) ? $sItem['style_id'] : null;
                            $capPcs = isset($sItem['capacity_pcs']) ? (int)$sItem['capacity_pcs'] : 0;
                            if ($capPcs >= 0) {
                                WarehouseBrandCapacity::create([
                                    'warehouse_id' => $warehouse->id,
                                    'brand_id' => $bId,
                                    'style_id' => $sId,
                                    'capacity_pcs' => $capPcs,
                                    'status' => 'Active',
                                    'created_by' => auth()->id(),
                                    'updated_by' => auth()->id()
                                ]);
                            }
                        }
                    }
                } else {
                    $brandIds = $request->brand_ids ?? [];
                    $styleIds = $request->style_ids ?? [];
                    $capacities = $request->capacities ?? [];

                    foreach ($brandIds as $idx => $bId) {
                        $capPcs = isset($capacities[$idx]) ? (int)$capacities[$idx] : 0;
                        $sId = !empty($styleIds[$idx]) ? $styleIds[$idx] : null;
                        if ($bId && $capPcs >= 0) {
                            WarehouseBrandCapacity::create([
                                'warehouse_id' => $warehouse->id,
                                'brand_id' => $bId,
                                'style_id' => $sId,
                                'capacity_pcs' => $capPcs,
                                'status' => 'Active',
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id()
                            ]);
                        }
                    }
                }

                $newData = $warehouse->fresh(['brandCapacities'])->toArray();
                if (function_exists('addLog')) {
                    addLog($id ? 'update' : 'create', 'Warehouse', 'warehouses', $warehouse->id, $oldData, $newData);
                }

                DB::commit();
                return redirect('warehouses')->with('success', $msg);

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('danger', 'Error saving Warehouse: ' . $e->getMessage());
            }
        }

        $existingBrandBlocks = [];
        if ($warehouse) {
            $grouped = $warehouse->brandCapacities->groupBy('brand_id');
            foreach ($grouped as $bId => $caps) {
                $stylesList = [];
                foreach ($caps as $cap) {
                    $stylesList[] = [
                        'style_id' => $cap->style_id,
                        'capacity_pcs' => (int)$cap->capacity_pcs
                    ];
                }
                $existingBrandBlocks[] = [
                    'brand_id' => $bId,
                    'styles' => $stylesList
                ];
            }
        }

        return view('warehouse.add', compact('warehouse', 'brands', 'styles', 'existingBrandBlocks'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete warehouses')) {
            return unauthorizedRedirect();
        }
        $warehouse = Warehouse::findOrFail($id);

        if (\App\Models\ProductionReceipt::where('warehouse_id', $id)->exists()) {
            return redirect('warehouses')->with('danger', 'This warehouse is currently referenced in Production Receipts and cannot be deleted.');
        }

        if (\App\Models\StockEntry::where('warehouse_id', $id)->exists()) {
            return redirect('warehouses')->with('danger', 'This warehouse is currently referenced in Stock Entries and cannot be deleted.');
        }

        if (\App\Models\StockEntryItem::where('warehouse_id', $id)->exists()) {
            return redirect('warehouses')->with('danger', 'This warehouse is currently referenced in Stock Entry Items and cannot be deleted.');
        }

        $oldData = $warehouse->toArray();
        WarehouseBrandCapacity::where('warehouse_id', $id)->delete();
        $warehouse->delete();
        if (function_exists('addLog')) {
            addLog('delete', 'Warehouse', 'warehouses', $id, $oldData, null);
        }
        return redirect('warehouses')->with('success', 'Warehouse deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $oldData = $warehouse->toArray();
        $warehouse->status = $request->status;
        $warehouse->updated_by = auth()->id();
        $warehouse->save();
        $newData = $warehouse->toArray();
        if (function_exists('addLog')) {
            addLog('update_status', 'Warehouse Status', 'warehouses', $warehouse->id, $oldData, $newData);
        }
        return response()->json([
            'success' => true,
            'status'  => $warehouse->status
        ]);
    }
}
