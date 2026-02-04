<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$stages = \App\Models\OperationStage::where('status', 'Active')->get();
foreach ($stages as $s) {
    echo "ID: {$s->id}, Name: {$s->name}\n"; 
}
