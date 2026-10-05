<?php

declare(strict_types=1);

namespace App\Services\Neuron\Tools;

use App\Services\RAG\ErpRagRetrieverService;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class ErpRagKnowledgeTool extends Tool
{
    protected string $name = 'search_erp_knowledge';

    protected ?string $description = 'Search the authoritative Nachias ERP knowledge base for documented workflows, controller validation rules, form fields, and documentation.';

    protected ErpRagRetrieverService $retriever;

    public function __construct(?ErpRagRetrieverService $retriever = null)
    {
        $this->retriever = $retriever ?? app(ErpRagRetrieverService::class);
    }

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'query',
                type: PropertyType::STRING,
                description: 'The search query or keyword about Nachias ERP (e.g., "purchase order workflow", "grn entry fields", "credit notes validation rules").',
                required: true
            ),
            new ToolProperty(
                name: 'limit',
                type: PropertyType::INTEGER,
                description: 'Maximum number of knowledge chunks to retrieve (default: 4).',
                required: false
            ),
        ];
    }

    public function __invoke(string $query, int $limit = 4): string
    {
        $result = $this->retriever->retrieve($query, $limit);

        if (empty($result['chunks']) || empty(trim($result['context']))) {
            return "No matching Nachias ERP knowledge chunks found for query: \"{$query}\".";
        }

        $formatted = "### RETRIEVED NACHIAS ERP KNOWLEDGE (Intent: {$result['intent']}):\n\n";
        $formatted .= $result['context'];

        return $formatted;
    }
}
