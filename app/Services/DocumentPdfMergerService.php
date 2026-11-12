<?php

namespace App\Services;

use App\Models\SalesInvoice;
use App\Models\Setting;
use App\Models\TransportBooking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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

        // Import Attached LR Image Pages (if uploaded on any transport booking for this invoice)
        $tmpAttachmentFiles = [];
        try {
            $hasLrImageCol = true;
            try {
                $hasLrImageCol = Schema::hasTable('transport_bookings') && Schema::hasColumn('transport_bookings', 'lr_image');
            } catch (\Throwable $schemaEx) {
                $hasLrImageCol = false;
            }

            if ($hasLrImageCol) {
                $lrBookings = TransportBooking::where('sales_invoice_id', $invoice->id)
                    ->whereNotNull('lr_image')
                    ->where('lr_image', '!=', '')
                    ->orderBy('box_index')
                    ->orderBy('id')
                    ->get();

                $groupedAttachments = [];
                foreach ($lrBookings as $tb) {
                    $imgPath = self::resolveLrImagePath($tb->lr_image);
                    if (!$imgPath) {
                        continue;
                    }
                    if (!isset($groupedAttachments[$imgPath])) {
                        $groupedAttachments[$imgPath] = [
                            'path' => $imgPath,
                            'lr_no' => $tb->lr_no ?: '-',
                            'transport_name' => $tb->preferred_transport ?: '-',
                            'booking_date' => $tb->booking_date ? \Carbon\Carbon::parse($tb->booking_date)->format('d-m-Y') : '-',
                            'boxes' => [],
                        ];
                    }
                    if (!empty($tb->box_number) && !in_array($tb->box_number, $groupedAttachments[$imgPath]['boxes'])) {
                        $groupedAttachments[$imgPath]['boxes'][] = $tb->box_number;
                    }
                }

                foreach ($groupedAttachments as $att) {
                    $renderedViaBlade = false;
                    $attachment = [
                        'lr_no' => $att['lr_no'],
                        'transport_name' => $att['transport_name'],
                        'booking_date' => $att['booking_date'],
                        'box_numbers' => !empty($att['boxes']) ? implode(', ', $att['boxes']) : '1/1',
                        'image_data_uri' => null,
                    ];

                    if (view()->exists('sales_invoice.lr_attachment_pdf')) {
                        try {
                            $imageDataUri = self::buildPdfImageDataUri($att['path']);
                            if ($imageDataUri) {
                                $attachment['image_data_uri'] = $imageDataUri;
                                $pdfLr = Pdf::loadView('sales_invoice.lr_attachment_pdf', compact('invoice', 'setting', 'attachment'));
                                $pdfLr->setPaper('A4', 'portrait');
                                $tmpLr = $tmpDir . '/' . $uniq . '_lr_' . count($tmpAttachmentFiles) . '.pdf';
                                file_put_contents($tmpLr, $pdfLr->output());
                                $tmpAttachmentFiles[] = $tmpLr;

                                $countLr = $fpdi->setSourceFile($tmpLr);
                                for ($p = 1; $p <= $countLr; $p++) {
                                    $tpl = $fpdi->importPage($p);
                                    $size = $fpdi->getTemplateSize($tpl);
                                    $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                                    $fpdi->useTemplate($tpl);
                                }
                                $renderedViaBlade = true;
                            }
                        } catch (\Throwable $bladeEx) {
                            Log::warning('LR attachment Blade PDF fallback triggered for Invoice #' . $invoice->id . ': ' . $bladeEx->getMessage());
                        }
                    }

                    if (!$renderedViaBlade) {
                        $tmpJpg = self::appendLrImagePageWithFpdi($fpdi, $invoice, $setting, $att, $tmpDir, $uniq . '_' . count($tmpAttachmentFiles));
                        if ($tmpJpg) {
                            $tmpAttachmentFiles[] = $tmpJpg;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('QR merged PDF LR attachment skipped for Invoice #' . $invoice->id . ': ' . $e->getMessage());
        }

        @unlink($tmpInv);
        @unlink($tmpDO);
        foreach ($tmpAttachmentFiles as $tmpLrFile) {
            @unlink($tmpLrFile);
        }

        return $fpdi->Output('S');
    }

    /**
     * Resolve an uploaded LR image path safely across local/live directory layouts.
     */
    private static function resolveLrImagePath(?string $fileName): ?string
    {
        if (empty($fileName)) {
            return null;
        }
        $cleanName = ltrim(str_replace('\\', '/', $fileName), '/');
        $relPath = 'uploads/transport_bookings/lr_images/' . $cleanName;
        $candidates = array_filter([
            public_path($relPath),
            base_path('public/' . $relPath),
            !empty($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/' . $relPath : null,
        ]);

        foreach ($candidates as $candidate) {
            if (file_exists($candidate) && is_file($candidate)) {
                return $candidate;
            }
        }
        return null;
    }

    /**
     * Fallback renderer using native FPDF/FPDI directly so attached LR images always appear even without DomPDF/view.
     */
    private static function appendLrImagePageWithFpdi(Fpdi $fpdi, $invoice, $setting, array $att, string $tmpDir, string $filePrefix): ?string
    {
        $jpegPath = null;
        $raw = @file_get_contents($att['path']);
        if ($raw === false) {
            return null;
        }

        $ext = strtolower(pathinfo($att['path'], PATHINFO_EXTENSION));
        if (function_exists('imagecreatefromstring')) {
            $img = @imagecreatefromstring($raw);
            if ($img !== false) {
                $origW = imagesx($img);
                $origH = imagesy($img);
                $maxDim = 1400;
                $scale = min($maxDim / max($origW, 1), $maxDim / max($origH, 1), 1);
                $newW = max(1, (int) round($origW * $scale));
                $newH = max(1, (int) round($origH * $scale));
                $canvas = imagecreatetruecolor($newW, $newH);
                $white = imagecolorallocate($canvas, 255, 255, 255);
                imagefill($canvas, 0, 0, $white);
                imagecopyresampled($canvas, $img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
                $jpegPath = $tmpDir . '/' . $filePrefix . '_lrimg.jpg';
                imagejpeg($canvas, $jpegPath, 88);
                imagedestroy($canvas);
                imagedestroy($img);
            }
        } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            $jpegPath = $att['path'];
        }

        if (!$jpegPath || !file_exists($jpegPath)) {
            return null;
        }

        $fpdi->AddPage('P', 'A4');
        $fpdi->SetMargins(12, 12, 12);
        $fpdi->SetXY(12, 12);
        $fpdi->SetFont('Helvetica', 'B', 13);
        $fpdi->SetTextColor(76, 29, 149);
        $fpdi->Cell(0, 7, (string) ($setting->company_name ?? 'NACHIAS FASHION PRIVATE LIMITED'), 0, 1, 'L');

        $fpdi->SetFont('Helvetica', 'B', 10);
        $fpdi->SetTextColor(17, 24, 39);
        $fpdi->Cell(0, 6, 'ATTACHED LORRY RECEIPT (LR) / DISPATCH COPY', 0, 1, 'L');

        $fpdi->SetFont('Helvetica', '', 9);
        $fpdi->SetTextColor(55, 65, 81);
        $custName = $invoice->customer->name ?? '-';
        $boxesText = !empty($att['boxes']) ? implode(', ', $att['boxes']) : '1/1';
        $metaLine = 'Invoice: ' . ($invoice->inv_no ?? '-') . '  |  LR No: ' . ($att['lr_no'] ?? '-') . '  |  Transport: ' . ($att['transport_name'] ?? '-') . '  |  Box: ' . $boxesText;
        $fpdi->Cell(0, 5, $metaLine, 0, 1, 'L');
        $fpdi->Cell(0, 5, 'Customer: ' . $custName . '  |  Dispatched: ' . ($att['booking_date'] ?? '-'), 0, 1, 'L');

        $yStart = 38;
        $fpdi->SetDrawColor(203, 213, 225);
        $fpdi->Line(12, $yStart - 2, 198, $yStart - 2);

        $imgInfo = @getimagesize($jpegPath);
        if ($imgInfo && $imgInfo[0] > 0 && $imgInfo[1] > 0) {
            $maxW = 186; // mm on A4 portrait (210 - 24 margins)
            $maxH = 242; // mm remaining height
            $ratio = min($maxW / $imgInfo[0], $maxH / $imgInfo[1]);
            $drawW = $imgInfo[0] * $ratio;
            $drawH = $imgInfo[1] * $ratio;
            $xPos = 12 + (($maxW - $drawW) / 2);
            $fpdi->Image($jpegPath, $xPos, $yStart, $drawW, $drawH);
        }

        return ($jpegPath !== $att['path']) ? $jpegPath : null;
    }

    /**
     * Convert an image file on disk into a DomPDF-safe base64 data URI, scaling down large photos to save memory.
     */
    private static function buildPdfImageDataUri(string $filePath): ?string
    {
        $raw = @file_get_contents($filePath);
        if ($raw === false) {
            return null;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Downscale large images or convert WebP/GIF via GD to keep DomPDF memory usage low
        if (function_exists('imagecreatefromstring')) {
            $img = @imagecreatefromstring($raw);
            if ($img !== false) {
                $origW = imagesx($img);
                $origH = imagesy($img);
                $maxDim = 1200;
                $needsConversion = !in_array($ext, ['jpg', 'jpeg', 'png'], true);

                if ($origW > $maxDim || $origH > $maxDim || $needsConversion) {
                    $scale = min($maxDim / max($origW, 1), $maxDim / max($origH, 1), 1);
                    $newW = max(1, (int) round($origW * $scale));
                    $newH = max(1, (int) round($origH * $scale));
                    $resized = imagecreatetruecolor($newW, $newH);
                    $white = imagecolorallocate($resized, 255, 255, 255);
                    imagefill($resized, 0, 0, $white);
                    imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
                    ob_start();
                    imagejpeg($resized, null, 85);
                    $jpgData = ob_get_clean();
                    imagedestroy($resized);
                    imagedestroy($img);
                    if (!empty($jpgData)) {
                        return 'data:image/jpeg;base64,' . base64_encode($jpgData);
                    }
                } else {
                    imagedestroy($img);
                }
            }
        }

        if ($ext === 'jpg' || $ext === 'jpeg') {
            return 'data:image/jpeg;base64,' . base64_encode($raw);
        }
        if ($ext === 'png') {
            return 'data:image/png;base64,' . base64_encode($raw);
        }
        if ($ext === 'gif') {
            return 'data:image/gif;base64,' . base64_encode($raw);
        }

        return null;
    }
}

