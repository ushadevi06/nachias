<?php

namespace App\Services;

use App\Models\SalesInvoice;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EInvoiceService
{
    public function generateEInvoice(SalesInvoice $invoice, array $transporterData = [])
    {
        $invoice->load(['customer.state', 'customer.city', 'items.uom', 'items.item', 'items.stockEntryItem']);

        if (empty($invoice->customer->gst_no)) {
            return [
                'success' => false,
                'message' => 'E-Invoice can only be generated for registered GST customers (B2B). This customer does not have a GSTIN.'
            ];
        }

        $setting = Setting::first();
        $sellerStateCode = substr(env('EINV_GSTIN'), 0, 2);
        $buyerStateCode = $invoice->customer->state->state_code ?? "33";

        // Explicit inter-state evaluation (other_state === 'Y' / 'N' / 1 / 0)
        $otherStateRaw = strtoupper(trim((string)($invoice->other_state ?? '')));
        $isInterState = ($otherStateRaw === 'Y' || $otherStateRaw === 'YES' || $otherStateRaw === '1' || $invoice->other_state === true);

        $rndOff = (float) ($invoice->round_off ?? 0);
        if (strtolower($invoice->round_off_type ?? '') === 'less') {
            $rndOff = -$rndOff;
        }   

        $itemList = [];
        $totalAssVal = 0.00;
        $totalCgstVal = 0.00;
        $totalSgstVal = 0.00;
        $totalIgstVal = 0.00;

        $slNo = 1;

        foreach ($invoice->items as $item) {
            $totAmt = (float) number_format((float) $item->amount, 2, '.', '');
            $salesDiscountPercent = (float) ($invoice->sales_discount ?? 0);
            $boxDiscountAmount = (float) ($invoice->box_discount_amount ?? 0);

            if ($salesDiscountPercent == 0 && $boxDiscountAmount == 0 && (float) ($invoice->discount ?? 0) > 0 && (float) ($invoice->sub_total ?? 0) > 0) {
                $discountPercent = ((float) $invoice->discount / (float) $invoice->sub_total) * 100;
                $itemDiscount = (float) number_format(($totAmt * $discountPercent) / 100, 2, '.', '');
            } else {
                $itemSalesDiscount = ($totAmt * $salesDiscountPercent) / 100;
                $itemBoxDiscount = (float) $item->quantity * $boxDiscountAmount;
                $itemDiscount = (float) number_format($itemSalesDiscount + $itemBoxDiscount, 2, '.', '');
            }
            $assAmt = (float) number_format($totAmt - $itemDiscount, 2, '.', '');
            
            if ($isInterState) {
                // Inter-State: Set CGST & SGST to 0, calculate IGST
                $cgstAmt = 0.00;
                $sgstAmt = 0.00;
                $igstAmt = (float) number_format(($assAmt * (float) ($invoice->igst_percent ?? 0)) / 100, 2, '.', '');
                $taxRate = (float) ($invoice->igst_percent ?? 0);
            } else {
                // Intra-State: Calculate CGST & SGST based on percentages, set IGST to 0
                $cgstAmt = (float) number_format(($assAmt * (float) ($invoice->cgst_percent ?? 0)) / 100, 2, '.', '');
                $sgstAmt = (float) number_format(($assAmt * (float) ($invoice->sgst_percent ?? 0)) / 100, 2, '.', '');
                $igstAmt = 0.00;
                $taxRate = (float) ($invoice->cgst_percent ?? 0) + (float) ($invoice->sgst_percent ?? 0);
            }

            $totItemVal = (float) number_format($assAmt + $cgstAmt + $sgstAmt + $igstAmt, 2, '.', '');

            $totalAssVal += $assAmt;
            $totalCgstVal += $cgstAmt;
            $totalSgstVal += $sgstAmt;
            $totalIgstVal += $igstAmt;

            $prdDesc = substr(
                $item->stockEntryItem->finished_item_code ??
                $item->item->code ??
                $item->item->name ??
                'Product',
                0,
                30
            );

            $itemList[] = [
                "SlNo" => (string) $slNo++,
                "PrdDesc" => $prdDesc,
                "IsServc" => "N",
                "HsnCd" => (string) ($item->hsn_sac ?? "61099090"),
                "Qty" => (float) number_format((float) $item->quantity, 2, '.', ''),
                "Unit" => "PCS",
                "UnitPrice" => (float) number_format((float) $item->rate, 2, '.', ''),
                "TotAmt" => $totAmt,
                "Discount" => $itemDiscount,
                "AssAmt" => $assAmt,
                "GstRt" => (float) number_format($taxRate, 2, '.', ''),
                "IgstAmt" => $igstAmt,
                "CgstAmt" => $cgstAmt,
                "SgstAmt" => $sgstAmt,
                "TotItemVal" => $totItemVal,
            ];
        }

        $grandTotal = round((float) ($invoice->grand_total ?? 0), 2);
        $othChrg = (float) number_format((float) ($invoice->other_charges ?? 0.00), 2, '.', '');
        $expectedTot = round($totalAssVal + $totalCgstVal + $totalSgstVal + $totalIgstVal + $othChrg, 2);
        
        // Dynamically calculate round-off to absorb item-level tax rounding differences
        $rndOffAmt = round($grandTotal - $expectedTot, 2);

        if ($rndOffAmt < -99.99 || $rndOffAmt > 99.99) {
            return [
                'success' => false,
                'message' => 'E-Invoice cannot be generated. Round-off amount must be between -99.99 and 99.99. Current round-off: ' . number_format($rndOffAmt, 2),
            ];
        }
        
        $totInvVal = round($expectedTot + $rndOffAmt, 2);
        
        dd($totInvVal);
        if (abs($totInvVal - $grandTotal) > 0.01) {
            \Log::error('E-Invoice Mismatch Detail', [
                'invoice_id' => $invoice->id,
                'invoice_no' => $invoice->inv_no,
                'saved_grand_total' => $grandTotal,
                'expected_tot_before_roundoff' => $expectedTot,
                'round_off_amount' => $rndOffAmt,
                'calculated_tot_inv_val' => $totInvVal,
                'ass_val_sum' => $totalAssVal,
                'cgst_val_sum' => $totalCgstVal,
                'sgst_val_sum' => $totalSgstVal,
                'igst_val_sum' => $totalIgstVal,
                'other_charges' => $othChrg
            ]);
            return [
                'success' => false,
                'message' => 'E-Invoice total mismatch. Sales Invoice Grand Total: ₹'
                    . number_format($grandTotal, 2)
                    . ', E-Invoice calculated total: ₹'
                    . number_format($totInvVal, 2)
                    . '. Please verify discount, tax, other charges and round-off.',
            ];
        }

        $payload = [
            "Version" => "1.1",
            "TranDtls" => [
                "TaxSch" => "GST",
                "SupTyp" => "B2B",
                "RegRev" => "N",
                "IgstOnIntra" => "N"
            ],
            "DocDtls" => [
                "Typ" => "INV",
                "No" => $invoice->inv_no,
                "Dt" => $invoice->inv_date->format('d/m/Y')
            ],
            "SellerDtls" => [
                "Gstin" => env('EINV_GSTIN'),
                "LglNm" => $setting->company_name,
                "Addr1" => substr($setting->address, 0, 100),
                "Loc" => $setting->city->city_name,
                "Pin" => (int) $setting->zip_code,
                "Stcd" => substr(env('EINV_GSTIN'), 0, 2),
            ],
            "BuyerDtls" => [
                "Gstin" => $invoice->customer->gst_no,
                "LglNm" => $invoice->customer->name,
                "Pos" => $invoice->customer->state->state_code ?? "33",
                "Addr1" => substr($invoice->customer->address ?? "Buyer Address", 0, 100),
                "Loc" => $invoice->customer->city->city_name ?? "Buyer City",
                "Pin" => (int) ($invoice->customer->zip_code ?? 600001),
                "Stcd" => $invoice->customer->state->state_code ?? "33"
            ],
            "ItemList" => $itemList,
            "ValDtls" => [
                "AssVal" => (float) number_format($totalAssVal, 2, '.', ''),
                "CgstVal" => (float) number_format($totalCgstVal, 2, '.', ''),
                "SgstVal" => (float) number_format($totalSgstVal, 2, '.', ''),
                "IgstVal" => (float) number_format($totalIgstVal, 2, '.', ''),
                "OthChrg" => $othChrg,
                "RndOffAmt" => $rndOffAmt,
                "TotInvVal" => (float) number_format($totInvVal, 2, '.', ''),
            ]
        ];
        \Log::info('E-Invoice Request Payload', [
            'invoice_id' => $invoice->id,
            'invoice_no' => $invoice->inv_no,
            'payload' => $payload
        ]);
        $authData = $this->authenticate($setting);
        if (!$authData['success']) {
            return $authData;
        }


        $response = Http::withHeaders([
            'aspid' => env('EINV_ASP_ID'),
            'password' => env('EINV_ASP_PASSWORD'),
            'Gstin' => env('EINV_GSTIN'),
            'User_Name' => env('EINV_USERNAME'),
            'AuthToken' => $authData['token'],
            'Content-Type' => 'application/json',
        ])->post(env('EINV_API_URL'), $payload);


        $apiData = $response->json();

        if ($response->successful() && (isset($apiData['Status']) && $apiData['Status'] == 1 || isset($apiData['Irn']))) {

            $responseData = is_string($apiData['Data']) ? json_decode($apiData['Data'], true) : ($apiData['Data'] ?? $apiData);

            \DB::table('sales_invoices')->where('id', $invoice->id)->update([
                'irn' => $responseData['Irn'] ?? null,
                'ack_no' => $responseData['AckNo'] ?? null,
                'ack_date' => $responseData['AckDt'] ?? Carbon::now()->format('Y-m-d H:i:s'),
                'signed_qr_code' => $responseData['SignedQRCode'] ?? null,
                'einvoice_status' => 'generated',
            ]);
            $invoice->refresh();
            $ewayBillResult = null;
            if ($invoice->grand_total >= 50000 && !empty($transporterData) && (
                !empty($transporterData['vehicle_no']) || 
                (!empty($transporterData['transporter_id']) && !empty($transporterData['tran_doc_no']))
            )) {
                $ewayBillResult = $this->generateEWayBill($invoice, $transporterData);
            } elseif ($invoice->grand_total < 50000) {
                $ewayBillResult = ['success' => false, 'message' => 'Invoice value is under 50,000. E-Way Bill must be generated manually.'];
            }

            return [
                'success' => true,
                'message' => 'E-Invoice generated successfully',
                'data' => $apiData,
                'eway_bill' => $ewayBillResult
            ];
        }
    
        $errorMessage = 'Failed to generate E-Invoice';
        
        \Log::error('E-Invoice Failure', [
            'payload' => $payload,
            'response' => $apiData
        ]);

        if (isset($apiData['ErrorDetails']) && is_array($apiData['ErrorDetails'])) {
            $errs = [];
            foreach ($apiData['ErrorDetails'] as $err) {
                if (isset($err['ErrorMessage'])) {
                    $errs[] = $err['ErrorMessage'];
                }
            }
            if (count($errs) > 0) {
                $errorMessage = implode(" | ", $errs);
            }
        } elseif (isset($apiData['message'])) {
            $errorMessage = $apiData['message'];
        }

        return ['success' => false, 'message' => 'API Error: ' . $errorMessage, 'response' => $apiData];
    }
    public function authenticate($setting)
    {
        try {
            $response = Http::withHeaders([
                'aspid' => env('EINV_ASP_ID'),
                'password' => env('EINV_ASP_PASSWORD'),
                'Gstin' => env('EINV_GSTIN'),
                'User_Name' => env('EINV_USERNAME'),
                'eInvPwd' => env('EINV_PASSWORD'),
                'Content-Type' => 'application/json',
            ])->get(env('EINV_AUTH_URL'));
            
            $data = $response->json();
            
            if ($response->successful() && isset($data['Status']) && $data['Status'] == 1) {
                $authToken = $data['Data']['AuthToken'] ?? $data['AuthToken'] ?? null;
                $sek = $data['Data']['Sek'] ?? $data['Sek'] ?? null;
                
                if ($authToken) {
                    return ['success' => true, 'token' => $authToken, 'sek' => $sek];
                }
            }

            \Log::error('E-Invoice Authentication Failed', [
                'status_code' => $response->status(),
                'response' => $data ?? $response->body()
            ]);

            $errorMessage = $data['ErrorDetails'][0]['ErrorMessage'] ?? $data['message'] ?? null;
            if (!$errorMessage) {
                $errorMessage = 'Unknown Error (HTTP Status: ' . $response->status() . ')';
            }
            return ['success' => false, 'message' => 'E-Invoice Authentication failed: ' . $errorMessage];
        } catch (\Exception $e) {
            \Log::error('E-Invoice Authentication Exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => 'E-Invoice Authentication failed: ' . $e->getMessage()];
        }
    }
    public function authenticateEWayBill($setting)
    {
        $response = Http::get(env('EINV_EWAYBILL_AUTH_URL'), [
            'action' => 'ACCESSTOKEN',
            'aspid' => env('EINV_ASP_ID'),
            'password' => env('EINV_ASP_PASSWORD'),
            'gstin' => env('EINV_GSTIN'),
            'username' => env('EINV_USERNAME'),
            'ewbpwd' => env('EINV_PASSWORD'),
        ]);

        $data = $response->json();
        $authToken = $data['authtoken'] ?? null;

        if (!empty($authToken)) {
            return ['success' => true, 'token' => $authToken];
        }

        return ['success' => false, 'message' => 'E-Way Bill Authentication failed: ' . json_encode($data)];
    }

    public function generateEWayBill(SalesInvoice $invoice, array $transporterData)
    {
        if (empty($invoice->irn)) {
            return [
                'success' => false,
                'message' => 'E-Invoice (IRN) must be generated before creating E-Way Bill.'
            ];
        }

        $invoice->load(['customer.state', 'customer.city', 'items.item']);
        $setting = Setting::first()->load(['state', 'city']);

        $authData = $this->authenticateEWayBill($setting);
        if (!$authData['success']) {
            return $authData;
        }

        $vehNo = $transporterData['vehicle_no'] ?? null;
        if ($vehNo) {
            $vehNo = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $vehNo));
        }

        $tranDocNo   = !empty($transporterData['tran_doc_no']) ? $transporterData['tran_doc_no'] : null;
        $tranDocDate = $tranDocNo
            ? (!empty($transporterData['tran_doc_date']) ? $transporterData['tran_doc_date'] : now()->format('d/m/Y'))
            : null;

        $sellerStateCode = substr(env('EINV_GSTIN'), 0, 2);
        $buyerStateCode  = $invoice->customer->state->state_code ?? '33';
        $isInterState = (bool)($invoice->other_state ?? false);
        if ((float)($invoice->igst_percent ?? 0) > 0 || (float)($invoice->igst ?? 0) > 0) {
            $isInterState = true;
        }

        $taxableAmount = (float) ($invoice->taxable_amount ?? $invoice->total);
        $totalValue    = (float) $invoice->grand_total;

        $cleanFromAddr = preg_replace('/\s+/', ' ', trim($setting->address ?? 'NA'));
        $cleanToAddr   = preg_replace('/\s+/', ' ', trim($invoice->customer->address ?? 'NA'));

        $itemList = [];
        foreach ($invoice->items as $idx => $item) {
            $totAmt = (float) number_format((float) $item->amount, 2, '.', '');
            $salesDiscountPercent = (float) ($invoice->sales_discount ?? 0);
            $boxDiscountAmount = (float) ($invoice->box_discount_amount ?? 0);

            if ($salesDiscountPercent == 0 && $boxDiscountAmount == 0 && (float) ($invoice->discount ?? 0) > 0 && (float) ($invoice->sub_total ?? 0) > 0) {
                $discountPercent = ((float) $invoice->discount / (float) $invoice->sub_total) * 100;
                $itemDiscount = (float) number_format(($totAmt * $discountPercent) / 100, 2, '.', '');
            } else {
                $itemSalesDiscount = ($totAmt * $salesDiscountPercent) / 100;
                $itemBoxDiscount = (float) $item->quantity * $boxDiscountAmount;
                $itemDiscount = (float) number_format($itemSalesDiscount + $itemBoxDiscount, 2, '.', '');
            }
            $assAmt = (float) number_format($totAmt - $itemDiscount, 2, '.', '');

            $itemList[] = [
                'itemNo'        => $idx + 1,
                'productName'   => substr($item->item->name ?? 'Garment', 0, 100),
                'productDesc'   => substr($item->item->name ?? 'Garment', 0, 100),
                'hsnCode'       => $item->hsn_sac ?? '61099090',
                'quantity'      => (float) $item->quantity,
                'qtyUnit'       => 'PCS',
                'taxableAmount' => $assAmt,
                'sgstRate'      => $isInterState ? 0.0 : (float) $invoice->sgst_percent,
                'cgstRate'      => $isInterState ? 0.0 : (float) $invoice->cgst_percent,
                'igstRate'      => $isInterState ? (float) $invoice->igst_percent : 0.0,
            ];
        }

        $payload = array_filter([
            'Irn'              => $invoice->irn,
            'supplyType'       => 'O',
            'subSupplyType'    => '1',
            'docType'          => 'INV',
            'docNo'            => $invoice->inv_no,
            'docDate'          => $invoice->inv_date->format('d/m/Y'),
            'fromGstin'        => env('EINV_GSTIN'),
            'fromTrdName'      => $setting->company_name,
            'fromAddr1'        => substr($cleanFromAddr, 0, 120),
            'fromPlace'        => $setting->city->city_name ?? '',
            'fromPincode'      => (int) ($setting->zip_code ?? 0),
            'fromStateCode'    => $sellerStateCode,
            'actFromStateCode' => $sellerStateCode,
            'toGstin'          => $invoice->customer->gst_no,
            'toTrdName'        => $invoice->customer->name,
            'toAddr1'          => substr($cleanToAddr ?: 'NA', 0, 120),
            'toPlace'          => $invoice->customer->city->city_name ?? '',
            'toPincode'        => (int) ($invoice->customer->zip_code ?? 0),
            'toStateCode'      => $buyerStateCode,
            'actToStateCode'   => $buyerStateCode,
            'transactionType'  => 1,
            'transDistance'    => (int) ($transporterData['transport_distance'] ?? 0),
            'transMode'        => $transporterData['transport_mode'] ?? '1',
            'transDocNo'       => $tranDocNo,
            'transDocDate'     => $tranDocDate,
            'vehicleNo'        => $vehNo,
            'vehicleType'      => $transporterData['veh_type'] ?? 'R',
            'totInvValue'      => $totalValue,
            'totalValue'       => $taxableAmount,
            'cgstValue'        => $isInterState ? 0.0 : (float) $invoice->cgst,
            'sgstValue'        => $isInterState ? 0.0 : (float) $invoice->sgst,
            'igstValue'        => $isInterState ? (float) $invoice->igst : 0.0,
            'otherValue'       => (float) ($invoice->other_charges ?? 0.0),
            'taxableAmount'    => $taxableAmount,
            'itemList'         => $itemList,
        ], fn($v) => $v !== null && $v !== '');


        $queryString = http_build_query([
            'action'    => 'GENEWAYBILL',
            'aspid'     => env('EINV_ASP_ID'),
            'password'  => env('EINV_ASP_PASSWORD'),
            'gstin'     => env('EINV_GSTIN'),
            'authtoken' => $authData['token'],
        ]);

        $fullUrl = env('EINV_EWAYBILL_URL') . '?' . $queryString;

        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($fullUrl, $payload);

        $apiData = $response->json();

        $ewbNo       = $apiData['ewayBillNo'] ?? $apiData['EwbNo'] ?? null;
        $ewbDate     = $apiData['ewayBillDate'] ?? $apiData['EwbDt'] ?? null;
        $ewbValidTill = $apiData['validUpto'] ?? $apiData['EwbValidTill'] ?? null;

        if (!empty($ewbNo)) {
            \DB::table('sales_invoices')->where('id', $invoice->id)->update([
                'eway_bill_no'        => $ewbNo,
                'eway_bill_date'      => $ewbDate
                    ? (strpos($ewbDate, '/') !== false 
                        ? Carbon::createFromFormat('d/m/Y h:i:s A', $ewbDate)->format('Y-m-d H:i:s')
                        : Carbon::parse($ewbDate)->format('Y-m-d H:i:s'))
                    : now()->format('Y-m-d H:i:s'),
                'eway_bill_valid_till' => $ewbValidTill
                    ? (strpos($ewbValidTill, '/') !== false 
                        ? Carbon::createFromFormat('d/m/Y h:i:s A', $ewbValidTill)->format('Y-m-d H:i:s')
                        : Carbon::parse($ewbValidTill)->format('Y-m-d H:i:s'))
                    : now()->addDays(2)->format('Y-m-d H:i:s'),
                'vehicle_no'          => $transporterData['vehicle_no'] ?? null,
                'transporter_id'      => $transporterData['transporter_id'] ?? null,
                'transport_mode'      => $transporterData['transport_mode'] ?? null,
                'transport_distance'  => $transporterData['transport_distance'] ?? null,
                'transporter_name'    => $transporterData['transporter_name'] ?? null,
                'tran_doc_no'         => $transporterData['tran_doc_no'] ?? null,
                'tran_doc_date'       => !empty($transporterData['tran_doc_date'])
                    ? (strpos($transporterData['tran_doc_date'], '/') !== false
                        ? \Carbon\Carbon::createFromFormat('d/m/Y', explode(' ', $transporterData['tran_doc_date'])[0])->format('Y-m-d')
                        : \Carbon\Carbon::parse($transporterData['tran_doc_date'])->format('Y-m-d'))
                    : null,
                'veh_type'            => $transporterData['veh_type'] ?? null,
            ]);

            return [
                'success' => true,
                'message' => 'E-Way Bill generated successfully',
                'alert'   => $apiData['alert'] ?? null,
                'data'    => $apiData,
            ];
        }

        $errorMessage = 'Failed to generate E-Way Bill';
        if (!empty($apiData['error']) && is_array($apiData['error'])) {
            $errorMessage = $apiData['error']['message'] ?? json_encode($apiData['error']);
        } elseif (!empty($apiData['error']) && is_string($apiData['error'])) {
            $errorMessage = $apiData['error'];
        } elseif (!empty($apiData['message'])) {
            $errorMessage = $apiData['message'];
        } elseif (!empty($apiData['ErrorDetails']) && is_array($apiData['ErrorDetails'])) {
            $errorMessage = $apiData['ErrorDetails'][0]['ErrorMessage'] ?? json_encode($apiData['ErrorDetails']);
        }

        return [
            'success'  => false,
            'message'  => 'API Error: ' . $errorMessage,
            'response' => $apiData,
        ];
    }
    /**
     * Cancel E-Invoice.
     */
    public function cancelEInvoice(SalesInvoice $invoice, $cancelReason = '2', $cancelRemarks = 'Data Entry Mistake')
    {
        if (empty($invoice->irn)) {
            return [
                'success' => false,
                'message' => 'No IRN found for this invoice.'
            ];
        }

        $setting = Setting::first();
        $authData = $this->authenticate($setting);
        if (!$authData['success']) {
            return $authData;
        }

        $payload = [
            "Irn" => $invoice->irn,
            "CnlRsn" => (string)$cancelReason,
            "CnlRem" => substr(trim($cancelRemarks), 0, 100)
        ];

        $cancelUrl = env('EINV_API_URL') . '/Cancel';

        $response = Http::withHeaders([
            'aspid' => env('EINV_ASP_ID'),
            'password' => env('EINV_ASP_PASSWORD'),
            'Gstin' => env('EINV_GSTIN'),
            'User_Name' => env('EINV_USERNAME'),
            'AuthToken' => $authData['token'],
            'Content-Type' => 'application/json',
        ])->post($cancelUrl, $payload);

        $apiData = $response->json();

        if ($response->successful() && (isset($apiData['Status']) && $apiData['Status'] == 1 || isset($apiData['CancelDate']))) {
            \DB::table('sales_invoices')->where('id', $invoice->id)->update([
                'irn' => null,
                'ack_no' => null,
                'ack_date' => null,
                'signed_qr_code' => null,
                'eway_bill_no' => null,
                'eway_bill_date' => null,
                'eway_bill_valid_till' => null,
                'einvoice_status' => 'cancelled',
            ]);

            return [
                'success' => true,
                'message' => 'E-Invoice cancelled successfully',
                'data' => $apiData
            ];
        }

        $errorMessage = 'Failed to cancel E-Invoice';
        if (isset($apiData['ErrorDetails'][0]['ErrorMessage'])) {
            $errorMessage = $apiData['ErrorDetails'][0]['ErrorMessage'];
        } elseif (isset($apiData['message'])) {
            $errorMessage = $apiData['message'];
        }

        return ['success' => false, 'message' => 'API Error: ' . $errorMessage, 'response' => $apiData];
    }

    /**
     * Cancel E-Way Bill.
     */
    public function cancelEWayBill(SalesInvoice $invoice, $cancelReason = 2, $cancelRemarks = 'Data Entry Mistake')
    {
        if (empty($invoice->eway_bill_no)) {
            return [
                'success' => false,
                'message' => 'No active E-Way Bill found for this invoice.'
            ];
        }

        $setting = Setting::first();
        $authData = $this->authenticateEWayBill($setting);
        if (!$authData['success']) {
            return $authData;
        }

        $payload = [
            'ewbNo' => (int) $invoice->eway_bill_no,
            'cancelRsnCode' => (int) $cancelReason,
            'cancelRmrk' => substr(trim($cancelRemarks), 0, 100)
        ];

        $queryString = http_build_query([
            'action'    => 'CANEWB',
            'aspid'     => env('EINV_ASP_ID'),
            'password'  => env('EINV_ASP_PASSWORD'),
            'gstin'     => env('EINV_GSTIN'),
            'authtoken' => $authData['token'],
        ]);

        $fullUrl = env('EINV_EWAYBILL_URL') . '?' . $queryString;
        \Log::info('Cancel E-Way Bill Payload', $payload);

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($fullUrl, $payload);

        $apiData = $response->json();
        \Log::info('Cancel E-Way Bill Response', $apiData ?? []);

        if ($response->successful() && (
            (isset($apiData['status']) && $apiData['status'] == 1) || 
            (isset($apiData['Status']) && $apiData['Status'] == 1) || 
            !empty($apiData['cancelDate']) ||
            !empty($apiData['CancelDate']) ||
            (isset($apiData['ewbNo']) && empty($apiData['error']))
        )) {
            \DB::table('sales_invoices')->where('id', $invoice->id)->update([
                'eway_bill_no' => null,
                'eway_bill_date' => null,
                'eway_bill_valid_till' => null,
            ]);

            return [
                'success' => true,
                'message' => 'E-Way Bill cancelled successfully',
                'data' => $apiData
            ];
        }

        $errorMessage = 'Failed to cancel E-Way Bill';
        if (!empty($apiData['error']) && is_array($apiData['error'])) {
            $errorMessage = $apiData['error']['message'] ?? json_encode($apiData['error']);
        } elseif (!empty($apiData['error']) && is_string($apiData['error'])) {
            $errorMessage = $apiData['error'];
        } elseif (!empty($apiData['message'])) {
            $errorMessage = $apiData['message'];
        } elseif (!empty($apiData['ErrorDetails']) && is_array($apiData['ErrorDetails'])) {
            $errorMessage = $apiData['ErrorDetails'][0]['ErrorMessage'] ?? json_encode($apiData['ErrorDetails']);
        }

        return [
            'success' => false,
            'message' => 'API Error: ' . $errorMessage,
            'response' => $apiData
        ];
    }
    public function generateCreditNoteEInvoice(\App\Models\CreditNote $creditNote, array $transporterData = [])
    {
        $creditNote->load(['customer.state', 'customer.city', 'items.uom', 'items.item', 'items.salesInvoiceItem.stockEntryItem']);

        if (empty($creditNote->customer->gst_no)) {
            return [
                'success' => false,
                'message' => 'E-Invoice can only be generated for registered GST customers (B2B). This customer does not have a GSTIN.'
            ];
        }

        $setting = Setting::first();
        $sellerStateCode = substr(env('EINV_GSTIN'), 0, 2);
        $buyerStateCode = $creditNote->customer->state->state_code ?? "33";
        $isInterState = (bool)($creditNote->other_state ?? false);
        if ((float)($creditNote->igst_percent ?? 0) > 0 || (float)($creditNote->igst ?? 0) > 0) {
            $isInterState = true;
        }

        $rndOff = (float) ($creditNote->round_off ?? 0);
        if (strtolower($creditNote->round_off_type ?? '') === 'less') {
            $rndOff = -$rndOff;
        }

        $itemList = [];
        $totalAssVal = 0.00;
        $totalCgstVal = 0.00;
        $totalSgstVal = 0.00;
        $totalIgstVal = 0.00;

        $slNo = 1;

        foreach ($creditNote->items as $item) {
            $taxRate = $isInterState ? (float) $creditNote->igst_percent : (float) ($creditNote->cgst_percent + $creditNote->sgst_percent);

            $totAmt = (float) number_format((float) $item->amount, 2, '.', '');
            $discountPercent = (float) ($creditNote->discount_percent ?? 0);
            if ($discountPercent == 0 && (float) ($creditNote->discount ?? 0) > 0 && (float) ($creditNote->sub_total ?? 0) > 0) {
                $discountPercent = ((float) $creditNote->discount / (float) $creditNote->sub_total) * 100;
            }
            $itemDiscount = (float) number_format(($totAmt * $discountPercent) / 100, 2, '.', '');
            $assAmt = (float) number_format($totAmt - $itemDiscount, 2, '.', '');
            
            $cgstAmt = $isInterState ? 0.00 : (float) number_format(($assAmt * (float) $creditNote->cgst_percent) / 100, 2, '.', '');
            $sgstAmt = $isInterState ? 0.00 : (float) number_format(($assAmt * (float) $creditNote->sgst_percent) / 100, 2, '.', '');
            $igstAmt = $isInterState ? (float) number_format(($assAmt * (float) $creditNote->igst_percent) / 100, 2, '.', '') : 0.00;
            $totItemVal = (float) number_format($assAmt + $cgstAmt + $sgstAmt + $igstAmt, 2, '.', '');

            $totalAssVal += $assAmt;
            $totalCgstVal += $cgstAmt;
            $totalSgstVal += $sgstAmt;
            $totalIgstVal += $igstAmt;

            $prdDesc = substr(
                $item->salesInvoiceItem->stockEntryItem->finished_item_code ??
                $item->item->code ??
                $item->item->name ??
                'Product',
                0,
                30
            );

            $itemList[] = [
                "SlNo" => (string) $slNo++,
                "PrdDesc" => $prdDesc,
                "IsServc" => "N",
                "HsnCd" => (string) ($item->hsn_sac ?? "61099090"),
                "Qty" => (float) number_format((float) $item->quantity, 2, '.', ''),
                "Unit" => "PCS",
                "UnitPrice" => (float) number_format((float) $item->rate, 2, '.', ''),
                "TotAmt" => $totAmt,
                "Discount" => $itemDiscount,
                "AssAmt" => $assAmt,
                "GstRt" => (float) number_format($taxRate, 2, '.', ''),
                "IgstAmt" => $igstAmt,
                "CgstAmt" => $cgstAmt,
                "SgstAmt" => $sgstAmt,
                "TotItemVal" => $totItemVal,
            ];
        }

        $othChrg = (float) number_format((float) ($creditNote->other_charges ?? 0.00), 2, '.', '');
        $expectedTot = $totalAssVal + $totalCgstVal + $totalSgstVal + $totalIgstVal + $othChrg;
        $rndOffAmt = (float) number_format((float) $creditNote->grand_total - $expectedTot, 2, '.', '');
        $totInvVal = $expectedTot + $rndOffAmt;

        $precDocDtls = [];
        if ($creditNote->sales_invoice_id) {
            $originalInvoice = \App\Models\SalesInvoice::find($creditNote->sales_invoice_id);
            if ($originalInvoice) {
                $precDocDtls[] = [
                    "InvNo" => $originalInvoice->inv_no,
                    "InvDt" => $originalInvoice->inv_date->format('d/m/Y'),
                ];
            }
        }

        $payload = [
            "Version" => "1.1",
            "TranDtls" => [
                "TaxSch" => "GST",
                "SupTyp" => "B2B",
                "RegRev" => "N",
                "IgstOnIntra" => "N"
            ],
            "DocDtls" => [
                "Typ" => "CRN",
                "No" => $creditNote->note_no,
                "Dt" => $creditNote->note_date->format('d/m/Y')
            ],
            "SellerDtls" => [
                "Gstin" => env('EINV_GSTIN'),
                "LglNm" => $setting->company_name,
                "Addr1" => substr($setting->address, 0, 100),
                "Loc" => $setting->city->city_name,
                "Pin" => (int) $setting->zip_code,
                "Stcd" => substr(env('EINV_GSTIN'), 0, 2),
            ],
            "BuyerDtls" => [
                "Gstin" => $creditNote->customer->gst_no,
                "LglNm" => $creditNote->customer->name,
                "Pos" => $creditNote->customer->state->state_code ?? "33",
                "Addr1" => substr($creditNote->customer->address ?? "Buyer Address", 0, 100),
                "Loc" => $creditNote->customer->city->city_name ?? "Buyer City",
                "Pin" => (int) ($creditNote->customer->zip_code ?? 600001),
                "Stcd" => $creditNote->customer->state->state_code ?? "33"
            ],
            "ItemList" => $itemList,
            "ValDtls" => [
                "AssVal" => (float) number_format($totalAssVal, 2, '.', ''),
                "CgstVal" => (float) number_format($totalCgstVal, 2, '.', ''),
                "SgstVal" => (float) number_format($totalSgstVal, 2, '.', ''),
                "IgstVal" => (float) number_format($totalIgstVal, 2, '.', ''),
                "OthChrg" => $othChrg,
                "RndOffAmt" => $rndOffAmt,
                "TotInvVal" => (float) number_format($totInvVal, 2, '.', ''),
            ]
        ];

        if (!empty($precDocDtls)) {
            $payload["PrecDocDtls"] = $precDocDtls;
        }

        $authData = $this->authenticate($setting);
        if (!$authData['success']) {
            return $authData;
        }

        $response = Http::withHeaders([
            'aspid' => env('EINV_ASP_ID'),
            'password' => env('EINV_ASP_PASSWORD'),
            'Gstin' => env('EINV_GSTIN'),
            'User_Name' => env('EINV_USERNAME'),
            'AuthToken' => $authData['token'],
            'Content-Type' => 'application/json',
        ])->post(env('EINV_API_URL'), $payload);

        $apiData = $response->json();

        if ($response->successful() && (isset($apiData['Status']) && $apiData['Status'] == 1 || isset($apiData['Irn']))) {

            $responseData = is_string($apiData['Data']) ? json_decode($apiData['Data'], true) : ($apiData['Data'] ?? $apiData);

            \DB::table('credit_notes')->where('id', $creditNote->id)->update([
                'irn' => $responseData['Irn'] ?? null,
                'ack_no' => $responseData['AckNo'] ?? null,
                'ack_date' => $responseData['AckDt'] ?? \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                'signed_qr_code' => $responseData['SignedQRCode'] ?? null,
                'einvoice_status' => 'generated',
            ]);
            $creditNote->refresh();
            
            $ewayBillResult = ['success' => false, 'message' => 'E-Way Bill generation is typically handled for Sales Invoices.'];

            return [
                'success' => true,
                'message' => 'Credit Note E-Invoice generated successfully',
                'data' => $apiData,
                'eway_bill' => $ewayBillResult
            ];
        }
    
        $errorMessage = 'Failed to generate Credit Note E-Invoice';
        
        \Log::error('Credit Note E-Invoice Failure', [
            'response' => $apiData
        ]);

        if (isset($apiData['ErrorDetails']) && is_array($apiData['ErrorDetails'])) {
            $errs = [];
            foreach ($apiData['ErrorDetails'] as $err) {
                if (isset($err['ErrorMessage'])) {
                    $errs[] = $err['ErrorMessage'];
                }
            }
            if (count($errs) > 0) {
                $errorMessage = implode(" | ", $errs);
            }
        } elseif (isset($apiData['message'])) {
            $errorMessage = $apiData['message'];
        }

        return ['success' => false, 'message' => 'API Error: ' . $errorMessage, 'response' => $apiData];
    }

    public function cancelCreditNoteEInvoice(\App\Models\CreditNote $creditNote, $cancelReason = '2', $cancelRemarks = 'Data Entry Mistake')
    {
        if (empty($creditNote->irn)) {
            return [
                'success' => false,
                'message' => 'No IRN found for this credit note.'
            ];
        }

        $setting = Setting::first();
        $authData = $this->authenticate($setting);
        if (!$authData['success']) {
            return $authData;
        }

        $payload = [
            "Irn" => $creditNote->irn,
            "CnlRsn" => (string)$cancelReason,
            "CnlRem" => substr(trim($cancelRemarks), 0, 100)
        ];

        $cancelUrl = env('EINV_API_URL') . '/Cancel';

        $response = Http::withHeaders([
            'aspid' => env('EINV_ASP_ID'),
            'password' => env('EINV_ASP_PASSWORD'),
            'Gstin' => env('EINV_GSTIN'),
            'User_Name' => env('EINV_USERNAME'),
            'AuthToken' => $authData['token'],
            'Content-Type' => 'application/json',
        ])->post($cancelUrl, $payload);

        $apiData = $response->json();

        if ($response->successful() && (isset($apiData['Status']) && $apiData['Status'] == 1 || isset($apiData['CancelDate']))) {
            \DB::table('credit_notes')->where('id', $creditNote->id)->update([
                'irn' => null,
                'ack_no' => null,
                'ack_date' => null,
                'signed_qr_code' => null,
                'eway_bill_no' => null,
                'eway_bill_date' => null,
                'eway_bill_valid_till' => null,
                'einvoice_status' => 'cancelled',
            ]);

            return [
                'success' => true,
                'message' => 'Credit Note E-Invoice cancelled successfully',
                'data' => $apiData
            ];
        }

        $errorMessage = 'Failed to cancel Credit Note E-Invoice';
        if (isset($apiData['ErrorDetails'][0]['ErrorMessage'])) {
            $errorMessage = $apiData['ErrorDetails'][0]['ErrorMessage'];
        } elseif (isset($apiData['message'])) {
            $errorMessage = $apiData['message'];
        }

        return ['success' => false, 'message' => 'API Error: ' . $errorMessage, 'response' => $apiData];
    }

    /**
     * Fetch TaxPro API Balance (GetApiBalance)
     */
    public function getApiBalance()
    {
        try {
            $aspId = env('EINV_ASP_ID');
            $aspPassword = env('EINV_ASP_PASSWORD');
            
            // Determine balance endpoint based on configured environment or explicit config
            $isSandbox = str_contains(env('EINV_AUTH_URL', ''), 'sandbox');
            $defaultUrl = $isSandbox 
                ? 'https://gstsandbox.charteredinfo.com/aspapi/v1.1/getapibalance'
                : 'https://einvapi.charteredinfo.com/aspapi/v1.1/getapibalance';
                
            $balanceUrl = env('EINV_BALANCE_URL', $defaultUrl);

            $response = Http::timeout(15)->withHeaders([
                'aspid' => $aspId,
                'password' => $aspPassword,
            ])->get($balanceUrl, [
                'aspid' => $aspId,
                'password' => $aspPassword,
            ]);

            // If sandbox URL returns error, try the primary production endpoint as fallback
            if (!$response->successful() && $isSandbox) {
                $response = Http::timeout(15)->withHeaders([
                    'aspid' => $aspId,
                    'password' => $aspPassword,
                ])->get('https://einvapi.charteredinfo.com/aspapi/v1.1/getapibalance', [
                    'aspid' => $aspId,
                    'password' => $aspPassword,
                ]);
            }

            $data = $response->json();

            if ($response->successful() && is_array($data)) {
                $ewbApiBal = $data['EWBApiBal'] ?? $data['ApiBal'] ?? $data['Balance'] ?? null;
                $ewbApiBalExpDt = $data['EWBApiBalExpDt'] ?? $data['ApiBalExpDt'] ?? $data['ExpDate'] ?? null;

                return [
                    'success' => true,
                    'ewb_api_bal' => $ewbApiBal,
                    'ewb_api_bal_exp_dt' => $ewbApiBalExpDt,
                    'raw_data' => $data,
                    'status_code' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? $data['ErrorMessage'] ?? 'Failed to retrieve API balance (HTTP ' . $response->status() . ')',
                'raw_data' => $data ?? $response->body(),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            \Log::error('TaxPro getApiBalance Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
            ];
        }
    }
}

