<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\StoreCategory;
use App\Models\Uom;
use App\Models\TaskAdjustmentItem;
use App\Models\StockEntryItem;
use App\Models\StockEntryAdjustment;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseInvoiceItem;
use App\Models\ProductionStageConsumable;
use App\Models\DebitNoteItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RawMaterialImport;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view raw-materials')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = RawMaterial::with('storeCategory', 'uom');

            if ($request->category_id) {
                $query->where('store_category_id', $request->category_id);
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
                    'name'        => $material->name . ' (' . $material->code . ')',
                    'uom'         => $material->uom->uom_code ?? '-',
                    'created_by'  => createdByName($material->created_by),
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('raw_materials.view');
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
                'code' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'not_in:0',
                    'regex:/^[a-zA-Z0-9\-_]+$/',
                    'regex:/^(?!0+$).*$/',
                    'unique:raw_materials,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:150',
                    'regex:/^(?!0+$).*$/'
                ],
                'uom_id' => 'required|exists:uoms,id',
                'material_type' => [
                    'nullable',
                    'string',
                    'max:100',
                    'regex:/^(?!0+$).*$/'
                ],
                'reference_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                'specification' => [
                    'nullable',
                    'string',
                    'min:5',
                    'max:255',
                    'regex:/^(?!0+$).*$/'
                ],
                'min_stock' => 'nullable|numeric|min:0',
                'status' => 'required|in:Active,Inactive',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                'code.regex' => 'Code can only contain letters, numbers, dashes, and underscores.',
                'code.not_in' => 'Code cannot be 0.',
                '*.regex' => 'This field is an invalid format.',
                'size_width' => 'Width must be a number.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
                '*.numeric' => 'This field must be a number.',
                'min_stock.min' => 'Please enter a valid numeric value greater than or equal to 0.',
            ];

            $validated = $request->validate($rules, $messages);

            $data = [
                'store_category_id' => $request->store_category_id,
                'code' => $request->code,
                'name' => $request->name,
                'uom_id' => $request->uom_id,
                'material_type' => $request->material_type,
                'specification' => $request->specification,
                'min_stock' => $request->min_stock,
                'status' => $request->status,
            ];

            if ($request->hasFile('reference_image')) {
                $image = $request->file('reference_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $uploadPath = public_path('uploads/raw_materials');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $filename);
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
            
            $savedMaterial = $id ? RawMaterial::find($id) : $material;
            if ($savedMaterial) {
                $savedMaterial->artNos()->delete();
                if ($request->has('art_nos') && is_array($request->art_nos)) {
                    foreach ($request->art_nos as $artNo) {
                        $savedMaterial->artNos()->create(['art_no' => $artNo]);
                    }
                }
            }

            return redirect('raw_materials')->with('success', $message);
        }

        $storeCategories = StoreCategory::where('status', 'Active')->get();
        $uoms = Uom::where('status', 'Active')->get();
        
        $artNos = \App\Models\GrnEntryItem::whereNotNull('art_no')->where('art_no', '!=', '')->distinct()->orderBy('art_no', 'asc')->pluck('art_no');
            
        $selectedArtNos = [];
        if ($rawMaterial) {
            $selectedArtNos = $rawMaterial->artNos()->pluck('art_no')->toArray();
        }
        
        return view('raw_materials.add', compact('rawMaterial', 'storeCategories', 'uoms', 'artNos', 'selectedArtNos'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete raw-materials')) {
            return unauthorizedRedirect();
        }

        $material = RawMaterial::findOrFail($id);
        if (PurchaseOrderItem::where('raw_material_id', $id)->exists()) {
            return redirect('raw_materials')->with('danger', 'This raw material is currently referenced in Purchase Order Items and cannot be deleted.');
        }
        if (TaskAdjustmentItem::where('raw_material_id', $id)->exists()) {
            return redirect('raw_materials')->with('danger', 'This raw material is currently referenced in Task Adjustment Items and cannot be deleted.');
        }
        if (StockEntryItem::where('raw_material_id', $id)->exists()) {
            return redirect('raw_materials')->with('danger', 'This raw material is currently referenced in Stock Entry Items and cannot be deleted.');
        }
        if (StockEntryAdjustment::where('raw_material_id', $id)->exists()) {
            return redirect('raw_materials')->with('danger', 'This raw material is currently referenced in Stock Entry Adjustments and cannot be deleted.');
        }
        if (PurchaseInvoiceItem::where('raw_material_id', $id)->exists()) {
            return redirect('raw_materials')->with('danger', 'This raw material is currently referenced in Purchase Invoice Items and cannot be deleted.');
        }
        if (ProductionStageConsumable::where('raw_material_id', $id)->exists()) {
            return redirect('raw_materials')->with('danger', 'This raw material is currently referenced in Production Stage Consumables and cannot be deleted.');
        }
        if (DebitNoteItem::where('raw_material_id', $id)->exists()) {
            return redirect('raw_materials')->with('danger', 'This raw material is currently referenced in Debit Note Items and cannot be deleted.');
        }
        $oldData = $material->toArray();
        $material->delete();
        addLog('delete', 'Raw Material', 'raw_materials', $id, $oldData, null);
        return redirect('raw_materials')->with('success', 'Raw Material deleted successfully');
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

    public function import(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create raw-materials')) {
            return unauthorizedRedirect();
        }

        $request->validate([
            'import_file' => 'required|mimes:csv,txt,xlsx,xls'
        ]);

        try {
            Excel::import(new RawMaterialImport, $request->file('import_file'));
            return redirect('raw_materials')->with('success', 'Raw Materials imported successfully.');
        } catch (\Exception $e) {
            return redirect('raw_materials')->with('error', $e->getMessage());
        }
    }

    public function downloadSample()
    {
        $headers = [
            'Name', 'Code', 'Store Category', 'UOM', 'Material Type', 'Specification', 'Min Stock', 'Status'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->streamDownload($callback, 'Raw_Material_Sample_Format.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
