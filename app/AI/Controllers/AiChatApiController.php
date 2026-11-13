<?php

namespace App\AI\Controllers;

use App\Http\Controllers\Controller;
use App\AI\Interface\AIService;
use App\Models\AiConversation;
use App\Models\AiMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiChatApiController extends Controller
{
    protected AIService $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * GET /api/ai/conversations
     * Returns list of user's past AI conversations
     */
    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = AiConversation::where('user_id', $userId)
            ->with(['latestMessage'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'title' => $c->title,
                    'updated_at' => $c->updated_at->toISOString(),
                    'last_message' => $c->latestMessage ? Str::limit($c->latestMessage->content, 60) : '',
                ];
            });

        return response()->json(['conversations' => $conversations]);
    }

    /**
     * GET /api/ai/conversations/{id}
     * Returns full message history for a conversation
     */
    public function messages(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;

        $conversation = AiConversation::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $messages = $conversation->messages()->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'role' => $m->role,
                'content' => $m->content,
                'created_at' => $m->created_at->toISOString(),
            ];
        });

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * DELETE /api/ai/conversations/{id}
     * Deletes a conversation and its messages
     */
    public function deleteConversation(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;

        $conversation = AiConversation::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $conversation->delete();

        return response()->json(['success' => true]);
    }

    /**
     * POST /api/ai/chat
     * Non-streaming endpoint
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:4000',
            'conversation_id' => 'nullable|integer',
            'history' => 'nullable|array',
            'history.*.role' => 'required|in:user,assistant',
            'history.*.content' => 'required|string',
        ]);

        $user = $request->user();
        $messageText = $request->input('message');
        $conversation = $this->resolveConversation($user->id, $request->input('conversation_id'), $messageText);

        // Save user message
        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $messageText,
        ]);

        $systemPrompt = "You are a helpful AI assistant for ByndAI, a professional networking app. "
            . "Help users with networking tips, managing leads, business connections, profile setup, "
            . "and general app usage. Be concise and professional.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...($request->input('history', [])),
            ['role' => 'user', 'content' => $messageText],
        ];

        try {
            $reply = $this->ai->chat($messages);

            // Save assistant reply
            AiMessage::create([
                'ai_conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $reply,
            ]);

            $conversation->touch();

            return response()->json([
                'reply' => $reply,
                'conversation_id' => $conversation->id,
                'conversation_title' => $conversation->title,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/ai/chat/stream
     * SSE stream with DB persistence
     */
    public function stream(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:4000',
            'conversation_id' => 'nullable|integer',
            'history' => 'nullable|array',
        ]);

        $user = $request->user();
        $messageText = $request->input('message');
        $conversation = $this->resolveConversation($user->id, $request->input('conversation_id'), $messageText);

        // Save user message immediately
        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $messageText,
        ]);

        $systemPrompt = "You are a helpful AI assistant for ByndAI, a professional networking app. "
            . "Help users with networking tips, managing leads, business connections, profile setup, "
            . "and general app usage. Be concise and professional.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...($request->input('history', [])),
            ['role' => 'user', 'content' => $messageText],
        ];

        @ini_set('zlib.output_compression', 'Off');
        @ini_set('output_buffering', 'Off');
        @ini_set('implicit_flush', 'On');
        set_time_limit(0);

        $driver = config('services.ai.driver');
        if (!$driver || $driver === 'ollama') {
            if (env('OPENAI_API_SECRET_KEY') || env('OPENAI_API_KEY') || config('services.openai.key')) {
                $driver = 'openai';
            } else {
                $driver = 'ollama';
            }
        }

        $conversationId = $conversation->id;
        $conversationTitle = $conversation->title;

        return response()->stream(function () use ($messages, $driver, $conversationId, $conversationTitle) {
            echo ":" . str_repeat(' ', 2048) . "\n\n";
            // Send conversation meta first so client knows conversation_id
            echo "data: " . json_encode([
                'meta' => [
                    'conversation_id' => $conversationId,
                    'title' => $conversationTitle,
                ]
            ]) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            $fullReply = '';

            try {
                if ($driver === 'openai') {
                    $apiKey  = config('services.openai.key') ?: env('OPENAI_API_SECRET_KEY') ?: env('OPENAI_API_KEY');
                    $model   = config('services.openai.model', 'gpt-4o-mini');
                    $baseUrl = config('services.openai.url', 'https://api.openai.com/v1');

                    if (empty($apiKey)) {
                        echo "data: " . json_encode(['content' => 'Error: OpenAI API key is missing in .env']) . "\n\n";
                        echo "data: [DONE]\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                        return;
                    }

                    $response = Http::withToken($apiKey)
                        ->withOptions(['stream' => true, 'timeout' => 60, 'connect_timeout' => 10])
                        ->post("{$baseUrl}/chat/completions", [
                            'model'    => $model,
                            'messages' => $messages,
                            'stream'   => true,
                        ]);

                    $stream = $response->toPsrResponse()->getBody()->detach();

                    if (is_resource($stream)) {
                        while (!feof($stream)) {
                            $line = fgets($stream);
                            if ($line === false || trim($line) === '') continue;
                            $line = ltrim($line, 'data: ');
                            if (trim($line) === '[DONE]') break;
                            $data = json_decode(trim($line), true);
                            $content = $data['choices'][0]['delta']['content'] ?? null;
                            if ($content !== null) {
                                $fullReply .= $content;
                                echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                if (ob_get_level() > 0) ob_flush();
                                flush();
                            }
                        }
                        fclose($stream);
                    }
                } else {
                    $ollamaUrl = config('services.ollama.url', 'http://localhost:11434');
                    $model     = config('services.ollama.model', 'gemma4:e4b');

                    $response = Http::withOptions(['stream' => true, 'timeout' => 60, 'connect_timeout' => 5])
                        ->post("{$ollamaUrl}/api/chat", [
                            'model'    => $model,
                            'messages' => $messages,
                            'stream'   => true,
                            'options'  => ['num_predict' => 4096, 'num_ctx' => 4096],
                        ]);

                    $stream = $response->toPsrResponse()->getBody()->detach();

                    if (is_resource($stream)) {
                        while (!feof($stream)) {
                            $line = fgets($stream);
                            if ($line === false) continue;
                            $data = json_decode(trim($line), true);
                            if (isset($data['message']['content'])) {
                                $content = $data['message']['content'];
                                $fullReply .= $content;
                                echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                if (ob_get_level() > 0) ob_flush();
                                flush();
                            }
                        }
                        fclose($stream);
                    }
                }

                // Persist completed assistant reply
                if (!empty($fullReply)) {
                    AiMessage::create([
                        'ai_conversation_id' => $conversationId,
                        'role' => 'assistant',
                        'content' => $fullReply,
                    ]);
                    AiConversation::where('id', $conversationId)->update(['updated_at' => now()]);
                }
            } catch (\Throwable $e) {
                echo "data: " . json_encode(['content' => 'AI Service error: ' . $e->getMessage()]) . "\n\n";
            }

            echo "data: [DONE]\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();
        }, 200, [
            'Cache-Control'    => 'no-cache',
            'Content-Type'     => 'text/event-stream',
            'Connection'       => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Finds or creates a conversation for the user
     */
    protected function resolveConversation(int $userId, ?int $conversationId, string $firstMessage): AiConversation
    {
        if ($conversationId) {
            $existing = AiConversation::where('id', $conversationId)
                ->where('user_id', $userId)
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        $title = Str::limit(trim($firstMessage), 32, '...');
        if (empty($title)) {
            $title = 'New Chat';
        }

        return AiConversation::create([
            'user_id' => $userId,
            'title' => $title,
        ]);
    }
}
