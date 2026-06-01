<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use App\Services\OllamaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        protected OllamaService $ollama
    ) {}

    public function index()
    {
        return view('chatbot.index');
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $message = $request->input('message');

        ChatHistory::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => $message,
        ]);

        $history = ChatHistory::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        $ollamaMessages = $history->map(fn ($chat) => [
            'role' => $chat->role,
            'content' => $chat->message,
        ])->toArray();

        $response = $this->ollama->chat($ollamaMessages, $message);

        ChatHistory::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => $response,
        ]);

        return response()->json([
            'success' => true,
            'response' => $response,
        ]);
    }

    public function getHistory(Request $request): JsonResponse
    {
        $history = ChatHistory::where('user_id', $request->user()->id)
            ->oldest()
            ->get(['role', 'message', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    public function clearHistory(Request $request): JsonResponse
    {
        ChatHistory::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat chat berhasil dihapus.',
        ]);
    }
}
