<?php

namespace App\Http\Controllers;

use App\Services\OllamaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    protected OllamaService $ollamaService;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
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

        $message = $request->input('message');
        $history = $request->input('history', []);

        $result = $this->ollamaService->chat($message, $history);

        return response()->json($result);
    }
}
