<?php

namespace App\AI\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AI\Interface\AIService;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        //chat streaming initial logic
        
        return view('ai::chats.index', [
            'messages' => session('messages', [])
        ]);
    }

    public function create()
    {
        return view('ai::create.index');
    }

    public function plan(Request $request)
    {
        set_time_limit(120);
        $prompt = $request->input('prompt');

        $systemPrompt = "You are an AI website planner. The user will ask for a website/app design. " .
            "Based on the request, plan the necessary pages (e.g., home, about, menu, contact, pricing, etc.) and the key sections needed for each page.\n\n" .
            "You MUST respond with ONLY a valid JSON object matching this schema:\n" .
            "{\n" .
            "  \"pages\": [\n" .
            "    {\n" .
            "      \"slug\": \"homepage\",\n" .
            "      \"title\": \"Home\",\n" .
            "      \"sections\": [\"navbar\", \"hero\", \"features\", \"footer\"]\n" .
            "    },\n" .
            "    {\n" .
            "      \"slug\": \"about\",\n" .
            "      \"title\": \"About Us\",\n" .
            "      \"sections\": [\"navbar\", \"story-content\", \"team-members\", \"footer\"]\n" .
            "    }\n" .
            "  ]\n" .
            "}\n" .
            "Create as many pages as relevant to satisfy the user's prompt. Do not return any explanations, conversational text, markdown wrapping, or code blocks. ONLY RAW JSON.";

        $aiService = app(AIService::class);
        $response = $aiService->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $prompt]
        ]);

        // Robust extraction of JSON content from model output
        $cleaned = trim($response);
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $cleaned, $matches)) {
            $cleaned = $matches[1];
        } else {
            $firstBrace = strpos($cleaned, '{');
            $lastBrace = strrpos($cleaned, '}');
            if ($firstBrace !== false && $lastBrace !== false) {
                $cleaned = substr($cleaned, $firstBrace, $lastBrace - $firstBrace + 1);
            }
        }

        $planData = json_decode(trim($cleaned), true);

        if (!$planData || !isset($planData['pages'])) {
            return response()->json([
                'error' => 'Failed to parse plan from AI model',
                'raw' => $response
            ], 500);
        }

        return response()->json($planData);
    }

    public function generateSection(Request $request)
    {
        $prompt = $request->input('prompt');
        $pageSlug = $request->input('page_slug');
        $sectionName = $request->input('section_name');
        $existingSections = $request->input('existing_sections', []); // array of {name, html}

        // Just pass section names to guide structural order, keeping prompt small
        $secNames = array_map(fn($s) => $s['name'], $existingSections);
        $structureContext = empty($secNames) 
            ? "This is the first section on the page." 
            : "Sections already generated before this: " . implode(" -> ", $secNames) . ". Ensure this section fits logically under them.";

        $systemPrompt = "You are an expert frontend developer generating Tailwind CSS components. " .
            "The user is building a page block-by-block. You are generating ONLY the section: '{$sectionName}' for page '{$pageSlug}'.\n" .
            "YOU MUST: 1) Respond with ONLY valid HTML (a <div> or semantic section block). 2) Use Tailwind classes for all styling. 3) DO NOT output full <html>, <head>, or <body> tags. 4) DO NOT output markdown, explanations, or conversational text. ONLY HTML.\n\n" .
            "Context: {$structureContext}\n" .
            "Ensure the colors, fonts, and styling align with the overall project theme.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => "Generate the HTML code for the section '{$sectionName}' based on this overall project prompt: {$prompt}"]
        ];

        // Disable compression and buffering for streaming
        @ini_set('zlib.output_compression', 'Off');
        @ini_set('output_buffering', 'Off');
        @ini_set('implicit_flush', 'On');
        set_time_limit(0);

        $driver = config('services.ai.driver', 'ollama');

        return response()->stream(function () use ($messages, $driver) {
            echo ":" . str_repeat(' ', 4096) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            if ($driver === 'openai') {
                $response = Http::withToken(config('services.openai.key'))
                    ->withOptions(['stream' => true, 'timeout' => 0])
                    ->post(config('services.openai.url', 'https://api.openai.com/v1') . '/chat/completions', [
                        'model'       => config('services.openai.model', 'gpt-4o'),
                        'messages'    => $messages,
                        'stream'      => true,
                        'max_tokens'  => 4096,
                    ]);

                $stream = $response->toPsrResponse()->getBody()->detach();
                if (is_resource($stream)) {
                    $buffer = '';
                    while (!feof($stream)) {
                        $chunk = fread($stream, 4096);
                        if ($chunk === false) break;
                        $buffer .= $chunk;
                        $lines = explode("\n", $buffer);
                        $buffer = array_pop($lines);

                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (!str_starts_with($line, 'data: ')) continue;
                            $payload = substr($line, 6);
                            if ($payload === '[DONE]') break 2;
                            $data = json_decode($payload, true);
                            $content = $data['choices'][0]['delta']['content'] ?? null;
                            if ($content !== null) {
                                echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                if (ob_get_level() > 0) ob_flush();
                                flush();
                            }
                        }
                    }
                    fclose($stream);
                }
            } else {
                $response = Http::withOptions(['stream' => true, 'timeout' => 0])
                    ->post("http://localhost:11434/api/chat", [
                        'model'    => config('services.ollama.model', 'gemma4:e4b'),
                        'messages' => $messages,
                        'stream'   => true,
                        'options'  => [
                            'num_predict' => 4096,
                            'num_ctx'     => 8192,
                        ]
                    ]);

                $stream = $response->toPsrResponse()->getBody()->detach();
                if (is_resource($stream)) {
                    while (!feof($stream)) {
                        $line = fgets($stream);
                        if ($line === false) continue;
                        $data = json_decode(trim($line), true);
                        if (isset($data['message']['content'])) {
                            $content = $data['message']['content'];
                            echo "data: " . json_encode(['content' => $content]) . "\n\n";
                            if (ob_get_level() > 0) ob_flush();
                            flush();
                        }
                    }
                    fclose($stream);
                }
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'text/event-stream',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function generate(Request $request)
    {
        $prompt = $request->input('prompt');
        $existingHtml = $request->input('existingHtml', '');

        $messages = [
            [
                'role'    => 'system',
                'content' => "You are an expert frontend developer. The user wants you to design a web page. YOU MUST RESPOND WITH ONLY VALID HTML5 CODE. Start your response with `<!DOCTYPE html>`. Use Tailwind CSS via CDN for styling (`<script src=\"https://cdn.tailwindcss.com\"></script>`). Do NOT output any markdown, explanations, or conversational text. JUST HTML."
            ]
        ];

        if (!empty(trim($existingHtml))) {
            // Edit Mode System Prompt
            $messages[0]['content'] = "You are an expert frontend developer in EDIT MODE. The user will give you an existing HTML page and a modification request. YOU MUST: 1) Take the existing HTML as the base. 2) Apply ONLY the requested changes. 3) Return the COMPLETE modified HTML. 4) DO NOT rewrite from scratch. 5) Respond with ONLY raw HTML starting with <!DOCTYPE html>. No markdown, no explanations.";
            
            $messages[] = [
                'role'    => 'user',
                'content' => "EXISTING PAGE HTML:\n\n" . $existingHtml . "\n\nMODIFICATION REQUEST: " . $prompt . "\n\nIMPORTANT: Keep all existing content. Only apply the requested change. Return the complete HTML."
            ];
        } else {
            $messages[] = [
                'role'    => 'user',
                'content' => $prompt
            ];
        }

        // Disable compression and buffering for streaming
        @ini_set('zlib.output_compression', 'Off');
        @ini_set('output_buffering', 'Off');
        @ini_set('implicit_flush', 'On');
        set_time_limit(0);

        $driver = config('services.ai.driver', 'ollama');

        return response()->stream(function () use ($messages, $driver) {
            echo ":" . str_repeat(' ', 4096) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            if ($driver === 'openai') {
                // ── OpenAI streaming ──────────────────────────────────────
                $response = Http::withToken(config('services.openai.key'))
                    ->withOptions(['stream' => true, 'timeout' => 0])
                    ->post(config('services.openai.url', 'https://api.openai.com/v1') . '/chat/completions', [
                        'model'       => config('services.openai.model', 'gpt-4o'),
                        'messages'    => $messages,
                        'stream'      => true,
                        'max_tokens'  => 8192,
                    ]);

                $stream = $response->toPsrResponse()->getBody()->detach();

                if (is_resource($stream)) {
                    $buffer = '';
                    while (!feof($stream)) {
                        $chunk = fread($stream, 4096);
                        if ($chunk === false) break;
                        $buffer .= $chunk;
                        $lines = explode("\n", $buffer);
                        $buffer = array_pop($lines); // keep incomplete last line

                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (!str_starts_with($line, 'data: ')) continue;
                            $payload = substr($line, 6);
                            if ($payload === '[DONE]') break 2;
                            $data = json_decode($payload, true);
                            $content = $data['choices'][0]['delta']['content'] ?? null;
                            if ($content !== null) {
                                echo "data: " . json_encode(['content' => $content]) . "\n\n";
                                if (ob_get_level() > 0) ob_flush();
                                flush();
                            }
                        }
                    }
                    fclose($stream);
                }
            } else {
                // ── Ollama streaming ──────────────────────────────────────
                $response = Http::withOptions(['stream' => true, 'timeout' => 0])
                    ->post("http://localhost:11434/api/chat", [
                        'model'    => config('services.ollama.model', 'gemma4:e4b'),
                        'messages' => $messages,
                        'stream'   => true,
                        'options'  => [
                            'num_predict' => 8192,
                            'num_ctx'     => 8192,
                        ]
                    ]);

                $stream = $response->toPsrResponse()->getBody()->detach();

                if (is_resource($stream)) {
                    while (!feof($stream)) {
                        $line = fgets($stream);
                        if ($line === false) continue;
                        $data = json_decode(trim($line), true);
                        if (isset($data['message']['content'])) {
                            $content = $data['message']['content'];
                            echo "data: " . json_encode(['content' => $content]) . "\n\n";
                            if (ob_get_level() > 0) ob_flush();
                            flush();
                        }
                    }
                    fclose($stream);
                }
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'text/event-stream',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function store(Request $request)
    {
        $messages = session('messages', []);

        // Add user message
        $messages[] = [
            'role' => 'user',
            'content' => $request->input('message')
        ];

        // Call AI service
        $aiService = app(AIService::class);
        $response = $aiService->chat($messages);

        // Add AI response
        $messages[] = [
            'role' => 'assistant',
            'content' => $response
        ];

        // Save session
        session(['messages' => $messages]);

        return back();
    }

    public function stream(Request $request)
    {
        $message = $request->input('message');
        $messages = session('messages', []);

        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];
        session(['messages' => $messages]);

        // Disable compression and buffering
        @ini_set('zlib.output_compression', 'Off');
        @ini_set('output_buffering', 'Off');
        @ini_set('implicit_flush', 'On');

        return response()->stream(function () use ($messages) {
            // Force Nginx/Apache to flush headers and initial buffers
            echo ":" . str_repeat(' ', 4096) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            $response = Http::withOptions([
                'stream' => true,
                'timeout' => 0,
            ])->post("http://localhost:11434/api/chat", [
                'model' => config('services.ollama.model', 'llama2-uncensored:7b'),
                'messages' => $messages,
                'stream' => true
            ]);

            $stream = $response->toPsrResponse()->getBody()->detach();
            $fullResponse = '';

            if (is_resource($stream)) {
                while (!feof($stream)) {
                    $line = fgets($stream);
                    if ($line === false) continue;
                    $data = json_decode(trim($line), true);
                    if (isset($data['message']['content'])) {
                        $content = $data['message']['content'];
                        $fullResponse .= $content;
                        echo "data: " . json_encode(['content' => $content]) . "\n\n";
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }
                fclose($stream);
            }

            // Save assistant response to session
            $messages = session('messages', []);
            $messages[] = [
                'role' => 'assistant',
                'content' => $fullResponse
            ];
            session(['messages' => $messages]);
            session()->save();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function clear()
    {
        session()->forget('messages');
        return back();
    }
}
