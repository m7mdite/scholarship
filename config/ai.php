<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    */
    'default' => 'ollama', // استخدام failover كمزود افتراضي

    /*
    |--------------------------------------------------------------------------
    | Failover Configuration (الترتيب حسب الأولوية)
    |--------------------------------------------------------------------------
    */
    'failover' => [
        'providers' => ['ollama', 'gemini', 'groq'], // الأولوية: Ollama → Gemini → Groq
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'ollama' => [
            'driver' => 'ollama',
            'key' => env('OLLAMA_API_KEY', ''),
            'url' => env('OLLAMA_URL', 'http://localhost:11434'),
            'timeout' => 120, // إضافة مهلة 120 ثانية لـ Ollama
        ],

        'gemini' => [
            'driver' => 'gemini',
            'key' => env('GEMINI_API_KEY'),
            'model' => 'gemini-1.5-flash',
        ],

        'groq' => [
            'driver' => 'groq',
            'key' => env('GROQ_API_KEY'),
        ],
    ],

    // ... باقي الإعدادات (caching, default_for_images, إلخ)
];