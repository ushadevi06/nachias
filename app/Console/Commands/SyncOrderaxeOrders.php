<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncOrderaxeOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orderaxe:sync-orders {--limit= : Limit the number of orders to sync} {--rewind-days= : Number of days to rewind the sync timestamp to catch missed orders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync sales orders from Orderaxe API';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\OrderaxeService $orderaxeService)
    {
        $limit = $this->option('limit');
        $rewindDays = $this->option('rewind-days');

        if ($rewindDays) {
            $lastTimestamp = Cache::get('orderaxe_last_sync_timestamp');
            if ($lastTimestamp) {
                // Rewind the timestamp by X days
                $newTimestamp = $lastTimestamp - ((int)$rewindDays * 24 * 60 * 60 * 1000);
                Cache::put('orderaxe_last_sync_timestamp', $newTimestamp);
                $this->info("Rewound sync timestamp by {$rewindDays} days. New timestamp: {$newTimestamp}");
            } else {
                $this->info("No existing sync timestamp found to rewind.");
            }
        }

        $this->info('Starting Orderaxe sync' . ($limit ? " (limit: $limit)" : "") . '...');
        $result = $orderaxeService->syncOrders($limit);
        $this->info("Orderaxe Sync Completed: {$result['synced']} synced, {$result['skipped']} skipped, {$result['failed']} failed.");
    }
}
