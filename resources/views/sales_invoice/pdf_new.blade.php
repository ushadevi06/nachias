<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Invoice - {{ $invoice->inv_no }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;500;600;700;800&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

@php
    $isDomPdf = false;
    foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
        if (isset($trace['class']) && (str_contains($trace['class'], 'Dompdf') || str_contains($trace['class'], 'Barryvdh\DomPDF'))) {
            $isDomPdf = true;
            break;
        }
    }

    $robotoRegularPath = $isDomPdf 
        ? str_replace('\\', '/', public_path('assets/fonts/Roboto-Regular.ttf'))
        : asset('assets/fonts/Roboto-Regular.ttf');
    $robotoBoldPath = $isDomPdf 
        ? str_replace('\\', '/', public_path('assets/fonts/Roboto-Bold.ttf'))
        : asset('assets/fonts/Roboto-Bold.ttf');
@endphp

<style>
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: normal;
        src: url('{{ $robotoRegularPath }}') format('truetype');
    }
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 400;
        src: url('{{ $robotoRegularPath }}') format('truetype');
    }
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: bold;
        src: url('{{ $robotoBoldPath }}') format('truetype');
    }
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 700;
        src: url('{{ $robotoBoldPath }}') format('truetype');
    }
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 800;
        src: url('{{ $robotoBoldPath }}') format('truetype');
    }
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 900;
        src: url('{{ $robotoBoldPath }}') format('truetype');
    } 

        @page {
            margin: 16px 16px 10px 16px;
        }
        body {
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 10px;
            font-weight: 400;
            color: #01316f;
            margin: 0;
            padding: 0;
            letter-spacing: -0.15px;
        }
        .container {
            padding: 2px 4px;
            max-width: 1000px;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        th, td {
            vertical-align: top;
            color: #01316f;
            font-family: 'Roboto', Arial, sans-serif;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .bold { font-weight: 700 !important; }

        /* Items Table */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 5px;
            border: 1.5px solid #01316f;
        }
        .item-table th {
            background-color: #edf4fc;
            color: #01316f;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #01316f;
            padding: 5.5px 4px;
        }
        .item-table td {
            border: 1px solid #01316f;
            padding: 2px 3px;
            font-size: 11px;
            font-weight: 400;
            color: #01316f;
        }
        .item-table tr:nth-child(even) td{
            background: #f4f6f9
        }
        .item-table tr.total-row td {
            border: 1.5px solid #01316f;
            padding: 5.5px 4px;
            font-size: 11px;
            font-weight: 700;
            color: #01316f;
            background: #edf4fc
        }

        /* Summary Tables */
        .tax-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            border: 1px solid #01316f;
        }
        .tax-table td {
            border: 1px solid #01316f;
            padding: 2.5px 5px;
            color: #01316f;
        }
        .grand-total-box {
            background-color: #01316f;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 12px;
            padding: 6px 8px;
        }
        .qr-code {
            max-width: 100px;
        }

        /* Footer */
        .footer-page {
            position: fixed;
            bottom: -6px;
            left: 0;
            right: 0;
            height: 14px;
            font-size: 8px;
            color: #555555;
            border-top: 1px solid #C5D9EB;
            padding-top: 2px;
            font-family: 'Roboto', Arial, sans-serif;
        }
        .footer-page-left { position: absolute; left: 6px; }
        .footer-page-right { position: absolute; right: 6px; }
        .page-num:after { content: counter(page); }
        .page-total:after { content: counter(pages); }

        @media print {
            body { margin: 0; padding: 0; }
            .container { padding: 0; }
        }
        .comp-name{
            font-family: 'Roboto', Arial, sans-serif;
            font-weight: bold;
        }
        .logo-wrapper{
            display: flex;
            align-items: center; 
            flex-wrap: nowrap;
        }
        .logo-wrapper span{
            font-size: 45px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -3px;
            font-family: 'Roboto-Bold', Arial, sans-serif;
        }
    </style>
</head>
<body>
@php
// Copies configuration
$reqCopy = request()->query('copy');
if ($reqCopy) {
    $copies = [strtoupper($reqCopy)];
} else {
    $copies = ['ORIGINAL', 'DUPLICATE', 'TRIPLICATE'];
}

// Compare Bill To & Ship To addresses:
$cust = $invoice->customer;
$billAddr1 = trim($cust->address_line_1 ?? '');
$billAddr2 = trim($cust->address_line_2 ?? '');
$billAddr3 = trim($cust->address_line_3 ?? '');
$billCity  = trim($cust->city->city_name ?? '');
$billZip   = trim($cust->zip_code ?? '');
$billState = trim($cust->state->state_name ?? '');
$billStateCode = trim($cust->state->state_code ?? '');
$billGst   = trim($cust->gst_no ?? '');

$billFullString = strtoupper(preg_replace('/\s+/', ' ', implode(' ', array_filter([$billAddr1, $billAddr2, $billAddr3, $billCity, $billZip]))));
$shipRaw = strtoupper(preg_replace('/\s+/', ' ', trim($invoice->delivery_address ?? '')));

// Robust comparison: remove all punctuation/whitespace to compare addresses
$cleanStr = function($s) {
    return strtoupper(preg_replace('/[^A-Z0-9]/', '', (string)$s));
};
$billClean = $cleanStr($billFullString);
$shipClean = $cleanStr($shipRaw);

$isSameAddress = empty($shipClean) || ($shipClean === $billClean) || 
    (!empty($shipClean) && !empty($billClean) && (str_contains($shipClean, $billClean) || str_contains($billClean, $shipClean)));

// Brand / Company Logo
$logoPath = public_path('assets/images/doc-icon.png');
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoData = file_get_contents($logoPath);
    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
}

// Nachias Brand Logo (Blue version matching invoice theme)
$nachiasLogoPath = public_path('assets/images/logo_2_blue.png');
if (!file_exists($nachiasLogoPath)) {
    $nachiasLogoPath = public_path('assets/images/logo_2.png');
}
$nachiasLogoBase64 = '';
if (file_exists($nachiasLogoPath)) {
    $nachiasLogoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($nachiasLogoPath));
}

// E-Invoice QR Code (only when E-Invoice is generated and not cancelled)
$einvoiceQr = '';
if (($invoice->einvoice_status ?? '') !== 'cancelled') {
    if (!empty($invoice->signed_qr_code)) {
        try {
            $einvoiceQr = 'data:image/svg+xml;base64,' . base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($invoice->signed_qr_code));
        } catch (\Exception $e) {}
    } elseif (!empty($invoice->irn)) {
        try {
            $einvoiceQr = 'data:image/svg+xml;base64,' . base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($invoice->irn));
        } catch (\Exception $e) {}
    }
}

// UPI Payment QR Code
$upiQrBase64 = '';
if (!empty($setting->upi_id)) {
    $upiUrl = "upi://pay?pa=" . $setting->upi_id . "&pn=" . urlencode($setting->company_name ?? 'Nachias') . "&am=" . ($invoice->grand_total ?? 0) . "&cu=INR";
    try {
        $upiQrBase64 = 'data:image/svg+xml;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(65)->generate($upiUrl));
    } catch (\Exception $e) {}
}

// HDFC Bank Logo
$hdfcLogoBase64 = '';
$cleanHdfc = public_path('assets/images/hdfc_logo_clean.png');
$origHdfc = public_path('assets/images/hdfc_logo.jpg');
if (file_exists($cleanHdfc)) {
    $hdfcLogoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($cleanHdfc));
} elseif (file_exists($origHdfc)) {
    $hdfcLogoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($origHdfc));
}

// Zone name
$zoneName = is_object($cust->zone) || is_array($cust->zone) 
    ? ($cust->zone->zone_name ?? $cust->zone['zone_name'] ?? 'Zone - 1') 
    : (is_string($cust->zone) ? json_decode($cust->zone)->zone_name ?? $cust->zone : 'Zone - 1');

// Pre-process items for rendering exactly as in pdf.blade.php
$isFabricStore = ($invoice->store && str_contains(strtoupper($invoice->store->store_type_name), 'FABRIC')) || (int)$invoice->store_id === 1;
$isAccessoriesStore = ($invoice->store && str_contains(strtoupper($invoice->store->store_type_name), 'ACCESSORIES')) || (int)$invoice->store_id === 2;
$isScrapStore = $invoice->isScrap();
$isRawMaterial = $invoice->isRawMaterialOrScrap();
$allBrands = \App\Models\Brand::all();

$processedItems = [];
foreach ($invoice->items as $it) {
    $brandName = '';
    $styleName = '';
    $sleeve = '';
    $itemName = '';

    $seItem = $it->stockEntryItem;
    if (!$seItem && $it->stock_entry_item_id) {
        $seItem = \App\Models\StockEntryItem::with(['rawMaterial', 'item', 'style', 'brand', 'uom'])->find($it->stock_entry_item_id);
    }
    if (!$seItem && !empty($it->stock_allocations)) {
        $allocs = is_array($it->stock_allocations) ? $it->stock_allocations : json_decode($it->stock_allocations, true);
        if (!empty($allocs) && is_array($allocs)) {
            $firstAlloc = reset($allocs);
            $allocSeId = $firstAlloc['stock_entry_item_id'] ?? null;
            if ($allocSeId) {
                $seItem = \App\Models\StockEntryItem::with(['rawMaterial', 'item', 'style', 'brand', 'uom'])->find($allocSeId);
            }
        }
    }

    $artNo = $seItem ? ($seItem->art_no ?: $it->art_no) : $it->art_no;
    if (empty($artNo) && !empty($it->sku)) {
        $artNo = \App\Models\StockEntryItem::where('sku', $it->sku)->value('art_no') ?: '';
    }

    if ($isRawMaterial) {
        if ($seItem && $seItem->rawMaterial) {
            $itemName = $seItem->rawMaterial->name;
        } elseif ($seItem && $seItem->raw_material_id) {
            $rmObj = \DB::table('raw_materials')->where('id', $seItem->raw_material_id)->first();
            $itemName = $rmObj ? $rmObj->name : ($it->art_no ?: 'Raw Material');
        } elseif ($seItem && $seItem->item) {
            $itemName = $seItem->item->name ?: $seItem->item->code;
        } elseif ($it->item) {
            $itemName = $it->item->name ?: $it->item->code;
        } elseif (!empty($it->item_name)) {
            $itemName = $it->item_name;
        } else {
            $itemName = $it->art_no ?: 'Raw Material';
        }

        if ($seItem && $seItem->style) {
            $styleName = $seItem->style->style_name;
        } elseif ($it->item && $it->item->style) {
            $styleName = $it->item->style->style_name;
        } elseif (!empty($it->art_no)) {
            $matchedSe = \App\Models\StockEntryItem::where('art_no', $it->art_no)->whereNotNull('style_id')->with('style')->first();
            $styleName = ($matchedSe && $matchedSe->style) ? $matchedSe->style->style_name : '-';
        } else {
            $styleName = '-';
        }
    } else {
        $soItem = \App\Models\SalesOrderItem::where('sale_order_id', $invoice->so_id)->where('sku', $it->sku)->first();
        
        $codeToParse = '';
        if ($soItem) {
            $codeToParse = $soItem->getAttributes()['item_name'] ?? '';
            if (empty($codeToParse) || $codeToParse === '-') {
                $codeToParse = $soItem->finished_item_code;
            }
        }
        
        if (empty($codeToParse) || $codeToParse === '-') {
            if ($it->stockEntryItem) {
                $codeToParse = $it->stockEntryItem->finished_item_code;
            } elseif ($it->item) {
                $codeToParse = $it->item->code;
            }
        }
        
        if (empty($codeToParse) || $codeToParse === '-') {
            $codeToParse = $it->sku ?? '';
        }

        $resolved = false;
        if (!$seItem && $soItem && $soItem->stock_entry_item_id) {
            $seItem = \App\Models\StockEntryItem::find($soItem->stock_entry_item_id);
        }

        if ($seItem) {
            if ($seItem->brand) {
                $brandName = $seItem->brand->brand_name;
            }
            if ($seItem->style) {
                $styleName = $seItem->style->style_name;
            }
            
            if (empty($brandName) && !empty($seItem->finished_item_code)) {
                $seParts = explode('-', $seItem->finished_item_code);
                if (count($seParts) > 0) {
                    $dbBrand = \App\Models\Brand::where('code', trim($seParts[0]))->first();
                    if ($dbBrand) {
                        $brandName = $dbBrand->brand_name;
                    }
                }
            }
            
            if (!empty($brandName) || !empty($styleName)) {
                $resolved = true;
            }
            if (empty($sleeve) && !empty($seItem->sleeve_type)) {
                $sleeve = $seItem->sleeve_type;
            }
        }
        
        if (!empty($it->sleeve_type)) {
            $sleeveValUpper = strtoupper(trim($it->sleeve_type));
            if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                $sleeve = 'F/S';
            } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                $sleeve = 'H/S';
            } else {
                $sleeve = $it->sleeve_type;
            }
        } elseif (!empty($sleeve)) {
            $sleeveValUpper = strtoupper(trim($sleeve));
            if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                $sleeve = 'F/S';
            } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                $sleeve = 'H/S';
            }
        }

        if ((empty($brandName) || empty($styleName)) && !empty($codeToParse) && $codeToParse !== '-') {
            $parts = explode('-', $codeToParse);
            
            if (count($parts) >= 2) {
                $brandCode = trim($parts[0]);
                $styleCode = trim($parts[1]);
                
                $dbBrand = \App\Models\Brand::where('code', $brandCode)->first();
                $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                
                if ($dbBrand && empty($brandName)) {
                    $brandName = $dbBrand->brand_name;
                }
                if ($dbStyle && empty($styleName)) {
                    $styleName = $dbStyle->style_name;
                }
                if (!empty($brandName) || !empty($styleName)) {
                    $resolved = true;
                }
                
                if (empty($sleeve) && isset($parts[2])) {
                    $sleeveVal = trim($parts[2]);
                    $sleeveValUpper = strtoupper($sleeveVal);
                    if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                        $sleeve = 'F/S';
                    } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                        $sleeve = 'H/S';
                    } else {
                        $sleeve = $sleeveVal;
                    }
                }
            }     
            
            if ((empty($brandName) || empty($styleName)) && count($parts) >= 1) {
                $mainCode = trim($parts[0]);
                $dbBrand = null;
                $brandCode = '';
                foreach ($allBrands as $brand) {
                    $bCode = trim($brand->code);
                    if (!empty($bCode) && stripos($mainCode, $bCode) === 0) {
                        $dbBrand = $brand;
                        $brandCode = $bCode;
                        break;
                    }
                }
                
                if ($dbBrand) {
                    if (empty($brandName)) {
                        $brandName = $dbBrand->brand_name;
                    }
                    $styleCode = substr($mainCode, strlen($brandCode));
                    $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                    if ($dbStyle && empty($styleName)) {
                        $styleName = $dbStyle->style_name;
                    }
                    $resolved = true;
                    
                    if (empty($sleeve) && isset($parts[1])) {
                        $sleeveVal = trim($parts[1]);
                        $sleeveValUpper = strtoupper($sleeveVal);
                        if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                            $sleeve = 'F/S';
                        } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                            $sleeve = 'H/S';
                        } else {
                            $sleeve = $sleeveVal;
                        }
                    }
                }
            }
        }

        if ($resolved) {
            $itemName = trim($brandName . ' ' . $styleName . ' ' . $sleeve);
        } else {
            $itemName = $codeToParse;
        }
        
        if (empty($codeToParse) || $codeToParse === '-') {
            $codeToParse = $it->sku ?? '';
        }

        $resolved = false;
        if (!$seItem && $soItem && $soItem->stock_entry_item_id) {
            $seItem = \App\Models\StockEntryItem::find($soItem->stock_entry_item_id);
        }

        if ($seItem) {
            if ($seItem->brand) {
                $brandName = $seItem->brand->brand_name;
            }
            if ($seItem->style) {
                $styleName = $seItem->style->style_name;
            }
            
            if (empty($brandName) && !empty($seItem->finished_item_code)) {
                $seParts = explode('-', $seItem->finished_item_code);
                if (count($seParts) > 0) {
                    $dbBrand = \App\Models\Brand::where('code', trim($seParts[0]))->first();
                    if ($dbBrand) {
                        $brandName = $dbBrand->brand_name;
                    }
                }
            }
            
            if (!empty($brandName) || !empty($styleName)) {
                $resolved = true;
            }
            if (empty($sleeve) && !empty($seItem->sleeve_type)) {
                $sleeve = $seItem->sleeve_type;
            }
        }
        
        if (!empty($it->sleeve_type)) {
            $sleeveValUpper = strtoupper(trim($it->sleeve_type));
            if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                $sleeve = 'F/S';
            } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                $sleeve = 'H/S';
            } else {
                $sleeve = $it->sleeve_type;
            }
        } elseif (!empty($sleeve)) {
            $sleeveValUpper = strtoupper(trim($sleeve));
            if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                $sleeve = 'F/S';
            } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                $sleeve = 'H/S';
            }
        }

        if ((empty($brandName) || empty($styleName)) && !empty($codeToParse) && $codeToParse !== '-') {
            $parts = explode('-', $codeToParse);
            
            if (count($parts) >= 2) {
                $brandCode = trim($parts[0]);
                $styleCode = trim($parts[1]);
                
                $dbBrand = \App\Models\Brand::where('code', $brandCode)->first();
                $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                
                if ($dbBrand && empty($brandName)) {
                    $brandName = $dbBrand->brand_name;
                }
                if ($dbStyle && empty($styleName)) {
                    $styleName = $dbStyle->style_name;
                }
                if (!empty($brandName) || !empty($styleName)) {
                    $resolved = true;
                }
                
                if (empty($sleeve) && isset($parts[2])) {
                    $sleeveVal = trim($parts[2]);
                    $sleeveValUpper = strtoupper($sleeveVal);
                    if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                        $sleeve = 'F/S';
                    } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                        $sleeve = 'H/S';
                    } else {
                        $sleeve = $sleeveVal;
                    }
                }
            }
            
            if ((empty($brandName) || empty($styleName)) && count($parts) >= 1) {
                $mainCode = trim($parts[0]);
                $dbBrand = null;
                $brandCode = '';
                foreach ($allBrands as $brand) {
                    $bCode = trim($brand->code);
                    if (!empty($bCode) && stripos($mainCode, $bCode) === 0) {
                        $dbBrand = $brand;
                        $brandCode = $bCode;
                        break;
                    }
                }
                
                if ($dbBrand) {
                    if (empty($brandName)) {
                        $brandName = $dbBrand->brand_name;
                    }
                    $styleCode = substr($mainCode, strlen($brandCode));
                    $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                    if ($dbStyle && empty($styleName)) {
                        $styleName = $dbStyle->style_name;
                    }
                    $resolved = true;
                    
                    if (empty($sleeve) && isset($parts[1])) {
                        $sleeveVal = trim($parts[1]);
                        $sleeveValUpper = strtoupper($sleeveVal);
                        if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                            $sleeve = 'F/S';
                        } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                            $sleeve = 'H/S';
                        } else {
                            $sleeve = $sleeveVal;
                        }
                    }
                }
            }
        }

        if ($resolved) {
            $itemName = trim($brandName . ' ' . $styleName . ' ' . $sleeve);
        } else {
            $itemName = $codeToParse;
        }
    }

    $slvDisplay = '-';
    if (!empty($sleeve)) {
        if (stripos($sleeve, 'half') !== false || stripos($sleeve, 'h/s') !== false || strtoupper(trim($sleeve)) === 'HS') {
            $slvDisplay = 'H';
        } elseif (stripos($sleeve, 'full') !== false || stripos($sleeve, 'f/s') !== false || strtoupper(trim($sleeve)) === 'FS') {
            $slvDisplay = 'F';
        } else {
            $slvDisplay = strtoupper(substr(trim($sleeve), 0, 1));
        }
    }

    // Size
    $sizeDisplay = $it->sizeRatio ? $it->sizeRatio->size : ($it->size ?? '-');

    // HSN Code
    $hsnCode = !empty($it->hsn_sac)
        ? $it->hsn_sac
        : (($seItem && !empty($seItem->hsn_sac))
            ? $seItem->hsn_sac
            : (!empty($invoice->hsn_sac) ? $invoice->hsn_sac : '-'));

    $processedItems[] = [
        'art_no' => $artNo ?: '-',
        'desc' => strtoupper($itemName),
        'hsn' => $hsnCode,
        'slv' => $slvDisplay,
        'size' => $sizeDisplay,
        'qty' => (float)$it->quantity,
        'mrp' => (float)($it->mrp ?? 0),
        'rate' => (float)($it->rate ?? 0),
        'amount' => (float)$it->amount,
    ];
}

// Option B: Group by Size Range condition (Art No + Description + HSN + Sleeve + MRP + Price)
$isDetailedFormat = request()->query('format') === 'detailed' || request()->query('type') === 'pdf1_detailed';

if ($isDetailedFormat) {
    $finalItems = $processedItems;
} else {
    $grouped = [];
    foreach ($processedItems as $item) {
        $mrpKey = number_format($item['mrp'], 2, '.', '');
        $rateKey = number_format($item['rate'], 2, '.', '');
        $groupKey = $item['art_no'] . '___' . $item['desc'] . '___' . $item['hsn'] . '___' . $item['slv'] . '___' . $mrpKey . '___' . $rateKey;

        if (!isset($grouped[$groupKey])) {
            $grouped[$groupKey] = [
                'art_no' => $item['art_no'],
                'desc' => $item['desc'],
                'hsn' => $item['hsn'],
                'slv' => $item['slv'],
                'sizes' => [],
                'qty' => 0,
                'mrp' => $item['mrp'],
                'rate' => $item['rate'],
                'amount' => 0,
            ];
        }

        $grouped[$groupKey]['qty'] += (float)$item['qty'];
        $grouped[$groupKey]['amount'] += (float)$item['amount'];
        if (!empty($item['size']) && $item['size'] !== '-') {
            $grouped[$groupKey]['sizes'][] = trim($item['size']);
        }
    }

    $finalItems = [];
    foreach ($grouped as $g) {
        $sizes = array_values(array_unique(array_filter($g['sizes'])));
        $sizeStr = '-';
        if (empty($sizes)) {
            $sizeStr = '-';
        } elseif (count($sizes) === 1) {
            $sizeStr = $sizes[0];
        } else {
            $allNumeric = true;
            foreach ($sizes as $s) {
                if (!is_numeric($s)) {
                    $allNumeric = false;
                    break;
                }
            }
            if ($allNumeric) {
                sort($sizes, SORT_NUMERIC);
                $minSize = $sizes[0];
                $maxSize = end($sizes);
                $sizeStr = ($minSize == $maxSize) ? (string)$minSize : $minSize . '-' . $maxSize;
            } else {
                $sizeOrder = ['XS' => 1, 'S' => 2, 'M' => 3, 'L' => 4, 'XL' => 5, '2XL' => 6, 'XXL' => 6, '3XL' => 7, 'XXXL' => 7, '4XL' => 8, '5XL' => 9];
                $canOrder = true;
                foreach ($sizes as $s) {
                    if (!isset($sizeOrder[strtoupper($s)])) {
                        $canOrder = false;
                        break;
                    }
                }
                if ($canOrder) {
                    usort($sizes, function($a, $b) use ($sizeOrder) {
                        return ($sizeOrder[strtoupper($a)] ?? 99) <=> ($sizeOrder[strtoupper($b)] ?? 99);
                    });
                    $sizeStr = $sizes[0] . '-' . end($sizes);
                } else {
                    $sizeStr = implode(',', $sizes);
                }
            }
        }

        $finalItems[] = [
            'art_no' => $g['art_no'],
            'desc' => $g['desc'],
            'hsn' => $g['hsn'],
            'slv' => $g['slv'],
            'size' => $sizeStr,
            'qty' => $g['qty'],
            'mrp' => $g['mrp'],
            'rate' => $g['rate'],
            'amount' => $g['amount'],
        ];
    }
}

$allItems = collect($finalItems);
$totalItemsCount = $allItems->count();
$totalQty = $allItems->sum('qty');

// Pagination setup
// 1 page fits up to 14 items cleanly with footer & summary
$maxRowsFirstPageWithFooter = 16;
$maxRowsFullPage = 28;

$pages = [];
$footerPlacedOnValidChunk = false;

if ($totalItemsCount === 0) {
    $pages[] = collect();
    $footerPlacedOnValidChunk = true;
} else {
    $remaining = $allItems;
    while ($remaining->count() > 0) {
        if ($remaining->count() <= $maxRowsFirstPageWithFooter) {
            $pages[] = $remaining;
            $remaining = collect();
            $footerPlacedOnValidChunk = true;
        } else {
            $take = min($maxRowsFullPage, $remaining->count());
            $pages[] = $remaining->take($take);
            $remaining = $remaining->slice($take)->values();
        }
    }
}

if (!$footerPlacedOnValidChunk) {
    $pages[] = collect();
}
$totalChunks = count($pages);

// Calculations for Tax Breakdown
$taxableValue = $invoice->total > 0 ? $invoice->total : ($invoice->sub_total - ($invoice->discount ?? 0) + ($invoice->other_charges ?? 0));
$isOtherState = (bool)($invoice->other_state ?? false);
$cgstPercent = (float)($invoice->cgst_percent ?? 2.5);
$sgstPercent = (float)($invoice->sgst_percent ?? 2.5);
$igstPercent = (float)($invoice->igst_percent ?? 5.0);
@endphp

<div class="footer-page">
    <span class="footer-page-left">E.&O.E.</span>
    <span class="footer-page-right">Page: <span class="page-num"></span> / <span class="page-total"></span></span>
</div>

@foreach($copies as $copyIndex => $copyLabel)
    @php $rowCounter = 0; @endphp
    @foreach($pages as $chunkIndex => $chunk)
        @php
            $isLastChunk = ($chunkIndex == $totalChunks - 1);
        @endphp
    <div class="container" style="{{ ($copyIndex < count($copies) - 1) || !$isLastChunk ? 'page-break-after: always;' : '' }}">
        
        <!-- Header Section: Logo on Left | Centered Company Details | Copy & QR on Right -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
            <tr>
                <!-- Left: Logo -->
                <td style="width: 22%; vertical-align: middle; text-align: left; padding: 0 4px 0 0;">
                    @if(!empty($logoBase64))
                        <table style="border: none; border-collapse: collapse; margin: 0; padding: 0;">
                            <tr>
                                <td style="border: none; padding: 0 5px 0 0; vertical-align: middle; width: 36px;">
                                    <img src="{{ $logoBase64 }}" style="width: 35px; height: auto; display: block;" alt="Logo">
                                </td>
                                <td style="border: none; padding: 0; vertical-align: middle;">
                                    <span style="font-size: 38px; font-weight: bold; color: #01316f; text-transform: uppercase; letter-spacing: -2px; font-family: 'Roboto', Arial, sans-serif; line-height: 1; display: block; white-space: nowrap;">INVOICE</span>
                                </td>
                            </tr>
                        </table>
                    @endif
                </td>

                <!-- Center: Nachias Company Details -->
                <td style="width: 56%; text-align: center; vertical-align: middle; padding: 0 6px;" align="center">
                    <div style="text-align: center; margin-bottom: 2px;">
                        @if(!empty($nachiasLogoBase64))
                            <img src="{{ $nachiasLogoBase64 }}" style="height: 21px; width: auto; max-width: 16px; vertical-align: middle; display: inline; margin-right: 5px; position: relative; top: -1px;" alt="Nachias Logo">
                        @endif
                        <span style="font-size: 15px; font-weight: bold; color: #01316f; letter-spacing: 0.3px; line-height: 1.2; vertical-align: middle;" class="comp-name">
                            {{ strtoupper($setting->company_name ?? 'NACHIAS FASHION PRIVATE LIMITED') }}
                        </span>
                    </div>
                    <div style="text-align: center; font-size: 10px; line-height: 1.4; color: #01316f; font-weight: 400;">
                        272/2, SOMU NAGAR, SIRINGERI NAGAR (SARATHAMBAL KOVIL BACKSIDE),<br>
                        BYEPASS ROAD, MADURAI - 625016<br>
                        Mobile : {{ !empty($setting->toll_free_no) ? $setting->toll_free_no : ($setting->phone ?? '8489938071, 8489938073') }} &nbsp;|&nbsp; Email : {{ $setting->email ?? 'srinachias@yahoo.in, sales@nachias.com' }}<br>
                        GSTIN : {{ $setting->gst_no ?? '33AADCN9342A1ZU' }}
                    </div>
                </td>

                <!-- Right: Copy Label & E-Invoice QR Code directly underneath (only when E-Invoice generated) -->
                <td style="width: 22%; text-align: right; vertical-align: top; padding: 0 0 0 4px;">
                    <div style="font-size: 13px; font-weight: bold; color: #01316f; letter-spacing: 1px; text-transform: uppercase;">
                        {{ $copyLabel }}
                    </div>
                    @if(!empty($einvoiceQr))
                        <img src="{{ $einvoiceQr }}" class="qr-code" style="max-width: 100px; margin-top: 3px;" alt="E-Invoice QR Code">
                    @endif
                </td>
            </tr>
        </table>

        <!-- Thin Divider Line -->
        <div style="border-bottom: 1.5px solid #01316f; margin: 4px 0 6px 0;"></div>

        <!-- Bill To / Ship To / Invoice Metadata Section -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
            <tr>
                <!-- Column 1: BILL TO -->
                <td style="width: 38%; vertical-align: top; padding: 0 10px 0 0; border-right: 1.5px solid #01316f;">
                    <div style="font-weight: 700; color: #01316f; font-size: 11px; margin-bottom: 3px;">BILL TO</div>
                    <div style="font-weight: 700; font-size: 11.5px; color: #09143c; margin-bottom: 2px;">{{ $cust->name ?? 'N/A' }}</div>
                    <div style="font-size: 10.5px; line-height: 1.35; color: #01316f; font-weight: 400;">
                        {{ strtoupper($billAddr1) }}<br>
                        @if(!empty($billAddr2)) {{ strtoupper($billAddr2) }}<br> @endif
                        @if(!empty($billAddr3)) {{ strtoupper($billAddr3) }}<br> @endif
                        {{ strtoupper($billCity) }}{{ $billZip ? ' - ' . $billZip : '' }}, {{ strtoupper($billState) }} ({{ $billStateCode }})<br>
                        Phone : {{ $cust->mobile_no ?? ($cust->phone ?? '-') }}<br>
                        GSTIN : {{ $billGst ?: 'URP' }}
                    </div>
                </td>

                <!-- Column 2: SHIP TO -->
                <td style="width: 28%; vertical-align: top; padding: 0 10px; border-right: 1.5px solid #01316f;">
                    <div style="font-weight: 700; color: #01316f; font-size: 11px; margin-bottom: 3px;">SHIP TO</div>
                    @if($isSameAddress)
                        <div style="font-size: 16px; font-weight: 700; color: #01316f; margin-top: 4px;">-</div>
                    @else
                        <div style="font-size: 9.5px; line-height: 1.35; color: #01316f; font-weight: 400;">
                            <span style="font-weight: 700;">{{ $cust->name ?? 'N/A' }}</span><br>
                            {{ strtoupper($shipRaw ?: $billFullString) }}
                        </div>
                    @endif
                </td>

                <!-- Column 3: Invoice Info Box (Pale Blue Background) -->
                <td style="width: 34%; vertical-align: top; padding-left: 10px;">
                    <div style="background-color: #eef5fb; border-radius: 2px; padding: 6px 8px;">
                        <table style="width: 100%; border: none; border-collapse: collapse; font-size: 10.5px; line-height: 1.45; font-family: 'Roboto', Arial, sans-serif;">
                            <tr style="border-bottom: 1px solid #dceaea;">
                                <td style="width: 38%; font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">Invoice No.</td>
                                <td style="width: 6%; text-align: center; font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">:</td>
                                <td style="width: 56%; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">{{ $invoice->inv_no }}</td>
                            </tr>
                             <tr style="border-bottom: 1px solid #dceaea;">
                                <td style="font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">Invoice Date</td>
                                <td style="text-align: center; font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">:</td>
                                <td style="color: #01316f; font-family: 'Roboto', Arial, sans-serif; padding: 2px 0;">{{ $invoice->inv_date->format('d/m/Y') }}</td>
                            </tr>
                             <tr style="border-bottom: 1px solid #dceaea;">
                                <td style="font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">Order No.</td>
                                <td style="text-align: center; font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">:</td>
                                <td style="color: #01316f; font-family: 'Roboto', Arial, sans-serif; padding: 2px 0;">{{ !empty($invoice->salesOrder->order_no) ? $invoice->salesOrder->order_no : ($invoice->salesOrder->so_no ?? '-') }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #dceaea;">
                                <td style="font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">Zone</td>
                                <td style="text-align: center; font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">:</td>
                                <td style="color: #01316f; font-family: 'Roboto', Arial, sans-serif; padding: 2px 0;">{{ $zoneName }}</td>
                            </tr>
                             <tr style="border-bottom: 1px solid #dceaea;">
                                <td style="font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">Transport</td>
                                <td style="text-align: center; font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">:</td>
                                <td style="color: #01316f; font-family: 'Roboto', Arial, sans-serif; padding: 2px 0;">{{ !empty($invoice->transporter_name) ? $invoice->transporter_name : ($invoice->salesOrder->transporter_name ?? ($cust->transport_name ?? '-')) }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0; white-space: nowrap;">Booking Office</td>
                                <td style="text-align: center; font-weight: bold; font-family: 'Roboto', Arial, sans-serif; color: #01316f; padding: 2px 0;">:</td>
                                <td style="color: #01316f; font-family: 'Roboto', Arial, sans-serif; padding: 2px 0;">{{ $cust->booking_office ?? ($invoice->salesOrder->customer->booking_office ?? '-') }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 4.5%;" class="text-center">S.No.</th>
                    <th style="width: 13%;" class="text-center">Art No.</th>
                    <th style="width: 25%; padding-left: 6px;" class="text-left">Description</th>
                    <th style="width: 9.5%; white-space: nowrap;" class="text-center">HSN Code</th>
                    <th style="width: 5.5%;" class="text-center">Sleeve</th>
                    <th style="width: 8%;" class="text-center">Size</th>
                    <th style="width: 6.5%;" class="text-center">Qty</th>
                    <th style="width: 7%; padding-right: 5px;" class="text-right">MRP</th>
                    <th style="width: 8%; padding-right: 5px; white-space: nowrap;" class="text-right">WS Price</th>
                    <th style="width: 13%; padding-right: 6px;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $item)
                    @php $rowCounter++; @endphp
                    <tr>
                        <td class="text-center">{{ $rowCounter }}</td>
                        <td class="text-center">{{ $item['art_no'] }}</td>
                        <td class="text-left" style="padding-left: 6px; font-weight: 400;">{{ $item['desc'] }}</td>
                        <td class="text-center">{{ $item['hsn'] }}</td>
                        <td class="text-center">{{ $item['slv'] }}</td>
                        <td class="text-center">{{ $item['size'] }}</td>
                        <td class="text-center">{{ number_format($item['qty'], 2) }}</td>
                        <td class="text-right" style="padding-right: 5px;">{{ number_format($item['mrp'], 2) }}</td>
                        <td class="text-right" style="padding-right: 5px;">{{ number_format($item['rate'], 2) }}</td>
                        <td class="text-right bold" style="padding-right: 6px;">{{ number_format($item['amount'], 2) }}</td>
                    </tr>
                @endforeach

                <!-- Empty filler rows to push table and footer cleanly to bottom of page -->
                @php
                    $hasIrn = !empty($invoice->irn) || !empty($invoice->ack_no);
                    $targetRows = $isLastChunk ? 19 : 28;
                    $neededFillers = max(0, $targetRows - $chunk->count());
                @endphp
                @if($neededFillers > 0)
                    @for($j = 0; $j < $neededFillers; $j++)
                        <tr>
                            <td class="text-center" style="height: 18px;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                @endif
            </tbody>

            @if($isLastChunk)
            <!-- Table Total Row: Total Quantity and Gross Amount -->
            <tfoot>
                <tr class="total-row" style="background-color: #FFFFFF;">
                    <td colspan="3" style="border-right: none; border-left: 1.5px solid #01316f; border-bottom: 1.5px solid #01316f; border-top: 1.5px solid #01316f;"></td>
                    <td colspan="3" class="text-right bold" style="border-left: none; border-right: none; border-bottom: 1.5px solid #01316f; border-top: 1.5px solid #01316f; color: #01316f; font-size: 10.5px; white-space: nowrap; padding-right: 6px;">
                        Total Quantity :
                    </td>
                    <td class="text-center bold" style="border-left: none; border-right: 1px solid #01316f; border-bottom: 1.5px solid #01316f; border-top: 1.5px solid #01316f; font-size: 10.5px;">
                        {{ number_format($totalQty, 2) }}
                    </td>
                    <td colspan="2" class="text-right bold" style="border-left: none; border-right: none; border-bottom: 1.5px solid #01316f; border-top: 1.5px solid #01316f; color: #01316f; font-size: 10.5px; white-space: nowrap; padding-right: 5px;">
                        Gross Amount
                    </td>
                    <td class="text-right bold" style="border-left: none; border-right: 1.5px solid #01316f; border-bottom: 1.5px solid #01316f; border-top: 1.5px solid #01316f; font-size: 10.5px; padding-right: 6px;">
                        {{ number_format($invoice->sub_total, 2) }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>

        @if(!$isLastChunk)
        <div style="text-align: right; padding: 6px 10px; font-weight: bold; font-size: 11px; color: #01316f;">
            Continue to Page No. {{ $chunkIndex + 2 }}
        </div>
        @endif

        @if($isLastChunk)
        <!-- Summary Section: Amount in Words on Left (Solid Navy Blue Box) | Tax Breakdown on Right -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 6px;">
            <tr>
                <!-- Left Cell: Solid Navy Blue Block for Amount in Words (+ E-Invoice Details if available) -->
                <td style="width: 58%; vertical-align: top; padding-right: 8px;">
                    <div style="background-color: #013f8a; color: #FFFFFF; padding: 12px 15px; border-radius: 5px;">
                        <div style="font-size: 9px; color: #E0EBF7; margin-bottom: 3px; font-weight: 400;">Amount in Words :</div>
                        <div style="font-weight: 700; font-size: 11px; line-height: 1.35; text-transform: uppercase; color: #FFFFFF; letter-spacing: 0.2px;">
                            RUPEES {{ strtoupper($totalInWords) }}.
                        </div>
                    </div>

                    <!-- E-Invoice Details Box (IRN, Ack No, Ack Date, E-Way Bill) -->
                    @if(!empty($invoice->irn) || !empty($invoice->ack_no))
                    <div style="margin-top: 5px; background-color: #EDF4FA; border: 1.5px solid #01316f; border-radius: 2px; padding: 5px 8px; font-size: 10px; line-height: 1.35;">
                        <table style="width: 100%; border: none; border-collapse: collapse;">
                            @if(!empty($invoice->irn))
                            <tr>
                                <td style="width: 32px; font-weight: 700; color: #01316f; font-size: 10.5px; padding: 1.5px 0;">IRN:</td>
                                <td style="font-size: 10px; font-weight: 600; color: #01316f; word-break: break-all; padding: 1.5px 0; letter-spacing: 0.3px;">{{ $invoice->irn }}</td>
                            </tr>
                            @endif
                            @if(!empty($invoice->ack_no))
                            <tr>
                                <td style="width: 50px; font-weight: 700; color: #01316f; font-size: 10.5px; padding: 1.5px 0;">Ack No:</td>
                                <td style="font-size: 10px; color: #01316f; padding: 1.5px 0;">
                                    <strong>{{ $invoice->ack_no }}</strong>
                                    @if(!empty($invoice->ack_date))
                                        &nbsp;|&nbsp; <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->ack_date)->format('d/m/Y H:i') }}
                                    @endif
                                    @if(!empty($invoice->eway_bill_no))
                                        &nbsp;|&nbsp; <strong>E-Way:</strong> {{ $invoice->eway_bill_no }}
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    @endif
                </td>

                <!-- Right Cell: Taxable Value, CGST/SGST/IGST, Round Off, Grand Total -->
                <td style="width: 42%; vertical-align: top;">
                    <table class="tax-table">
                        @php
                            $salesDiscountPercent = (float)($invoice->sales_discount ?? 0);
                            $salesDiscountAmount = 0;
                            $boxDiscountTotal = 0;
                            $hasSpecificDiscounts = false;
                            
                            if ($salesDiscountPercent > 0 || (isset($invoice->box_discount_amount) && $invoice->box_discount_amount > 0)) {
                                $salesDiscountAmount = ($invoice->sub_total * $salesDiscountPercent) / 100;
                                $boxDiscountTotal = (float)($invoice->discount ?? 0) - $salesDiscountAmount;
                                $hasSpecificDiscounts = true;
                            }
                        @endphp

                        @if(isset($invoice->discount) && $invoice->discount > 0)
                            @if(!$hasSpecificDiscounts)
                                <tr>
                                    <td style="width: 60%; color: #01316f; font-weight: 400;">Discount</td>
                                    <td style="width: 40%; text-align: right; font-weight: 400;">{{ number_format($invoice->discount, 2) }}</td>
                                </tr>
                            @else
                                @if($salesDiscountAmount > 0)
                                    <tr>
                                        <td style="width: 60%; color: #01316f; font-weight: 400;">Discount ({{ number_format($salesDiscountPercent, 2) }}%)</td>
                                        <td style="width: 40%; text-align: right; font-weight: 400;">{{ number_format($salesDiscountAmount, 2) }}</td>
                                    </tr>
                                @endif
                                @if($boxDiscountTotal > 0)
                                    <tr>
                                        <td style="width: 60%; color: #01316f; font-weight: 400;">Box Discount</td>
                                        <td style="width: 40%; text-align: right; font-weight: 400;">{{ number_format($boxDiscountTotal, 2) }}</td>
                                    </tr>
                                @endif
                            @endif
                        @endif

                        @if(isset($invoice->other_charges) && $invoice->other_charges > 0)
                        <tr>
                            <td style="width: 60%; color: #01316f; font-weight: 400;">Courier Charge</td>
                            <td style="width: 40%; text-align: right; font-weight: 400;">{{ number_format($invoice->other_charges, 2) }}</td>
                        </tr>
                        @endif

                        <tr>
                            <td style="width: 60%; color: #01316f; font-weight: 400;">Taxable Value</td>
                            <td style="width: 40%; text-align: right; font-weight: 400;">{{ number_format($taxableValue, 2) }}</td>
                        </tr>
                        @if(!$isOtherState)
                        <tr>
                            <td style="color: #01316f; font-weight: 400;">Output CGST @ {{ number_format($cgstPercent, 2) }}%</td>
                            <td style="text-align: right; font-weight: 400;">{{ number_format($invoice->cgst, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="color: #01316f; font-weight: 400;">Output SGST @ {{ number_format($sgstPercent, 2) }}%</td>
                            <td style="text-align: right; font-weight: 400;">{{ number_format($invoice->sgst, 2) }}</td>
                        </tr>
                        @else
                        <tr>
                            <td style="color: #01316f; font-weight: 400;">Output IGST @ {{ number_format($igstPercent, 2) }}%</td>
                            <td style="text-align: right; font-weight: 400;">{{ number_format($invoice->igst, 2) }}</td>
                        </tr>
                        @endif

                        <tr>
                            <td style="color: #01316f; font-weight: 400;">Round Off</td>
                            <td style="text-align: right; font-weight: 400;">
                                {{ (in_array(strtolower($invoice->round_off_type ?? ''), ['less', 'minus']) ? '-' : '') . number_format($invoice->round_off ?? 0, 2) }}
                            </td>
                        </tr>

                        <!-- Grand Total Bar in Deep Blue -->
                        <tr>
                            <td style="background-color: #01418b; color: #FFFFFF; font-weight: 700; font-size: 12px; padding: 6px 8px; border: 1px solid #01316f;">
                                Grand Total
                            </td>
                            <td style="background-color: #01418b; color: #FFFFFF; font-weight: 700; font-size: 13px; text-align: right; padding: 6px 8px; border: 1px solid #01316f;">
                                {{ number_format($invoice->grand_total, 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Cash Discount Notice & Remarks / Alternate Transport Row -->
        @php
            $altTransport = trim($invoice->customer->alt_transport_name ?? ($invoice->salesOrder->customer->alt_transport_name ?? ''));
        @endphp
        <div style="margin-top: 5px; font-size: 8.5px; color: #01316f;">
            <div style="font-weight: 700; font-size: 10px; letter-spacing: 0.1px; margin-bottom: 2px;">
                CASH DISCOUNT IS VALID ONLY ON PAYMENTS RECEIVED WITHIN 30 DAYS AND ONLY ON THE TAXABLE VALUE
            </div>
            <table style="width: 100%; border: none; border-collapse: collapse; font-size: 10.5px;">
                <tr>
                    <td style="border: none; padding: 0; vertical-align: top; width: 60%; color: #01316f;">
                        <span style="font-weight: 700;">Remarks :</span>
                        <span>{{ !empty($invoice->remarks) ? $invoice->remarks : '-' }}</span>
                    </td>
                    <td style="border: none; padding: 0; vertical-align: top; width: 40%; text-align: right; color: #01316f;">
                        <span style="font-weight: 700;">Alternate Transport :</span>
                        <span>{{ !empty($altTransport) && $altTransport !== '-' ? $altTransport : '-' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Divider Line before Footer -->
        <div style="border-bottom: 1.5px solid #01316f; margin: 5px 0 5px 0;"></div>

        <!-- Footer Section: Bank Details | Terms & Conditions | Signature -->
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- Column 1: Bank Details & Payment QR (Side-by-Side) -->
                <td style="width: 37%; vertical-align: top; padding-right: 8px; border-right: 1.5px solid #01316f;">
                    <table style="width: 100%; border: none; border-collapse: collapse;">
                        <tr>
                            <!-- Left: HDFC Logo & Bank Info -->
                            <td style="border: none; padding: 0; vertical-align: top;">
                                @if(!empty($hdfcLogoBase64))
                                    <div style="margin-bottom: 2px;">
                                        <img src="{{ $hdfcLogoBase64 }}" style="max-width: 125px; max-height: 25px; height: auto;" alt="HDFC Bank">
                                    </div>
                                @else
                                    <div style="font-size: 11px; font-weight: bold; color: #004B8D; letter-spacing: 0.5px; margin-bottom: 2px;">
                                        HDFC BANK
                                    </div>
                                @endif
                                
                                <div style="color: #01316f; font-weight: bold; font-size: 9.5px; margin-top: 2px; margin-bottom: 2px; font-family: 'Roboto', Arial, sans-serif;">BANK DETAILS</div>
                                <div style="font-size: 9.5px; line-height: 1.35; color: #01316f; font-weight: 400;">
                                    HDFC Bank, {{ $setting->branch_location ?? 'Bye Pass Road, Madurai' }}<br>
                                    A/C No.: {{ $setting->account_no ?? '50200026954598' }}<br>
                                    IFSC: {{ $setting->ifsc_code ?? 'HDFC0002409' }}
                                </div>
                            </td>

                            <!-- Right: UPI QR Code (in marked place) -->
                            @if($upiQrBase64)
                            <td style="width: 88px; border: none; padding: 0 0 0 6px; vertical-align: top; text-align: center;">
                                <div style="display: inline-block; text-align: center;">
                                    <img src="{{ $upiQrBase64 }}" style="width: 58px; height: 58px; display: block; margin: 0 auto;" alt="UPI QR">
                                    <div style="font-size: 8px; color: #01316f; font-weight: bold; margin-top: 2px; line-height: 1.2; text-align: center; white-space: nowrap;">
                                        Scan to Pay via UPI
                                    </div>
                                </div>
                            </td>
                            @endif
                        </tr>
                    </table>
                </td>

                <!-- Column 2: Terms & Conditions -->
                <td style="width: 35%; vertical-align: top; padding: 0 8px; border-right: 1.5px solid #01316f;">
                    <div style="color: #01316f; font-weight: 700; font-size: 10px; margin-bottom: 2px;">Terms &amp; Conditions :</div>
                    <div style="font-size: 8.5px; line-height: 1.25; color: #01316f; font-weight: 400;">
                        @if(!empty($setting->terms_and_conditions))
                            {!! nl2br(e($setting->terms_and_conditions)) !!}
                        @else
                           '-'
                        @endif
                    </div>
                </td>

                <!-- Column 3: Authorised Signatory -->
                <td style="width: 30%;  padding-left: 8px; text-align: right;vertical-align: bottom;">
                    <div style="font-size: 10.5px; font-weight: 700; color: #01316f; text-align: center;width: 95%; margin-left: auto;">
                        For {{ strtoupper($setting->company_name ?? 'Nachias Fashion Private Limited') }}
                    </div>
                    <div style="height: 58px;width: 85%; margin-left: auto;"></div>
                    <div style="border-top: 1px dotted #01316f; text-align: center; font-size: 10px; font-weight: 700; color: #01316f; padding-top: 3px; width: 85%; margin-left: auto;">
                        Authorised Signatory
                    </div>
                </td>
            </tr>
        </table>
        @endif

    </div>
    @endforeach
@endforeach

@if(isset($is_print) && $is_print)
<script>
    window.onload = function() {
        window.print();
    }
</script>
@endif
</body>
</html>