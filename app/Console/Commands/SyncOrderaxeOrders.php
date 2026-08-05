<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncOrderaxeOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orderaxe:sync-orders {--limit= : Limit the number of orders to sync}';

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
        $this->info('Starting Orderaxe sync' . ($limit ? " (limit: $limit)" : "") . '...');
        $result = $orderaxeService->syncOrders($limit);
        $this->info("Orderaxe Sync Completed: {$result['synced']} synced, {$result['skipped']} skipped, {$result['failed']} failed.");
    }
}
