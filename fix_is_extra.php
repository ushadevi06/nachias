<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$invoices = DB::table('sales_invoices')->get();

$updatedExtra = 0;
$updatedNormal = 0;

foreach ($invoices as $invoice) {
    if (empty($invoice->so_ids) && empty($invoice->so_id)) {
        continue;
    }

    $soIdsArr = [];
    if (!empty($invoice->so_ids)) {
        $decoded = json_decode($invoice->so_ids, true);
        if (is_array($decoded)) {
            $soIdsArr = $decoded;
        }
    }
    if (empty($soIdsArr) && !empty($invoice->so_id)) {
        $soIdsArr = [$invoice->so_id];
    }

    if (empty($soIdsArr)) {
        continue;
    }

    // Get all Sales Order Items for this invoice
    $soItems = DB::table('sales_order_items')
        ->whereIn('sale_order_id', $soIdsArr)
        ->get();

    // Get all Sales Invoice Items
    $invoiceItems = DB::table('sales_invoice_items')
        ->where('sales_invoice_id', $invoice->id)
        ->whereNull('deleted_at')
        ->get();

    foreach ($invoiceItems as $invItem) {
        $existsInSO = false;
        
        foreach ($soItems as $soItem) {
            $match = false;
            if ($invItem->stock_entry_item_id && $soItem->stock_entry_item_id) {
                if ($invItem->stock_entry_item_id == $soItem->stock_entry_item_id) {
                    $match = true;
                }
            } else {
                if ($invItem->sku == $soItem->sku && 
                    $invItem->size == $soItem->size_id && 
                    $invItem->api_color == $soItem->api_color && 
                    $invItem->sleeve_type == $soItem->sleeve) {
                    $match = true;
                }
            }

            if ($match) {
                $existsInSO = true;
                break;
            }
        }

        $newIsExtra = $existsInSO ? 0 : 1;

        if ($invItem->is_extra != $newIsExtra) {
            DB::table('sales_invoice_items')
                ->where('id', $invItem->id)
                ->update(['is_extra' => $newIsExtra]);
            
            if ($newIsExtra == 1) {
                $updatedExtra++;
            } else {
                $updatedNormal++;
            }
        }
    }
}

echo "Successfully updated records.\n";
echo "Marked as extra (1): $updatedExtra\n";
echo "Marked as normal (0): $updatedNormal\n";
