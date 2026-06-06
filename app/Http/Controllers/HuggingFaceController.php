<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HuggingFaceController extends Controller
{
    public static function query($model, $prompt, $parameters = [])
    {
        $apiKey = env('HUGGINGFACE_API_KEY');
        if (!$apiKey) {
            Log::error('Hugging Face API key missing');
            return null;
        }

        $params = array_merge_recursive([
            'inputs' => $prompt,
            'parameters' => [
                'max_new_tokens' => 500,
                'temperature' => 0.7,
                'return_full_text' => false,
            ],
        ], $parameters);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)
            ->post("https://api-inference.huggingface.co/models/{$model}", $params);

            if ($response->successful()) {
                $result = $response->json();
                if (is_array($result) && isset($result[0]['generated_text'])) {
                    return trim($result[0]['generated_text']);
                } elseif (is_array($result) && isset($result['generated_text'])) {
                    return trim($result['generated_text']);
                }
                return null;
            } else {
                Log::error('Hugging Face API error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Hugging Face exception: ' . $e->getMessage());
            return null;
        }
    }
}