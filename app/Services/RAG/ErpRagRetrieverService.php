<?php

namespace App\Services\RAG;

use App\Models\ErpRagKnowledgeChunk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ErpRagRetrieverService
{
    /**
     * Retrieve the most relevant knowledge chunks for a query and format them for the LLM.
     *
     * @param string $query User prompt (English)
     * @param int $limit Maximum chunks to retrieve (default 4)
     * @return array ['context' => string, 'chunks' => array, 'intent' => string]
     */
    public function retrieve(string $query, int $limit = 4): array
    {
        $cleanQuery = trim($query);
        if ($cleanQuery === '') {
            return [
                'context' => '',
                'chunks' => [],
                'intent' => 'general',
            ];
        }

        $intent = $this->detectIntent($cleanQuery);
        $tokens = $this->extractQueryTokens($cleanQuery);

        $candidates = $this->searchCandidates($cleanQuery, $tokens, $intent);

        // Re-rank candidates based on composite relevance score
        $scored = $this->rerankCandidates($candidates, $cleanQuery, $tokens, $intent);

        // Select top distinct chunks with source diversity
        $topChunks = $this->selectDiverseChunks($scored, $limit, $intent);

        // Format context block
        $context = $this->formatContext($topChunks);

        Log::info('ERP RAG retrieval completed', [
            'query' => $cleanQuery,
            'intent' => $intent,
            'retrieved_count' => $topChunks->count(),
            'top_titles' => $topChunks->pluck('title')->all(),
        ]);

        return [
            'context' => $context,
            'chunks' => $topChunks->all(),
            'intent' => $intent,
        ];
    }

    /**
     * Classify user query intent.
     */
    protected function detectIntent(string $query): string
    {
        $q = strtolower($query);

        if (preg_match('/\b(table|database|schema|column|columns|datatype|primary key|foreign key|stored in|store in|sql)\b/i', $q)) {
            return 'schema';
        }

        if (preg_match('/\b(how to add|how to create|add new|create new|new employee|add employee|create employee|add role|create role|add |create )\b/i', $q)) {
            return 'creation';
        }

        if (preg_match('/\b(next step|next stage|after|before|workflow|lifecycle|process|flow|how does|what happens|approval|status transition)\b/i', $q)) {
            return 'workflow';
        }

        if (preg_match('/\b(where is|how to go|how to open|how to access|navigate|navigation|menu|path|url|link|screen|page|find|route)\b/i', $q)) {
            return 'navigation';
        }

        if (preg_match('/\b(what is|meaning of|purpose of|field|mandatory|dropdown|button|checkbox|calculated|formula)\b/i', $q)) {
            return 'field_explanation';
        }

        return 'general';
    }

    /**
     * Extract meaningful search tokens.
     */
    protected function extractQueryTokens(string $query): array
    {
        $lower = strtolower($query);
        $words = preg_split('/[^a-zA-Z0-9_\-]+/', $lower);

        $stopwords = [
            'what', 'is', 'the', 'next', 'step', 'stage', 'after', 'before', 'can', 'you',
            'how', 'to', 'in', 'on', 'at', 'for', 'from', 'with', 'about', 'and', 'or',
            'where', 'i', 'find', 'open', 'show', 'tell', 'me', 'please', 'does', 'do',
            'which', 'nachias', 'erp', 'system', 'get', 'who', 'whom', 'whose', 'why',
            'when', 'are', 'was', 'were', 'am', 'be', 'been', 'being', 'have', 'has',
            'had', 'a', 'an', 'this', 'that', 'these', 'those'
        ];

        $tokens = [];
        foreach ($words as $w) {
            $w = trim($w);
            if (strlen($w) >= 2 && !in_array($w, $stopwords, true)) {
                $tokens[] = $w;
            }
        }

        return array_unique($tokens);
    }

    /**
     * Fetch search candidates from MySQL via Fulltext and token matching.
     */
    protected function searchCandidates(string $rawQuery, array $tokens, string $intent): Collection
    {
        $candidates = collect();

        // 1. MySQL Fulltext Natural Language & Boolean Search
        if (!empty($tokens)) {
            $booleanTerms = array_map(function ($t) {
                return '+' . $t . '*';
            }, array_slice($tokens, 0, 6));
            $booleanQuery = implode(' ', $booleanTerms);

            try {
                $fulltextResults = ErpRagKnowledgeChunk::whereRaw(
                    "MATCH(title, keywords, content) AGAINST(? IN BOOLEAN MODE)",
                    [$booleanQuery]
                )->limit(25)->get();

                $candidates = $candidates->merge($fulltextResults);
            } catch (\Throwable $e) {
                // Fallback to LIKE if fulltext encounters an issue
            }
        }

        // 2. Token LIKE matching on title, screen, and keywords
        if (!empty($tokens)) {
            $likeQuery = ErpRagKnowledgeChunk::query();
            $likeQuery->where(function ($q) use ($tokens) {
                foreach (array_slice($tokens, 0, 4) as $token) {
                    $q->orWhere('title', 'LIKE', "%{$token}%")
                      ->orWhere('keywords', 'LIKE', "%{$token}%")
                      ->orWhere('screen', 'LIKE', "%{$token}%");
                }
            });
            $likeResults = $likeQuery->limit(25)->get();
            $candidates = $candidates->merge($likeResults);
        }

        // 3. Exact table or route match
        foreach ($tokens as $token) {
            if (strlen($token) >= 3) {
                $exactMatches = ErpRagKnowledgeChunk::where('screen', $token)
                    ->orWhere('screen', 'LIKE', "{$token}%")
                    ->orWhere('url', '/' . ltrim($token, '/'))
                    ->get();
                $candidates = $candidates->merge($exactMatches);
            }
        }

        return $candidates->unique('id');
    }

    /**
     * Re-rank candidate chunks based on exact keyword density, title relevance, and intent boost.
     */
    protected function rerankCandidates(Collection $candidates, string $rawQuery, array $tokens, string $intent): Collection
    {
        $lowerQuery = strtolower($rawQuery);
        $tokenCount = count($tokens);

        return $candidates->map(function (ErpRagKnowledgeChunk $chunk) use ($lowerQuery, $tokens, $tokenCount, $intent) {
            $score = 0;
            $titleLower = strtolower($chunk->title);
            $contentLower = strtolower($chunk->content);
            $keywordsLower = strtolower($chunk->keywords ?? '');
            $screenLower = strtolower($chunk->screen ?? '');
            $urlLower = strtolower($chunk->url ?? '');

            // Exact phrase match in screen or title
            if ($screenLower !== '' && str_contains($lowerQuery, $screenLower)) {
                $score += 150;
            }
            if (str_contains($titleLower, $lowerQuery)) {
                $score += 200;
            }

            // Exact URL match
            if ($urlLower !== '' && str_contains($lowerQuery, ltrim($urlLower, '/'))) {
                $score += 120;
            }

            // Multiple token matching bonus: how many of the query tokens match this chunk?
            $matchedTokens = 0;
            foreach ($tokens as $token) {
                $tokenMatched = false;
                if (str_contains($titleLower, $token)) {
                    $score += 40;
                    $tokenMatched = true;
                }
                if (str_contains($screenLower, $token)) {
                    $score += 45;
                    $tokenMatched = true;
                }
                if (str_contains($keywordsLower, $token)) {
                    $score += 25;
                    $tokenMatched = true;
                }
                if ($urlLower !== '' && str_contains($urlLower, $token)) {
                    $score += 30;
                    $tokenMatched = true;
                }
                if (str_contains($contentLower, $token)) {
                    $score += 10;
                    $tokenMatched = true;
                }
                if ($tokenMatched) {
                    $matchedTokens++;
                }
            }

            // Multi-token coverage multiplier: if 2+ tokens match, reward heavily
            if ($tokenCount > 1 && $matchedTokens >= 2) {
                $score += ($matchedTokens * 35);
            }

            // If the chunk has no matching tokens and neither title, screen, nor URL matches, its score is 0
            $hasAnyMatch = ($matchedTokens > 0)
                || ($screenLower !== '' && str_contains($lowerQuery, $screenLower))
                || str_contains($titleLower, $lowerQuery)
                || ($urlLower !== '' && str_contains($lowerQuery, ltrim($urlLower, '/')));

            if (!$hasAnyMatch) {
                $chunk->rag_score = 0;
                return $chunk;
            }

            // Intent-specific boosting
            if ($intent === 'navigation') {
                if ($chunk->source_type === 'menu') {
                    $score += 180; // Top priority for navigation queries!
                } elseif ($chunk->source_type === 'route') {
                    if (!str_contains($urlLower, '{')) {
                        $score += 60;
                    } else {
                        $score -= 30;
                    }
                }
            } elseif ($intent === 'creation') {
                $entityTokens = array_values(array_diff($tokens, ['add', 'create', 'new', 'insert', 'make', 'record', 'register', 'entry']));
                $matchesEntity = false;
                foreach ($entityTokens as $eTok) {
                    if (str_contains($titleLower, $eTok) || str_contains($screenLower, $eTok) || str_contains($keywordsLower, $eTok) || str_contains(strtolower($chunk->module ?? ''), $eTok)) {
                        $matchesEntity = true;
                        break;
                    }
                }

                if ($chunk->source_type === 'menu') {
                    $score += ($matchesEntity ? 220 : 50);
                } elseif ($chunk->source_type === 'documentation') {
                    $isAddDoc = str_contains($titleLower, 'add') || str_contains($screenLower, 'add');
                    if ($matchesEntity && $isAddDoc) {
                        $score += 260; // Top match: Add doc for the target entity!
                    } elseif ($matchesEntity) {
                        $score += 150;
                    } elseif ($isAddDoc) {
                        $score -= 50; // Penalize "add" docs of unrelated modules
                    }
                } elseif ($chunk->source_type === 'route') {
                    if (str_contains($urlLower, 'add') && $matchesEntity) {
                        $score += 180;
                    } elseif ($matchesEntity) {
                        $score += 100;
                    }
                }
            } elseif ($intent === 'schema') {
                if ($chunk->source_type === 'sql_schema') {
                    $score += 300; // Overwhelming priority for database table queries!
                }
            } elseif ($intent === 'workflow' || $intent === 'field_explanation') {
                if ($chunk->source_type === 'documentation') {
                    $score += 160;
                } elseif ($chunk->source_type === 'menu') {
                    $score += 70;
                }
            }

            $chunk->rag_score = $score;
            return $chunk;
        })->sortByDesc('rag_score');
    }

    /**
     * Ensure diversity across sources (e.g. Menu + Documentation + Schema if relevant).
     */
    protected function selectDiverseChunks(Collection $scored, int $limit, string $intent): Collection
    {
        // Require a meaningful relevance score (>= 50) to filter out accidental substring noise on non-ERP queries
        $scored = $scored->filter(function ($c) {
            return ($c->rag_score ?? 0) >= 50;
        });

        if ($scored->isEmpty()) {
            return collect();
        }

        $selected = collect();
        $seenTitles = [];
        $seenUrls = [];

        // If schema intent, ensure the highest-scoring sql_schema chunk is included first
        if ($intent === 'schema') {
            $topSchema = $scored->firstWhere('source_type', 'sql_schema');
            if ($topSchema) {
                $selected->push($topSchema);
                $seenTitles[$topSchema->title] = true;
            }
        }

        // If navigation intent, ensure the top menu chunk is included first
        if ($intent === 'navigation') {
            $topMenu = $scored->firstWhere('source_type', 'menu');
            if ($topMenu) {
                $selected->push($topMenu);
                $seenTitles[$topMenu->title] = true;
                if ($topMenu->url) {
                    $seenUrls[$topMenu->url] = true;
                }
            }
        }

        // If creation intent (e.g. "how to add new employee"), ensure top Menu chunk AND top Add Documentation chunk are both selected!
        if ($intent === 'creation') {
            $topMenu = $scored->firstWhere('source_type', 'menu');
            if ($topMenu) {
                $selected->push($topMenu);
                $seenTitles[$topMenu->title] = true;
                if ($topMenu->url) {
                    $seenUrls[$topMenu->url] = true;
                }
            }

            // Find matching Add documentation chunk for the same module/entity
            $moduleLower = $topMenu ? strtolower($topMenu->module ?? '') : '';
            $topDoc = $scored->first(function ($c) use ($moduleLower) {
                if ($c->source_type !== 'documentation') {
                    return false;
                }
                $cTitle = strtolower($c->title);
                $cScreen = strtolower($c->screen ?? '');
                $cMod = strtolower($c->module ?? '');

                $isAdd = str_contains($cTitle, 'add') || str_contains($cScreen, 'add') || str_contains($cTitle, 'create') || str_contains($cScreen, 'create');
                $matchesModule = $moduleLower !== '' && ($cMod === $moduleLower || str_contains($cTitle, $moduleLower) || str_contains($cScreen, $moduleLower));

                return $isAdd && $matchesModule;
            });

            // If no add doc specifically matched module, try any doc matching module
            if (!$topDoc && $moduleLower !== '') {
                $topDoc = $scored->first(function ($c) use ($moduleLower) {
                    return $c->source_type === 'documentation' && (strtolower($c->module ?? '') === $moduleLower || str_contains(strtolower($c->title), $moduleLower));
                });
            }

            if ($topDoc && !isset($seenTitles[$topDoc->title])) {
                $selected->push($topDoc);
                $seenTitles[$topDoc->title] = true;
            }

            // Also check for direct add route (e.g. /employees/add)
            $topAddRoute = $scored->first(function ($c) use ($moduleLower) {
                return $c->source_type === 'route' && str_contains(strtolower($c->url ?? ''), 'add') && ($moduleLower === '' || strtolower($c->module ?? '') === $moduleLower);
            });
            if ($topAddRoute && !isset($seenTitles[$topAddRoute->title]) && $selected->count() < $limit) {
                $selected->push($topAddRoute);
                $seenTitles[$topAddRoute->title] = true;
                if ($topAddRoute->url) {
                    $seenUrls[$topAddRoute->url] = true;
                }
            }
        }

        // If workflow intent, ensure top documentation chunk is included first
        if ($intent === 'workflow' || $intent === 'field_explanation') {
            $topDoc = $scored->firstWhere('source_type', 'documentation');
            if ($topDoc) {
                $selected->push($topDoc);
                $seenTitles[$topDoc->title] = true;
            }
        }

        // Fill remaining slots with the highest-scoring unique chunks
        $primaryModule = $selected->first() ? strtolower($selected->first()->module ?? '') : '';

        foreach ($scored as $chunk) {
            if ($selected->count() >= $limit) {
                break;
            }

            if (isset($seenTitles[$chunk->title])) {
                continue;
            }

            if (!empty($chunk->url) && isset($seenUrls[$chunk->url])) {
                continue;
            }

            // Prevent polluting context with completely unrelated modules when primary module is clear
            if ($primaryModule !== '' && !empty($chunk->module)) {
                $cMod = strtolower($chunk->module);
                if ($cMod !== $primaryModule && !str_contains(strtolower($chunk->title), $primaryModule) && !str_contains(strtolower($chunk->screen ?? ''), $primaryModule)) {
                    continue;
                }
            }

            $selected->push($chunk);
            $seenTitles[$chunk->title] = true;
            if (!empty($chunk->url)) {
                $seenUrls[$chunk->url] = true;
            }
        }

        return $selected;
    }

    /**
     * Format chunks into clean Markdown context for the LLM.
     */
    protected function formatContext(Collection $chunks): string
    {
        if ($chunks->isEmpty()) {
            return '';
        }

        $lines = [];
        $lines[] = "=== RETRIEVED NACHIAS ERP KNOWLEDGE (AUTHORITATIVE GROUND TRUTH) ===";

        $i = 1;
        foreach ($chunks as $chunk) {
            $lines[] = "--- [KNOWLEDGE SOURCE #{$i}: {$chunk->title}] ---";
            if (!empty($chunk->menu_path)) {
                $lines[] = "Official Menu Path: {$chunk->menu_path}";
            }
            if (!empty($chunk->url)) {
                $lines[] = "Direct URL: {$chunk->url}";
            }
            if (!empty($chunk->module)) {
                $lines[] = "Module: {$chunk->module}";
            }
            $lines[] = $chunk->content;
            $lines[] = "";
            $i++;
        }

        $lines[] = "=== END OF RETRIEVED KNOWLEDGE ===";

        return implode("\n", $lines);
    }
}
