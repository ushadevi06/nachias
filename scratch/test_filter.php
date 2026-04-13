<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobCardEntry;
use App\Models\PurchaseInvoice;
use App\Models\GrnEntryItem;

$id = 3;
$jobCard = JobCardEntry::with(['purchaseOrder.items.rawMaterial', 'fabricDetails'])->find($id);
if (!$jobCard) {
    die("Job Card 3 not found\n");
}
echo "Job Card Purchase Order ID: " . $jobCard->purchase_order_id . "\n";

$invoiceIds = PurchaseInvoice::where('purchase_order_id', $jobCard->purchase_order_id)->pluck('id');
echo "Invoice IDs Count: " . $invoiceIds->count() . "\n";
$grnItems = GrnEntryItem::whereIn('grn_entry_id', function ($query) use ($invoiceIds) {
    $query->select('id')->from('grn_entries')->whereIn('purchase_invoice_id', $invoiceIds);
})->with(['purchaseInvoiceItem.rawMaterial', 'purchaseInvoiceItem.purchaseOrderItem'])->get();
echo "GRN Items Count: " . $grnItems->count() . "\n";

$artCategoryMap = [];
foreach ($grnItems as $item) {
    $trimmedArtNo = trim($item->art_no);
    if (!isset($artCategoryMap[$trimmedArtNo])) {
        $artCategoryMap[$trimmedArtNo] = $item->purchaseInvoiceItem->purchaseOrderItem->store_category_id 
            ?? ($item->purchaseInvoiceItem->rawMaterial->store_category_id 
            ?? ($item->purchaseInvoiceItem->store_category_id ?? 1));
    }
}

if ($jobCard->purchaseOrder && $jobCard->purchaseOrder->items) {
    foreach ($jobCard->purchaseOrder->items as $poItem) {
        $artNo = trim($poItem->rawMaterial->code ?? '');
        if ($artNo && !isset($artCategoryMap[$artNo])) {
            $artCategoryMap[$artNo] = $poItem->store_category_id ?? ($poItem->rawMaterial->store_category_id ?? 1);
        }
    }
}

$fabricArtNos = $jobCard->fabricDetails->pluck('art_no')->map(fn($a) => trim($a))->unique()->toArray();
$missingArtNos = array_diff($fabricArtNos, array_keys($artCategoryMap));
echo "Missing Art Nos: " . implode(', ', $missingArtNos) . "\n";

if (!empty($missingArtNos)) {
    $otherGrnItems = GrnEntryItem::whereIn('art_no', $missingArtNos)
        ->with(['purchaseInvoiceItem.rawMaterial', 'purchaseInvoiceItem.purchaseOrderItem'])
        ->orderBy('id', 'desc')
        ->get();
    echo "Other GRN Items found: " . $otherGrnItems->count() . "\n";
    foreach ($otherGrnItems as $item) {
        $trimmedArtNo = trim($item->art_no);
        if (!isset($artCategoryMap[$trimmedArtNo])) {
            $catId = $item->purchaseInvoiceItem->purchaseOrderItem->store_category_id 
                ?? ($item->purchaseInvoiceItem->rawMaterial->store_category_id 
                ?? ($item->purchaseInvoiceItem->store_category_id ?? 1));
            echo "Found '$trimmedArtNo' in other GRN, Cat: $catId\n";
            $artCategoryMap[$trimmedArtNo] = $catId;
        }
    }

    foreach($missingArtNos as $mArtNo) {
        if (!isset($artCategoryMap[$mArtNo])) {
            $rm = App\Models\RawMaterial::where('code', $mArtNo)->orWhere('name', $mArtNo)->first();
            if ($rm) {
                echo "Found '$mArtNo' in RawMaterial, Cat: " . ($rm->store_category_id ?? 1) . "\n";
                $artCategoryMap[$mArtNo] = $rm->store_category_id ?? 1;
            }
        }
    }
}

print_r($artCategoryMap);

$fabricDetails = $jobCard->fabricDetails->filter(function($detail) use ($artCategoryMap) {
    $trimmedArt = trim($detail->art_no);
    $catId = $artCategoryMap[$trimmedArt] ?? 1;
    echo "Checking Row: '$trimmedArt', Mapped Cat: $catId -> Result: " . ($catId == 1 ? "SHOW" : "HIDE") . "\n";
    return $catId == 1;
});

echo "Filtered Details Count: " . $fabricDetails->count() . "\n";
foreach ($fabricDetails as $d) {
    echo "Remaining: " . $d->art_no . "\n";
}
