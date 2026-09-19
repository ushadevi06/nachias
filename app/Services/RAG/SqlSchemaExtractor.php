<?php

namespace App\Services\RAG;

class SqlSchemaExtractor
{
    /**
     * Extract database schema knowledge chunks from nachias.sql.
     *
     * @return array<int, array>
     */
    public function extract(?string $sqlPath = null): array
    {
        $path = $sqlPath ?: base_path('nachias.sql');
        if (!file_exists($path)) {
            return [];
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            return [];
        }

        $chunks = [];
        $currentTable = null;
        $columns = [];
        $primaryKey = 'id';
        $foreignKeys = [];
        $indexes = [];
        $tableComment = '';

        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);

            // Table creation start: CREATE TABLE `users` (
            if (preg_match('/CREATE TABLE [`"]?([a-zA-Z0-9_]+)[`"]?/i', $trimmed, $m)) {
                $currentTable = $m[1];
                $columns = [];
                $primaryKey = 'id';
                $foreignKeys = [];
                $indexes = [];
                $tableComment = '';
                continue;
            }

            if ($currentTable) {
                // Table closing: ) ENGINE=InnoDB ...
                if (preg_match('/^\)\s*ENGINE/i', $trimmed)) {
                    $chunk = $this->buildTableChunk($currentTable, $columns, $primaryKey, $foreignKeys, $indexes, $tableComment);
                    if ($chunk) {
                        $chunks[] = $chunk;
                    }
                    $currentTable = null;
                    continue;
                }

                // Primary key
                if (preg_match('/PRIMARY KEY\s*\(([^)]+)\)/i', $trimmed, $m)) {
                    $primaryKey = str_replace(['`', '"', ' '], '', $m[1]);
                    continue;
                }

                // Foreign key constraint
                if (preg_match('/FOREIGN KEY\s*\(([^)]+)\)\s*REFERENCES\s*[`"]?([a-zA-Z0-9_]+)[`"]?\s*\(([^)]+)\)/i', $trimmed, $m)) {
                    $localCol = str_replace(['`', '"', ' '], '', $m[1]);
                    $refTable = $m[2];
                    $refCol = str_replace(['`', '"', ' '], '', $m[3]);
                    $foreignKeys[] = "{$localCol} -> {$refTable}({$refCol})";
                    continue;
                }

                // Standard index or unique index
                if (preg_match('/(?:UNIQUE\s+KEY|KEY)\s+[`"]?([a-zA-Z0-9_]+)[`"]?\s*\(([^)]+)\)/i', $trimmed, $m)) {
                    $idxName = $m[1];
                    $idxCols = str_replace(['`', '"', ' '], '', $m[2]);
                    $indexes[] = "{$idxName} ({$idxCols})";
                    continue;
                }

                // Column definition: `emp_code` varchar(30) NOT NULL ...
                if (preg_match('/^[`"]([a-zA-Z0-9_]+)[`"]\s+([a-zA-Z0-9_()]+)(.*)/i', $trimmed, $m)) {
                    $colName = $m[1];
                    $colType = $m[2];
                    $rest = $m[3];

                    $isNullable = !str_contains(strtoupper($rest), 'NOT NULL');
                    $hasDefault = preg_match('/DEFAULT\s+([^,]+)/i', $rest, $defMatches);
                    $defaultVal = $hasDefault ? trim($defMatches[1]) : ($isNullable ? 'NULL' : 'None');

                    $columns[] = [
                        'name' => $colName,
                        'type' => $colType,
                        'nullable' => $isNullable,
                        'default' => $defaultVal,
                    ];
                }
            }
        }

        fclose($handle);

        return $chunks;
    }

    /**
     * Build structured knowledge chunk for a table.
     */
    protected function buildTableChunk(string $table, array $columns, string $primaryKey, array $foreignKeys, array $indexes, string $comment): ?array
    {
        if (empty($columns)) {
            return null;
        }

        $module = $this->guessModuleFromTable($table);
        $title = "Database Table: {$table} ({$module} Module)";

        $colList = [];
        $keywords = [$table, str_replace('_', ' ', $table), $module, 'table', 'database', 'sql', 'schema'];

        foreach ($columns as $c) {
            $keywords[] = $c['name'];
            $colList[] = "  - `{$c['name']}` ({$c['type']})" . ($c['nullable'] ? ' [nullable]' : ' [NOT NULL]');
        }

        $keywordStr = strtolower(implode(', ', array_unique($keywords)));

        $markdownContent = "### {$title}\n"
                         . "- **Table Name**: `{$table}`\n"
                         . "- **Module**: {$module}\n"
                         . "- **Primary Key**: `{$primaryKey}`\n"
                         . "- **Total Columns**: " . count($columns) . "\n";

        if (!empty($foreignKeys)) {
            $markdownContent .= "- **Foreign Key Relations**:\n";
            foreach (array_slice($foreignKeys, 0, 8) as $fk) {
                $markdownContent .= "  - {$fk}\n";
            }
        }

        $markdownContent .= "- **Columns**:\n" . implode("\n", array_slice($colList, 0, 35));
        if (count($colList) > 35) {
            $markdownContent .= "\n  - *(and " . (count($colList) - 35) . " more columns)*";
        }

        return [
            'source_type' => 'sql_schema',
            'module' => $module,
            'screen' => $table,
            'title' => $title,
            'keywords' => substr($keywordStr, 0, 2000),
            'url' => null,
            'menu_path' => null,
            'content' => $markdownContent,
            'meta' => [
                'table' => $table,
                'primary_key' => $primaryKey,
                'column_count' => count($columns),
            ],
        ];
    }

    protected function guessModuleFromTable(string $table): string
    {
        $t = strtolower($table);
        if (str_contains($t, 'job_card') || str_contains($t, 'task') || str_contains($t, 'production') || str_contains($t, 'process')) {
            return 'Production';
        }
        if (str_contains($t, 'sale') || str_contains($t, 'credit_note') || str_contains($t, 'retailer') || str_contains($t, 'billing')) {
            return 'Sales';
        }
        if (str_contains($t, 'purchase') || str_contains($t, 'supplier') || str_contains($t, 'debit_note') || str_contains($t, 'grn')) {
            return 'Purchase';
        }
        if (str_contains($t, 'stock') || str_contains($t, 'warehouse') || str_contains($t, 'raw_material') || str_contains($t, 'item')) {
            return 'Inventory';
        }
        if (str_contains($t, 'attendance') || str_contains($t, 'employee') || str_contains($t, 'leave') || str_contains($t, 'shift') || str_contains($t, 'payroll') || str_contains($t, 'salary')) {
            return 'Attendance & HR';
        }
        return 'Master';
    }
}
