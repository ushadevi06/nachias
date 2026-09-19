<?php

namespace App\Services\RAG;

use ZipArchive;

class DocxDocumentationExtractor
{
    /**
     * Extract structured knowledge chunks from Nachias Documentation.docx.
     *
     * @return array<int, array>
     */
    public function extract(?string $filePath = null): array
    {
        $path = $filePath ?: base_path('Nachias Documentation.docx');
        if (!file_exists($path)) {
            return [];
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (empty($xml)) {
            return [];
        }

        return $this->parseXmlToChunks($xml);
    }

    /**
     * Parse document XML into structured chunks with accurate module tracking.
     */
    protected function parseXmlToChunks(string $xml): array
    {
        // Replace paragraph and break tags with newlines
        $xml = preg_replace('/<\/w:p>/i', "\n", $xml);
        $xml = preg_replace('/<w:br\/>/i', "\n", $xml);
        $xml = preg_replace('/<w:tab\/>/i', "\t", $xml);
        $text = strip_tags($xml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = $this->sanitizeString($text);

        $lines = explode("\n", $text);
        $cleanLines = [];
        foreach ($lines as $line) {
            $l = trim($line);
            if ($l !== '') {
                $cleanLines[] = $l;
            }
        }

        $chunks = [];
        $currentModule = 'General';
        $currentSection = 'General Overview';
        $currentLines = [];

        foreach ($cleanLines as $line) {
            // Detect explicit module headers: e.g. "3. Employees Module:", "24. Purchase Invoices Module:", "28. Job Card Entry:"
            $matchedModule = null;

            if (preg_match('/^(\d+\.\s*)?([A-Za-z0-9\s&()–—\/-]+)\s+Module:?$/i', $line, $m)) {
                $matchedModule = trim($m[2]);
            } elseif (preg_match('/^(\d+\.\s+)([A-Za-z0-9\s&()–—\/-]+):?$/', $line, $m)) {
                $titleCandidate = trim($m[2]);
                // Filter out non-module numbered items (like "1. Basic Information" or "1. Identification")
                if (!preg_match('/^(Basic|Identification|Location|Contact|Tax|Form|Filter|Data Table|Search|Actions|Pagination|Status)/i', $titleCandidate)) {
                    $matchedModule = $titleCandidate;
                }
            } elseif (preg_match('/^(All Attendance Tab|Absent Report|Manage Leaves|Overtime Condition|Monthly Payroll|Bulk Payslip)/i', $line)) {
                if (str_contains($line, 'Attendance') || str_contains($line, 'Absent')) {
                    $matchedModule = 'Attendance';
                } elseif (str_contains($line, 'Leave')) {
                    $matchedModule = 'Leaves';
                } elseif (str_contains($line, 'Overtime')) {
                    $matchedModule = 'Overtime';
                } elseif (str_contains($line, 'Payroll') || str_contains($line, 'Payslip')) {
                    $matchedModule = 'Payroll';
                }
            }

            if ($matchedModule) {
                if (!empty($currentLines)) {
                    $chunk = $this->buildChunk($currentModule, $currentSection, $currentLines);
                    if ($chunk) {
                        $chunks[] = $chunk;
                    }
                    $currentLines = [];
                }
                $currentModule = $this->normalizeModuleName($matchedModule);
                $currentSection = $currentModule . " Overview";
                $currentLines[] = $line;
                continue;
            }

            // Check if line represents a screen or sub-section header (e.g. "Sales & Order Dashboard:", "Employee Add Page:", "Roles Add Page:", "Employee’s View Details Page:")
            if (preg_match('/^[A-Za-z0-9\s\'’&()–—\/-]{3,65}:$/', $line) && !preg_match('/^(Email|Password|Site Link|Note|Status|Address|Phone|Contact|Gross|Total|PF|ESI|IFSC|State & City):/i', $line)) {
                if (preg_match('/(Add|Edit|View Details|Details|Dashboard|Management|Report|Creation|Entry|Matrix|Import|Page)/i', $line)) {
                    if (!empty($currentLines)) {
                        $chunk = $this->buildChunk($currentModule, $currentSection, $currentLines);
                        if ($chunk) {
                            $chunks[] = $chunk;
                        }
                        $currentLines = [];
                    }
                    $currentSection = rtrim(trim($line), ':');
                    $currentLines[] = "### " . $currentSection;
                    continue;
                }
            }

            $currentLines[] = $line;

            // Keep chunk sizes focused (~20 lines) for 1.5B model
            if (count($currentLines) >= 20) {
                $chunk = $this->buildChunk($currentModule, $currentSection, $currentLines);
                if ($chunk) {
                    $chunks[] = $chunk;
                }
                $currentLines = [];
            }
        }

        if (!empty($currentLines)) {
            $chunk = $this->buildChunk($currentModule, $currentSection, $currentLines);
            if ($chunk) {
                $chunks[] = $chunk;
            }
        }

        return $chunks;
    }

    /**
     * Normalize module name to clean standard.
     */
    protected function normalizeModuleName(string $raw): string
    {
        $m = trim(preg_replace('/^\d+\.\s*/', '', $raw));
        if (str_contains($m, 'System Utility - Logs')) return 'System Utility';
        if (str_contains($m, 'System Utility - Document')) return 'System Utility';
        if (str_contains($m, 'System Utility - Backup')) return 'System Utility';
        if (str_contains($m, 'Reports -')) return 'Reports';
        if (str_contains($m, 'Ticket Management')) return 'Ticket Management';
        if (str_contains($m, 'Settings')) return 'Settings';
        if (str_contains($m, 'Employees')) return 'Employees';
        if (str_contains($m, 'Job Card')) return 'Production';
        if (str_contains($m, 'GRN Entry')) return 'Store';
        if (str_contains($m, 'Stock Entry')) return 'Store';
        if (str_contains($m, 'Debit Notes')) return 'Store';
        if (str_contains($m, 'Manage Payments')) return 'Sales';
        return $m;
    }

    /**
     * Build knowledge chunk array.
     */
    protected function buildChunk(string $module, string $section, array $lines): ?array
    {
        $body = trim(implode("\n", $lines));
        if (strlen($body) < 20) {
            return null;
        }

        $title = "Documentation: {$section} ({$module})";

        // Clean alphanumeric keywords only
        $words = preg_split('/[^a-zA-Z0-9]+/', strtolower($title . ' ' . $body));
        $stopWords = ['the', 'and', 'for', 'with', 'this', 'that', 'from', 'into', 'shows', 'displays', 'helps', 'each', 'page', 'module', 'nachias', 'erp', 'allows', 'enter', 'select', 'user', 'users'];
        $keywords = [];
        foreach ($words as $w) {
            if (strlen($w) >= 3 && !in_array($w, $stopWords, true)) {
                $keywords[$w] = true;
            }
        }

        // Add contextual synonyms
        if (str_contains(strtolower($section), 'employee add')) {
            $keywords['add'] = true;
            $keywords['new'] = true;
            $keywords['create'] = true;
            $keywords['employee'] = true;
        }

        $keywordStr = implode(', ', array_slice(array_keys($keywords), 0, 30));

        $markdownContent = "### {$title}\n"
                         . "- **Module**: {$module}\n"
                         . "- **Section / Screen**: {$section}\n"
                         . "- **Source**: Official Nachias ERP Documentation\n\n"
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
            ],
        ];
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
