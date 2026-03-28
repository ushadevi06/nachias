<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobCardEntry;
use App\Models\ProcessSchedule;
use App\Models\ProductionMovement;
use App\Models\ProductionService;

class WipLedgerSync extends Command
{
    protected $signature   = 'production:wip-sync';
    protected $description = 'Sync production_movements WIP ledger for all active job cards';

    public function handle()
    {
        $this->info('Starting WIP Ledger Sync...');

        $jobCards = JobCardEntry::with(['tasks.assignments', 'tasks.stage'])
            ->where('grand_total_qty', '>', 0)
            ->whereNotIn('status', ['Production Completed', 'Closed'])
            ->get();

        $bar = $this->output->createProgressBar($jobCards->count());
        $bar->start();

        $synced  = 0;
        $skipped = 0;

        foreach ($jobCards as $jc) {
            $schedules = ProcessSchedule::where('job_card_entry_id', $jc->id)
                ->orderBy('id', 'asc')
                ->get();

            if ($schedules->isEmpty()) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // ── 1. Seed INWARD for first stage ──────────────────────────────
            $firstSchedule = $schedules->first();
            ProductionMovement::updateOrCreate(
                [
                    'job_card_id'         => $jc->id,
                    'process_schedule_id' => $firstSchedule->id,
                    'operation_stage_id'  => $firstSchedule->operation_stage_id,
                    'task_id'             => null,
                ],
                [
                    'inward_qty' => $jc->grand_total_qty,
                    'remarks'    => '[WipSync] Initial inward — grand total qty',
                    'created_by' => 1,
                    'updated_by' => 1,
                ]
            );
            $synced++;

            // ── 2. Sync OUTWARD per stage ────────────────────────────────────
            foreach ($schedules as $schedule) {
                $stageId = $schedule->operation_stage_id;

                $stageTasks = $jc->tasks->filter(function ($t) use ($schedule, $stageId) {
                    if ($t->stage_id == $schedule->id)
                        return true;
                    if ($t->stage && $t->stage->operation_stage_id == $stageId)
                        return true;
                    return false;
                });

                $assignments = $stageTasks->flatMap->assignments;
                if ($assignments->isEmpty())
                    continue;

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

                if ($actualOutward <= 0)
                    continue;

                $task = $stageTasks->first();

                ProductionMovement::updateOrCreate(
                    [
                        'job_card_id'         => $jc->id,
                        'process_schedule_id' => $schedule->id,
                        'operation_stage_id'  => $stageId,
                        'task_id'             => $task?->id,
                    ],
                    [
                        'outward_qty' => $actualOutward,
                        'remarks'     => '[WipSync] Outward from task progress',
                        'created_by'  => 1,
                        'updated_by'  => 1,
                    ]
                );
                $synced++;

                // Inward for next stage
                $nextSchedule = $schedules->where('id', '>', $schedule->id)->sortBy('id')->first();
                if ($nextSchedule) {
                    ProductionMovement::updateOrCreate(
                        [
                            'job_card_id'         => $jc->id,
                            'process_schedule_id' => $nextSchedule->id,
                            'operation_stage_id'  => $nextSchedule->operation_stage_id,
                            'task_id'             => null,
                        ],
                        [
                            'inward_qty' => $actualOutward,
                            'remarks'    => '[WipSync] Inward from previous stage outward',
                            'created_by' => 1,
                            'updated_by' => 1,
                        ]
                    );
                    $synced++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("WIP Sync Done! Records synced: {$synced} | Skipped (no schedules): {$skipped}");
    }
}
