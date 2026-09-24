<?php

namespace App\Http\Controllers;

use App\Models\ErpRagKnowledgeChunk;
use App\Services\OllamaService;
use App\Services\RAG\ErpRagRetrieverService;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    protected OllamaService $ollamaService;
    protected TranslationService $translationService;
    protected ErpRagRetrieverService $ragService;

    public function __construct(
        OllamaService $ollamaService,
        TranslationService $translationService,
        ErpRagRetrieverService $ragService
    ) {
        $this->ollamaService = $ollamaService;
        $this->translationService = $translationService;
        $this->ragService = $ragService;
    }

    /**
     * Display the chatbot interface.
     */
    public function index(): View
    {
        $model = $this->ollamaService->getModel();
        $isAvailable = $this->ollamaService->isAvailable();

        return view('chatbot.index', compact('model', 'isAvailable'));
    }

    /**
     * Handle incoming chatbot messages.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $hasImage = $request->filled('image');

        $validator = Validator::make($request->all(), [
            'message' => [$hasImage ? 'nullable' : 'required', 'string', 'max:2000'],
            'image' => ['nullable', 'string'],
            'image_name' => ['nullable', 'string', 'max:255'],
            'image_text' => ['nullable', 'string', 'max:5000'],
            'is_voice' => ['nullable', 'boolean'],
            'audio' => ['nullable', 'string'],
            'history' => ['nullable', 'array', 'max:12'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
        ], [
            'message.required' => 'Please enter a message or upload an image.',
            'message.max' => 'The message is too long (maximum 2000 characters).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('message') ?: 'Invalid input provided.',
            ], 422);
        }

        $rawMessage = trim((string) $request->input('message'));
        $imageData = $request->input('image');
        $imageName = $request->input('image_name');
        $imageText = trim((string) $request->input('image_text'));
        $history = $request->input('history', []);
        $isVoice = (bool) $request->input('is_voice', false);
        $audioData = $request->input('audio');
        $requestedTargetLang = $request->input('target_lang');

        // If message is empty but image was uploaded, supply a default query
        if ($rawMessage === '' && !empty($imageData)) {
            $rawMessage = "Please analyze this uploaded ERP image/screenshot and explain the screen, fields, or next workflow step.";
        }

        // Check if query is in Tamil or comes from a Tamil voice note
        $isTamilInput = $this->translationService->isTamil($rawMessage) || $isVoice || ($requestedTargetLang === 'ta');

        if ($isTamilInput) {
            // Translate Tamil to English for Ollama reasoning
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

        // Fast Guardrail: reject dangerous questions, general definitions, and small talk immediately
        $guardrailRefusal = $this->checkGuardrail($effectivePrompt);
        if ($guardrailRefusal !== null) {
            $result = [
                'success' => true,
                'message' => $guardrailRefusal,
                'original_message' => $rawMessage,
                'translated_message' => $effectivePrompt,
                'is_translated' => $translation['is_translated'],
                'source_language' => $translation['source_language'] ?? ($isTamilInput ? 'ta' : 'en'),
                'is_voice' => $isVoice,
                'rag_sources' => [],
                'rag_intent' => 'refusal',
            ];

            if ($isTamilInput) {
                $erpTranslation = $this->translationService->translateErpResponse($guardrailRefusal, 'ta');
                $result['message'] = $erpTranslation['translated'];
                $result['english_message'] = $guardrailRefusal;
                $result['response_language'] = 'ta';
                $result['is_response_translated'] = true;
            } else {
                $result['response_language'] = 'en';
                $result['is_response_translated'] = false;
            }

            if (!empty($audioData)) {
                $result['audio'] = $audioData;
            }
            if (!empty($imageData)) {
                $result['image'] = $imageData;
                $result['image_name'] = $imageName;
            }

            return response()->json($result);
        }

        // Prepare query and LLM prompt with image OCR context if available
        $ragQuery = $effectivePrompt;
        $detectedScreen = null;

        if (!empty($imageText)) {
            // Detect primary ERP screen from OCR text and prompt
            $detectedScreen = $this->detectScreenFromText($imageText, $effectivePrompt);

            if ($detectedScreen) {
                $detectedChunk = $detectedScreen['chunk'];
                $detectedUrl = $detectedChunk->url;
                if ($detectedScreen['is_add']) {
                    $detectedUrl = rtrim($detectedUrl, '/') . '/add';
                }

                $screenHeader = "[IDENTIFIED ERP SCREEN FROM IMAGE]: Screen: {$detectedChunk->screen} | Menu Path: {$detectedChunk->menu_path} | URL: {$detectedUrl}" . ($detectedScreen['is_add'] ? " (Add Form)" : "");

                $ragQuery = $detectedChunk->screen . " " . $detectedChunk->menu_path . " " . $ragQuery . " " . $imageText;
                $effectivePromptForLlm = $effectivePrompt . "\n\n" . $screenHeader . "\n\n[Context from Uploaded Image / Screenshot]:\n" . $imageText;
            } else {
                $ragQuery .= " " . $imageText;
                $effectivePromptForLlm = $effectivePrompt . "\n\n[Context from Uploaded Image / Screenshot]:\n" . $imageText;
            }
        } else {
            $effectivePromptForLlm = $effectivePrompt;
        }

        // 1. Retrieve authoritative ground-truth knowledge from ERP RAG
        $ragResult = $this->ragService->retrieve($ragQuery, 4);

        // If a primary screen was detected from the image, ensure it is the top navigation source
        if ($detectedScreen) {
            $detectedChunk = clone $detectedScreen['chunk'];
            if ($detectedScreen['is_add']) {
                $detectedChunk->url = rtrim($detectedChunk->url, '/') . '/add';
            }
            $detectedChunk->rag_score = 9999;

            // Remove any existing duplicate of this screen
            $filteredChunks = array_values(array_filter($ragResult['chunks'], function ($c) use ($detectedChunk) {
                return $c->id !== $detectedChunk->id;
            }));

            // Prepend detected screen at position 0 so navigation pill directly points to it
            array_unshift($filteredChunks, $detectedChunk);
            $ragResult['chunks'] = array_slice($filteredChunks, 0, 4);
        }

        // If no documented Nachias ERP knowledge chunks match this question and it has no Nachias context:
        $hasNachiasContext = stripos($effectivePrompt, 'nachias') !== false || !empty($imageData);
        if (empty($ragResult['chunks']) && !$hasNachiasContext) {
            $refusalMessage = "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
            $result = [
                'success' => true,
                'message' => $refusalMessage,
                'original_message' => $rawMessage,
                'translated_message' => $effectivePrompt,
                'is_translated' => $translation['is_translated'],
                'source_language' => $translation['source_language'] ?? ($isTamilInput ? 'ta' : 'en'),
                'is_voice' => $isVoice,
                'rag_sources' => [],
                'rag_intent' => 'refusal',
            ];

            if ($isTamilInput) {
                $erpTranslation = $this->translationService->translateErpResponse($refusalMessage, 'ta');
                $result['message'] = $erpTranslation['translated'];
                $result['english_message'] = $refusalMessage;
                $result['response_language'] = 'ta';
                $result['is_response_translated'] = true;
            } else {
                $result['response_language'] = 'en';
                $result['is_response_translated'] = false;
            }

            if (!empty($audioData)) {
                $result['audio'] = $audioData;
            }
            if (!empty($imageData)) {
                $result['image'] = $imageData;
                $result['image_name'] = $imageName;
            }

            return response()->json($result);
        }

        // 2. Send the translated English input + retrieved knowledge to the local Ollama LLM
        $result = $this->ollamaService->chatWithRag($effectivePromptForLlm, $ragResult['context'], $history, $imageData);

        // If Ollama succeeds and user asked in Tamil (or requested Tamil):
        // Translate the final ERP response to Tamil while preserving English Page Names & Menu Navigation!
        if (!empty($result['success']) && !empty($result['message']) && $isTamilInput) {
            $englishMessage = $result['message'];
            $erpTranslation = $this->translationService->translateErpResponse($englishMessage, 'ta');

            $result['message'] = $erpTranslation['translated'];
            $result['english_message'] = $englishMessage;
            $result['response_language'] = 'ta';
            $result['is_response_translated'] = true;
        } else {
            $result['response_language'] = 'en';
            $result['is_response_translated'] = false;
        }

        // Attach translation & voice metadata to response
        $result['original_message'] = $rawMessage;
        $result['translated_message'] = $effectivePrompt;
        $result['is_translated'] = $translation['is_translated'];
        $result['source_language'] = $translation['source_language'] ?? ($isTamilInput ? 'ta' : 'en');
        $result['is_voice'] = $isVoice;
        if (!empty($audioData)) {
            $result['audio'] = $audioData;
        }
        if (!empty($imageData)) {
            $result['image'] = $imageData;
            $result['image_name'] = $imageName;
        }

        // Attach RAG metadata & sources to response
        $result['rag_sources'] = array_map(function ($chunk) {
            return [
                'title' => $chunk->title,
                'source_type' => $chunk->source_type,
                'module' => $chunk->module,
                'screen' => $chunk->screen,
                'menu_path' => $chunk->menu_path,
                'url' => $chunk->url,
            ];
        }, $ragResult['chunks']);
        $result['rag_intent'] = $ragResult['intent'];

        return response()->json($result);
    }

    /**
     * Check if a prompt is dangerous, a jailbreak attempt, or a general/unrelated question.
     * Returns a refusal string if blocked, or null if the query should proceed to RAG and Ollama.
     */
    protected function checkGuardrail(string $prompt): ?string
    {
        $q = trim($prompt);
        if ($q === '') {
            return null;
        }

        // 1. Dangerous questions & cyber security attacks
        if (preg_match('/\b(hack|hacking|sql\s*injection|sqli|xss|cross\s*site\s*scripting|remote\s*code\s*execution|exploit|payload|rootkit|malware|ransomware|trojan|keylogger|ddos|bomb|weapon|explosive|kill|suicide|drop\s+database|truncate\s+table|delete\s+from\s+users)\b/i', $q)) {
            return "I cannot fulfill this request. I am only permitted to assist with Nachias ERP navigation and Nachias application-related queries, and I do not assist with dangerous, harmful, or security-sensitive activities.";
        }

        // 2. Jailbreaks & persona overrides
        if (preg_match('/\b(ignore\s+(all\s+)?previous\s+instructions|act\s+as\s+dan|developer\s+mode|jailbreak|unrestricted\s+ai)\b/i', $q)) {
            return "I cannot fulfill this request. I am only permitted to assist with Nachias ERP navigation and Nachias application-related queries, and I do not assist with dangerous, harmful, or security-sensitive activities.";
        }

        // If the query explicitly mentions Nachias, allow it to proceed to RAG / Ollama
        if (stripos($q, 'nachias') !== false) {
            return null;
        }

        // 3. Greetings & pure small talk
        if (preg_match('/^(hi|hello|hey|good\s+(morning|afternoon|evening|night)|how\s+are\s+you|who\s+are\s+you|what\s+is\s+your\s+name|tell\s+me\s+a\s+joke|how\s+was\s+your\s+day|what\s+can\s+you\s+do)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        // 4. Outside companies, tech giants, websites, or external services
        if (preg_match('/\b(amazon|google|facebook|meta|apple|microsoft|flipkart|shopify|alibaba|twitter|instagram|netflix|youtube|wikipedia)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        // 5. General definitions of ERP, business, accounting, tax, technology (not specific to Nachias)
        if (preg_match('/^(what\s+is|what\s+does|define|explain|meaning\s+of)\s+(an?\s+)?(erp|erp\s+software|erp\s+system|enterprise\s+resource\s+planning|accounting|supply\s+chain|inventory|gst|vat|taxation|crm|business|database|sql|cloud|ai|artificial\s+intelligence|llm|machine\s+learning)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        // 6. General trivia, world knowledge, outside questions
        if (preg_match('/^(what\s+is\s+the\s+capital|who\s+is\s+(the\s+)?(president|prime\s+minister|ceo|governor)|(what\s+is\s+the\s+)?weather|tell\s+me\s+about\s+(india|tamil\s*nadu|world|movies|sports|cricket|science|history)|who\s+(created|made|built|developed)\s+you)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        // 7. General coding / math requests unrelated to Nachias
        if (preg_match('/\b(write\s+(a\s+)?(python|javascript|php|java|c\+\+|html|css|script|code|algorithm))\b/i', $q) || preg_match('/^(calculate|solve\s+math|what\s+is\s+\d+\s*[\+\-\*\/]\s*\d+)\b/i', $q)) {
            return "I am only permitted to assist with Nachias ERP application features and navigation. I cannot answer general or unrelated questions.";
        }

        return null;
    }

    /**
     * Detect the primary ERP screen from OCR text and user prompt.
     * Prioritizes header titles (e.g. "Add Purchase Order") and transactional forms
     * over secondary dropdown select fields (e.g. "Purchase Commission Agent").
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

            // 1. Explicit Form Actions: "Add <Screen>", "Edit <Screen>", "Create <Screen>"
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
                // Header Position Bonus: earlier in OCR = header of the screen
                if ($pos !== false) {
                    if ($pos < 40) {
                        $score += 350;
                    } elseif ($pos < 100) {
                        $score += 180;
                    } elseif ($pos < 200) {
                        $score += 60;
                    }
                }

                // Transactional screen bonus vs master lookup dropdown
                $isTransactional = in_array($menu->screen, [
                    'Purchase Orders', 'Purchase Invoices', 'Sales Orders', 'Sales Invoices',
                    'GRN Entry', 'Stock Entry', 'Debit Notes', 'Credit Notes', 'Job Card Entry',
                    'Production Receipts', 'Billing', 'Manage Payments'
                ], true);

                if ($isTransactional) {
                    $score += 180;
                }

                // Sub-dropdown penalty: e.g. "Purchase Commission Agent" appearing on a "Purchase Order" page
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
