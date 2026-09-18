<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TranslationService
{
    /**
     * Check if a string contains Tamil characters.
     * Tamil Unicode range is U+0B80 to U+0BFF.
     *
     * @param string $text
     * @return bool
     */
    public function isTamil(string $text): bool
    {
        return (bool) preg_match('/[\x{0B80}-\x{0BFF}]/u', $text);
    }

    /**
     * Translate text between languages (e.g. Tamil to English or English to Tamil).
     *
     * @param string $text
     * @param string $targetLang
     * @param string $sourceLang
     * @return array
     */
    public function translate(string $text, string $targetLang = 'en', string $sourceLang = 'auto'): array
    {
        $trimmedText = trim($text);

        if ($trimmedText === '') {
            return [
                'original' => $text,
                'translated' => $text,
                'is_translated' => false,
                'source_language' => $sourceLang,
                'target_language' => $targetLang,
            ];
        }

        $hasTamil = $this->isTamil($trimmedText);

        // Resolve source language if auto
        if ($sourceLang === 'auto') {
            $sourceLang = $hasTamil ? 'ta' : 'en';
        }

        // If target and source are the same, no translation needed
        if ($targetLang === $sourceLang) {
            return [
                'original' => $trimmedText,
                'translated' => $trimmedText,
                'is_translated' => false,
                'source_language' => $sourceLang,
                'target_language' => $targetLang,
            ];
        }

        // If translating to English and text is already pure ASCII without Tamil
        if ($targetLang === 'en' && !$hasTamil && !preg_match('/[^\x20-\x7E\r\n\t]/', $trimmedText)) {
            return [
                'original' => $trimmedText,
                'translated' => $trimmedText,
                'is_translated' => false,
                'source_language' => 'en',
                'target_language' => $targetLang,
            ];
        }

        // If translating to Tamil and text is already predominantly Tamil characters
        if ($targetLang === 'ta' && $hasTamil && !preg_match('/[A-Za-z]{3,}/', $trimmedText)) {
            return [
                'original' => $trimmedText,
                'translated' => $trimmedText,
                'is_translated' => false,
                'source_language' => 'ta',
                'target_language' => $targetLang,
            ];
        }

        // Execute translation with chunking & caching
        $translated = $this->translateTextChunks($trimmedText, $targetLang, $sourceLang);

        $wasTranslated = ($translated !== '' && $translated !== $trimmedText);

        return [
            'original' => $trimmedText,
            'translated' => $translated !== '' ? $translated : $trimmedText,
            'is_translated' => $wasTranslated,
            'source_language' => $sourceLang,
            'target_language' => $targetLang,
            'provider' => 'mymemory',
        ];
    }

    /**
     * Smart ERP Response Translator:
     * Translates ERP responses to Tamil while preserving English Page Names,
     * Menu Navigation paths, URLs, Modules, and Stages.
     *
     * @param string $response The full English AI response
     * @param string $targetLang Default 'ta' (Tamil)
     * @return array
     */
    public function translateErpResponse(string $response, string $targetLang = 'ta'): array
    {
        $trimmedResponse = trim($response);

        if ($trimmedResponse === '' || $targetLang !== 'ta') {
            return [
                'original' => $response,
                'translated' => $response,
                'is_translated' => false,
                'source_language' => 'en',
                'target_language' => $targetLang,
            ];
        }

        // English-only structural field keys (case-insensitive)
        $englishKeys = [
            'erp module',
            'module',
            'page name',
            'page',
            'menu navigation',
            'current menu navigation',
            'next menu navigation',
            'navigation',
            'url',
            'route',
            'current stage',
            'next expected stage',
            'stage',
            'related workflow',
            'workflow',
            'flow',
            'evidence',
            'table',
            'database table',
            'field name',
            'confidence',
        ];

        // Split response by block paragraphs (double newline or markdown breaks)
        $blocks = preg_split('/(\r\n\s*\r\n|\n\s*\n)/', $trimmedResponse);
        $processedBlocks = [];

        foreach ($blocks as $block) {
            $trimmedBlock = trim($block);
            if ($trimmedBlock === '') {
                continue;
            }

            // Match "**Key:** Content" or "**Key**: Content" or "Key: Content"
            if (preg_match('/^(?:\*\*)?([A-Za-z0-9\s\/\-_]+?)(?:\:?\*\*|\*\*\:|\:)\s*(.*)$/s', $trimmedBlock, $matches)) {
                $keyRaw = trim($matches[1]);
                $keyLower = strtolower($keyRaw);
                $content = ltrim(trim($matches[2]), "*: \t\r\n");

                // Check if this key belongs to English-only navigation fields
                $isEnglishOnly = false;
                foreach ($englishKeys as $ek) {
                    if ($keyLower === $ek || str_starts_with($keyLower, $ek)) {
                        $isEnglishOnly = true;
                        break;
                    }
                }

                if ($isEnglishOnly) {
                    // Keep entirely in English as requested
                    $processedBlocks[] = $trimmedBlock;
                } elseif ($keyLower === 'restrictions') {
                    // Standardized ERP disclaimer in Tamil
                    $processedBlocks[] = "**Restrictions (கட்டுப்பாடு):**\nஇந்த பதில் ERP வழிகாட்டுதலுக்கு மட்டுமே. எந்த ERP பரிவர்த்தனையும் மேற்கொள்ளப்படவில்லை.";
                } else {
                    // Main purpose or explanation: translate content into Tamil
                    $translatedContent = $this->translateProtectingEnglishPaths($content, $targetLang);

                    // Provide clear bilingual label so users easily recognize the section
                    $label = $keyRaw;
                    if (str_contains($keyLower, 'purpose')) {
                        $label = 'Purpose (நோக்கம்)';
                    } elseif (str_contains($keyLower, 'reason')) {
                        $label = 'Reason (காரணம்)';
                    } elseif (str_contains($keyLower, 'explanation')) {
                        $label = 'Explanation (விளக்கம்)';
                    } elseif (str_contains($keyLower, 'behavior')) {
                        $label = 'Field Behavior (புலத்தின் செயல்பாடு)';
                    } elseif (str_contains($keyLower, 'answer')) {
                        $label = 'Answer (பதில்)';
                    }

                    $separator = str_contains($trimmedBlock, "\n") ? "\n" : ' ';
                    $processedBlocks[] = "**{$label}:**{$separator}{$translatedContent}";
                }
            } else {
                // Freeform text paragraph: protect any embedded page paths/URLs, then translate
                $processedBlocks[] = $this->translateProtectingEnglishPaths($trimmedBlock, $targetLang);
            }
        }

        $finalTranslated = implode("\n\n", $processedBlocks);

        return [
            'original' => $trimmedResponse,
            'translated' => $finalTranslated,
            'is_translated' => true,
            'source_language' => 'en',
            'target_language' => 'ta',
            'provider' => 'mymemory_selective',
        ];
    }

    /**
     * Protect breadcrumbs (e.g. "Store > GRN Entry") and URLs (e.g. "/grn_entries")
     * from being translated into Tamil, translate the remainder, and restore the paths.
     *
     * @param string $text
     * @param string $targetLang
     * @return string
     */
    public function translateProtectingEnglishPaths(string $text, string $targetLang = 'ta'): string
    {
        $placeholders = [];

        // 1. Protect breadcrumb menu paths (e.g. "Store > GRN Entry" or "Purchase > Purchase Orders")
        $patternBreadcrumb = '/\b[A-Za-z0-9\.\s]+(?:\s*>\s*[A-Za-z0-9\.\s]+)+\b/';
        $textWithPlaceholders = preg_replace_callback($patternBreadcrumb, function ($m) use (&$placeholders) {
            $token = 'XYZNAV' . count($placeholders) . 'XYZ';
            $placeholders[$token] = trim($m[0]);
            return $token;
        }, $text);

        // 2. Protect route URLs (e.g. "/purchase_orders" or "/grn_entries")
        $patternUrl = '/\/[a-zA-Z0-9_\-\/]+/';
        $textWithPlaceholders = preg_replace_callback($patternUrl, function ($m) use (&$placeholders) {
            $token = 'XYZURL' . count($placeholders) . 'XYZ';
            $placeholders[$token] = $m[0];
            return $token;
        }, $textWithPlaceholders);

        // Translate the text
        $translated = $this->translateTextChunks($textWithPlaceholders, $targetLang, 'en');

        // Restore English placeholders (case-insensitive replace or clean regex)
        foreach ($placeholders as $token => $original) {
            $translated = str_ireplace($token, $original, $translated);
            // Also handle if MT added spaces around token
            $spaced = preg_replace('/\s*(' . preg_quote($token, '/') . ')\s*/i', ' $original ', $translated);
            if ($spaced !== null) {
                $translated = $spaced;
            }
        }

        return $translated;
    }

    /**
     * Chunk text into safe slices <= 400 characters, translate each chunk, and recombine.
     *
     * @param string $text
     * @param string $targetLang
     * @param string $sourceLang
     * @return string
     */
    protected function translateTextChunks(string $text, string $targetLang, string $sourceLang): string
    {
        $chunks = $this->chunkText($text, 400);
        $translatedChunks = [];

        foreach ($chunks as $chunk) {
            $trimmedChunk = trim($chunk);
            if ($trimmedChunk === '') {
                $translatedChunks[] = '';
                continue;
            }

            $translated = $this->translateSingleChunk($trimmedChunk, $targetLang, $sourceLang);
            $translatedChunks[] = $translated ?? $trimmedChunk;
        }

        return implode("\n", $translatedChunks);
    }

    /**
     * Split text into chunks bounded by sentences or lines <= maxChunkSize characters.
     *
     * @param string $text
     * @param int $maxChunkSize
     * @return array
     */
    protected function chunkText(string $text, int $maxChunkSize = 400): array
    {
        // If already within size limit, return directly
        if (mb_strlen($text) <= $maxChunkSize) {
            return [$text];
        }

        $lines = explode("\n", $text);
        $chunks = [];
        $currentChunk = '';

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if (mb_strlen($currentChunk . "\n" . $trimmedLine) <= $maxChunkSize) {
                $currentChunk = ($currentChunk === '') ? $trimmedLine : ($currentChunk . "\n" . $trimmedLine);
            } else {
                if ($currentChunk !== '') {
                    $chunks[] = $currentChunk;
                    $currentChunk = '';
                }

                // If line itself is longer than maxChunkSize, split by sentence or period
                if (mb_strlen($trimmedLine) > $maxChunkSize) {
                    $sentences = preg_split('/(?<=[.!?])\s+/', $trimmedLine);
                    $subChunk = '';

                    foreach ($sentences as $sentence) {
                        if (mb_strlen($subChunk . ' ' . $sentence) <= $maxChunkSize) {
                            $subChunk = ($subChunk === '') ? $sentence : ($subChunk . ' ' . $sentence);
                        } else {
                            if ($subChunk !== '') {
                                $chunks[] = $subChunk;
                            }
                            $subChunk = $sentence;
                        }
                    }
                    if ($subChunk !== '') {
                        $chunks[] = $subChunk;
                    }
                } else {
                    $currentChunk = $trimmedLine;
                }
            }
        }

        if ($currentChunk !== '') {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }

    /**
     * Translate a single chunk (< 450 chars) with caching and rate limit protection.
     *
     * @param string $chunk
     * @param string $targetLang
     * @param string $sourceLang
     * @return string|null
     */
    protected function translateSingleChunk(string $chunk, string $targetLang, string $sourceLang): ?string
    {
        $cacheKey = 'trans_' . md5("{$sourceLang}_{$targetLang}_{$chunk}");
        $cacheTtl = config('services.translation.cache_seconds', 86400);

        try {
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
        } catch (Throwable $e) {
            // Cache failure should not block translation
        }

        $translated = null;

        // 1. Primary engine: MyMemory API with email parameter for extended quota
        $email = config('services.translation.mymemory_email', 'admin@nachias.com');
        $timeout = config('services.translation.timeout', 10);

        try {
            $queryParams = [
                'q' => $chunk,
                'langpair' => "{$sourceLang}|{$targetLang}",
            ];
            if (!empty($email)) {
                $queryParams['de'] = $email;
            }

            $response = Http::timeout($timeout)
                ->withHeaders(['Accept' => 'application/json'])
                ->get('https://api.mymemory.translated.net/get', $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                $rawTranslated = $data['responseData']['translatedText'] ?? null;

                if (!empty($rawTranslated) && is_string($rawTranslated)) {
                    $clean = trim(html_entity_decode($rawTranslated, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    $lowerClean = strtolower($clean);

                    // Ensure response does not contain quota or length warnings
                    if (
                        !str_contains($lowerClean, 'mymemory warning') &&
                        !str_contains($lowerClean, 'quota exceeded') &&
                        !str_contains($lowerClean, 'limit exceeded') &&
                        !str_contains($lowerClean, 'invalid target language')
                    ) {
                        $translated = $clean;
                    }
                }
            }
        } catch (Throwable $e) {
            Log::warning('MyMemory chunk translation failed: ' . $e->getMessage());
        }

        // Cache the successful translation
        if ($translated !== null && $translated !== '') {
            try {
                Cache::put($cacheKey, $translated, $cacheTtl);
            } catch (Throwable $e) {
                // Ignore cache storage error
            }
            return $translated;
        }

        return $chunk;
    }
}
