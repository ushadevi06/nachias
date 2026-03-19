<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Billing::latest();

            if ($request->filled('bill_type')) {
                $query->where('billing_type', $request->bill_type);
            }

            $billings = $query->get();
            $data = [];
            $i = 1;

            foreach ($billings as $row) {
                $statusOptions = ['Pending', 'Paid', 'Partially Paid', 'Cancelled'];
                $status = '<select class="form-select status-dropdown" data-id="' . $row->id . '">';
                foreach ($statusOptions as $option) {
                    $selected = ($row->status == $option) ? 'selected' : '';
                    $status .= '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                }
                $status .= '</select><div class="status-msg-' . $row->id . ' small mt-1"></div>';

                $action = '
                <div class="button-box">
                    <a href="' . url('billing/view/' . $row->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                    <a href="' . url('billing/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                    <a href="javascript:;" class="btn btn-delete delete-btn" onclick="delete_data(\'' . url('billing/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>
                </div>';

                $data[] = [
                    'DT_RowIndex'  => $i++,
                    'bill_no'      => $row->bill_no,
                    'billing_type' => $row->billing_type,
                    'bill_date'    => $row->bill_date ? $row->bill_date->format('d-m-Y') : '',
                    'amount'       => '₹' . number_format($row->amount, 2),
                    'status'       => $status,
                    'action'       => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('billings/view');
    }

    public function add(Request $request, $id = null)
    {
        $billing = $id ? Billing::findOrFail($id) : null;

        if ($request->isMethod('POST')) {
            $request->validate([
                'bill_no'      => 'required|string|max:100',
                'billing_type' => 'required|string',
                'bill_date'    => 'required|date',
                'amount'       => 'nullable|numeric|min:0',
                'reason'       => 'nullable|string',
                'status'       => 'required|string',
            ],[
                'required' => 'This field is required.',
            ]);

            $data = $request->only(['bill_no', 'billing_type', 'bill_date', 'amount', 'reason', 'status']);

            if ($billing) {
                $oldData = $billing->toArray();
                $billing->update($data);
                addLog('update', 'Billing', 'billings', $billing->id, $oldData, $billing->fresh()->toArray());
                $msg = 'Billing updated successfully.';
            } else {
                $newBilling = Billing::create($data);
                addLog('create', 'Billing', 'billings', $newBilling->id, null, $newBilling->toArray());
                $msg = 'Billing added successfully.';
            }

            return redirect(url('billing'))->with('success', $msg);
        }

        return view('billings/add', compact('billing'));
    }

    public function view($id)
    {
        $billing = Billing::findOrFail($id);
        return view('billings/view_details', compact('billing'));
    }

    public function destroy($id)
    {
        $billing = Billing::findOrFail($id);
        $oldData = $billing->toArray();
        $billing->delete();
        addLog('delete', 'Billing', 'billings', $id, $oldData, null);
        return redirect(url('billing'))->with('success', 'Billing deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $billing = Billing::findOrFail($id);
        $oldData = $billing->toArray();
        $billing->status = $request->status;
        $billing->save();
        addLog('update_status', 'Billing Status', 'billings', $id, $oldData, $billing->fresh()->toArray());
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function print($id)
    {
        $billing = Billing::findOrFail($id);
        $setting = Setting::with(['state', 'city'])->first();
        $totalInWords = numberToWords($billing->amount);
        $is_print = true;

        return view('billings.pdf', compact('billing', 'setting', 'totalInWords', 'is_print'));
    }

    public function download($id)
    {
        $billing = Billing::findOrFail($id);
        $setting = Setting::with(['state', 'city'])->first();
        $totalInWords = numberToWords($billing->amount);

        $pdf = Pdf::loadView('billings.pdf', compact('billing', 'setting', 'totalInWords'));
        $pdf->setPaper('A4', 'portrait');

        $safeBillNo = str_replace(['/', '\\'], '_', $billing->bill_no);
        return $pdf->download('Billing_' . $safeBillNo . '.pdf');
    }
}
