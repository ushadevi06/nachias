<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use App\Services\Neuron\NeuronAiService;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class ChatbotController extends Controller
{
    protected NeuronAiService $neuronService;
    protected TranslationService $translationService;

    public function __construct(
        NeuronAiService $neuronService,
        TranslationService $translationService
    ) {
        $this->neuronService = $neuronService;
        $this->translationService = $translationService;
    }

    /**
     * Display the chatbot interface.
     */
    public function index(): View
    {
        $model = $this->neuronService->getModel();
        $isAvailable = $this->neuronService->isAvailable();

        $userId = Auth::id();
        $savedSessions = [];

        if ($userId) {
            $savedSessions = ChatHistory::where('user_id', $userId)
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($chat) {
                    return [
                        'id' => $chat->session_id,
                        'title' => $chat->title,
                        'createdAt' => $chat->created_at ? $chat->created_at->timestamp * 1000 : now()->timestamp * 1000,
                        'updatedAt' => $chat->updated_at ? $chat->updated_at->timestamp * 1000 : now()->timestamp * 1000,
                        'messages' => is_string($chat->messages) ? json_decode($chat->messages, true) : ($chat->messages ?? []),
                        'conversationHistory' => is_string($chat->conversation_history) ? json_decode($chat->conversation_history, true) : ($chat->conversation_history ?? []),
                    ];
                })
                ->values()
                ->toArray();
        }

        return view('chatbot.index', compact('model', 'isAvailable', 'savedSessions'));
    }

    /**
     * Handle incoming chatbot messages via Neuron AI Agent and RAG pipeline.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $hasImage = $request->filled('image');

        $validator = Validator::make($request->all(), [
            'message' => [$hasImage ? 'nullable' : 'required', 'string', 'max:5000'],
            'session_id' => ['nullable', 'string', 'max:100'],
            'chat_title' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string'],
            'image_name' => ['nullable', 'string', 'max:255'],
            'image_text' => ['nullable', 'string', 'max:20000'],
            'is_voice' => ['nullable', 'boolean'],
            'audio' => ['nullable', 'string'],
            'target_lang' => ['nullable', 'string', 'max:10'],
            'history' => ['nullable', 'array'],
            'history.*.role' => ['nullable', 'string', 'in:user,assistant'],
            'history.*.content' => ['nullable', 'string'],
        ], [
            'message.required' => 'Please enter a message or upload a screenshot.',
            'message.max' => 'The message is too long (maximum 5000 characters).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Invalid input provided.',
            ], 422);
        }

        $rawMessage = trim((string) $request->input('message'));
        $imageData = $request->input('image');
        $imageName = $request->input('image_name');
        $imageText = trim((string) $request->input('image_text'));
        $rawHistory = $request->input('history', []);
        $history = is_array($rawHistory) ? array_slice($rawHistory, -12) : [];
        $isVoice = (bool) $request->input('is_voice', false);
        $audioData = $request->input('audio');
        $targetLang = $request->input('target_lang');

        // Process chat through Neuron AI Service & RAG
        $result = $this->neuronService->processChat(
            message: $rawMessage,
            history: $history,
            imageData: $imageData,
            imageText: $imageText,
            isVoice: $isVoice,
            targetLang: $targetLang
        );

        if (!empty($audioData)) {
            $result['audio'] = $audioData;
        }
        if (!empty($imageData)) {
            $result['image'] = $imageData;
            $result['image_name'] = $imageName;
        }

        // Persist chat session to database for authenticated user
        $this->saveChatHistoryToDatabase(
            $request->input('session_id'),
            $request->input('chat_title'),
            $rawMessage,
            $result,
            $history,
            $isVoice,
            $audioData,
            $imageData,
            $imageName
        );

        return response()->json($result);
    }

    /**
     * Transcribe audio voice notes and translate Tamil to English.
     */
    public function transcribeAudio(Request $request): JsonResponse
    {
        $transcript = trim((string) $request->input('transcript'));
        $audioData = $request->input('audio');

        if ($transcript === '' && empty($audioData)) {
            return response()->json([
                'success' => false,
                'message' => 'No voice recording or transcript provided.',
            ], 422);
        }

        $effectiveText = $transcript;
        $isTamil = $this->translationService->isTamil($effectiveText);

        $translatedText = $effectiveText;
        if ($isTamil && $effectiveText !== '') {
            $transResult = $this->translationService->translate($effectiveText, 'en', 'ta');
            $translatedText = $transResult['translated'];
        }

        return response()->json([
            'success' => true,
            'transcript' => $effectiveText,
            'translated_english' => $translatedText,
            'is_tamil' => $isTamil,
            'audio' => $audioData,
        ]);
    }

    /**
     * Persist chat session to the database table for the authenticated user.
     */
    protected function saveChatHistoryToDatabase(
        ?string $sessionId,
        ?string $chatTitle,
        string $rawMessage,
        array $result,
        array $history,
        bool $isVoice = false,
        ?string $audioData = null,
        ?string $imageData = null,
        ?string $imageName = null
    ): void {
        $userId = Auth::id();
        if (!$sessionId || !$userId) {
            return;
        }

        try {
            $chat = ChatHistory::firstOrNew([
                'user_id' => $userId,
                'session_id' => $sessionId,
            ]);

            $existingMessages = is_array($chat->messages)
                ? $chat->messages
                : (json_decode($chat->messages ?? '[]', true) ?: []);

            if (!$chat->exists || empty($chat->title) || $chat->title === 'New Chat') {
                $chat->title = !empty($chatTitle) ? $chatTitle : mb_substr($rawMessage ?: 'New Chat', 0, 36);
            }

            // User message record
            $existingMessages[] = [
                'id' => 'user_msg_' . round(microtime(true) * 1000),
                'role' => 'user',
                'text' => $rawMessage,
                'time' => date('h:i A'),
                'isVoice' => $isVoice,
                'audioData' => $audioData,
                'image' => $imageData,
                'imageName' => $imageName,
                'isTranslated' => $result['is_translated'] ?? false,
                'translatedMsg' => $result['translated_message'] ?? null,
            ];

            // AI message record
            $existingMessages[] = [
                'id' => 'ai_msg_' . round(microtime(true) * 1000 + 1),
                'role' => 'assistant',
                'text' => $result['message'] ?? '',
                'time' => date('h:i A'),
                'responseLanguage' => $result['response_language'] ?? 'en',
                'englishOriginal' => $result['english_message'] ?? null,
                'ragSources' => $result['rag_sources'] ?? [],
                'ragIntent' => $result['rag_intent'] ?? 'general',
                'framework' => $result['framework'] ?? 'Neuron AI',
            ];

            $chat->messages = $existingMessages;

            // Updated LLM conversation history
            $chatHistoryList = $history;
            $chatHistoryList[] = [
                'role' => 'user',
                'content' => ($result['is_translated'] ?? false) ? ($result['translated_message'] ?? $rawMessage) : $rawMessage,
            ];
            $chatHistoryList[] = [
                'role' => 'assistant',
                'content' => $result['english_message'] ?? ($result['message'] ?? ''),
            ];
            $chat->conversation_history = array_slice($chatHistoryList, -12);
            $chat->save();
        } catch (Throwable $e) {
            Log::warning('Failed saving chat history to database: ' . $e->getMessage());
        }
    }

    /**
     * Get all chat sessions for the authenticated user.
     */
    public function getSessions(): JsonResponse
    {
        $userId = Auth::id();
        $sessions = ChatHistory::where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->session_id,
                    'title' => $chat->title,
                    'createdAt' => $chat->created_at ? $chat->created_at->timestamp * 1000 : now()->timestamp * 1000,
                    'updatedAt' => $chat->updated_at ? $chat->updated_at->timestamp * 1000 : now()->timestamp * 1000,
                    'messages' => is_string($chat->messages) ? json_decode($chat->messages, true) : ($chat->messages ?? []),
                    'conversationHistory' => is_string($chat->conversation_history) ? json_decode($chat->conversation_history, true) : ($chat->conversation_history ?? []),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Save / sync a chat session for the authenticated user.
     */
    public function saveSession(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $sessionId = $request->input('session_id');

        if (!$sessionId || !$userId) {
            return response()->json(['success' => false, 'message' => 'Session ID and authentication required.'], 422);
        }

        $title = $request->input('title') ?: 'New Chat';
        $messages = $request->input('messages', []);
        $conversationHistory = $request->input('conversation_history', []);

        $chat = ChatHistory::updateOrCreate(
            [
                'user_id' => $userId,
                'session_id' => $sessionId,
            ],
            [
                'title' => $title,
                'messages' => $messages,
                'conversation_history' => $conversationHistory,
            ]
        );

        return response()->json([
            'success' => true,
            'session' => [
                'id' => $chat->session_id,
                'title' => $chat->title,
                'createdAt' => $chat->created_at ? $chat->created_at->timestamp * 1000 : now()->timestamp * 1000,
                'updatedAt' => $chat->updated_at ? $chat->updated_at->timestamp * 1000 : now()->timestamp * 1000,
            ],
        ]);
    }

    /**
     * Delete a chat session for the authenticated user.
     */
    public function deleteSession(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $sessionId = $request->input('session_id');

        if ($sessionId && $userId) {
            ChatHistory::where('user_id', $userId)
                ->where('session_id', $sessionId)
                ->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Rename a chat session for the authenticated user.
     */
    public function renameSession(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $sessionId = $request->input('session_id');
        $title = trim((string) $request->input('title'));

        if ($sessionId && $userId && $title !== '') {
            ChatHistory::where('user_id', $userId)
                ->where('session_id', $sessionId)
                ->update(['title' => $title]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Clear all chat sessions for the authenticated user.
     */
    public function clearAllSessions(): JsonResponse
    {
        $userId = Auth::id();
        if ($userId) {
            ChatHistory::where('user_id', $userId)->delete();
        }

        return response()->json(['success' => true]);
    }
}
