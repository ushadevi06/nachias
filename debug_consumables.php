<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$consumables = \App\Models\ProductionStageConsumable::all();
foreach ($consumables as $c) {
    echo "ID: {$c->id}, Stage: {$c->stage}, RM: {$c->raw_material_id}, Qty: {$c->quantity_per_unit}, Status: {$c->status}\n";
}
