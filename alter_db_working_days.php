<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('operation_stages', function (Blueprint $table) {
    if (!Schema::hasColumn('operation_stages', 'working_days')) {
        $table->integer('working_days')->default(0)->after('operation_stage_name');
        echo "Successfully added 'working_days' column to operation_stages table.\n";
    } else {
        echo "'working_days' column already exists in operation_stages table.\n";
    }
});
