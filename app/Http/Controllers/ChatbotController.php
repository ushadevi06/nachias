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

        // 1. Retrieve authoritative ground-truth knowledge from ERP RAG
        $ragResult = $this->ragService->retrieve($effectivePrompt, 4);

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
}
