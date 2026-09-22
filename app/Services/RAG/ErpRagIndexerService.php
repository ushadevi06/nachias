<?php

namespace App\Services\RAG;

use App\Models\ErpRagKnowledgeChunk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ErpRagIndexerService
{
    protected CodeRouteMenuExtractor $menuExtractor;
    protected DocxDocumentationExtractor $docxExtractor;
    protected SqlSchemaExtractor $sqlExtractor;
    protected FormDataLineageExtractor $fieldLineageExtractor;

    public function __construct(
        CodeRouteMenuExtractor $menuExtractor,
        DocxDocumentationExtractor $docxExtractor,
        SqlSchemaExtractor $sqlExtractor,
        FormDataLineageExtractor $fieldLineageExtractor
    ) {
        $this->menuExtractor = $menuExtractor;
        $this->docxExtractor = $docxExtractor;
        $this->sqlExtractor = $sqlExtractor;
        $this->fieldLineageExtractor = $fieldLineageExtractor;
    }

    /**
     * Run full indexing of all RAG sources.
     *
     * @param bool $fresh Whether to truncate the knowledge base first
     * @return array Summary of indexing results
     */
    public function indexAll(bool $fresh = true): array
    {
        try {
            DB::statement('ALTER TABLE erp_rag_knowledge_chunks CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        } catch (\Throwable $e) {
            // Ignore if already utf8mb4
        }

        if ($fresh) {
            ErpRagKnowledgeChunk::truncate();
        }

        $stats = [
            'menus_and_routes' => 0,
            'documentation' => 0,
            'sql_tables' => 0,
            'field_data_sources' => 0,
            'total_indexed' => 0,
        ];

        // 1. Index Laravel Code (Menus & Routes)
        $codeChunks = $this->menuExtractor->extract();
        $this->batchInsert($codeChunks);
        $stats['menus_and_routes'] = count($codeChunks);

        // 2. Index Nachias Documentation.docx
        $docxChunks = $this->docxExtractor->extract();
        $this->batchInsert($docxChunks);
        $stats['documentation'] = count($docxChunks);

        // 3. Index nachias.sql
        $sqlChunks = $this->sqlExtractor->extract();
        $this->batchInsert($sqlChunks);
        $stats['sql_tables'] = count($sqlChunks);

        // 4. Index Form Field & Select Box Data Lineages
        $fieldChunks = $this->fieldLineageExtractor->extract();
        $this->batchInsert($fieldChunks);
        $stats['field_data_sources'] = count($fieldChunks);

        $stats['total_indexed'] = $stats['menus_and_routes'] + $stats['documentation'] + $stats['sql_tables'] + $stats['field_data_sources'];

        Log::info('ERP RAG indexing completed successfully', $stats);

        return $stats;
    }

    /**
     * Batch insert chunks for performance.
     */
    protected function batchInsert(array $chunks, int $batchSize = 100): void
    {
        $now = now();
        $batch = [];

        foreach ($chunks as $chunk) {
            $batch[] = [
                'source_type' => $this->cleanString($chunk['source_type']),
                'module' => $this->cleanString($chunk['module'] ?? 'General'),
                'screen' => !empty($chunk['screen']) ? $this->cleanString($chunk['screen']) : null,
                'title' => $this->cleanString($chunk['title']),
                'keywords' => !empty($chunk['keywords']) ? $this->cleanString($chunk['keywords']) : '',
                'url' => !empty($chunk['url']) ? $this->cleanString($chunk['url']) : null,
                'menu_path' => !empty($chunk['menu_path']) ? $this->cleanString($chunk['menu_path']) : null,
                'content' => $this->cleanString($chunk['content']),
                'meta' => isset($chunk['meta']) ? json_encode($chunk['meta']) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= $batchSize) {
                ErpRagKnowledgeChunk::insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            ErpRagKnowledgeChunk::insert($batch);
        }
    }

    protected function cleanString(?string $str): string
    {
        if ($str === null) {
            return '';
        }

        $search = [
            "\xc2\xab", "\xc2\xbb", "\xe2\x80\x98", "\xe2\x80\x99",
            "\xe2\x80\x9a", "\xe2\x80\x9b", "\xe2\x80\x9c", "\xe2\x80\x9d",
            "\xe2\x80\x9e", "\xe2\x80\x9f", "\xe2\x80\x93", "\xe2\x80\x94",
            "\xe2\x80\xa6", "\xa0", chr(145), chr(146), chr(147), chr(148), chr(150), chr(151)
        ];
        $replace = [
            '<<', '>>', "'", "'",
            "'", "'", '"', '"',
            '"', '"', '-', '-',
            '...', ' ', "'", "'", '"', '"', '-', '-'
        ];
        $cleaned = str_replace($search, $replace, $str);

        return mb_convert_encoding($cleaned, 'UTF-8', 'UTF-8');
    }
}
