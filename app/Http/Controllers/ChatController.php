<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ai\Agents\ScholarshipChatAgent;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $providersOrder = ['ollama', 'gemini', 'groq'];

    public function handleChat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);
        $lastError = null;
        foreach ($this->providersOrder as $provider) {
            try {
                $agent = new ScholarshipChatAgent();
                $agent->provider = $provider;
                $response = $agent->prompt($validated['message']);
                return response()->json([
                    'status' => 'success',
                    'data' => ['reply' => (string) $response]
                ]);
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("Provider [{$provider}] failed: " . $lastError);
                if ($provider === end($this->providersOrder)) {
                    break;
                }
                usleep(500000); 
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'فشل الحصول على رد من جميع مزودي الخدمة. حاول مرة أخرى',
            'debug' => config('app.debug') ? $lastError : null,
        ], 500);
    }
}