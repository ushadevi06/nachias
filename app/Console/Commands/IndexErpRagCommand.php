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
    protected $description = 'Extract and index Laravel code (routes, menus, controllers), Nachias Documentation (.docx & .md), and nachias.sql into the RAG knowledge base';

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
                ['Laravel Controllers (Codebase & Business Logic)', $stats['controllers']],
                ['Documentation (Nachias Documentation.docx)', $stats['documentation_docx']],
                ['Documentation (Nachias Documentation.md)', $stats['documentation_md']],
                ['Database Schema (nachias.sql)', $stats['sql_tables']],
                ['Field & Select Box Data Lineages', $stats['field_data_sources']],
                ['Total Knowledge Chunks', $stats['total_indexed']],
            ]
        );

        $this->info("Indexing completed successfully in {$elapsed} seconds!");

        return Command::SUCCESS;
    }
}
