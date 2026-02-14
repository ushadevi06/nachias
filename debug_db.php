<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tasks = \App\Models\Task::latest()->take(5)->get();
foreach ($tasks as $t) {
    echo "Task: [{$t->task_no}], ID: {$t->id}, Services: " . json_encode($t->services) . "\n";
}
