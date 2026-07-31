<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateWidths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:widths';

    protected $description = 'Migrate width strings to fabric_sizes IDs in job card tables';

    public function handle()
    {
        $this->info('Starting width migration...');
        
        $jobCardWidths = \DB::table('job_card_entries')
            ->whereNotNull('width')
            ->where('width', '!=', '')
            ->distinct()
            ->pluck('width');

        $fabricDetailWidths = \DB::table('job_card_fabric_details')
            ->whereNotNull('width')
            ->where('width', '!=', '')
            ->distinct()
            ->pluck('width');

        $allWidths = $jobCardWidths->concat($fabricDetailWidths)->unique();

        $widthToIdMap = [];

        foreach ($allWidths as $widthString) {
            if (is_numeric($widthString) && \App\Models\FabricSize::find($widthString) && !\App\Models\FabricSize::where('width', $widthString)->exists()) {
                // It might already be an ID, but we should verify it's just a string representation of an ID.
                // To be completely safe, let's strictly look up by width string.
            }
            
            $master = \App\Models\FabricSize::firstOrCreate(
                ['width' => $widthString],
                ['status' => 'Active', 'created_by' => 1, 'updated_by' => 1]
            );
            $widthToIdMap[$widthString] = $master->id;
        }

        $this->info('Master records ensured. Updating tables...');

        $jcCount = 0;
        foreach (\App\Models\JobCardEntry::whereNotNull('width')->where('width', '!=', '')->cursor() as $jc) {
            $currentVal = $jc->width;
            if (isset($widthToIdMap[$currentVal])) {
                $jc->width = $widthToIdMap[$currentVal];
                $jc->save();
                $jcCount++;
            }
        }
        $this->info("Updated {$jcCount} JobCardEntry records.");

        $fdCount = 0;
        foreach (\App\Models\JobCardFabricDetail::whereNotNull('width')->where('width', '!=', '')->cursor() as $fd) {
            $currentVal = $fd->width;
            if (isset($widthToIdMap[$currentVal])) {
                $fd->width = $widthToIdMap[$currentVal];
                $fd->save();
                $fdCount++;
            }
        }
        $this->info("Updated {$fdCount} JobCardFabricDetail records.");

        $this->info('Migration complete.');
    }
}
