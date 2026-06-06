<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function handleChat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $systemPrompt = "أنت مساعد ذكي ومتخصص في المنح الدراسية...";
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $validated['message']],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                // 'model' => 'mixtral-8x7b-32768', // نموذج مجاني وسريع
                'model' => 'llama-3.1-8b-instant', // نموذج مجاني وسريع
                'messages' => $messages,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $aiReply = $response->json()['choices'][0]['message']['content'];
                return response()->json([
                    'status' => 'success',
                    'data' => ['reply' => $aiReply]
                ]);
            } else {
                Log::error('Groq API error: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'فشل الاتصال بـ Groq: ' . $response->body(),
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Groq exception: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء الاتصال بـ Groq.',
            ], 500);
        }
    }
}