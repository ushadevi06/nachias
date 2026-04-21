<?php
$tasks = App\Models\Task::with(['jobCard', 'stage.operationStage', 'operationStage'])->get();
foreach($tasks as $t) {
    $stage = $t->stage;
    if (!$stage && $t->job_card_entry_id) {
        $osId = $t->operation_stage_id ?? $t->stage_id;
        $stage = App\Models\ProcessSchedule::where('job_card_entry_id', $t->job_card_entry_id)->where('operation_stage_id', $osId)->first();
    }
    $stageName = 'No Stage';
    if ($stage) {
        $stageName = $stage->operationStage ? $stage->operationStage->operation_stage_name : ($stage->stage ?: 'No Stage');
    } elseif ($t->operationStage) {
        $stageName = $t->operationStage->operation_stage_name ?: 'No Stage';
    }
    echo "Task: {$t->task_no} | Result Stage: {$stageName} | Task Stage ID: " . ($t->stage_id ?? 'NULL') . " | JC ID: " . ($t->job_card_entry_id ?? 'NULL') . PHP_EOL;
}
