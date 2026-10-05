<?php

declare(strict_types=1);

namespace App\Services\Neuron\Tools;

use App\Models\ErpRagKnowledgeChunk;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class ErpNavigationGuideTool extends Tool
{
    protected string $name = 'get_erp_navigation_path';

    protected ?string $description = 'Get the exact top navigation bar menu path, relative URL, and Add button instructions for any Nachias ERP screen.';

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'screen_name',
                type: PropertyType::STRING,
                description: 'The name of the ERP screen or module (e.g. "Credit Notes", "Purchase Orders", "Employees", "GRN Entry", "Billing").',
                required: true
            ),
        ];
    }

    public function __invoke(string $screen_name): string
    {
        $cleanScreen = trim($screen_name);

        $chunk = ErpRagKnowledgeChunk::where('source_type', 'menu')
            ->where(function ($q) use ($cleanScreen) {
                $q->where('screen', 'like', "%{$cleanScreen}%")
                  ->orWhere('title', 'like', "%{$cleanScreen}%")
                  ->orWhere('keywords', 'like', "%{$cleanScreen}%");
            })
            ->first();

        if ($chunk) {
            $mainMenu = $chunk->module ?: 'Main Menu';
            $screen = $chunk->screen ?: $cleanScreen;
            $menuPath = $chunk->menu_path ?: "{$mainMenu} > {$screen}";
            $url = $chunk->url ?: '/' . strtolower(str_replace(' ', '_', $screen));
            $addUrl = rtrim($url, '/') . '/add';

            return "### ERP NAVIGATION GUIDE FOR: {$screen}\n\n"
                . "1. In the top navigation bar, click **{$mainMenu}** main menu -> navigate to **{$screen}** (`{$menuPath}` | URL: `{$url}`).\n"
                . "2. Click on the 'Add' button located at the top right of the page (or navigate directly to `{$addUrl}`).\n";
        }

        return "No specific navigation path registered for screen: \"{$screen_name}\". Please check the menu list.";
    }
}
