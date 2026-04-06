<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobCardEntry;
use App\Models\ProcessSchedule;
use Illuminate\Support\Facades\DB;

$jc001 = JobCardEntry::where('job_card_no', 'JC001')->first();

if ($jc001) {
    echo "JC001 Status: " . $jc001->status . "\n";
    echo "JC001 Total Qty: " . $jc001->grand_total_qty . "\n";
    
    $schedules = ProcessSchedule::where('job_card_entry_id', $jc001->id)->get();
    foreach($schedules as $s) {
        $stageName = DB::table('operation_stages')->where('id', $s->operation_stage_id)->value('operation_stage_name');
        echo "Stage: " . $stageName . " (ID: " . $s->id . ")\n";
    }
} else {
    echo "JC001 not found.\n";
}
