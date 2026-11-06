<?php

namespace App\Mail;

use App\Models\TransportBooking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\DocumentPdfMergerService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Common\ErrorCorrectionLevel;

class DispatchCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $cardData;

    /**
     * Create a new message instance.
     */
    public function __construct(TransportBooking $booking, array $cardData = [])
    {
        $this->booking = $booking;
        $this->cardData = !empty($cardData) ? $cardData : self::prepareCardData($booking);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $invNo = $this->cardData['invoice_no'];
        $customerName = $this->cardData['customer_name'];
        $subject = "See You Soon! Your Package is Out for Delivery [{$invNo}] - Casino";

        $pdf = Pdf::loadView('transport_bookings.dispatch_card_pdf', $this->cardData)->setPaper('a4', 'portrait');
        $pdfFileName = "Casino_Dispatch_Notification_" . str_replace(['/', '\\', ' '], '_', $invNo) . ".pdf";

        return $this->subject($subject)
            ->view('emails.dispatch_card')
            ->with($this->cardData)
            ->attachData($pdf->output(), $pdfFileName, [
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Helper to prepare all computed display variables for email, web view & PDF.
     */
    public static function prepareCardData(TransportBooking $booking)
    {
        $booking->loadMissing(['salesInvoice.items', 'customer.city', 'customer.place', 'deliveryAgent']);

        $salesInvoice = $booking->salesInvoice;
        $customer = $booking->customer;

        $items = $salesInvoice ? $salesInvoice->items : collect();
        $totalQty = (int)$items->sum('quantity');
        $distinctArticles = $items->pluck('art_no')->filter()->unique()->count();

        $totalBoxesCount = TransportBooking::where('sales_invoice_id', $booking->sales_invoice_id)->count();
        if ($totalBoxesCount === 0) {
            $totalBoxesCount = $booking->total_boxes ?: 1;
        }

        $boxDisplay = $booking->box_number ?: '1 / 1';
        if (!str_contains($boxDisplay, '/')) {
            $boxDisplay = "{$boxDisplay} / {$totalBoxesCount}";
        }

        $baseRoot = (!app()->runningInConsole() && request()->getHost()) ? request()->getSchemeAndHttpHost() : config('app.url');
        $docUrl = $salesInvoice ? DocumentPdfMergerService::getEncryptedDocUrl($salesInvoice, $baseRoot) : url('/');
        $encryptedToken = $salesInvoice ? basename($docUrl) : '';

        $qrSvg = '';
        $qrBase64 = self::generateCasinoQrCode($docUrl);
        if (empty($qrBase64)) {
            try {
                $qrSvg = QrCode::size(130)->margin(0)->generate($docUrl);
                $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
            } catch (\Exception $e) {
                \Log::error('QR code generation error: ' . $e->getMessage());
            }
        }

        $cityOrPlace = $customer && $customer->city ? $customer->city->city_name : ($customer && $customer->place ? $customer->place->place_name : '');
        $destination = (!empty($booking->booking_office) && $booking->booking_office !== 'None')
            ? $booking->booking_office
            : ($cityOrPlace ?: 'Tamil Nadu');

        $driverName = $booking->deliveryAgent ? $booking->deliveryAgent->name : ($booking->delivery_agent_name ?: 'Transport Driver');

        $toBase64 = function ($relPath) {
            $full = public_path($relPath);
            if (!file_exists($full)) return '';
            $ext = pathinfo($full, PATHINFO_EXTENSION);
            $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($full));
        };

        return [
            'booking' => $booking,
            'salesInvoice' => $salesInvoice,
            'customer' => $customer,
            'customer_name' => $customer ? $customer->name : 'Valued Customer',
            'customer_email' => $customer ? ($customer->email ?: $customer->contact_email) : null,
            'customer_phone' => $customer ? ($customer->mobile_no ?: $customer->contact_mobile_no) : '',
            'invoice_no' => $salesInvoice ? $salesInvoice->inv_no : 'INV-0000',
            'invoice_date' => $salesInvoice && $salesInvoice->inv_date ? \Carbon\Carbon::parse($salesInvoice->inv_date)->format('d-m-Y') : date('d-m-Y'),
            'cartons' => $boxDisplay,
            'total_qty' => $totalQty > 0 ? $totalQty : (int)$booking->quantity,
            'articles_count' => $distinctArticles > 0 ? $distinctArticles : 1,
            'lr_no' => $booking->lr_no ?: 'PENDING-LR',
            'transporter_name' => $booking->preferred_transport ?: 'Direct Transport',
            'destination' => $destination,
            'driver_name' => $driverName,
            'doc_url' => $docUrl,
            'qr_svg' => $qrSvg,
            'qr_base64' => $qrBase64,
            'invoice_pdf_url' => $salesInvoice ? "{$baseRoot}/doc/pdf/invoice/{$encryptedToken}" : '#',
            'delivery_challan_url' => $salesInvoice ? "{$baseRoot}/doc/pdf/delivery-order/{$encryptedToken}" : '#',
            'img_header' => $toBase64('assets/images/dispatch_exact/full_header.jpg'),
            'img_store' => $toBase64('assets/images/dispatch_exact/icon_store.png'),
            'img_invoice' => $toBase64('assets/images/dispatch_exact/icon_invoice.png'),
            'img_cartons' => $toBase64('assets/images/dispatch_exact/icon_cartons.png'),
            'img_shirt' => $toBase64('assets/images/dispatch_exact/icon_shirt.png'),
            'img_articles' => $toBase64('assets/images/dispatch_exact/icon_articles.png'),
            'img_truck' => $toBase64('assets/images/dispatch_exact/icon_truck.png'),
            'img_qr_inv' => $toBase64('assets/images/dispatch_exact/qr_icon_invoice.png'),
            'img_qr_challan' => $toBase64('assets/images/dispatch_exact/qr_icon_challan.png'),
            'img_qr_carton' => $toBase64('assets/images/dispatch_exact/qr_icon_carton.png'),
            'img_qr_lr' => $toBase64('assets/images/dispatch_exact/qr_icon_lr.png'),
            'img_footer_box' => $toBase64('assets/images/dispatch_exact/icon_footer_box.png'),
            'casino_logo' => $toBase64('assets/images/delivery_order/casino_header_logo.png'),
        ];
    }

    /**
     * Helper to draw a filled rectangle with 4 rounded corners.
     */
    private static function drawFilledRoundedRect($im, $x1, $y1, $x2, $y2, $rad, $col): void
    {
        imagefilledrectangle($im, $x1 + $rad, $y1, $x2 - $rad, $y2, $col);
        imagefilledrectangle($im, $x1, $y1 + $rad, $x2, $y2 - $rad, $col);
        imagefilledarc($im, $x1 + $rad, $y1 + $rad, $rad * 2, $rad * 2, 180, 270, $col, IMG_ARC_PIE);
        imagefilledarc($im, $x2 - $rad, $y1 + $rad, $rad * 2, $rad * 2, 270, 360, $col, IMG_ARC_PIE);
        imagefilledarc($im, $x2 - $rad, $y2 - $rad, $rad * 2, $rad * 2, 0, 90, $col, IMG_ARC_PIE);
        imagefilledarc($im, $x1 + $rad, $y2 - $rad, $rad * 2, $rad * 2, 90, 180, $col, IMG_ARC_PIE);
    }

    /**
     * Helper to draw a smooth scanner bracket corner with rounded bend and rounded caps.
     */
    private static function drawScannerCorner($im, $cx, $cy, $rad, $armLen, $startDeg, $endDeg, $thick, $col): void
    {
        imagesetthickness($im, $thick);
        for ($deg = $startDeg; $deg <= $endDeg; $deg += 1) {
            $radVal = deg2rad($deg);
            $px = $cx + ($rad * cos($radVal));
            $py = $cy + ($rad * sin($radVal));
            imagefilledellipse($im, (int)round($px), (int)round($py), $thick, $thick, $col);
        }
        if ($startDeg == 180 && $endDeg == 270) {
            imageline($im, $cx, $cy - $rad, $cx + $armLen, $cy - $rad, $col);
            imagefilledellipse($im, $cx + $armLen, $cy - $rad, $thick, $thick, $col);
            imageline($im, $cx - $rad, $cy, $cx - $rad, $cy + $armLen, $col);
            imagefilledellipse($im, $cx - $rad, $cy + $armLen, $thick, $thick, $col);
        } elseif ($startDeg == 270 && $endDeg == 360) {
            imageline($im, $cx, $cy - $rad, $cx - $armLen, $cy - $rad, $col);
            imagefilledellipse($im, $cx - $armLen, $cy - $rad, $thick, $thick, $col);
            imageline($im, $cx + $rad, $cy, $cx + $rad, $cy + $armLen, $col);
            imagefilledellipse($im, $cx + $rad, $cy + $armLen, $thick, $thick, $col);
        } elseif ($startDeg == 90 && $endDeg == 180) {
            imageline($im, $cx, $cy + $rad, $cx + $armLen, $cy + $rad, $col);
            imagefilledellipse($im, $cx + $armLen, $cy + $rad, $thick, $thick, $col);
            imageline($im, $cx - $rad, $cy, $cx - $rad, $cy - $armLen, $col);
            imagefilledellipse($im, $cx - $rad, $cy - $armLen, $thick, $thick, $col);
        } elseif ($startDeg == 0 && $endDeg == 90) {
            imageline($im, $cx, $cy + $rad, $cx - $armLen, $cy + $rad, $col);
            imagefilledellipse($im, $cx - $armLen, $cy + $rad, $thick, $thick, $col);
            imageline($im, $cx + $rad, $cy, $cx + $rad, $cy - $armLen, $col);
            imagefilledellipse($im, $cx + $rad, $cy - $armLen, $thick, $thick, $col);
        }
    }

    /**
     * Generate branded Casino QR code with 4 corner brackets and centered Casino logo badge.
     * High resolution, 4-module quiet zone, and proportional badge for 100% reliable scanning.
     */
    public static function generateCasinoQrCode(string $url): string
    {
        try {
            $qr = Encoder::encode($url, ErrorCorrectionLevel::H(), 'UTF-8');
            $matrix = $qr->getMatrix();
            $matrixW = $matrix->getWidth();
            $matrixH = $matrix->getHeight();

            $canvasSize = 480;
            $img = imagecreatetruecolor($canvasSize, $canvasSize);
            imagealphablending($img, false);
            imagesavealpha($img, true);

            $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
            $white = imagecolorallocate($img, 255, 255, 255);
            $blue = imagecolorallocate($img, 0, 88, 179); // #0058b3
            $black = imagecolorallocate($img, 15, 23, 42); // #0f172a

            imagefill($img, 0, 0, $transparent);
            imagealphablending($img, true);

            // 1. White rounded card in center
            $cInset = 24;
            $cRad = 20;
            self::drawFilledRoundedRect($img, $cInset, $cInset, $canvasSize - $cInset, $canvasSize - $cInset, $cRad, $white);

            // 2. QR Modules rendering with proper quiet zone
            $availableSize = $canvasSize - ($cInset * 2) - 20;
            $scale = (int)floor($availableSize / $matrixW);
            if ($scale < 3) $scale = 3;
            $actualQrSize = $scale * $matrixW;
            $qrOffsetX = (int)floor(($canvasSize - $actualQrSize) / 2);
            $qrOffsetY = (int)floor(($canvasSize - $actualQrSize) / 2);

            for ($my = 0; $my < $matrixH; $my++) {
                for ($mx = 0; $mx < $matrixW; $mx++) {
                    if ($matrix->get($mx, $my) == 1) {
                        $px = $qrOffsetX + ($mx * $scale);
                        $py = $qrOffsetY + ($my * $scale);
                        imagefilledrectangle($img, $px, $py, $px + $scale - 1, $py + $scale - 1, $black);
                    }
                }
            }

            // 3. Compact Center "CASINO" Badge (<= 17% of QR size so error correction is never exceeded)
            $badgeW = max(44, (int)round($actualQrSize * 0.16));
            $badgeH = max(44, (int)round($actualQrSize * 0.16));
            $badgeRad = 8;
            $badgePad = 4;

            $badgeX = (int)(($canvasSize - $badgeW) / 2);
            $badgeY = (int)(($canvasSize - $badgeH) / 2);

            // Outer white rounded border
            self::drawFilledRoundedRect($img, $badgeX - $badgePad, $badgeY - $badgePad, $badgeX + $badgeW + $badgePad, $badgeY + $badgeH + $badgePad, $badgeRad + 2, $white);
            // Inner blue rounded badge
            self::drawFilledRoundedRect($img, $badgeX, $badgeY, $badgeX + $badgeW, $badgeY + $badgeH, $badgeRad, $blue);

            $font = public_path('assets/fonts/Montserrat-Bold.ttf');
            if (!file_exists($font)) {
                $font = public_path('assets/fonts/Poppins-Bold.ttf');
            }
            if (!file_exists($font)) {
                $font = public_path('assets/fonts/Montserrat-Black.ttf');
            }
            if (file_exists($font)) {
                $text = 'CASINO';
                $fontSize = max(7, (int)round($badgeW * 0.18));
                $bbox = imagettfbbox($fontSize, 0, $font, $text);
                $textW = $bbox[2] - $bbox[0];
                $textH = $bbox[1] - $bbox[7];
                $textX = $badgeX + (int)(($badgeW - $textW) / 2);
                $textY = $badgeY + (int)(($badgeH + $textH) / 2) - 1;
                imagettftext($img, $fontSize, 0, $textX, $textY, $white, $font, $text);
            }

            // 4. 4 Blue Scanner Corner Brackets outside the white card
            $bPad = 8;
            $bRad = 16;
            $bArm = 34;
            $thick = 7;

            // Top-Left
            self::drawScannerCorner($img, $bPad + $bRad, $bPad + $bRad, $bRad, $bArm, 180, 270, $thick, $blue);
            // Top-Right
            self::drawScannerCorner($img, $canvasSize - $bPad - $bRad, $bPad + $bRad, $bRad, $bArm, 270, 360, $thick, $blue);
            // Bottom-Left
            self::drawScannerCorner($img, $bPad + $bRad, $canvasSize - $bPad - $bRad, $bRad, $bArm, 90, 180, $thick, $blue);
            // Bottom-Right
            self::drawScannerCorner($img, $canvasSize - $bPad - $bRad, $canvasSize - $bPad - $bRad, $bRad, $bArm, 0, 90, $thick, $blue);

            ob_start();
            imagepng($img);
            $png = ob_get_clean();
            imagedestroy($img);

            return 'data:image/png;base64,' . base64_encode($png);
        } catch (\Exception $e) {
            \Log::error('Casino QR generation error: ' . $e->getMessage());
            return '';
        }
    }
}
