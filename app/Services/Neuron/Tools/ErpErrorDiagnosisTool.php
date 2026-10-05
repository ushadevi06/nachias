<?php

declare(strict_types=1);

namespace App\Services\Neuron\Tools;

use App\Models\ErpRagKnowledgeChunk;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class ErpErrorDiagnosisTool extends Tool
{
    protected string $name = 'diagnose_erp_error';

    protected ?string $description = 'Diagnose ERP validation errors, SQL unique constraint failures, duplicate codes, draft-only edit restrictions, or missing field errors and return step-by-step resolution.';

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'error_message',
                type: PropertyType::STRING,
                description: 'The exact error message, alert text, or validation failure from the user or screenshot.',
                required: true
            ),
            new ToolProperty(
                name: 'screen_context',
                type: PropertyType::STRING,
                description: 'The ERP screen, form, or transaction where the error occurred (e.g., "Purchase Order", "GRN Entry", "Sales Invoice").',
                required: false
            ),
        ];
    }

    public function __invoke(string $error_message, ?string $screen_context = null): string
    {
        $cleanError = trim($error_message);
        $cleanContext = trim((string) $screen_context);

        // Search for relevant controller validation and error handling chunks
        $query = ErpRagKnowledgeChunk::where('source_type', 'controller');

        if ($cleanContext !== '') {
            $query->where(function ($q) use ($cleanContext) {
                $q->where('screen', 'like', "%{$cleanContext}%")
                  ->orWhere('title', 'like', "%{$cleanContext}%")
                  ->orWhere('keywords', 'like', "%{$cleanContext}%");
            });
        }

        $chunks = $query->limit(3)->get();

        $output = "### ERP ERROR DIAGNOSIS & RESOLUTION REPORT\n\n";
        $output .= "**Reported Error / Issue:** {$cleanError}\n";
        if ($cleanContext !== '') {
            $output .= "**Screen Context:** {$cleanContext}\n";
        }
        $output .= "\n";

        // General heuristics based on common ERP patterns
        $errorLower = strtolower($cleanError);

        if (str_contains($errorLower, 'already taken') || str_contains($errorLower, 'duplicate entry') || str_contains($errorLower, 'already exists')) {
            $output .= "**Root Cause:** A unique constraint was violated. The code/number/email/name you entered is already in use by another record in the database.\n";
            $output .= "**Resolution Steps:**\n";
            $output .= "1. Change the code or identifier to a new, unique value.\n";
            $output .= "2. Check the existing list to verify if the record was already created.\n";
        } elseif (str_contains($errorLower, 'required') || str_contains($errorLower, 'cannot be null') || str_contains($errorLower, 'field is mandatory')) {
            $output .= "**Root Cause:** One or more mandatory fields were left blank or not selected before submitting the form.\n";
            $output .= "**Resolution Steps:**\n";
            $output .= "1. Inspect the form for highlighted red inputs or fields with an asterisk (*).\n";
            $output .= "2. Ensure all dropdowns have a selected option and required dates/quantities are entered.\n";
        } elseif (str_contains($errorLower, 'draft') || str_contains($errorLower, 'cannot edit') || str_contains($errorLower, 'approved') || str_contains($errorLower, 'dispatched')) {
            $output .= "**Root Cause:** Record status restriction. In Nachias ERP, only records in 'Draft' status can be modified. Once a record is Approved, Completed, or Dispatched, it is locked against direct edits to preserve financial and audit integrity.\n";
            $output .= "**Resolution Steps:**\n";
            $output .= "1. Check the current status badge of the record.\n";
            $output .= "2. If a correction is needed on an approved transaction, create a Debit/Credit Note or contact an administrator with reversal permissions.\n";
        } elseif (str_contains($errorLower, 'stock') || str_contains($errorLower, 'insufficient') || str_contains($errorLower, 'out of stock')) {
            $output .= "**Root Cause:** Insufficient inventory or warehouse stock for the selected item and batch.\n";
            $output .= "**Resolution Steps:**\n";
            $output .= "1. Verify current stock in **Store > Stock Entry** or **Reports > Warehouse Reports**.\n";
            $output .= "2. Create a Goods Receipt (GRN) or Stock Entry before dispatching.\n";
        } else {
            $output .= "**Root Cause:** Form validation failure or transaction constraint in controller.\n";
            $output .= "**Resolution Steps:**\n";
            $output .= "1. Verify all input field data types and mandatory select options.\n";
            $output .= "2. Ensure master dependencies (e.g. Supplier, Customer, Warehouse, UOM) are properly configured before creating transactional entries.\n";
        }

        if ($chunks->isNotEmpty()) {
            $output .= "\n**Controller Business Logic Context:**\n";
            foreach ($chunks as $c) {
                $output .= "--- [{$c->title}] ---\n" . mb_substr($c->content, 0, 500) . "\n\n";
            }
        }

        return $output;
    }
}
