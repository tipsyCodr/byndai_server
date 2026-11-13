<?php

namespace App\AI\Controllers;

use App\Http\Controllers\Controller;
use App\AI\Interface\AIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class AiChatApiController extends Controller
{
    protected AIService $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * POST /api/ai/chat
     * Body: { "message": "...", "history": [{"role":"user","content":"..."},...] }
     * Returns: { "reply": "..." }
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:4000',
            'history' => 'nullable|array',
            'history.*.role' => 'required|in:user,assistant',
            'history.*.content' => 'required|string',
        ]);

        $systemPrompt = "You are a helpful AI assistant for ByndAI, a professional networking app. "
            . "Help users with networking tips, managing leads, business connections, profile setup, "
            . "and general app usage. Be concise and professional.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...($request->input('history', [])),
            ['role' => 'user', 'content' => $request->input('message')],
        ];

        try {
            $reply = $this->ai->chat($messages);
            return response()->json(['reply' => $reply]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/ai/chat/stream
     * Body: { "message": "...", "history": [...] }
     * Returns: SSE stream — data: {"content":"..."}
     */
    public function stream(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:4000',
            'history' => 'nullable|array',
        ]);

        $systemPrompt = "You are a helpful AI assistant for ByndAI, a professional networking app. "
            . "Help users with networking tips, managing leads, business connections, profile setup, "
            . "and general app usage. Be concise and professional.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...($request->input('history', [])),
            ['role' => 'user', 'content' => $request->input('message')],
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

        return response()->stream(function () use ($messages, $driver) {
            echo ":" . str_repeat(' ', 2048) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            try {
                if ($driver === 'openai') {
                    // ── OpenAI streaming ───────────────────────────────────────────
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
                                echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                if (ob_get_level() > 0) ob_flush();
                                flush();
                            }
                        }
                        fclose($stream);
                    }
                } else {
                    // ── Ollama streaming ───────────────────────────────────────────
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
                                echo "data: " . json_encode(['content' => $data['message']['content']]) . "\n\n";
                                if (ob_get_level() > 0) ob_flush();
                                flush();
                            }
                        }
                        fclose($stream);
                    }
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
}

