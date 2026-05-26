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
        $isInterState = $sellerStateCode !== $buyerStateCode;

        $rndOff = (float) ($invoice->round_off ?? 0);
        if (strtolower($invoice->round_off_type ?? '') === 'less') {
            $rndOff = -$rndOff;
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
                "Stcd" => $setting->state->state_code,
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
            "ItemList" => [],
            "ValDtls" => [
                "AssVal" => (float) number_format((float) ($invoice->taxable_amount ?? $invoice->total), 2, '.', ''),
                "CgstVal" => $isInterState ? 0.00 : (float) number_format((float) $invoice->cgst, 2, '.', ''),
                "SgstVal" => $isInterState ? 0.00 : (float) number_format((float) $invoice->sgst, 2, '.', ''),
                "IgstVal" => $isInterState ? (float) number_format((float) $invoice->igst, 2, '.', '') : 0.00,
                "OthChrg" => (float) number_format((float) ($invoice->other_charges ?? 0.00), 2, '.', ''),
                "RndOffAmt" => (float) number_format((float) $rndOff, 2, '.', ''),
                "TotInvVal" => (float) number_format((float) $invoice->grand_total, 2, '.', ''),
            ]
        ];

        $slNo = 1;

        foreach ($invoice->items as $item) {
            $taxRate = $isInterState ? (float) $invoice->igst_percent : (float) ($invoice->cgst_percent + $invoice->sgst_percent);

            $totAmt = (float) number_format((float) $item->amount, 2, '.', '');
            $itemDiscount = (float) number_format(($totAmt * (float) ($invoice->discount_percent ?? 0)) / 100, 2, '.', '');
            $assAmt = (float) number_format($totAmt - $itemDiscount, 2, '.', '');
            
            $cgstAmt = $isInterState ? 0.00 : (float) number_format(($assAmt * (float) $invoice->cgst_percent) / 100, 2, '.', '');
            $sgstAmt = $isInterState ? 0.00 : (float) number_format(($assAmt * (float) $invoice->sgst_percent) / 100, 2, '.', '');
            $igstAmt = $isInterState ? (float) number_format(($assAmt * (float) $invoice->igst_percent) / 100, 2, '.', '') : 0.00;
            $totItemVal = (float) number_format($assAmt + $cgstAmt + $sgstAmt + $igstAmt, 2, '.', '');

            $prdDesc = substr(
                $item->stockEntryItem->finished_item_code ??
                $item->item->code ??
                $item->item->name ??
                'Product',
                0,
                30
            );

            $payload['ItemList'][] = [
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
        if (isset($apiData['ErrorDetails'][0]['ErrorMessage'])) {
            $errorMessage = $apiData['ErrorDetails'][0]['ErrorMessage'];
        } elseif (isset($apiData['message'])) {
            $errorMessage = $apiData['message'];
        }

        return ['success' => false, 'message' => 'API Error: ' . $errorMessage, 'response' => $apiData];
    }
    public function authenticate($setting)
    {
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

        $errorMessage = $data['ErrorDetails'][0]['ErrorMessage'] ?? $data['message'] ?? 'Unknown Error';
        return ['success' => false, 'message' => 'E-Invoice Authentication failed: ' . $errorMessage];
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

        $sellerStateCode = $setting->state->state_code;
        $buyerStateCode  = $invoice->customer->state->state_code ?? '33';
        $isInterState    = $sellerStateCode !== $buyerStateCode;

        $taxableAmount = (float) ($invoice->taxable_amount ?? $invoice->total);
        $totalValue    = (float) $invoice->grand_total;

        $cleanFromAddr = preg_replace('/\s+/', ' ', trim($setting->address ?? 'NA'));
        $cleanToAddr   = preg_replace('/\s+/', ' ', trim($invoice->customer->address ?? 'NA'));

        $itemList = [];
        foreach ($invoice->items as $idx => $item) {
            $totAmt = (float) number_format((float) $item->amount, 2, '.', '');
            $itemDiscount = (float) number_format(($totAmt * (float) ($invoice->discount_percent ?? 0)) / 100, 2, '.', '');
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
}
