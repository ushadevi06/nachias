<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoice = \App\Models\SalesInvoice::where('inv_no', 'SV-0003')->first();
$groupedItems = $invoice->items->groupBy(function($item) { return 'test'; });

foreach($groupedItems as $groupName => $items) {
    foreach ($items as $item) {
        $soItem = \App\Models\SalesOrderItem::where('sale_order_id', $invoice->so_id)
            ->where('sku', $item->sku)
            ->first();
        $codeToParse = '';
        if ($soItem) {
            $codeToParse = $soItem->getAttributes()['item_name'] ?? '';
            if (empty($codeToParse) || $codeToParse === '-') {
                $codeToParse = $soItem->finished_item_code;
            }
        }
        
        if (empty($codeToParse) || $codeToParse === '-') {
            if ($item->stockEntryItem) {
                $codeToParse = $item->stockEntryItem->finished_item_code;
            } elseif ($item->item) {
                $codeToParse = $item->item->code;
            }
        }
        
        if (empty($codeToParse) || $codeToParse === '-') {
            $codeToParse = $item->sku ?? '';
        }

        $brandName = '';
        $styleName = '';
        $sleeve = '';
        $resolved = false;

        $seItem = $item->stockEntryItem;
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
        
        if (!empty($item->sleeve_type)) {
            $sleeveValUpper = strtoupper(trim($item->sleeve_type));
            if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                $sleeve = 'F/S';
            } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                $sleeve = 'H/S';
            } else {
                $sleeve = $item->sleeve_type;
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
                $allBrands = \App\Models\Brand::all();
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
        
        echo "ItemName: " . $itemName . "\n";
    }
}
