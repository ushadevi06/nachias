<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('sales_invoices', function (Blueprint $table) {
    if (!Schema::hasColumn('sales_invoices', 'dispatch_completed_at')) {
        $table->dateTime('dispatch_completed_at')->nullable()->after('delivery_status');
        echo "Added dispatch_completed_at column.\n";
    } else {
        echo "Column already exists.\n";
    }
});
