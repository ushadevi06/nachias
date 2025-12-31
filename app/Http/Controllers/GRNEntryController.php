<?php

namespace App\Http\Controllers;

use App\Models\GrnEntry;
use App\Models\GrnEntryItem;
use App\Models\GrnEntryItemVariant;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\User;
use App\Models\FabricType;
use App\Models\StoreLocation;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GrnEntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = GrnEntry::with(['purchaseInvoice', 'supplier', 'grnEntryItems']);

            if ($request->supplier_id) {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->status) {
                $query->whereHas('grnEntryItems', function($q) use ($request) {
                    $q->where('quality_check_status', $request->status);
                });
            }

            $grnEntries = $query->latest()->get();
            $data = [];
            $count = 1;

            foreach ($grnEntries as $grn) {
                $statusLabel = $grn->status ?? 'Draft';
                $statusBadgeClass = 'bg-secondary';
                if ($statusLabel == 'Received') $statusBadgeClass = 'bg-success';
                if ($statusLabel == 'Invoiced') $statusBadgeClass = 'bg-info';
                if ($statusLabel == 'Closed') $statusBadgeClass = 'bg-dark';

                $statusDisplay = '<span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view grn-entry')) {
                    $action .= '<a href="' . url('grn_entries/view/' . $grn->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('edit grn-entry')) {
                    $action .= '<a href="' . url('grn_entries/add/' . $grn->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('delete grn-entry')) {
                    $action .= '<button class="btn btn-delete" onclick="delete_data(`' . url('grn_entries/delete/' . $grn->id) . '`)"><i class="icon-base ri ri-delete-bin-line"></i></button>';
                }
                $action .= '</div>';

                $qcStatuses = $grn->grnEntryItems->pluck('quality_check_status')->unique();
                $finalQcStatus = 'Pass';
                $badgeClass = 'bg-success';

                if ($qcStatuses->contains('Fail')) {
                    $finalQcStatus = 'Fail';
                    $badgeClass = 'bg-danger';
                } elseif ($qcStatuses->contains('Hold')) {
                    $finalQcStatus = 'Hold';
                    $badgeClass = 'bg-warning';
                }

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'grn_number' => $grn->grn_number,
                    'grn_date' => $grn->grn_date->format('d-m-Y'),
                    'po_invoice_no' => $grn->purchaseInvoice->invoice_no ?? 'N/A',
                    'supplier_name' => ($grn->supplier->name ?? 'N/A') . ' <span class="mini-title">(' . ($grn->supplier->code ?? '') . ')</span>',
                    'supplier_invoice_no' => $grn->purchaseInvoice->po_reference ?? 'N/A',
                    'total_items' => $grn->grnEntryItems->count(),
                    'amount' => '₹' . number_format($grn->grnEntryItems->sum('amount'), 2),
                    'qc_status' => '<span class="badge ' . $badgeClass . '">' . $finalQcStatus . '</span>',
                    'status' => $statusDisplay,
                    'action' => $action,
                ];
            }
            return response()->json(['data' => $data]);
        }
        $suppliers = \App\Models\Supplier::where('status', 'Active')->orderBy('name')->get();
        return view('grn_entry.view', compact('suppliers'));
    }

    public function add(Request $request, $id = null)
    {
        // dd($request->all());
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit grn-entry')) {
                 return unauthorizedRedirect();
            }
            $grn = GrnEntry::with(['grnEntryItems.variants.color', 'grnEntryItems.fabricType', 'grnEntryItems.storeLocation', 'purchaseInvoice'])->findOrFail($id);
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create grn-entry')) {
                 return unauthorizedRedirect();
            }
        }
        $grn = $grn ?? null;

        $purchaseInvoices = PurchaseInvoice::orderBy('invoice_no')->get();
        $fabricTypes = FabricType::where('status', 'Active')->orderBy('fabric_type')->get();
        $storeLocations = StoreLocation::where('status', 'Active')->orderBy('store_location')->get();
        $colors = Color::orderBy('color_name')->get();

        if ($request->isMethod('post')) {
            // Custom validation to only validate selected rows
            $selectedItems = collect($request->items)->filter(function($item) {
                return ($item['row_selected'] ?? 0) == 1;
            });

            if ($selectedItems->isEmpty()) {
                return back()->withInput()->withErrors(['error' => 'Please select at least one item to proceed.']);
            }

            $rules = [
                'grn_date' => 'required|date_format:d-m-Y',
                'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
                'supplier_invoice_date' => 'required|date_format:d-m-Y',
                'status' => 'required|in:Draft,Received,Partially Received,Invoiced,Cancelled',
                'items' => 'required|array',
            ];

            foreach ($request->items as $index => $item) {
                if (($item['row_selected'] ?? 0) == 1) {
                    $rules["items.$index.art_no"] = 'required';
                    $rules["items.$index.qty_received"] = 'required|numeric|gt:0';
                    $rules["items.$index.qty_accepted"] = 'required|numeric|min:0';
                    $rules["items.$index.quality_check_status"] = 'required|in:Pass,Fail,Hold';
                    $rules["items.$index.store_location_id"] = 'required|exists:store_locations,id';
                    $rules["items.$index.fabric_type_id"] = 'nullable|exists:fabric_types,id';
                    $rules["items.$index.item_image"] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
                }
            }
            $messages = [
                '*.required' => 'This field is required',
                '*.numeric' => 'This field must be a number',
                '*.min' => 'This field must be at least :min',
                '*.exists' => 'This field is invalid',
                'items.*.art_no.required' => 'This field is required',
                'items.*.qty_received.required' => 'This field is required',
                'items.*.qty_received.gt' => 'This field is required',
                'items.*.qty_received.min' => 'This field is required',
                'items.*.qty_accepted.required' => 'This field is required',
                'items.*.quality_check_status.required' => 'This field is required',
                'items.*.store_location_id.required' => 'This field is required',
            ];
            $request->validate($rules,$messages);

            DB::beginTransaction();
            try {
                $invoice = PurchaseInvoice::findOrFail($request->purchase_invoice_id);
                $headerData = [
                    'grn_date' => Carbon::createFromFormat('d-m-Y', $request->grn_date)->format('Y-m-d'),
                    'purchase_invoice_id' => $request->purchase_invoice_id,
                    'supplier_id' => $invoice->supplier_id,
                    'supplier_invoice_date' => Carbon::createFromFormat('d-m-Y', $request->supplier_invoice_date)->format('Y-m-d'),
                    'status' => $request->status,
                ];

                if ($id) {
                    $oldData = GrnEntry::find($id)->toArray();
                    $grn = GrnEntry::findOrFail($id);
                    $headerData['updated_by'] = auth()->id();
                    $grn->update($headerData);
                    $grn->grnEntryItems()->delete();
                    $newData = $grn->fresh()->toArray();
                    addLog('update', 'GRN Entry', 'grn_entries', $id, $oldData, $newData);
                } else {
                    $lastGrn = GrnEntry::latest('id')->first();
                    $nextNumber = $lastGrn ? (int)substr($lastGrn->grn_number, 3) + 1 : 1;
                    $headerData['grn_number'] = 'GRN' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                    $headerData['created_by'] = auth()->id();
                    $grn = GrnEntry::create($headerData);
                    $newData = $grn->toArray();
                    addLog('create', 'GRN Entry', 'grn_entries', $grn->id, null, $newData);
                }
                foreach ($request->items as $idx => $itemData) {
                    if (($itemData['row_selected'] ?? 0) == 0) continue;

                    $imagePath = $itemData['old_image'] ?? null;
                    if ($request->hasFile("items.$idx.item_image")) {
                        $file = $request->file("items.$idx.item_image");
                        $filename = 'grn_item_' . time() . '_' . $idx . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('uploads/grn_items'), $filename);
                        $imagePath = 'uploads/grn_items/' . $filename;
                    }

                    $item = GrnEntryItem::create([
                        'grn_entry_id' => $grn->id,
                        'purchase_invoice_item_id' => $itemData['purchase_invoice_item_id'] ?? null,
                        'art_no' => $itemData['art_no'] ?? null,
                        'fabric_type_id' => $itemData['fabric_type_id'] ?? null,
                        'qty_ordered' => $itemData['qty_ordered'] ?? 0,
                        'qty_received' => $itemData['qty_received'] ?? 0,
                        'qty_accepted' => $itemData['qty_accepted'] ?? 0,
                        'qty_rejected' => $itemData['qty_rejected'] ?? 0,
                        'qty_balanced' => $itemData['qty_balanced'] ?? 0,
                        'rate' => $itemData['rate'] ?? 0,
                        'amount' => $itemData['amount'] ?? 0,
                        'quality_check_status' => $itemData['quality_check_status'] ?? null,
                        'store_location_id' => $itemData['store_location_id'] ?? null,
                        'image' => $imagePath,
                    ]);

                    if (isset($itemData['variants']) && is_array($itemData['variants'])) {
                        foreach ($itemData['variants'] as $v) {
                            if (($v['qty'] ?? 0) > 0) {
                                GrnEntryItemVariant::create([
                                    'grn_entry_item_id' => $item->id,
                                    'color_id' => $v['color_id'],
                                    'qty_received' => $v['qty'],
                                ]);
                            }
                        }
                    }
                }

                DB::commit();
                return redirect('grn_entries')->with('success', 'GRN Entry saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => $e->getMessage()]);
            }
        }

        $nextGrnNo = $id ? $grn->grn_number : 'GRN' . str_pad((GrnEntry::latest('id')->first()->id ?? 0) + 1, 3, '0', STR_PAD_LEFT);

        return view('grn_entry.add', compact('grn', 'purchaseInvoices', 'fabricTypes', 'storeLocations', 'colors', 'nextGrnNo'));
    }

    public function getInvoiceDetails($id)
    {
        $invoice = PurchaseInvoice::with(['supplier', 'items.rawMaterial', 'items.uom', 'items.purchaseOrderItem'])->findOrFail($id);
        
        $items = $invoice->items->map(function($item) {
            $already_received = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item->id)->sum('qty_received');
            
            return [
                'id' => $item->id,
                'design_name' => ($item->rawMaterial->name ?? '') . '(' . ($item->rawMaterial->code ?? '') . ')',
                'art_no' => $item->purchaseOrderItem->art_no ?? '',
                'uom' => $item->uom->uom_name ?? 'MTR',
                'qty_ordered' => $item->quantity - $already_received,
                'qty_already_received' => $already_received,
                'rate' => $item->rate,
                'amount' => $item->amount,
            ];
        })->filter(function($item) {
            return $item['qty_ordered'] > 0;
        })->values();

        return response()->json([
            'success' => true,
            'supplier_id' => $invoice->supplier_id,
            'supplier_name' => $invoice->supplier->name ?? 'N/A',
            'invoice_date' => $invoice->invoice_date->format('d-m-Y'),
            'items' => $items
        ]);
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view grn-entry')) {
             return unauthorizedRedirect();
        }
        $grn = GrnEntry::with([
            'purchaseInvoice',
            'supplier',
            'grnEntryItems.purchaseInvoiceItem.rawMaterial',
            'grnEntryItems.purchaseInvoiceItem.uom',
            'grnEntryItems.fabricType',
            'grnEntryItems.storeLocation',
            'grnEntryItems.variants.color'
        ])->findOrFail($id);

        return view('grn_entry.view_details', compact('grn'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete grn-entry')) {
             return unauthorizedRedirect();
        }

        $grn = GrnEntry::findOrFail($id);
        $oldData = $grn->toArray();
        
        // Delete related items and variants if necessary? 
        // Typically cascade deletes handle this in DB, but if soft/hard delete logic exists...
        // Assuming standard delete is fine or model events handle it. 
        // However, explicit deletion of items might be safer if constraints aren't perfect.
        $grn->grnEntryItems()->each(function($item) {
            $item->variants()->delete(); // delete variants
            $item->delete(); // delete item
        });

        $grn->delete();
        addLog('delete', 'GRN Entry', 'grn_entries', $id, $oldData, null);

        return redirect('grn_entries')->with('success', 'GRN Entry deleted successfully');
    }
}
