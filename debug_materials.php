<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$materials = \App\Models\RawMaterial::where('name', 'like', '%Button%')
    ->orWhere('name', 'like', '%Cuff%')
    ->orWhereIn('id', [40, 8, 96])
    ->get();

foreach ($materials as $m) {
    echo "ID: {$m->id}, Name: {$m->name}, Code: {$m->code}, UOM: {$m->uom_id}\n";
}
