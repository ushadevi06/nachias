<?php

namespace App\Http\Controllers;

use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\StockEntryAdjustment;
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
            $query = StockEntry::with(['grnEntry', 'stockEntryItems.rawMaterial', 'stockEntryItems.storeCategory', 'stockEntryItems.grnEntryItem', 'stockEntryItems.item', 'stockEntryItems.fabricType']);
            if ($request->material_category) {
                $query->whereHas('stockEntryItems', function ($q) use ($request) {
                    $q->where('store_category_id', $request->material_category);
                });
            }

            if ($request->entry_type) {
                $query->where('entry_type', $request->entry_type);
            }

            if ($request->material) {
                $query->whereHas('stockEntryItems', function ($q) use ($request) {
                    $q->where('raw_material_id', $request->material);
                });
            }

            if ($request->art_no) {
                $query->whereHas('stockEntryItems', function ($q) use ($request) {
                    $q->where('art_no', $request->art_no);
                });
            }

            if ($request->grn_no) {
                $query->whereHas('grnEntry', function ($q) use ($request) {
                    $q->where('grn_number', 'LIKE', '%' . $request->grn_no . '%');
                });
            }
            $stockEntries = $query->with('productionReceipt.jobCard.fabricType', 'productionReceipt.jobCard.purchaseOrder')->orderBy('id', 'desc')->get();
            $data = [];
            $count = 1;

            foreach ($stockEntries as $entry) {
                $itemsToShow = $entry->stockEntryItems;
                $isFinishedGoods = ($entry->entry_type === 'Finished Goods');

                if ($isFinishedGoods) {
                    foreach ($itemsToShow as $item) {
                        $materialDisplay = $item->finished_item_code ?: '-';
                        if ($item->size) {
                            $materialDisplay .= ' <span class="badge bg-label-secondary mx-1">' . $item->size . '</span>';
                        }

                        $action = '<div class="button-box">';
                        $action .= '<a href="' . url('stock_entries/view/' . $entry->id . '/entry_type=finished_goods?item_id=' . $item->id) . '" class="btn btn-view" title="View Details"><i class="icon-base ri ri-eye-line"></i></a>';
                        $action .= '</div>';

                        $jobCardNo = '-';
                        if ($entry->productionReceipt && $entry->productionReceipt->jobCard) {
                            $jobCardNo = $entry->productionReceipt->jobCard->job_card_no;
                        }

                        $fabricType = $item->fabricType->fabric_type ?? null;
                        if (!$fabricType && $entry->productionReceipt && $entry->productionReceipt->jobCard) {
                            $fabricType = $entry->productionReceipt->jobCard->fabricType->fabric_type ?? '-';
                        }
                        $fabricType = $fabricType ?: '-';

                        $data[] = [
                            'DT_RowIndex' => $count++,
                            'stock_entry_no' => $entry->stock_entry_no,
                            'stock_date' => $entry->stock_date->format('d-m-Y'),
                            'material_category' => '<span class="badge bg-label-info">Finished Goods</span>',
                            'art_no' => $item->art_no ?? '-',
                            'material' => $materialDisplay,
                            'grn_no' => $jobCardNo,
                            'item_name' => ($item->item->name ?? '-') . ' <span class="mini-title">(' . ($item->finished_item_code ?: '-') . ')</span>',
                            'fabric_type' => $fabricType,
                            'sleeve_type' => $item->sleeve_type ?? '-',
                            'size' => $item->size ?? '-',
                            'sku' => $item->sku ?? '-',
                            'total_qty' => '+' . number_format($item->qty_in, 2),
                            'action' => $action,
                        ];
                    }
                }
                else {
                    $totalQtyIn = $entry->stockEntryItems->sum('qty_in');
                    $totalQtyOut = $entry->stockEntryItems->sum('qty_out');

                    $firstItem = $entry->stockEntryItems->first();
                    if ($request->art_no) {
                        $matchedItem = $entry->stockEntryItems->first(function ($item) use ($request) {
                            return $item->art_no === $request->art_no;
                        });
                        if ($matchedItem) {
                            $firstItem = $matchedItem;
                        }
                    }

                    $categoryDisplay = $firstItem && $firstItem->storeCategory
                        ? $firstItem->storeCategory->category_name . ' <span class="mini-title">(' . $firstItem->storeCategory->code . ')</span>'
                        : '-';

                    $artNo = $firstItem ? ($firstItem->art_no ?: '-') : '-';

                    $materialDisplay = $firstItem && $firstItem->rawMaterial
                        ? $firstItem->rawMaterial->name . ' <span class="mini-title">(' . $firstItem->rawMaterial->code . ')</span>'
                        : ($firstItem && $firstItem->finished_item_code ? $firstItem->finished_item_code : '-');

                    $action = '<div class="button-box">';
                    $action .= '<button type="button" class="btn btn-adjust" data-entry-id="' . $entry->id . '" data-item-id="' . ($firstItem->id ?? 0) . '" data-art-no="' . $artNo . '" data-grn-no="' . ($entry->grnEntry->grn_number ?? '-') . '" data-material="' . ($firstItem && $firstItem->rawMaterial ? $firstItem->rawMaterial->name : '-') . '" data-current-qty="' . $totalQtyIn . '" title="Quick Adjust Stock"><i class="ri ri-pulse-line"></i></button>';
                    $action .= '<a href="' . url('stock_entries/adjustment-logs/' . $entry->id) . '" class="btn btn-item" title="View Adjustment Logs"><i class="icon-base ri ri-history-line"></i></a>';
                    if (auth()->id() == 1 || auth()->user()->can('view_details stock entries')) {
                        $action .= '<a href="' . url('stock_entries/view/' . $entry->id . '/entry_type=raw_materials') . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                    }
                    $action .= '</div>';

                    $data[] = [
                        'DT_RowIndex' => $count++,
                        'stock_entry_no' => $entry->stock_entry_no,
                        'stock_date' => $entry->stock_date->format('d-m-Y'),
                        'material_category' => $categoryDisplay,
                        'art_no' => $artNo,
                        'material' => $materialDisplay,
                        'grn_no' => $entry->grnEntry->grn_number ?? '-',
                        'item_name' => $firstItem && $firstItem->rawMaterial ? $firstItem->rawMaterial->name : '-',
                        'fabric_type' => '-',
                        'sleeve_type' => '-',
                        'size' => '-',
                        'sku' => '-',
                        'total_qty' => $totalQtyIn > 0 ? '+' . number_format($totalQtyIn, 2) : '-' . number_format($totalQtyOut, 2),
                        'action' => $action,
                    ];
                }
            }
            return response()->json(['data' => $data]);
        }

        return view('stock_entry.view');
    }
    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit stock entries')) {
                return unauthorizedRedirect();
            }
        }
        else {
            if (auth()->id() != 1 && !auth()->user()->can('create stock entries')) {
                return unauthorizedRedirect();
            }
        }
        $stockEntry = $id ?StockEntry::with(['stockEntryItems.rawMaterial', 'stockEntryItems.storeCategory', 'stockEntryItems.storeLocation', 'grnEntry'])->findOrFail($id) : null;
        $grnEntries = GrnEntry::where('status', 'Received')
            ->where(function ($query) use ($id, $stockEntry) {
            $query->whereHas('grnEntryItems', function ($q) use ($id) {
                    $q->whereRaw('qty_accepted > (
                        select COALESCE(sum(qty_in), 0) 
                        from stock_entry_items 
                        where grn_entry_item_id = grn_entry_items.id 
                        ' . ($id ? "AND stock_entry_id != $id" : "") . '
                    )');
                }
                );

                if ($id && $stockEntry) {
                    $query->orWhere('id', $stockEntry->grn_entry_id);
                }
            })->orderBy('grn_number', 'desc')->get();

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
                'reference_document.mimes' => 'Upload a valid file (e.g., .pdf, .doc, .docx, .jpg, .png, .jpeg, .webp).',
                'reference_document.max' => 'Uploaded file cannot exceed 2MB.',
                'items.*.store_location_id.required' => 'Location is required.',
            ];

            $rules['reference_document'] = 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:2048';

            $hasSelectedItem = false;
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $index => $item) {
                    if (isset($item['selected'])) {
                        $hasSelectedItem = true;
                        $rules["items.$index.qty_in"] = 'required|numeric|min:0.01';
                        $rules["items.$index.store_location_id"] = 'required|exists:store_locations,id';
                    }
                }
            }

            $request->validate($rules, $messages);

            if (!$hasSelectedItem) {
                return back()->withInput()->withErrors(['items' => 'Please select at least one item to stock.']);
            }

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $index => $item) {
                    if (isset($item['selected'])) {
                        $grnItem = GrnEntryItem::findOrFail($item['grn_entry_item_id']);
                        $totalStocked = StockEntryItem::where('grn_entry_item_id', $item['grn_entry_item_id'])
                            ->when($id, function ($q) use ($id) {
                            $q->where('stock_entry_id', '!=', $id);
                        })->sum('qty_in');
                        $availableBalance = $grnItem->qty_accepted - $totalStocked;

                        if ($item['qty_in'] > $availableBalance) {
                            return back()->withInput()->withErrors(['items' => "The quantity entered ({$item['qty_in']}) exceeds the available balance ($availableBalance) for a GRN item."]);
                        }
                    }
                }
            }

            DB::beginTransaction();
            try {
                $headerData = [
                    'stock_date' => Carbon::createFromFormat('d-m-Y', $request->stock_date)->format('Y-m-d'),
                    'grn_entry_id' => $request->grn_entry_id,
                    'entry_type' => 'Raw Material',
                    'from_store_location_id' => null,
                    'to_store_location_id' => null,
                    'remarks' => $request->remarks,
                    'status' => $request->status ?? 'Draft',
                    'price' => $request->price ?? 0,
                ];

                if ($request->hasFile('reference_document')) {
                    $file = $request->file('reference_document');
                    $filename = 'stock_ref_' . time() . '.' . $file->getClientOriginalExtension();
                    $uploadPath = public_path('uploads/stock_entries');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $file->move($uploadPath, $filename);
                    $headerData['reference_document'] = $filename;
                }

                if ($id) {
                    $headerData['updated_by'] = auth()->id();
                    $oldValues = $stockEntry->toArray();
                    $stockEntry->update($headerData);
                    $stockEntry->stockEntryItems()->forceDelete();
                    addLog('update', 'Stock Entry', 'stock_entries', $stockEntry->id, $oldValues, $headerData);
                }
                else {
                    $lastEntry = StockEntry::latest('id')->first();
                    $nextNumber = $lastEntry ? (int)substr($lastEntry->stock_entry_no, 2) + 1 : 1;
                    $headerData['stock_entry_no'] = 'SE' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                    $headerData['created_by'] = auth()->id();
                    $stockEntry = StockEntry::create($headerData);
                    addLog('create', 'Stock Entry', 'stock_entries', $stockEntry->id, null, $headerData);
                }

                if ($request->has('items') && is_array($request->items)) {
                    foreach ($request->items as $item) {
                        if (isset($item['selected']) && $item['selected'] == 1) {
                            $grnEntryItem = GrnEntryItem::find($item['grn_entry_item_id']);
                            StockEntryItem::create([
                                'stock_entry_id' => $stockEntry->id,
                                'stock_type' => 'raw_material',
                                'grn_entry_item_id' => $item['grn_entry_item_id'] ?? null,
                                'art_no' => $grnEntryItem->art_no ?? null,
                                'raw_material_id' => $item['raw_material_id'] ?? null,
                                'store_category_id' => $item['store_category_id'] ?? null,
                                'store_location_id' => $item['store_location_id'],
                                'uom_id' => $item['uom_id'] ?? null,
                                'qty_in' => $item['qty_in'] ?? 0,
                                'qty_out' => 0,
                                'price' => $item['price'] ?? 0,
                                'fabric_type_id' => $item['fabric_type_id'] ?? null,
                            ]);
                        }
                    }
                }
                DB::commit();
                return redirect('stock_entries')->with('success', 'Stock Entry saved successfully');
            }
            catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => $e->getMessage()]);
            }
        }

        $nextStockNo = $id ? $stockEntry->stock_entry_no : 'SE' . str_pad((StockEntry::latest('id')->first()->id ?? 0) + 1, 5, '0', STR_PAD_LEFT);

        $savedItems = [];
        if ($stockEntry && $stockEntry->grn_entry_id) {
            $savedItems = $stockEntry->stockEntryItems->map(function ($item) {
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
            })->values()->all();
        }

        return view('stock_entry.add', compact('stockEntry', 'grnEntries', 'storeCategories', 'rawMaterials', 'storeLocations', 'uoms', 'nextStockNo', 'savedItems'));
    }
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
            'stockEntryItems.fabricType',
            'stockEntryItems.item',
            'stockEntryItems.color',
            'productionReceipt.jobCard.fabricType',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);

        return view('stock_entry.view_details', compact('stockEntry'));
    }
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
            ->filter(function ($item) use ($stockEntryId) {
            $stockQty = $item->stockEntryItems->sum('qty_in');
            return $stockQty < $item->qty_accepted || ($stockEntryId && $item->stockEntryItems->contains('stock_entry_id', $stockEntryId));
        })
            ->values()
            ->map(function ($item) use ($stockEntryId) {
            $piItem = $item->purchaseInvoiceItem ?? null;
            $rawMaterial = $piItem ? $piItem->rawMaterial : null;

            $rawMaterialId = $rawMaterial ? $rawMaterial->id : ($item->raw_material_id ?? null);
            $rawMaterialName = $rawMaterial
                ? ($rawMaterial->name . ' (' . $rawMaterial->code . ')')
                : ($item->raw_material_name ?? 'Unknown Material');

            $storeCategoryId = $rawMaterial ? $rawMaterial->store_category_id : null;
            $storeCategoryName = ($rawMaterial && $rawMaterial->storeCategory)
                ? ($rawMaterial->storeCategory->category_name . ' (' . $rawMaterial->storeCategory->code . ')')
                : '-';

            $uomId = $piItem ? $piItem->uom_id : ($item->uom_id ?? null);
            $uomName = ($piItem && $piItem->uom) ? $piItem->uom->uom_code : '-';

            $rate = $piItem ? $piItem->rate : ($item->rate ?? 0);

            return [
            'id' => $item->id,
            'grn_entry_item_id' => $item->id,
            'stock_type' => 'raw_material',
            'raw_material_id' => $rawMaterialId,
            'raw_material_name' => $rawMaterialName,
            'store_category_id' => $storeCategoryId,
            'store_category_name' => $storeCategoryName,
            'store_location_id' => $item->store_location_id,
            'store_location_name' => $item->storeLocation->store_location ?? '-',
            'uom_id' => $uomId,
            'uom_name' => $uomName,
            'qty_accepted' => $item->qty_accepted - $item->stockEntryItems->where('stock_entry_id', '!=', $stockEntryId)->sum('qty_in'),
            'rate' => $rate,
            'fabric_type_id' => $item->fabric_type_id,
            ];
        });

        return response()->json([
            'success' => true,
            'grn_number' => $grnEntry->grn_number,
            'items' => $items
        ]);
    }

    public function getEntryItems($id)
    {
        $stockEntry = StockEntry::with(['stockEntryItems.rawMaterial.storeCategory', 'stockEntryItems.storeCategory'])->findOrFail($id);

        $items = $stockEntry->stockEntryItems->map(function ($item) {
            $category = $item->storeCategory ? ($item->storeCategory->category_name . ' (' . $item->storeCategory->code . ')') : '-';
            $material = $item->rawMaterial ? ($item->rawMaterial->name . ' (' . $item->rawMaterial->code . ')') : ($item->finished_item_code ?: '-');

            return [
            'id' => $item->id,
            'category' => $category,
            'material' => $material,
            'art_no' => $item->art_no ?: '-',
            'current_qty' => $item->qty_in,
            ];
        });

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function quickAdjustment(Request $request)
    {
        $request->validate([
            'adjustments' => 'required|array',
            'adjustments.*.item_id' => 'required|exists:stock_entry_items,id',
            'adjustments.*.qty_to_add' => 'required|numeric',
            'adjustments.*.reason' => 'required',
            'adjustments.*.approved_by' => 'required'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->adjustments as $adj) {
                $item = StockEntryItem::findOrFail($adj['item_id']);

                $previousQtyIn = $item->qty_in;
                $newQtyIn = $previousQtyIn + $adj['qty_to_add'];
                $item->qty_in = $newQtyIn;
                $item->save();

                $count = StockEntryAdjustment::count();
                $adjNo = 'ADJ-SE-' . date('Ymd') . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                StockEntryAdjustment::create([
                    'adjustment_no' => $adjNo,
                    'stock_entry_item_id' => $item->id,
                    'raw_material_id' => $item->raw_material_id,
                    'qty' => $adj['qty_to_add'],
                    'previous_stock' => $previousQtyIn,
                    'new_stock' => $newQtyIn,
                    'approved_by' => $adj['approved_by'],
                    'reason' => $adj['reason'],
                    'status' => 'Posted',
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Stock adjusted successfully']);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function adjustmentLogs($id = null)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view stock entries')) {
            return unauthorizedRedirect();
        }

        $query = StockEntryAdjustment::with(['stockEntryItem.stockEntry', 'rawMaterial', 'creator']);

        if ($id) {
            $query->whereHas('stockEntryItem', function ($q) use ($id) {
                $q->where('stock_entry_id', $id);
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        return view('stock_entry.adjustment_logs', compact('logs'));
    }
}
