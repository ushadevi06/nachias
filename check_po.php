<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$po = App\Models\PurchaseOrder::where('po_number', 'PO-0001')->with('items')->first();
if ($po) {
    echo "PO ID: " . $po->id . " (PO-0001)\n";
    foreach ($po->items as $item) {
        $invoiced = App\Models\PurchaseInvoiceItem::where('purchase_order_item_id', $item->id)->sum('quantity');
        echo "  Item ID: " . $item->id . ", Qty: " . $item->quantity . ", Invoiced: " . $invoiced . "\n";
    }

    $invoices = App\Models\PurchaseInvoice::where('purchase_order_id', $po->id)->get();
    echo "\nInvoices linked to this PO ID:\n";
    if ($invoices->isEmpty()) {
        echo "  None found.\n";
    } else {
        foreach ($invoices as $inv) {
            echo "  Invoice ID: " . $inv->id . ", No: " . $inv->invoice_no . ", Status: " . $inv->invoice_status . "\n";
            $invItems = App\Models\PurchaseInvoiceItem::where('purchase_invoice_id', $inv->id)->get();
            echo "    Items in this invoice:\n";
            foreach ($invItems as $invItem) {
                echo "      ID: " . $invItem->id . ", PO Item ID: " . ($invItem->purchase_order_item_id ?? 'NULL') . ", Qty: " . $invItem->quantity . "\n";
            }
        }
    }
} else {
    echo "PO-0001 not found\n";
}
