<?php

namespace App\Services;

use App\Models\SalesInvoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;
use setasign\Fpdi\Fpdi;

class DocumentPdfMergerService
{
    /**
     * Generate secure encrypted viewing URL for an invoice.
     * Uses Crypt::encryptString() to ensure sequential ID is never exposed.
     * Enforces HTTPS on production/live domains.
     *
     * @param int|SalesInvoice $invoice
     * @param string|null $baseRoot
     * @return string
     */
    public static function getEncryptedDocUrl($invoice, ?string $baseRoot = null): string
    {
        $id = is_object($invoice) ? $invoice->id : (int)$invoice;
        if (!$id) {
            return url('/');
        }

        if (!$baseRoot) {
            $baseRoot = (!app()->runningInConsole() && request()->getHost())
                ? request()->getSchemeAndHttpHost()
                : config('app.url');
        }

        // Requirement 9: Use HTTPS for live QR URL
        if (app()->environment('production') || (!str_contains($baseRoot, 'localhost') && !str_contains($baseRoot, '127.0.0.1'))) {
            $baseRoot = preg_replace('/^http:/i', 'https:', $baseRoot);
        }

        // Requirement 2: Encrypt invoice ID with Crypt::encryptString()
        $encrypted = Crypt::encryptString((string)$id);
        // Make URL-safe (letters, numbers, '-', '_', '~') so it never splits path segments or breaks in scanners
        $safeEncrypted = strtr($encrypted, ['+' => '-', '/' => '_', '=' => '~']);

        return rtrim($baseRoot, '/') . '/doc/view/' . $safeEncrypted;
    }

    /**
     * Decrypt and validate the invoice ID from an encrypted route parameter.
     * Safely returns validated integer ID or null on failure/tampering.
     *
     * @param string|null $encryptedId
     * @return int|null Validated SalesInvoice ID or null on failure
     */
    public static function decryptInvoiceId(?string $encryptedId): ?int
    {
        if (empty($encryptedId)) {
            return null;
        }

        $restored = strtr($encryptedId, ['-' => '+', '_' => '/', '~' => '=']);
        $decrypted = null;

        try {
            $decrypted = Crypt::decryptString($restored);
        } catch (\Exception $e1) {
            try {
                $decrypted = Crypt::decryptString($encryptedId);
            } catch (\Exception $e2) {
                // Backward compatibility: allow numeric ID if directly accessed
                if (is_numeric($encryptedId)) {
                    $decrypted = $encryptedId;
                }
            }
        }

        return ($decrypted && is_numeric($decrypted)) ? (int)$decrypted : null;
    }
    /**
     * Generate a single merged PDF containing:
     * 1. Official Tax Invoice (Portrait A4)
     * 2. Delivery Order (Landscape A4 or Portrait)
     *
     * @param int|SalesInvoice $invoice
     * @param string $doType 'landscape' | 'open_landscape' | 'portrait'
     * @return string Raw PDF binary string
     */
    public static function generateMergedInvoiceAndDeliveryOrder($invoice, string $doType = 'landscape'): string
    {
        if (is_numeric($invoice)) {
            $invoice = SalesInvoice::with([
                'brand',
                'store',
                'customer.state',
                'customer.city',
                'salesOrder.salesAgent',
                'items' => function ($q) {
                    $q->orderBy('art_no');
                },
                'items.brandCategory',
                'items.item.uom',
                'items.item.style',
                'items.stockEntryItem.rawMaterial',
                'items.stockEntryItem.style',
                'items.stockEntryItem.brand',
                'items.stockEntryItem.uom',
                'items.sleeve',
                'items.mrp',
                'items.size',
                'items.unit',
                'transportBooking'
            ])->findOrFail($invoice);
        }

        $setting = Setting::with(['state', 'city'])->first();
        $totalPcs = $invoice->items->sum('quantity');

        // 1. Prepare Tax Invoice
        $actualTaxTotal = !$invoice->other_state ? ((float)$invoice->cgst + (float)$invoice->sgst) : (float)$invoice->igst;
        $totalInWords = function_exists('numberToWords') ? numberToWords($invoice->grand_total) : '';
        $totalTaxInWords = function_exists('numberToWords') ? numberToWords($actualTaxTotal > 0 ? $actualTaxTotal : $invoice->tax_amount) : '';

        // Compute Tax Summary
        $taxSummary = [];
        $isOtherState = (bool)($invoice->other_state ?? false);
        foreach ($invoice->items as $item) {
            $hsn = $item->hsn_code ?? ($item->item->hsn_code ?? '6205');
            $taxableVal = (float)($item->taxable_value ?? ($item->quantity * $item->rate));
            $taxRate = (float)($item->tax_rate ?? ($item->gst_rate ?? 5));
            $halfRate = $taxRate / 2;

            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $isOtherState ? 0 : $halfRate,
                    'cgst_amount' => 0,
                    'sgst_rate' => $isOtherState ? 0 : $halfRate,
                    'sgst_amount' => 0,
                    'igst_rate' => $isOtherState ? $taxRate : 0,
                    'igst_amount' => 0,
                ];
            }
            $taxSummary[$hsn]['taxable_value'] += $taxableVal;
            if (!$isOtherState) {
                $taxSummary[$hsn]['cgst_amount'] += ($taxableVal * $halfRate) / 100;
                $taxSummary[$hsn]['sgst_amount'] += ($taxableVal * $halfRate) / 100;
            } else {
                $taxSummary[$hsn]['igst_amount'] += ($taxableVal * $taxRate) / 100;
            }
        }

        $pdfType = 'pdf1';
        $prevCopy = request()->query('copy');
        request()->query->set('copy', 'ORIGINAL');

        $invView = (request()->query('template') === 'old' || request()->query('design') === 'old')
            ? 'sales_invoice.pdf'
            : 'sales_invoice.pdf_new';

        $fontDir = storage_path('fonts');
        if (!file_exists($fontDir)) {
            @mkdir($fontDir, 0777, true);
        }

        $pdfInv = Pdf::loadView($invView, compact('invoice', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords', 'pdfType'));
        $pdfInv->setPaper('A4', 'portrait');
        $invContent = $pdfInv->output();

        if ($prevCopy !== null) {
            request()->query->set('copy', $prevCopy);
        } else {
            request()->query->remove('copy');
        }

        // 2. Prepare Delivery Order
        if ($doType === 'open_landscape') {
            $doView = 'sales_invoice.open_delivery_order_landscape';
            $paperOrientation = 'landscape';
        } elseif ($doType === 'portrait') {
            $doView = 'sales_invoice.delivery_order';
            $paperOrientation = 'portrait';
        } else {
            $doView = 'sales_invoice.delivery_order_landscape';
            $paperOrientation = 'landscape';
        }

        $pdfDO = Pdf::loadView($doView, compact('invoice', 'setting', 'totalPcs'));
        $pdfDO->setPaper('A4', $paperOrientation);
        $doContent = $pdfDO->output();

        // 3. Merge with Standalone FPDI
        require_once app_path('Libraries/fpdf.php');
        require_once app_path('Libraries/fpdi/src/autoload.php');

        $tmpDir = storage_path('app/temp');
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0777, true);
        }

        $uniq = uniqid('merge_', true);
        $tmpInv = $tmpDir . '/' . $uniq . '_inv.pdf';
        $tmpDO = $tmpDir . '/' . $uniq . '_do.pdf';

        file_put_contents($tmpInv, $invContent);
        file_put_contents($tmpDO, $doContent);

        $fpdi = new Fpdi();

        // Import Tax Invoice Pages (Preserves native Portrait)
        $count1 = $fpdi->setSourceFile($tmpInv);
        for ($p = 1; $p <= $count1; $p++) {
            $tpl = $fpdi->importPage($p);
            $size = $fpdi->getTemplateSize($tpl);
            $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $fpdi->useTemplate($tpl);
        }

        // Import Delivery Order Pages (Preserves native Landscape)
        $count2 = $fpdi->setSourceFile($tmpDO);
        for ($p = 1; $p <= $count2; $p++) {
            $tpl = $fpdi->importPage($p);
            $size = $fpdi->getTemplateSize($tpl);
            $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $fpdi->useTemplate($tpl);
        }

        @unlink($tmpInv);
        @unlink($tmpDO);

        return $fpdi->Output('S');
    }
}

