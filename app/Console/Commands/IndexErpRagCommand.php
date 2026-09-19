<?php

namespace App\Console\Commands;

use App\Services\RAG\ErpRagIndexerService;
use Illuminate\Console\Command;

class IndexErpRagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:rag-index {--append : Append to existing chunks instead of wiping}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract and index Laravel code (routes, menus), Nachias Documentation.docx, and nachias.sql into the RAG knowledge base';

    /**
     * Execute the console command.
     */
    public function handle(ErpRagIndexerService $indexer): int
    {
        $this->info('Starting Nachias ERP RAG Indexing...');
        // Default to fresh=true (clean wipe) unless explicitly asked to --append
        $fresh = !$this->option('append');

        $start = microtime(true);
        $stats = $indexer->indexAll($fresh);
        $elapsed = round(microtime(true) - $start, 2);

        $this->table(
            ['Source', 'Chunks Indexed'],
            [
                ['Laravel Code (Menus & Routes)', $stats['menus_and_routes']],
                ['Documentation (Nachias Documentation.docx)', $stats['documentation']],
                ['Database Schema (nachias.sql)', $stats['sql_tables']],
                ['Total Knowledge Chunks', $stats['total_indexed']],
            ]
        );

        $this->info("Indexing completed successfully in {$elapsed} seconds!");

        return Command::SUCCESS;
    }
}
