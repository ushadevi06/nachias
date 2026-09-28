<?php

namespace App\Services\RAG;

class MarkdownDocumentationExtractor
{
    /**
     * Extract structured knowledge chunks from Nachias Documentation.md.
     *
     * @param string|null $filePath
     * @return array<int, array>
     */
    public function extract(?string $filePath = null): array
    {
        $path = $filePath ?: base_path('Nachias Documentation.md');
        if (!file_exists($path) || !is_readable($path)) {
            return [];
        }

        $content = file_get_contents($path);
        if (empty($content)) {
            return [];
        }

        return $this->parseMarkdownToChunks($content);
    }

    /**
     * Parse the Markdown content into structured chunks with accurate module tracking.
     */
    protected function parseMarkdownToChunks(string $content): array
    {
        $lines = explode("\n", $content);
        $cleanLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Skip image lines — not useful for text-based RAG
            if (preg_match('/^!\[.*?\]\(.*?\)$/', $trimmed)) {
                continue;
            }

            // Skip empty bold markers like "****" or "** **"
            if (preg_match('/^\*{2,}$/', $trimmed) || $trimmed === '** **' || $trimmed === '****') {
                continue;
            }

            // Skip lines that are only whitespace/tabs
            if ($trimmed === '' || preg_match('/^\*?\t+\*?$/', $trimmed)) {
                continue;
            }

            $cleanLines[] = $trimmed;
        }

        $chunks = [];
        $currentModule = 'General';
        $currentSection = 'General Overview';
        $currentLines = [];

        foreach ($cleanLines as $line) {
            // Detect explicit module headers
            $matchedModule = $this->detectModuleHeader($line);

            if ($matchedModule) {
                // Flush current chunk
                if (!empty($currentLines)) {
                    $chunk = $this->buildChunk($currentModule, $currentSection, $currentLines);
                    if ($chunk) {
                        $chunks[] = $chunk;
                    }
                    $currentLines = [];
                }
                $currentModule = $this->normalizeModuleName($matchedModule);
                $currentSection = $currentModule . ' Overview';
                $currentLines[] = $this->stripBold($line);
                continue;
            }

            // Detect sub-section headers (Add Page, View Details, Management, Dashboard, Entry, etc.)
            $matchedSection = $this->detectSectionHeader($line);
            if ($matchedSection) {
                if (!empty($currentLines)) {
                    $chunk = $this->buildChunk($currentModule, $currentSection, $currentLines);
                    if ($chunk) {
                        $chunks[] = $chunk;
                    }
                    $currentLines = [];
                }
                $currentSection = $matchedSection;
                $currentLines[] = '### ' . $currentSection;
                continue;
            }

            // Strip bold markers for cleaner text but preserve content
            $currentLines[] = $this->stripBold($line);

            // Keep chunk sizes focused (~25 lines) for the 1.5B model's context window
            if (count($currentLines) >= 25) {
                $chunk = $this->buildChunk($currentModule, $currentSection, $currentLines);
                if ($chunk) {
                    $chunks[] = $chunk;
                }
                $currentLines = [];
            }
        }

        // Flush remaining lines
        if (!empty($currentLines)) {
            $chunk = $this->buildChunk($currentModule, $currentSection, $currentLines);
            if ($chunk) {
                $chunks[] = $chunk;
            }
        }

        return $chunks;
    }

    /**
     * Detect if a line is a module header.
     *
     * Matches patterns like:
     *   - "**2. Dashboard Module:**"
     *   - "**19. Parties->Customers Module:**"
     *   - "**22. Item Setup ->Items Module:**"
     *   - "**28. Job Card Entry:**"
     *   - "**34. Emp.Payroll & Attendance - Attendance:**"
     *   - "**39. Reports - Sales & Marketing Reports:**"
     *   - "**40. Ticket Management:**"
     *   - "**41. Settings:**"
     *   - "- **Login Module:**"
     *
     * @return string|null The raw module name if detected, null otherwise
     */
    protected function detectModuleHeader(string $line): ?string
    {
        // Remove leading "- " if present
        $line = preg_replace('/^-\s*/', '', $line);

        // Pattern 1: Numbered module headers like "**23. Purchase Orders Module:**" or "**28. Job Card Entry:**"
        if (preg_match('/^\*{0,2}\s*(\d+)\.\s*(.+?)\s*:?\s*\*{0,2}\s*$/', $line, $m)) {
            $title = trim($m[2], " *:\t");

            // Filter out sub-items that look like numbered steps/fields (e.g., "1. Basic Information", "2. Location Details")
            if ((int) $m[1] <= 10 && preg_match('/^(Basic|Identification|Location|Contact|Tax|Form|Filter|Data Table|Search|Actions|Pagination|Status|Financial|Address|Salary|Bank)/i', $title)) {
                return null;
            }

            // Must be a meaningful module name (at least has a known module keyword or is a numbered section >=2)
            if ((int) $m[1] >= 2 || preg_match('/(Module|Entry|Orders|Invoices|Notes|Management|Dashboard|Settings|Reports|Billing|Payments|Payroll|Attendance|Overtime)/i', $title)) {
                return $title;
            }
        }

        return null;
    }

    /**
     * Detect if a line is a sub-section header.
     *
     * Matches patterns like:
     *   - "**Employee Add Page:**"
     *   - "**Purchase Order Management:**"
     *   - "**GRN Entry Add:**"
     *   - "**Sales & Order Dashboard:**"
     *   - "#### **Step 1: General Information**"
     *   - "**Section 1: General Information**"
     *   - "Step 1: Task Issue (Main Form):"
     *   - "Production Dashboard:"
     *   - "Declared Holidays Tab"
     *   - "Staff Wise Report:"
     *   - "Add Monthly Payroll:"
     *
     * @return string|null The section name if detected, null otherwise
     */
    protected function detectSectionHeader(string $line): ?string
    {
        // Strip leading markdown heading markers (##, ###, ####, etc.)
        $stripped = preg_replace('/^#{1,6}\s*/', '', $line);
        // Strip bold markers
        $stripped = trim(str_replace(['**', '__'], '', $stripped));
        // Strip trailing colon
        $stripped = rtrim($stripped, ':');
        $stripped = trim($stripped);

        if (strlen($stripped) < 4 || strlen($stripped) > 80) {
            return null;
        }

        // Must contain a recognized section keyword
        if (preg_match('/(Add Page|Edit Page|View Details|Details Page|Management|Dashboard|Entry Add|Entry Details|Entry Management|Add Page|Print|Download|Report|Add Module|Tab$|Sticker|Barcode|Consumption|Costing|Work Order|Matrix|Import|Section \d|Step \d|Staff Wise|Declared Holidays|Monthly Payroll|Payroll Reports|Payslip)/i', $stripped)) {
            // Filter out non-section items
            if (preg_match('/^(Email|Password|Site Link|Note|Status|Address|Phone|Contact|Gross|Total|PF|ESI|IFSC|State \& City)$/i', $stripped)) {
                return null;
            }
            return $stripped;
        }

        // Check for explicit "Add <Something>" or "<Something> Add" patterns
        if (preg_match('/^(Add\s+\w+|.+\s+Add)$/i', $stripped) && strlen($stripped) < 50) {
            return $stripped;
        }

        return null;
    }

    /**
     * Normalize module name to clean standard categories.
     */
    protected function normalizeModuleName(string $raw): string
    {
        $m = trim($raw, " *:\t");
        // Remove "Module" suffix
        $m = preg_replace('/\s*Module\s*$/i', '', $m);
        // Remove leading number
        $m = preg_replace('/^\d+\.\s*/', '', $m);
        $m = trim($m);

        // Normalize nested menu paths
        if (preg_match('/Parties\s*->\s*(.+)/i', $m, $pm)) {
            return trim($pm[1]);
        }
        if (preg_match('/Item Setup\s*->\s*(.+)/i', $m, $pm)) {
            return trim($pm[1]);
        }
        if (preg_match('/Logistics Master\s*->\s*(.+)/i', $m, $pm)) {
            return trim($pm[1]);
        }
        if (preg_match('/Tailoring Specification\s*->\s*(.+)/i', $m, $pm)) {
            return trim($pm[1]);
        }
        if (preg_match('/Production Master\s*->\s*(.+)/i', $m, $pm)) {
            return trim($pm[1]);
        }

        // Group related modules
        if (str_contains($m, 'System Utility')) return 'System Utility';
        if (str_contains($m, 'Reports -')) return 'Reports';
        if (str_contains($m, 'Ticket Management')) return 'Ticket Management';
        if (str_contains($m, 'Settings')) return 'Settings';
        if (str_contains($m, 'Employees')) return 'Employees';
        if (str_contains($m, 'Job Card')) return 'Production';
        if (str_contains($m, 'Task Management')) return 'Production';
        if (str_contains($m, 'Production Receipts')) return 'Production';
        if (str_contains($m, 'GRN Entry')) return 'Store';
        if (str_contains($m, 'Stock Entry')) return 'Store';
        if (str_contains($m, 'Debit Notes')) return 'Store';
        if (str_contains($m, 'Manage Payments')) return 'Finance';
        if (str_contains($m, 'Billing')) return 'Finance';
        if (str_contains($m, 'Credit Notes')) return 'Sales';
        if (str_contains($m, 'Sales Invoices')) return 'Sales';
        if (str_contains($m, 'Sales Orders')) return 'Sales';
        if (str_contains($m, 'Purchase Orders')) return 'Purchase';
        if (str_contains($m, 'Purchase Invoices')) return 'Purchase';
        if (preg_match('/Emp\.?\s*Payroll\s*&?\s*Attendance\s*-?\s*(.+)/i', $m, $pa)) {
            $sub = trim($pa[1]);
            if (str_contains($sub, 'Attendance')) return 'Attendance';
            if (str_contains($sub, 'Leave')) return 'Leaves';
            if (str_contains($sub, 'Overtime')) return 'Overtime';
            if (str_contains($sub, 'Payroll')) return 'Payroll';
            return 'Payroll';
        }

        return $m ?: 'General';
    }

    /**
     * Build a knowledge chunk array from parsed lines.
     */
    protected function buildChunk(string $module, string $section, array $lines): ?array
    {
        $body = trim(implode("\n", $lines));
        if (strlen($body) < 25) {
            return null;
        }

        $title = "Documentation: {$section} ({$module})";

        // Extract clean keywords
        $words = preg_split('/[^a-zA-Z0-9]+/', strtolower($title . ' ' . $body));
        $stopWords = [
            'the', 'and', 'for', 'with', 'this', 'that', 'from', 'into', 'shows',
            'displays', 'helps', 'each', 'page', 'module', 'nachias', 'erp', 'allows',
            'enter', 'select', 'user', 'users', 'using', 'used', 'based', 'can', 'will',
            'all', 'are', 'its', 'has', 'was', 'been', 'being', 'such', 'also', 'new',
            'any', 'not', 'only', 'once', 'when', 'upon', 'like', 'directly', 'provides',
        ];

        $keywords = [];
        foreach ($words as $w) {
            if (strlen($w) >= 3 && !in_array($w, $stopWords, true)) {
                $keywords[$w] = true;
            }
        }

        // Add contextual synonyms based on section type
        $sectionLower = strtolower($section);
        if (str_contains($sectionLower, 'add page') || str_contains($sectionLower, 'add ')) {
            $keywords['add'] = true;
            $keywords['new'] = true;
            $keywords['create'] = true;
        }
        if (str_contains($sectionLower, 'edit')) {
            $keywords['edit'] = true;
            $keywords['update'] = true;
            $keywords['modify'] = true;
        }
        if (str_contains($sectionLower, 'view details')) {
            $keywords['view'] = true;
            $keywords['details'] = true;
        }
        if (str_contains($sectionLower, 'dashboard')) {
            $keywords['dashboard'] = true;
            $keywords['summary'] = true;
            $keywords['overview'] = true;
        }
        if (str_contains($sectionLower, 'report')) {
            $keywords['report'] = true;
            $keywords['analysis'] = true;
        }

        $keywordStr = implode(', ', array_slice(array_keys($keywords), 0, 35));

        $markdownContent = "### {$title}\n"
            . "- **Module**: {$module}\n"
            . "- **Section / Screen**: {$section}\n"
            . "- **Source**: Official Nachias ERP Documentation (Markdown)\n\n"
            . $body;

        return [
            'source_type' => 'documentation',
            'module' => $module,
            'screen' => $section,
            'title' => $this->sanitizeString($title),
            'keywords' => $this->sanitizeString($keywordStr),
            'url' => null,
            'menu_path' => null,
            'content' => $this->sanitizeString($markdownContent),
            'meta' => [
                'module' => $module,
                'section' => $section,
                'format' => 'markdown',
            ],
        ];
    }

    /**
     * Strip bold markers (**) from text while preserving the content.
     */
    protected function stripBold(string $str): string
    {
        // Replace **text** with text
        $str = preg_replace('/\*\*(.+?)\*\*/', '$1', $str);
        // Remove leftover consecutive asterisks
        $str = preg_replace('/\*{2,}/', '', $str);
        return trim($str);
    }

    /**
     * Sanitize smart quotes, special dashes, and non-UTF8 bytes.
     */
    protected function sanitizeString(string $str): string
    {
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
        $str = str_replace($search, $replace, $str);

        return mb_convert_encoding($str, 'UTF-8', 'UTF-8');
    }
}
