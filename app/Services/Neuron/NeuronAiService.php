<?php

declare(strict_types=1);

namespace App\Services\Neuron;

use App\Models\ErpRagKnowledgeChunk;
use App\Services\RAG\ErpRagRetrieverService;
use App\Services\TranslationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NeuronAI\Chat\Messages\AssistantMessage;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\Ollama\Ollama;
use Throwable;

class NeuronAiService
{
    protected TranslationService $translationService;
    protected ErpRagRetrieverService $ragRetriever;
    protected string $url;
    protected string $model;
    protected int $timeout;

    public function __construct(
        TranslationService $translationService,
        ErpRagRetrieverService $ragRetriever
    ) {
        $this->translationService = $translationService;
        $this->ragRetriever = $ragRetriever;
        $this->url = rtrim(config('services.ollama.url', 'http://127.0.0.1:11434'), '/');
        $this->model = config('services.ollama.model', 'gpt-oss:120b-cloud');
        $this->timeout = (int) config('services.ollama.timeout', 300);
    }

    /**
     * Check if the AI provider / Ollama service is reachable.
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(3)->get("{$this->url}/api/tags");
            return $response->successful();
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Get the active AI model name.
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Process a chat query using Neuron AI Framework and RAG pipeline.
     *
     * @param string $message The user's input message
     * @param array $history Previous conversation history
     * @param string|null $imageData Base64 image/screenshot data if attached
     * @param string|null $imageText OCR extracted text from the screenshot
     * @param bool $isVoice Whether this was recorded as a voice note
     * @param string|null $targetLang Requested target language (e.g. 'ta' for Tamil)
     * @return array
     */
    public function processChat(
        string $message,
        array $history = [],
        ?string $imageData = null,
        ?string $imageText = null,
        bool $isVoice = false,
        ?string $targetLang = null
    ): array {
        $rawMessage = trim($message);
        $imageText = trim((string) $imageText);

        // If an image is provided but client-side OCR was empty, run server-side OCR extraction
        if ($imageText === '' && !empty($imageData)) {
            $extractedOcr = $this->extractTextFromImage($imageData);
            if (!empty($extractedOcr)) {
                $imageText = $extractedOcr;
            }
        }

        // Supply a default query if message is blank but image is attached
        if ($rawMessage === '' && !empty($imageData)) {
            $rawMessage = "Please analyze this uploaded ERP image/screenshot and explain the screen, fields, or next workflow step.";
        }

        // 1. Language Detection & Tamil-to-English translation
        $isTamilInput = $this->translationService->isTamil($rawMessage) || $isVoice || ($targetLang === 'ta');

        if ($isTamilInput && $rawMessage !== '') {
            $translation = $this->translationService->translate($rawMessage, 'en', 'ta');
            $effectivePrompt = $translation['translated'];
        } else {
            $effectivePrompt = $rawMessage;
            $translation = [
                'original' => $rawMessage,
                'translated' => $rawMessage,
                'is_translated' => false,
                'source_language' => 'en',
                'target_language' => 'en',
            ];
        }

        // 2. Fast Guardrail: reject harmful / unrelated queries
        $guardrailRefusal = $this->checkGuardrail($effectivePrompt);
        if ($guardrailRefusal !== null) {
            return $this->formatResponse(
                message: $guardrailRefusal,
                rawMessage: $rawMessage,
                effectivePrompt: $effectivePrompt,
                translation: $translation,
                isTamilInput: $isTamilInput,
                isVoice: $isVoice,
                ragChunks: [],
                intent: 'refusal'
            );
        }

        // 3. Screen detection and OCR context enrichment
        $ragQuery = $effectivePrompt;
        $detectedScreen = null;

        if (!empty($imageText)) {
            $detectedScreen = $this->detectScreenFromText($imageText, $effectivePrompt);
            if ($detectedScreen) {
                $detectedChunk = $detectedScreen['chunk'];
                $detectedUrl = $detectedChunk->url;
                if ($detectedScreen['is_add']) {
                    $detectedUrl = rtrim($detectedUrl, '/') . '/add';
                }

                $screenHeader = "[IDENTIFIED ERP SCREEN FROM SCREENSHOT]: Screen: {$detectedChunk->screen} | Menu Path: {$detectedChunk->menu_path} | URL: {$detectedUrl}" . ($detectedScreen['is_add'] ? " (Add Form)" : "");
                $ragQuery = $detectedChunk->screen . " " . $detectedChunk->menu_path . " " . $ragQuery . " " . $imageText;
                $effectivePromptForLlm = $screenHeader . "\n\n[Context from Uploaded Screenshot / OCR]:\n" . $imageText . "\n\n[User Question]:\n" . $effectivePrompt;
            } else {
                $ragQuery .= " " . $imageText;
                $effectivePromptForLlm = "[Context from Uploaded Screenshot / OCR]:\n" . $imageText . "\n\n[User Question]:\n" . $effectivePrompt;
            }
        } else {
            $effectivePromptForLlm = $effectivePrompt;
        }

        // 4. Retrieve authoritative knowledge from RAG
        $ragResult = $this->ragRetriever->retrieve($ragQuery, 4);

        if ($detectedScreen) {
            $detectedChunk = clone $detectedScreen['chunk'];
            if ($detectedScreen['is_add']) {
                $detectedChunk->url = rtrim($detectedChunk->url, '/') . '/add';
            }
            $detectedChunk->rag_score = 9999;

            $filteredChunks = array_values(array_filter($ragResult['chunks'], function ($c) use ($detectedChunk) {
                return $c->id !== $detectedChunk->id;
            }));

            array_unshift($filteredChunks, $detectedChunk);
            $ragResult['chunks'] = array_slice($filteredChunks, 0, 4);
        }

        // Fallback for Nachias error/screenshot questions if RAG returned empty
        $hasNachiasContext = stripos($effectivePrompt, 'nachias') !== false || !empty($imageData) || stripos($effectivePrompt, 'error') !== false || stripos($effectivePrompt, 'screen') !== false || stripos($effectivePrompt, 'fix') !== false;
        if (empty($ragResult['chunks']) && $hasNachiasContext) {
            $fallbackChunks = ErpRagKnowledgeChunk::where('source_type', 'controller')
                ->whereIn('screen', ['Purchase Orders', 'Sales Orders', 'GRN Entry', 'Customers', 'Billing', 'Job Card Entry'])
                ->limit(3)
                ->get();
            if ($fallbackChunks->isNotEmpty()) {
                $ragResult['chunks'] = $fallbackChunks->all();
                $ragResult['context'] = $this->ragRetriever->formatContext($fallbackChunks);
            }
        }

        // Scope boundary: if no Nachias knowledge matched and no Nachias context
        if (empty($ragResult['chunks']) && !$hasNachiasContext) {
            $refusalMessage = "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
            return $this->formatResponse(
                message: $refusalMessage,
                rawMessage: $rawMessage,
                effectivePrompt: $effectivePrompt,
                translation: $translation,
                isTamilInput: $isTamilInput,
                isVoice: $isVoice,
                ragChunks: [],
                intent: 'refusal'
            );
        }

        // 5. Execute via Neuron AI Agent
        $aiAnswer = $this->runNeuronAgent($effectivePromptForLlm, $ragResult['context'], $history, $imageData);

        $englishReply = $this->cleanAiResponse($aiAnswer['message'] ?? 'I was unable to retrieve an answer at this time. Please try again.');

        return $this->formatResponse(
            message: $englishReply,
            rawMessage: $rawMessage,
            effectivePrompt: $effectivePrompt,
            translation: $translation,
            isTamilInput: $isTamilInput,
            isVoice: $isVoice,
            ragChunks: $ragResult['chunks'],
            intent: $ragResult['intent'] ?? 'general'
        );
    }

    /**
     * Extract text from base64 image data using local Tesseract OCR if available.
     */
    public function extractTextFromImage(?string $imageData): string
    {
        if (empty($imageData)) {
            return '';
        }

        try {
            $raw = preg_match('/^data:image\/[a-zA-Z0-9+\.-]+;base64,(.+)$/s', $imageData, $m) ? $m[1] : $imageData;
            $decoded = base64_decode($raw);
            if (!$decoded) {
                return '';
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'ocr_') . '.png';
            file_put_contents($tempFile, $decoded);

            $tesseractBin = 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe';
            if (!file_exists($tesseractBin)) {
                $tesseractBin = 'tesseract';
            }

            $outputTxtBase = tempnam(sys_get_temp_dir(), 'ocr_out_');
            $command = sprintf('"%s" "%s" "%s" --oem 1 -l eng 2>&1', $tesseractBin, $tempFile, $outputTxtBase);
            exec($command, $output, $returnCode);

            $txtFile = $outputTxtBase . '.txt';
            $result = '';
            if (file_exists($txtFile)) {
                $result = trim((string) file_get_contents($txtFile));
                @unlink($txtFile);
            }
            @unlink($tempFile);
            @unlink($outputTxtBase);

            return $result;
        } catch (Throwable $e) {
            Log::warning('Server OCR extraction notice: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Run the Neuron AI Agent with tool capabilities.
     */
    protected function runNeuronAgent(string $prompt, string $ragContext, array $history = [], ?string $imageBase64 = null): array
    {
        $apiUrl = str_ends_with($this->url, '/api') ? $this->url : "{$this->url}/api";
        $numPredict = (int) (config('services.ollama.num_predict') ?: env('OLLAMA_NUM_PREDICT', 2048));
        $numCtx = (int) (config('services.ollama.num_ctx') ?: env('OLLAMA_NUM_CTX', 8192));

        $instructions = "You are the \"Nachias ERP Flow Navigator AI\", built with the Neuron AI Agent framework.\n"
            . "You are the authoritative ERP navigation, workflow, and error troubleshooting assistant for Nachias ERP.\n\n"
            . "CRITICAL OPERATING RULES:\n"
            . "1. NACHIAS ERP SCOPE ONLY: Only answer questions directly concerning Nachias ERP navigation, workflows, screens, fields, and error troubleshooting. Refuse general questions outside Nachias ERP.\n"
            . "2. ZERO-TOLERANCE SAFETY: Refuse dangerous/cyber security questions immediately.\n"
            . "3. ERROR DIAGNOSIS: When asked about an error on a screen, inspect the cause (duplicate entry, missing field, draft-only edit) and give exact step-by-step resolution.\n"
            . "4. NAVIGATION FORMAT: Always state top navigation bar menu -> screen name -> URL -> Add button navigation.\n"
            . "5. SCREENSHOT & LIST VIEW DETAILS: When asked about a list page, table, or uploaded screenshot (e.g. Job Card Entry list, Purchase Orders list, GRN Entry list):\n"
            . "   - State the screen name, top navigation menu, and relative URL.\n"
            . "   - If asked to take a record number/row from the list and get details (e.g., 'take first job card number, get details'):\n"
            . "     * Identify the record number/code from the screenshot/OCR context (e.g., Job Card No: 778899).\n"
            . "     * Explain how to view details in the table (clicking the View Details eye icon or navigating to `/<resource>/{id}`).\n"
            . "     * Explain what is shown on the Details page (Job Card printable sheet, Cutting Matrix, Fabric Details, Production Workflow Tracking, Status) using ERP knowledge.\n"
            . "     * Detail available actions (Print, PDF download, Edit if draft) and required permissions.\n"
            . "6. COMPLETE FULL RESPONSES: Provide clear, thorough, and complete answers without stopping in the middle.\n"
            . "7. LANGUAGE: Respond strictly in English text.\n\n"
            . $ragContext;

        try {
            $provider = new Ollama(
                url: $apiUrl,
                model: $this->model,
                parameters: [
                    'temperature' => 0.1,
                    'num_predict' => $numPredict,
                    'num_ctx' => $numCtx,
                ]
            );

            $threadId = 'chat_' . bin2hex(random_bytes(8));
            $agent = new ErpFlowNavigatorAgent(
                provider: $provider,
                systemPrompt: $instructions,
                threadId: $threadId
            );
            $agent->setThreadId($threadId);

            // Convert conversation history to Neuron AI Message instances
            $messages = [];
            $recentHistory = array_slice($history, -4);
            foreach ($recentHistory as $item) {
                if (isset($item['role'], $item['content']) && is_string($item['content']) && trim($item['content']) !== '') {
                    if ($item['role'] === 'user') {
                        $messages[] = new UserMessage(trim($item['content']));
                    } elseif ($item['role'] === 'assistant') {
                        $messages[] = new AssistantMessage(trim($item['content']));
                    }
                }
            }

            $messages[] = new UserMessage($prompt);

            $state = $agent->chat($messages);
            $replyMessage = $state->getMessage();
            $replyContent = $replyMessage ? $replyMessage->getContent() : null;

            if ($replyContent !== null && trim($replyContent) !== '') {
                return [
                    'success' => true,
                    'message' => trim($replyContent),
                    'framework' => 'Neuron AI',
                ];
            }
        } catch (Throwable $e) {
            Log::info('Neuron AI agent execution notice: ' . $e->getMessage() . '. Running direct provider call.');

            // Direct provider API fallback using configured parameters
            try {
                $payloadMessages = [
                    ['role' => 'system', 'content' => $instructions . "\n\nCRITICAL: Respond ONLY in English text. Complete the full response."]
                ];
                foreach ($history as $h) {
                    if (isset($h['role'], $h['content']) && is_string($h['content']) && trim($h['content']) !== '') {
                        $payloadMessages[] = ['role' => $h['role'], 'content' => trim($h['content'])];
                    }
                }
                $userMsg = ['role' => 'user', 'content' => $prompt];
                if (!empty($imageBase64)) {
                    $rawImg = preg_match('/^data:image\/[a-zA-Z0-9+\.-]+;base64,(.+)$/s', $imageBase64, $m) ? $m[1] : $imageBase64;
                    $userMsg['images'] = [$rawImg];
                }
                $payloadMessages[] = $userMsg;

                $res = Http::timeout($this->timeout)->post("{$this->url}/api/chat", [
                    'model' => $this->model,
                    'messages' => $payloadMessages,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.1,
                        'num_predict' => $numPredict,
                        'num_ctx' => $numCtx,
                    ],
                ]);

                if (!$res->successful() && !empty($imageBase64)) {
                    // Retry without the images field in case the active Ollama model is text-only
                    foreach ($payloadMessages as &$msg) {
                        unset($msg['images']);
                    }
                    unset($msg);

                    $res = Http::timeout($this->timeout)->post("{$this->url}/api/chat", [
                        'model' => $this->model,
                        'messages' => $payloadMessages,
                        'stream' => false,
                        'options' => [
                            'temperature' => 0.1,
                            'num_predict' => $numPredict,
                            'num_ctx' => $numCtx,
                        ],
                    ]);
                }

                if ($res->successful()) {
                    $data = $res->json();
                    $content = $data['message']['content'] ?? null;
                    if ($content) {
                        return [
                            'success' => true,
                            'message' => trim($content),
                            'framework' => 'Neuron AI',
                        ];
                    }
                }
            } catch (Throwable $ex) {
                Log::error('Direct AI provider error: ' . $ex->getMessage());
            }
        }

        return ['success' => false];
    }

    /**
     * Format standard response array with bilingual translation metadata.
     */
    protected function formatResponse(
        string $message,
        string $rawMessage,
        string $effectivePrompt,
        array $translation,
        bool $isTamilInput,
        bool $isVoice,
        array $ragChunks,
        string $intent
    ): array {
        $result = [
            'success' => true,
            'message' => $message,
            'original_message' => $rawMessage,
            'translated_message' => $effectivePrompt,
            'is_translated' => $translation['is_translated'],
            'source_language' => $translation['source_language'] ?? ($isTamilInput ? 'ta' : 'en'),
            'is_voice' => $isVoice,
            'rag_sources' => array_map(function ($chunk) {
                return [
                    'title' => $chunk->title,
                    'source_type' => $chunk->source_type,
                    'module' => $chunk->module,
                    'screen' => $chunk->screen,
                    'menu_path' => $chunk->menu_path,
                    'url' => $chunk->url,
                ];
            }, $ragChunks),
            'rag_intent' => $intent,
            'framework' => 'Neuron AI',
        ];

        if ($isTamilInput) {
            $erpTranslation = $this->translationService->translateErpResponse($message, 'ta');
            $result['message'] = $erpTranslation['translated'];
            $result['english_message'] = $message;
            $result['response_language'] = 'ta';
            $result['is_response_translated'] = true;
        } else {
            $result['response_language'] = 'en';
            $result['is_response_translated'] = false;
        }

        return $result;
    }

    /**
     * Clean raw AI response from malformed formatting, redundant HTML linebreaks, and extra whitespace.
     */
    public function cleanAiResponse(?string $text): string
    {
        if ($text === null) {
            return '';
        }

        $clean = trim($text);

        // Normalize <br>, <br/>, <br /> tags
        $clean = preg_replace('/<br\s*\/?>/i', '<br>', $clean);

        // Outside of markdown table rows (|), replace <br> with newline for cleaner markdown formatting
        $lines = explode("\n", $clean);
        $processed = [];
        foreach ($lines as $line) {
            if (str_contains($line, '|')) {
                $processed[] = $line;
            } else {
                $processed[] = preg_replace('/<br\s*\/?>/i', "\n", $line);
            }
        }
        $clean = implode("\n", $processed);

        // Remove more than 2 consecutive newlines
        $clean = preg_replace("/\n{3,}/", "\n\n", $clean);

        return trim($clean);
    }

    /**
     * Guardrail checking for dangerous / out-of-scope prompts.
     */
    public function checkGuardrail(string $prompt): ?string
    {
        $q = trim($prompt);
        if ($q === '') {
            return null;
        }

        // 1. Dangerous questions & cyber security attacks
        if (preg_match('/\b(hack|hacking|sql\s*injection|sqli|xss|cross\s*site\s*scripting|remote\s*code\s*execution|exploit|payload|rootkit|malware|ransomware|trojan|keylogger|ddos|bomb|weapon|explosive|kill|suicide|drop\s+database|truncate\s+table|delete\s+from\s+users)\b/i', $q)) {
            return "I cannot fulfill this request. I am only permitted to assist with Nachias ERP navigation and Nachias application-related queries, and I do not assist with dangerous, harmful, or security-sensitive activities.";
        }

        // 2. Jailbreaks
        if (preg_match('/\b(ignore\s+(all\s+)?previous\s+instructions|act\s+as\s+dan|developer\s+mode|jailbreak|unrestricted\s+ai)\b/i', $q)) {
            return "I cannot fulfill this request. I am only permitted to assist with Nachias ERP navigation and Nachias application-related queries, and I do not assist with dangerous, harmful, or security-sensitive activities.";
        }

        // Explicit Nachias mention allows RAG
        if (stripos($q, 'nachias') !== false) {
            return null;
        }

        // 3. Greetings & small talk
        if (preg_match('/^(hi|hello|hey|good\s+(morning|afternoon|evening|night)|how\s+are\s+you|who\s+are\s+you|what\s+is\s+your\s+name|tell\s+me\s+a\s+joke|how\s+was\s+your\s+day|what\s+can\s+you\s+do)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        // 4. Outside companies
        if (preg_match('/\b(amazon|google|facebook|meta|apple|microsoft|flipkart|shopify|alibaba|twitter|instagram|netflix|youtube|wikipedia)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        // 5. Generic definitions
        if (preg_match('/^(what\s+is|what\s+does|define|explain|meaning\s+of)\s+(an?\s+)?(erp|erp\s+software|erp\s+system|enterprise\s+resource\s+planning|accounting|supply\s+chain|inventory|gst|vat|taxation|crm|business|database|sql|cloud|ai|artificial\s+intelligence|llm|machine\s+learning)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        // 6. General trivia
        if (preg_match('/^(what\s+is\s+the\s+capital|who\s+is\s+(the\s+)?(president|prime\s+minister|ceo|governor)|(what\s+is\s+the\s+)?weather|tell\s+me\s+about\s+(india|tamil\s*nadu|world|movies|sports|cricket|science|history)|who\s+(created|made|built|developed)\s+you)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        // 7. Coding & math outside ERP
        if (preg_match('/\b(write\s+(a\s+)?(python|javascript|php|java|c\+\+|html|css|script|code|algorithm))\b/i', $q) || preg_match('/^(calculate|solve\s+math|what\s+is\s+\d+\s*[\+\-\*\/]\s*\d+)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        return null;
    }

    /**
     * Detect primary ERP screen from OCR text.
     */
    protected function detectScreenFromText(string $ocrText, string $userPrompt): ?array
    {
        $combined = strtolower(trim($userPrompt . ' ' . $ocrText));
        if ($combined === '') {
            return null;
        }

        $menuChunks = ErpRagKnowledgeChunk::where('source_type', 'menu')->get();
        if ($menuChunks->isEmpty()) {
            return null;
        }

        $candidates = [];
        foreach ($menuChunks as $menu) {
            $screenLower = strtolower($menu->screen ?? '');
            if ($screenLower === '') {
                continue;
            }
            $screenSingular = rtrim($screenLower, 's');

            $score = 0;
            $isAdd = false;
            $pos = false;

            if (str_contains($combined, 'add ' . $screenLower) || str_contains($combined, 'add ' . $screenSingular)) {
                $score += 500;
                $isAdd = true;
                $pos = strpos($combined, 'add ' . $screenSingular);
                if ($pos === false) $pos = strpos($combined, 'add ' . $screenLower);
            } elseif (str_contains($combined, 'edit ' . $screenLower) || str_contains($combined, 'edit ' . $screenSingular)) {
                $score += 500;
                $pos = strpos($combined, 'edit ' . $screenSingular);
                if ($pos === false) $pos = strpos($combined, 'edit ' . $screenLower);
            } elseif (str_contains($combined, 'create ' . $screenLower) || str_contains($combined, 'create ' . $screenSingular)) {
                $score += 500;
                $isAdd = true;
                $pos = strpos($combined, 'create ' . $screenSingular);
                if ($pos === false) $pos = strpos($combined, 'create ' . $screenLower);
            } elseif (str_contains($combined, $screenLower)) {
                $score += 200;
                $pos = strpos($combined, $screenLower);
            } elseif ($screenSingular !== '' && strlen($screenSingular) >= 4 && str_contains($combined, $screenSingular)) {
                $score += 180;
                $pos = strpos($combined, $screenSingular);
            }

            if ($score > 0) {
                if ($pos !== false) {
                    if ($pos < 40) $score += 350;
                    elseif ($pos < 100) $score += 180;
                    elseif ($pos < 200) $score += 60;
                }

                $isTransactional = in_array($menu->screen, [
                    'Purchase Orders', 'Purchase Invoices', 'Sales Orders', 'Sales Invoices',
                    'GRN Entry', 'Stock Entry', 'Debit Notes', 'Credit Notes', 'Job Card Entry',
                    'Production Receipts', 'Billing', 'Manage Payments'
                ], true);

                if ($isTransactional) {
                    $score += 180;
                }

                if ((str_contains($combined, 'purchase order') || str_contains($combined, 'purchase orders'))
                    && !str_contains($screenLower, 'order')
                    && str_contains($screenLower, 'commission agent')) {
                    $score -= 400;
                }
                if ((str_contains($combined, 'sales order') || str_contains($combined, 'sales orders'))
                    && !str_contains($screenLower, 'order')
                    && (str_contains($screenLower, 'agent') || str_contains($screenLower, 'category'))) {
                    $score -= 400;
                }

                if (!$isTransactional && $pos !== false && $pos > 40) {
                    $score -= 120;
                }

                $candidates[] = [
                    'chunk' => $menu,
                    'score' => $score,
                    'pos' => $pos,
                    'is_add' => $isAdd
                ];
            }
        }

        if (empty($candidates)) {
            return null;
        }

        usort($candidates, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $candidates[0];
    }
}
