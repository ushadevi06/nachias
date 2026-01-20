<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseInvoiceCharge;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view purchase-invoice')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $query = PurchaseInvoice::with(['supplier'])
                ->orderBy('id', 'desc');

            if (!empty($request->supplier_id)) {
                $query->where('supplier_id', $request->supplier_id);
            }

            if (!empty($request->invoice_status)) {
                $query->where('invoice_status', $request->invoice_status);
            }

            $invoices = $query->get();
            $data = [];
            $count = 1;

            foreach ($invoices as $invoice) {
                $statusOptions = '';

                $allStatuses = ['Draft', 'Unpaid/Credit', 'Partially Paid', 'Paid'];
                $currentStatus = $invoice->invoice_status;

                foreach ($allStatuses as $status) {

                    $selected = ($currentStatus === $status) ? 'selected' : '';
                    $disabled = '';
                    if ($currentStatus === 'Draft') {
                        $disabled = '';
                    } elseif ($currentStatus === 'Unpaid/Credit' && $status === 'Draft') {
                        $disabled = 'disabled';
                    } elseif ($currentStatus === 'Partially Paid' && $status !== 'Paid') {
                        $disabled = 'disabled';
                    } elseif ($currentStatus === 'Paid' && $status !== 'Paid') {
                        $disabled = 'disabled';
                    }
                    $statusOptions .= "<option value=\"{$status}\" {$selected} {$disabled}>{$status}</option>";
                }

                $statusDropdown = '
                <div class="form-floating form-floating-outline">
                    <select class="form-select invoice-status-change" data-id="' . $invoice->id . '">
                        ' . $statusOptions . '
                    </select>
                </div>
                <div class="status_msg_' . $invoice->id . ' mt-1"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view purchase-invoice')) {
                    $action .= '<a href="' . url('purchase_invoices/view/' . $invoice->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if ((auth()->id() == 1 || auth()->user()->can('edit purchase-invoice')) && $invoice->invoice_status !== 'Paid') {
                    $action .= '<a href="' . url('purchase_invoices/add/' . $invoice->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                // if (auth()->id() == 1 || auth()->user()->can('delete purchase-invoice')) {
                //     $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('purchase_invoices/delete/' . $invoice->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                // }
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'invoice_no' => $invoice->invoice_no,
                    'invoice_date' => $invoice->invoice_date->format('d-m-Y'),
                    'supplier_name' => $invoice->supplier ? $invoice->supplier->name .' <a href="' . url('view_supplier/' . $invoice->supplier->id) . '" target="_blank"><span class="mini-title">(' . $invoice->supplier->code . ')</span></a>' : '-',
                    'destination' => $invoice->destination ?? '-',
                    'total_amount' => '₹' . number_format($invoice->grand_total, 2),
                    'status' => $statusDropdown,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        $suppliers = Supplier::where('status', 'Active')->get();
        return view('purchase_invoice.view', compact('suppliers'));
    }


    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit purchase-invoice')) {
                return unauthorizedRedirect();
            }
            $invoice = PurchaseInvoice::with(['items.rawMaterial', 'items.uom', 'charges'])->findOrFail($id);
            $charges = $invoice->charges;
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create purchase-invoice')) {
                return unauthorizedRedirect();
            }
        }
        $invoice = $invoice ?? null;
        $charges = $charges ?? collect();

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'invoice_no' => ($id ? 'nullable' : 'required') . '|string|max:50|unique:purchase_invoices,invoice_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'invoice_date' => 'required|date',
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'supplier_id' => 'required|exists:suppliers,id',
                'invoice_status' => 'required|in:Draft,Unpaid/Credit,Paid,Partially Paid',
                'items' => 'required|array|min:1',
                'items.*.raw_material_id' => 'required|exists:raw_materials,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.rate' => 'required|numeric|min:0',
                'other_state' => ($id ? 'nullable' : 'required') . '|in:Y,N',
                'igst_percent' => 'nullable|numeric|min:0|max:100',
                'cgst_percent' => 'nullable|numeric|min:0|max:100',
                'sgst_percent' => 'nullable|numeric|min:0|max:100',
                'other_charges' => 'nullable|numeric|min:0',
                'round_off' => 'nullable|numeric',
                'round_off_type' => 'required|in:Add,Less',
                'grand_total' => 'required|numeric|min:0',
                'received_amount' => 'nullable|numeric|min:0',
                'charges_select' => 'nullable',
                'charge_amount' => 'nullable|numeric|min:0',
                'charges.amount.*' => 'nullable|numeric|min:0',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.exists' => 'Selected value is invalid.',
                '*.unique' => 'This field already exists.',
                '*.date' => 'Please enter a valid date.',
                'items.required' => 'Please add at least one item.',
                'items.*.quantity' => 'Quantity is required.',
                'other_state.required' => 'Please select if it is an other state transaction.',
                'igst_percent.numeric' => 'IGST % must be a number.',
                'cgst_percent.numeric' => 'CGST % must be a number.',
                'sgst_percent.numeric' => 'SGST % must be a number.',
                'other_charges.numeric' => 'Other Charges must be a number.',
                'grand_total.required' => 'Grand Total is required.',
                'grand_total.min' => 'Grand Total cannot be negative.',
                'received_amount.numeric' => 'Received Amount must be a number.',
                'charges_select.required' => 'Please select a charge.',
                'charge_amount.numeric' => 'Charge amount must be a number.',
                'charges.amount.*.numeric' => 'Charge amount must be a number.',
            ];

            $validated = $request->validate($rules, $messages);

            $purchaseOrder = PurchaseOrder::with('items')->findOrFail($request->purchase_order_id);

            $errors = [];
            $hasSelectedItems = false;

            foreach ($request->items as $index => $item) {
                if (!isset($item['selected'])) {
                    continue;
                }

                $hasSelectedItems = true;

                if (empty($item['quantity']) || $item['quantity'] <= 0) {
                    $errors["items.$index.quantity"] = 'This field is required.';
                    continue;
                }

                $poItem = $purchaseOrder->items->firstWhere('id', $item['purchase_order_item_id']);

                if ($poItem && $item['quantity'] > $poItem->quantity) {
                    $errors["items.$index.quantity"] = 'Received quantity cannot exceed ordered quantity.';
                }
            }

            if (!$hasSelectedItems) {
                return back()->withInput()->withErrors([
                    'items' => 'Please select at least one item'
                ]);
            }

            if (!empty($errors)) {
                return back()->withInput()->withErrors($errors);
            }

            DB::beginTransaction();
            try {
                $invoiceData = [
                    'invoice_no' => $request->invoice_no,
                    'invoice_date' => Carbon::createFromFormat('d-m-Y', $request->invoice_date)->format('Y-m-d'),
                    'purchase_order_id' => $request->purchase_order_id,
                    'purchase_order_no' => $request->purchase_order_no,
                    'supplier_id' => $request->supplier_id,
                    'destination' => $request->destination,
                    'po_reference' => $request->po_reference,
                    'sub_total' => $request->sub_total ?? 0,
                    'discount_percent' => $request->discount_percent ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'taxable_amount' => $request->taxable_amount ?? 0,
                    'other_state' => $request->other_state === 'Y',
                    'igst_percent' => $request->igst_percent ?? 0,
                    'igst_amount' => $request->igst_amount ?? 0,
                    'cgst_percent' => $request->cgst_percent ?? 0,
                    'cgst_amount' => $request->cgst_amount ?? 0,
                    'sgst_percent' => $request->sgst_percent ?? 0,
                    'sgst_amount' => $request->sgst_amount ?? 0,
                    'tax_amount' => $request->tax_amount ?? 0,
                    'other_charges' => $request->other_charges ?? 0,
                    'round_off' => $request->round_off ?? 0,
                    'round_off_type' => $request->round_off_type ?? 'Add',
                    'grand_total' => $request->grand_total ?? 0,
                    'received_amount' => $request->received_amount ?? 0,
                    'due_amount' => $request->due_amount ?? 0,
                    'invoice_status' => $request->invoice_status,
                    'payment_mode' => $request->payment_mode,
                    'due_date' => $request->due_date ? Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d') : null,
                    'notes' => $request->notes,
                    'transaction_id' => $request->transaction_id,
                ];

                $uploadPath = public_path('uploads/purchase_invoices');

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                if ($request->hasFile('auth_sign')) {
                    $file = $request->file('auth_sign');
                    $fileName = time() . '_auth_' . $file->getClientOriginalName();
                    $file->move($uploadPath, $fileName);
                    $invoiceData['auth_signature'] = $fileName;
                }

                if ($request->hasFile('attachments')) {
                    $file = $request->file('attachments');
                    $fileName = time() . '_attach_' . $file->getClientOriginalName();
                    $file->move($uploadPath, $fileName);
                    $invoiceData['attachments'] = $fileName;
                }


                if ($id) {
                    $oldInvoice = PurchaseInvoice::findOrFail($id);
                    $oldData = $oldInvoice->toArray();
                    $oldReceived = $oldInvoice->received_amount ?? 0;
                    
                    $invoiceData['updated_by'] = auth()->id();
                    $oldInvoice->update($invoiceData);
                    $invoice = $oldInvoice;

                    PurchaseInvoiceItem::where('purchase_invoice_id', $id)->forceDelete();
                    PurchaseInvoiceCharge::where('purchase_invoice_id', $id)->forceDelete();
                    
                    $newData = $invoice->fresh()->toArray();
                    addLog('update','Purchase Invoice','purchase_invoices',$id,$oldData,$newData);
                    $message = 'Purchase Invoice updated successfully';

                    $newPayment = $request->received_amount ?? 0;
                    if ($newPayment > 0) {
                        \App\Models\PurchaseInvoicePayment::create([
                            'purchase_invoice_id' => $invoice->id,
                            'amount' => $newPayment,
                            'payment_date' => now(),
                            'payment_mode' => $request->payment_mode ?? 'Cash',
                            'transaction_id' => $request->transaction_id,
                            'notes' => 'Additional payment from invoice edit',
                            'created_by' => auth()->id(),
                        ]);
                    }

                    $totalReceived = \App\Models\PurchaseInvoicePayment::where('purchase_invoice_id', $invoice->id)->sum('amount');
                    $invoice->update([
                        'received_amount' => $totalReceived,
                        'due_amount' => ($request->grand_total ?? $invoice->grand_total) - $totalReceived
                    ]);
                } else {
                    $invoiceData['created_by'] = auth()->id();
                    $invoice = PurchaseInvoice::create($invoiceData);
                    
                    if ($request->received_amount > 0) {
                        \App\Models\PurchaseInvoicePayment::create([
                            'purchase_invoice_id' => $invoice->id,
                            'amount' => $request->received_amount,
                            'payment_date' => now(),
                            'payment_mode' => $request->payment_mode ?? 'Cash',
                            'transaction_id' => $request->transaction_id,
                            'notes' => 'Initial payment from invoice creation',
                            'created_by' => auth()->id(),
                        ]);
                    }

                    $newData = $invoice->toArray();
                    addLog('create','Purchase Invoice','purchase_invoices',$invoice->id,null,$newData);
                    $message = 'Purchase Invoice created successfully';
                }


                if ($request->has('items')) {
                    foreach ($request->items as $item) {
                        if (isset($item['selected']) && $item['selected'] == '1') {
                            PurchaseInvoiceItem::create([
                                'purchase_invoice_id' => $invoice->id,
                                'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                                'raw_material_id' => $item['raw_material_id'],
                                'hsn_code' => $item['hsn_code'],
                                'quantity' => $item['quantity'],
                                'uom_id' => $item['uom_id'],
                                'rate' => $item['rate'],
                                'amount' => $item['quantity'] * $item['rate'],
                                'qty_ordered' => $item['qty_ordered'] ?? 0,
                                'qty_received' => $item['quantity'],
                                'qty_invoiced' => $item['quantity'],
                            ]);
                        }
                    }
                }

                if ($request->has('charges') && isset($request->charges['charge_id'])) {
                    $chargeIds = $request->charges['charge_id'];
                    $chargeNames = $request->charges['name'];
                    $chargeAmounts = $request->charges['amount'];

                    foreach ($chargeIds as $index => $chargeId) {
                        PurchaseInvoiceCharge::create([
                            'purchase_invoice_id' => $invoice->id,
                            'charge_id' => $chargeId,
                            'charge_name' => $chargeNames[$index],
                            'charge_amount' => $chargeAmounts[$index],
                        ]);
                    }
                }

                DB::commit();
                return redirect('purchase_invoices')->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save invoice: ' . $e->getMessage()]);
            }
        }

        $purchaseOrders = PurchaseOrder::with('supplier')
            ->where('purchase_orders.status', '!=', 'Draft')
            ->where(function($query) use ($invoice) {
                $query->whereIn('purchase_orders.id', function ($q) {
                    $q->select('purchase_order_items.purchase_order_id')
                        ->from('purchase_order_items')
                        ->leftJoin(
                            'purchase_invoice_items',
                            'purchase_invoice_items.purchase_order_item_id',
                            '=',
                            'purchase_order_items.id'
                        )
                        ->groupBy(
                            'purchase_order_items.id',
                            'purchase_order_items.quantity',
                            'purchase_order_items.purchase_order_id'
                        )
                        ->havingRaw('SUM(COALESCE(purchase_invoice_items.qty_invoiced,0)) < purchase_order_items.quantity');
                });
                
                if ($invoice) {
                    $query->orWhere('id', $invoice->purchase_order_id);
                }
            })
            ->get();
            $suppliers = Supplier::where('status', 'Active')->get();
        $paid_so_far = $invoice ? $invoice->payments()->sum('amount') : 0;

        $nextInvoiceNumber = '';
        if (!$id) {
            $setting = \App\Models\Setting::first();
            if ($setting && $setting->purchase_invoice_prefix) {
                $prefix = $setting->purchase_invoice_prefix;
                $lastInvoice = PurchaseInvoice::where('invoice_no', 'like', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastInvoice) {
                    $lastNumberStr = substr($lastInvoice->invoice_no, strlen($prefix));
                    $lastNumber = intval($lastNumberStr);
                    $nextNumber = str_pad($lastNumber + 1, max(strlen($lastNumberStr), 4), '0', STR_PAD_LEFT);
                } else {
                    $nextNumber = '0001';
                }
                $nextInvoiceNumber = $prefix . $nextNumber;
            }
        }

        return view('purchase_invoice.add', compact('invoice', 'purchaseOrders', 'suppliers', 'charges', 'paid_so_far', 'nextInvoiceNumber'));
    }


    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view purchase-invoice')) {
            return unauthorizedRedirect();
        }
        $invoice = PurchaseInvoice::with(['supplier', 'items.rawMaterial', 'items.uom', 'charges'])->findOrFail($id);
        return view('purchase_invoice.view_details', compact('invoice'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete purchase-invoice')) {
            return unauthorizedRedirect();
        }
        $invoice = PurchaseInvoice::findOrFail($id);

        if ($invoice->auth_signature) {
            $filePath = public_path('uploads/purchase_invoices/' . $invoice->auth_signature);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        if ($invoice->attachments) {
            $filePath = public_path('uploads/purchase_invoices/' . $invoice->attachments);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $invoice->delete();
        return redirect('purchase_invoices')->with('success', 'Purchase Invoice deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        $oldData = $invoice->toArray();
        $invoice->invoice_status = $request->status;
        $invoice->save();
        $newData = $invoice->toArray();
        addLog('update_status', 'Purchase Invoice Status', 'purchase_invoices', $id, $oldData, $newData);
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function getPurchaseOrderDetails($id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'items.rawMaterial',
            'items.uom',
            'items.storeCategory'
        ])->findOrFail($id);

        $items = $purchaseOrder->items->map(function ($item) {
            $alreadyInvoicedQty = PurchaseInvoiceItem::where(
                'purchase_order_item_id',
                $item->id
            )->sum('quantity');

            $balanceQty = $item->quantity - $alreadyInvoicedQty;

            if ($balanceQty <= 0) {
                return null;
            }

            return [
                'id' => $item->id,
                'raw_material_id' => $item->raw_material_id,
                'raw_material_name' => $item->rawMaterial->name,
                'art_no' => $item->art_no,
                'hsn_code' => $item->rawMaterial->hsn_code ?? '',
                'quantity' => $balanceQty,
                'qty_ordered' => $item->quantity,
                'qty_invoiced' => $alreadyInvoicedQty, 
                'balance_qty' => $balanceQty,
                'uom_id' => $item->uom_id,
                'uom_code' => $item->uom->uom_code,
                'rate' => $item->rate,
                'amount' => $item->amount,
            ];
        })
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'po_number' => $purchaseOrder->po_number,
            'supplier_id' => $purchaseOrder->supplier_id,
            'supplier_name' => $purchaseOrder->supplier->name . ' (' . $purchaseOrder->supplier->code . ')',
            'items' => $items,
        ]);
    }


    public function downloadPdf($id)
    {
        $invoice = PurchaseInvoice::with(['supplier', 'items.rawMaterial', 'items.uom', 'charges'])->findOrFail($id);
        $pdf = Pdf::loadView('purchase_invoice.purchase_invoice_pdf', compact('invoice'));
        $pdf->setPaper('A4', 'portrait');
        $safeInvoiceNo = str_replace(['/', '\\'], '_', $invoice->invoice_no);
        return $pdf->stream('Purchase_Invoice_' . $safeInvoiceNo . '.pdf');
    }
    public function deleteCharge($id)
    {
        try {
            $charge = PurchaseInvoiceCharge::findOrFail($id);
            $charge->delete();

            return response()->json([
                'success' => true,
                'message' => 'Charge deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete charge: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPaymentHistory($id)
    {
        $payments = \App\Models\PurchaseInvoicePayment::where('purchase_invoice_id', $id)
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }
}
