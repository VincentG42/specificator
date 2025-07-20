<?php

return [
    'default_provider' => env('LLM_PROVIDER', 'claude'),
    
    'providers' => [
        'claude' => [
            'api_key' => env('CLAUDE_API_KEY'),
            'model' => 'claude-3-sonnet-20240229',
            'base_url' => 'https://api.anthropic.com/v1'
        ],
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model' => 'gemini-pro',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta'
        ]
    ],
    
    'limits' => [
        'max_tokens' => env('LLM_MAX_TOKENS', 1000),
        'timeout' => env('LLM_TIMEOUT', 30),
        'max_requests_per_session' => env('LLM_MAX_REQUESTS_SESSION', 10),
        'max_context_length' => 8000 
    ],
    
    'fallback' => [
        'enabled' => true,
        'message' => 'Service d\'aide temporairement indisponible. Consultez la documentation ou contactez l\'équipe.'
    ]
];