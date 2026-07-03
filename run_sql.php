<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';
$app = app();
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Illuminate\Support\Facades\DB::statement('ALTER TABLE purchase_order_items ADD COLUMN cgst_percent DECIMAL(8,2) DEFAULT 0.00 AFTER amount, ADD COLUMN cgst_amount DECIMAL(15,2) DEFAULT 0.00 AFTER cgst_percent, ADD COLUMN sgst_percent DECIMAL(8,2) DEFAULT 0.00 AFTER cgst_amount, ADD COLUMN sgst_amount DECIMAL(15,2) DEFAULT 0.00 AFTER sgst_percent, ADD COLUMN igst_percent DECIMAL(8,2) DEFAULT 0.00 AFTER sgst_amount, ADD COLUMN igst_amount DECIMAL(15,2) DEFAULT 0.00 AFTER igst_percent;');
echo "Success\n";
