<?php

namespace App\Http\Controllers;

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
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'max:2000'],
            'is_voice' => ['nullable', 'boolean'],
            'audio' => ['nullable', 'string'],
            'history' => ['nullable', 'array', 'max:12'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
        ], [
            'message.required' => 'Please enter a message.',
            'message.max' => 'The message is too long (maximum 2000 characters).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('message') ?: 'Invalid input provided.',
            ], 422);
        }

        $rawMessage = $request->input('message');
        $history = $request->input('history', []);
        $isVoice = (bool) $request->input('is_voice', false);
        $audioData = $request->input('audio');
        $requestedTargetLang = $request->input('target_lang');

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

            return response()->json($result);
        }

        // 1. Retrieve authoritative ground-truth knowledge from ERP RAG
        $ragResult = $this->ragService->retrieve($effectivePrompt, 4);

        // If no documented Nachias ERP knowledge chunks match this question and it has no Nachias context:
        $hasNachiasContext = stripos($effectivePrompt, 'nachias') !== false;
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

            return response()->json($result);
        }

        // 2. Send the translated English input + retrieved knowledge to the local Ollama LLM
        $result = $this->ollamaService->chatWithRag($effectivePrompt, $ragResult['context'], $history);

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
}
