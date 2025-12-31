<?php

namespace App\Http\Controllers;

use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\GrnEntry;
use App\Models\GrnEntryItem;
use App\Models\StoreCategory;
use App\Models\RawMaterial;
use App\Models\StoreLocation;
use App\Models\Uom;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockEntryController extends Controller
{
    /**
     * Display listing with AJAX DataTable support
     */
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view stock entries')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = StockEntry::with(['grnEntry', 'stockEntryItems.rawMaterial', 'stockEntryItems.storeCategory']);

            // Filters
            if ($request->material_category) {
                $query->whereHas('stockEntryItems', function($q) use ($request) {
                    $q->where('store_category_id', $request->material_category);
                });
            }

            if ($request->material) {
                $query->whereHas('stockEntryItems', function($q) use ($request) {
                    $q->where('raw_material_id', $request->material);
                });
            }

            $stockEntries = $query->orderBy('id', 'desc')->get();
            $data = [];
            $count = 1;

            foreach ($stockEntries as $entry) {


                $totalQtyIn = $entry->stockEntryItems->sum('qty_in');
                $totalQtyOut = $entry->stockEntryItems->sum('qty_out');

                $firstItem = $entry->stockEntryItems->first();
                $categoryDisplay = $firstItem && $firstItem->storeCategory 
                    ? $firstItem->storeCategory->category_name . ' <span class="mini-title">(' . $firstItem->storeCategory->code . ')</span>'
                    : '-';
                $materialDisplay = $firstItem && $firstItem->rawMaterial 
                    ? $firstItem->rawMaterial->name . ' <span class="mini-title">(' . $firstItem->rawMaterial->code . ')</span>'
                    : ($firstItem && $firstItem->finished_item_code ? $firstItem->finished_item_code : '-');

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view_details stock entries')) {
                    $action .= '<a href="' . url('stock_entries/view/' . $entry->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('edit stock entries')) {
                    $action .= '<a href="' . url('stock_entries/add/' . $entry->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('delete stock entries')) {
                    $action .= '<button class="btn btn-delete" onclick="delete_data(`' . url('stock_entries/delete/' . $entry->id) . '`)"><i class="icon-base ri ri-delete-bin-line"></i></button>';
                }
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'stock_entry_no' => $entry->stock_entry_no,
                    'stock_date' => $entry->stock_date->format('d-m-Y'),
                    'material_category' => $categoryDisplay,
                    'material' => $materialDisplay,
                    'grn_no' => $entry->grnEntry->grn_number ?? '-',
                    'total_qty' => $totalQtyIn > 0 ? '+' . number_format($totalQtyIn, 2) : '-' . number_format($totalQtyOut, 2),
                    'action' => $action,
                ];
            }
            return response()->json(['data' => $data]);
        }

        $storeCategories = StoreCategory::where('status', 'Active')->orderBy('category_name')->get();
        $rawMaterials = RawMaterial::where('status', 'Active')->orderBy('name')->get();
        
        return view('stock_entry.view', compact('storeCategories', 'rawMaterials'));
    }

    /**
     * Add/Edit stock entry (single item form - matching original UI)
     */
    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit stock entries')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create stock entries')) {
                return unauthorizedRedirect();
            }
        }
        $stockEntry = $id ? StockEntry::with(['stockEntryItems.rawMaterial', 'stockEntryItems.storeCategory', 'stockEntryItems.storeLocation', 'grnEntry'])->findOrFail($id) : null;
        
        $grnEntries = GrnEntry::where('status', 'Received')
            ->whereHas('grnEntryItems', function($query) use ($id) {
                $query->whereDoesntHave('stockEntryItems', function($q) use ($id) {
                    if ($id) {
                        $q->where('stock_entry_id', '!=', $id);
                    }
                });
            })
            ->orderBy('grn_number')
            ->get();
        $storeCategories = StoreCategory::where('status', 'Active')->orderBy('category_name')->get();
        $rawMaterials = RawMaterial::where('status', 'Active')->orderBy('name')->get();
        $storeLocations = StoreLocation::where('status', 'Active')->orderBy('store_location')->get();
        $uoms = Uom::where('status', 'Active')->orderBy('uom_name')->get();

        if ($request->isMethod('post')) {
            $rules = [
                'stock_date' => 'required|date_format:d-m-Y',
                'grn_entry_id' => 'required|exists:grn_entries,id',
            ];
            $messages = [
                '*.required' => 'This field is required.',
            ];

            if ($request->has('items') && is_array($request->items) && count($request->items) > 0) {
                foreach ($request->items as $index => $item) {
                    $rules["items.$index.store_location_id"] = 'required';
                }
            } else {
                $rules['grn_entry_item_id'] = 'required|exists:grn_entry_items,id';
                $rules['qty_in'] = 'required|numeric|min:1';
                $rules['qty_in'] = 'required|numeric|min:1';
                $rules['store_location_id'] = 'required|exists:store_locations,id';
                $rules['price'] = 'nullable|numeric|min:0';
            }

            $request->validate($rules, $messages);

            DB::beginTransaction();
            try {
                $headerData = [
                    'stock_date' => Carbon::createFromFormat('d-m-Y', $request->stock_date)->format('Y-m-d'),
                    'grn_entry_id' => $request->grn_entry_id,
                    'remarks' => $request->remarks,
                    'status' => $request->status ?? 'Draft',
                ];

                // Handle file upload
                if ($request->hasFile('reference_document')) {
                    $file = $request->file('reference_document');
                    $filename = 'stock_ref_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/stock_entries'), $filename);
                    $headerData['reference_document'] = 'uploads/stock_entries/' . $filename;
                }

                if ($id) {
                    $headerData['updated_by'] = auth()->id();
                    $oldValues = $stockEntry->toArray();
                    $stockEntry->update($headerData);
                    $stockEntry->stockEntryItems()->delete();
                    
                    addLog('update', 'Stock Entry', 'stock_entries', $stockEntry->id, $oldValues, $headerData);
                } else {
                    $lastEntry = StockEntry::latest('id')->first();
                    $nextNumber = $lastEntry ? (int)substr($lastEntry->stock_entry_no, 2) + 1 : 1;
                    $headerData['stock_entry_no'] = 'SE' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                    $headerData['created_by'] = auth()->id();
                    $stockEntry = StockEntry::create($headerData);
                    
                    addLog('create', 'Stock Entry', 'stock_entries', $stockEntry->id, null, $headerData);
                }

                if ($id) {
                    // Split logic not applicable for existing edit
                    $itemData = [
                        'stock_entry_id' => $stockEntry->id,
                        'stock_type' => 'raw_material',
                        'grn_entry_item_id' => $request->grn_entry_item_id ?? null,
                        'raw_material_id' => $request->raw_material_id ?? null,
                        'store_category_id' => $request->store_category_id ?? null,
                        'store_location_id' => $request->store_location_id,
                        'uom_id' => $request->uom_id ?? null,
                        'qty_in' => $request->qty_in ?? 1,
                        'qty_out' => 0,
                        'price' => $request->price ?? 0,
                    ];
                    StockEntryItem::create($itemData);
                } else {
                    $totalQty = (int)$request->qty_in;
                    
                    for ($i = 0; $i < $totalQty; $i++) {
                        $lastEntry = StockEntry::latest('id')->first();
                        $nextNumber = $lastEntry ? (int)substr($lastEntry->stock_entry_no, 2) + 1 : 1;
                        $iterHeaderData = $headerData;
                        $iterHeaderData['stock_entry_no'] = 'SE' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                        $iterHeaderData['created_by'] = auth()->id();
                        
                        $newStockEntry = StockEntry::create($iterHeaderData);
                        addLog('create', 'Stock Entry', 'stock_entries', $newStockEntry->id, null, $iterHeaderData);
                        
                        StockEntryItem::create([
                            'stock_entry_id' => $newStockEntry->id,
                            'stock_type' => 'raw_material',
                            'grn_entry_item_id' => $request->grn_entry_item_id ?? null,
                            'raw_material_id' => $request->raw_material_id ?? null,
                            'store_category_id' => $request->store_category_id ?? null,
                            'store_location_id' => $request->store_location_id,
                            'uom_id' => $request->uom_id ?? null,
                            'qty_in' => 1,
                            'qty_out' => 0,
                            'price' => $request->price ?? 0,
                        ]);
                    }
                }

                DB::commit();
                return redirect('stock_entries')->with('success', 'Stock Entry saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => $e->getMessage()]);
            }
        }

        $nextStockNo = $id ? $stockEntry->stock_entry_no : 'SE' . str_pad((StockEntry::latest('id')->first()->id ?? 0) + 1, 5, '0', STR_PAD_LEFT);
        
        $savedItems = [];
        if ($stockEntry && $stockEntry->grn_entry_id) {
            $savedItems = $stockEntry->stockEntryItems->map(function($item) {
                return [
                    'store_category_id' => $item->store_category_id,
                    'store_category_name' => $item->storeCategory->category_name . ' (' . $item->storeCategory->code . ')',
                    'grn_entry_item_id' => $item->grn_entry_item_id,
                    'raw_material_id' => $item->raw_material_id,
                    'raw_material_name' => $item->rawMaterial->name . ' (' . $item->rawMaterial->code . ')',
                    'uom_id' => $item->uom_id,
                    'uom_name' => $item->uom->uom_code ?? '',
                    'qty_in' => $item->qty_in,
                    'qty_out' => $item->qty_out,
                    'store_location_id' => $item->store_location_id,
                    'store_location_name' => $item->storeLocation->store_location ?? '',
                ];
            });
        }

        return view('stock_entry.add', compact('stockEntry', 'grnEntries', 'storeCategories', 'rawMaterials', 'storeLocations', 'uoms', 'nextStockNo', 'savedItems'));
    }

    /**
     * View stock entry details
     */
    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details stock entries')) {
            return unauthorizedRedirect();
        }
        $stockEntry = StockEntry::with([
            'grnEntry',
            'fromStoreLocation',
            'toStoreLocation',
            'stockEntryItems.rawMaterial.storeCategory',
            'stockEntryItems.storeCategory',
            'stockEntryItems.storeLocation',
            'stockEntryItems.uom',
            'stockEntryItems.grnEntryItem',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);

        return view('stock_entry.view_details', compact('stockEntry'));
    }

    /**
     * Delete (soft delete) stock entry
     */
    public function delete($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete stock entries')) {
            return unauthorizedRedirect();
        }
        $stockEntry = StockEntry::findOrFail($id);
        
       addLog('delete', 'Stock Entry', 'stock_entries', $stockEntry->id, $stockEntry->toArray(), null);
        
        $stockEntry->delete();

        return response()->json(['success' => true, 'message' => 'Stock Entry deleted successfully']);
    }

    /**
     * AJAX: Get GRN entry items for populating stock entry
     */
    public function getGrnEntryItems($grn_entry_id)
    {
        $stockEntryId = request('stock_entry_id');

        $grnEntry = GrnEntry::with([
            'grnEntryItems.purchaseInvoiceItem.rawMaterial.storeCategory',
            'grnEntryItems.storeLocation',
            'grnEntryItems.purchaseInvoiceItem.uom',
            'grnEntryItems.stockEntryItems'
        ])->findOrFail($grn_entry_id);

        $items = $grnEntry->grnEntryItems
            ->filter(function($item) use ($stockEntryId) {
                // We show the item if it has balance qty OR if it is already part of THIS stock entry (edit mode)
                $stockQty = $item->stockEntryItems->sum('qty_in');
                return $stockQty < $item->qty_accepted || ($stockEntryId && $item->stockEntryItems->contains('stock_entry_id', $stockEntryId));
            })
            ->values() 
            ->map(function($item) use ($stockEntryId) {
                // Fallback logic: Try to get data from PurchaseInvoiceItem, but handle if null
                $piItem = $item->purchaseInvoiceItem ?? null;
                $rawMaterial = $piItem ? $piItem->rawMaterial : null;

                // Prepare data with fallbacks
                $rawMaterialId = $rawMaterial ? $rawMaterial->id : ($item->raw_material_id ?? null); // In case GrnEntryItem has raw_material_id directly
                $rawMaterialName = $rawMaterial 
                    ? ($rawMaterial->name . ' (' . $rawMaterial->code . ')') 
                    : ($item->raw_material_name ?? 'Unknown Material'); // Fallback name if available

                $storeCategoryId = $rawMaterial ? $rawMaterial->store_category_id : null;
                $storeCategoryName = ($rawMaterial && $rawMaterial->storeCategory)
                    ? ($rawMaterial->storeCategory->category_name . ' (' . $rawMaterial->storeCategory->code . ')')
                    : '-';

                $uomId = $piItem ? $piItem->uom_id : ($item->uom_id ?? null);
                $uomName = ($piItem && $piItem->uom) ? $piItem->uom->uom_code : '-';
                
                // Allow rate to come from PI item or directly from GRN item if stored there
                $rate = $piItem ? $piItem->rate : ($item->rate ?? 0);

                return [
                    'id' => $item->id,
                    'grn_entry_item_id' => $item->id,
                    'stock_type' => 'raw_material',
                    'raw_material_id' => $rawMaterialId,
                    'raw_material_name' => $rawMaterialName,
                    'store_category_id' => $storeCategoryId,
                    'store_category_name' => $storeCategoryName,
                    'store_location_id' => $item->store_location_id, // Directly from GRN Item
                    'store_location_name' => $item->storeLocation->store_location ?? '-',
                    'uom_id' => $uomId,
                    'uom_name' => $uomName,
                    // Available Qty = Total Accepted - (Used by OTHER entries)
                    'qty_accepted' => $item->qty_accepted - $item->stockEntryItems->where('stock_entry_id', '!=', $stockEntryId)->sum('qty_in'),
                    'rate' => $rate,
                ];
            });

        return response()->json([
            'success' => true,
            'grn_number' => $grnEntry->grn_number,
            'items' => $items
        ]);
    }

}
