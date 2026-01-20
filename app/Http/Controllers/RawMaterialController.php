<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\StoreCategory;
use App\Models\Uom;
use App\Models\FabricType;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view raw-materials')) {
            return unauthorizedRedirect();
        }
        $canAdd    = true;
        $canEdit   = true;
        $canDelete = true;

        if ($request->ajax()) {

            $query = RawMaterial::with('storeCategory', 'uom', 'fabricType');

            if ($request->category_id) {
                $query->where('store_category_id', $request->category_id);
            }

            if ($request->fabric_type_id) {
                $query->where('fabric_type_id', $request->fabric_type_id);
            }

            $materials = $query->latest()->get();
            $data = [];
            $count = 1;

            foreach ($materials as $material) {

                $checked = $material->status === 'Active' ? 'checked' : '';

                $status = '
                    <label class="switch switch-success switch-lg">
                        <input type="checkbox"
                            class="switch-input raw-material-status-toggle"
                            data-id="' . $material->id . '" ' . $checked . '>
                        <span class="switch-toggle-slider"></span>
                    </label>
                    <div class="status_msg_' . $material->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit raw-materials')) {
                    $action .= '
                        <a href="' . url('raw_materials/add/' . $material->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete raw-materials')) {
                    $action .= '
                        <button class="btn btn-delete"
                            onclick="delete_data(\'' . url('raw_materials/delete/' . $material->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </button>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'category'    => '<span class="badge bg-light-primary">' .
                        ($material->storeCategory->category_name ?? '-') . '</span>',
                    'name'        => $material->name,
                    'uom'         => $material->uom->uom_code ?? '-',
                    'fabric_type' => $material->fabricType->fabric_type ?? '-',
                    'size_width'  => $material->size_width ?? '-',
                    'min_stock'   => $material->min_stock,
                    'created_by'  => createdByName($material->created_by),
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('raw_materials.view', compact('canAdd'));
    }


    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit raw-materials')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create raw-materials')) {
                return unauthorizedRedirect();
            }
        }
        $rawMaterial = null;
        if ($id) {
            $rawMaterial = RawMaterial::findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'store_category_id' => 'required|exists:store_categories,id',
                'code' => 'required|string|max:50|unique:raw_materials,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'name' => 'required|string|max:150',
                'supplier_design_name' => 'nullable|string|max:150',
                'size_width' => 'nullable|numeric|min:0',
                'uom_id' => 'required|exists:uoms,id',
                'fabric_type_id' => 'nullable|exists:fabric_types,id',
                'reference_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'specification' => 'nullable|string|max:255',
                'min_stock' => 'nullable|numeric|min:0',
                'status' => 'required|in:Active,Inactive',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                'size_width' => 'Width must be a number.',
            ];

            $validated = $request->validate($rules, $messages);

            $data = [
                'store_category_id' => $request->store_category_id,
                'code' => $request->code,
                'name' => $request->name,
                'supplier_design_name' => $request->supplier_design_name,
                'size_width' => $request->size_width,
                'uom_id' => $request->uom_id,
                'fabric_type_id' => $request->fabric_type_id,
                'specification' => $request->specification,
                'min_stock' => $request->min_stock ?? 0,
                'status' => $request->status,
            ];

            if ($request->hasFile('reference_image')) {
                $image = $request->file('reference_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/raw_materials'), $filename);
                $data['reference_image'] = 'uploads/raw_materials/' . $filename;
            }

            if ($id) {
                $rawMaterial = RawMaterial::find($id);
                if ($request->hasFile('reference_image') && $rawMaterial->reference_image) {
                    $oldPath = public_path($rawMaterial->reference_image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['updated_by'] = auth()->id();
                $oldData = RawMaterial::find($id)->toArray();

                RawMaterial::where('id', $id)->update($data);

                $newData = RawMaterial::find($id)->toArray();

                addLog('update', 'Raw Material', 'raw_materials', $id, $oldData, $newData);

                $message = 'Raw Material updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $material = RawMaterial::create($data);

                $newData = $material->toArray();

                addLog('create', 'Raw Material', 'raw_materials', $material->id, null, $newData);

                $message = 'Raw Material added successfully';
            }

            return redirect('raw_materials')->with('success', $message);
        }

        $storeCategories = StoreCategory::where('status', 'Active')->get();
        $uoms = Uom::where('status', 'Active')->get();
        $fabricTypes = FabricType::where('status', 'Active')->get();

        return view('raw_materials.add', compact('rawMaterial', 'storeCategories', 'uoms', 'fabricTypes'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete raw-materials')) {
            return unauthorizedRedirect();
        }
        $material = RawMaterial::findOrFail($id);

        $oldData = $material->toArray();

        $material->delete();

        addLog('delete', 'Raw Material', 'raw_materials', $id, $oldData, null);

        return response()->json([
            'status'  => true,
            'message' => 'Raw Material deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $material = RawMaterial::findOrFail($id);

        $oldData = $material->toArray();

        $material->status = $request->status;
        $material->save();

        $newData = $material->toArray();

        addLog('update_status', 'Raw Material Status', 'raw_materials', $id, $oldData, $newData);

        return response()->json([
            'status'  => true,
            'message' => 'Status updated successfully'
        ]);
    }
}