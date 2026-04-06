<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobCardEntry;
use App\Models\ProcessSchedule;
use App\Models\ProductionService;
use Illuminate\Support\Facades\DB;

$jobCards = JobCardEntry::with(['tasks.assignments', 'tasks.stage'])
    ->where('grand_total_qty', '>', 0)
    ->whereNotIn('status', ['Production Completed', 'Closed'])
    ->get();

echo str_pad("JC No", 15) . " | " . str_pad("Stage", 15) . " | " . "Outward Qty\n";
echo str_repeat("-", 45) . "\n";

$totalOutward = 0;
foreach ($jobCards as $jc) {
    $schedules = ProcessSchedule::where('job_card_entry_id', $jc->id)->get();

    foreach ($schedules as $schedule) {
        $stageName = DB::table('operation_stages')->where('id', $schedule->operation_stage_id)->value('operation_stage_name');
        
        if ($stageName !== 'Cutting') continue;

        $stageId = $schedule->operation_stage_id;
        $stageTasks = $jc->tasks->filter(function ($t) use ($schedule, $stageId) {
            return $t->stage_id == $schedule->id || ($t->stage && $t->stage->operation_stage_id == $stageId);
        });

        $assignments = $stageTasks->flatMap->assignments;
        if ($assignments->isEmpty()) continue;

        $requiredServices = ProductionService::where('operation_stage_id', $stageId)->active()->get();

        $actualOutward = 0;
        if ($requiredServices->isNotEmpty()) {
            $serviceCompletions = $requiredServices->map(function ($service) use ($assignments) {
                $sa = $assignments->where('service_id', $service->id);
                if ($sa->isEmpty()) return 0;
                $isSeq = $sa->count() > 1 && $sa->max('issue_qty') >= $sa->sum('issue_qty') * 0.9;
                return $isSeq ? (float) $sa->min('completed_qty') : (float) $sa->sum('completed_qty');
            });
            $actualOutward = (float) $serviceCompletions->min();
        }

        if ($actualOutward > 0) {
            echo str_pad($jc->job_card_no, 15) . " | " . str_pad($stageName, 15) . " | " . $actualOutward . "\n";
            $totalOutward += $actualOutward;
        }
    }
}

echo str_repeat("-", 45) . "\n";
echo "Total Cutting Outward: " . $totalOutward . "\n";
