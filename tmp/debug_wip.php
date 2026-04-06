<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobCardEntry;
use App\Models\ProcessSchedule;
use Illuminate\Support\Facades\DB;

$jobCards = JobCardEntry::where('grand_total_qty', '>', 0)
    ->whereNotIn('status', ['Production Completed', 'Closed'])
    ->get();

echo "Total Active Job Cards: " . $jobCards->count() . "\n";
echo str_pad("JC No", 15) . " | " . str_pad("Qty", 10) . " | " . "First Stage\n";
echo str_repeat("-", 40) . "\n";

$totalQty = 0;
foreach ($jobCards as $jc) {
    $firstSchedule = ProcessSchedule::where('job_card_entry_id', $jc->id)
        ->orderBy('id', 'asc')
        ->first();
    
    $stageName = 'N/A';
    if ($firstSchedule) {
        $stageName = DB::table('operation_stages')->where('id', $firstSchedule->operation_stage_id)->value('operation_stage_name');
    }

    if ($stageName == 'Cutting') {
        echo str_pad($jc->job_card_no, 15) . " | " . str_pad($jc->grand_total_qty, 10) . " | " . $stageName . "\n";
        $totalQty += $jc->grand_total_qty;
    }
}

echo str_repeat("-", 40) . "\n";
echo "Total Cutting Inward Qty: " . $totalQty . "\n";
