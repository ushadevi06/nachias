<?php

declare(strict_types=1);

namespace App\Services\Neuron\Tools;

use App\Models\ErpRagKnowledgeChunk;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class ErpSchemaLookupTool extends Tool
{
    protected string $name = 'inspect_erp_database_schema';

    protected ?string $description = 'Look up exact table columns, datatypes, nullable fields, primary keys, and foreign keys from nachias.sql and the ERP database schema.';

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'table_name',
                type: PropertyType::STRING,
                description: 'The exact or approximate database table name (e.g. "purchase_orders", "users", "grn_entries", "items").',
                required: true
            ),
        ];
    }

    public function __invoke(string $table_name): string
    {
        $cleanTable = strtolower(trim($table_name));

        // 1. Search in RAG indexed schema chunks
        $chunk = ErpRagKnowledgeChunk::where('source_type', 'sql_schema')
            ->where(function ($q) use ($cleanTable) {
                $q->where('title', 'like', "%Table: `{$cleanTable}`%")
                  ->orWhere('title', 'like', "%{$cleanTable}%")
                  ->orWhere('keywords', 'like', "%{$cleanTable}%");
            })
            ->first();

        if ($chunk) {
            return "### DATABASE SCHEMA FOR TABLE: `{$cleanTable}`\n\n" . $chunk->content;
        }

        // 2. Fallback: inspect active MySQL table columns if exists
        try {
            if (Schema::hasTable($cleanTable)) {
                $columns = DB::select("SHOW COLUMNS FROM `{$cleanTable}`");
                $formatted = "### LIVE DATABASE SCHEMA FOR TABLE: `{$cleanTable}`\n\n";
                $formatted .= "| Field | Type | Null | Key | Default |\n";
                $formatted .= "|---|---|---|---|---|\n";
                foreach ($columns as $col) {
                    $formatted .= "| {$col->Field} | {$col->Type} | {$col->Null} | {$col->Key} | " . ($col->Default ?? 'NULL') . " |\n";
                }
                return $formatted;
            }
        } catch (\Throwable $e) {
            // Ignore DB exception
        }

        return "Database table `{$cleanTable}` was not found in nachias.sql or the active database.";
    }
}
