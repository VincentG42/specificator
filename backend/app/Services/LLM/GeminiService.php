<?php

namespace App\Services\LLM;

use App\Contracts\LLMServiceInterface;
use App\DTOs\LLMResponse;
use GuzzleHttp\Client as HttpClient;
use Exception;

class GeminiService implements LLMServiceInterface 
{
    public function __construct(
        private HttpClient $client,
        private string $apiKey,
        private int $maxTokens = 1000,
        private int $timeout = 30
    ) {}
    
    public function getHelp(string $context, string $userQuestion): LLMResponse 
    {
        try {
            $response = $this->client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$this->apiKey}", [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $this->buildPrompt($context, $userQuestion)]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => $this->maxTokens,
                        'temperature' => 0.7
                    ]
                ],
                'timeout' => $this->timeout
            ]);
            
            $data = $response->json();
            return new LLMResponse(
                success: true,
                content: $data['candidates'][0]['content']['parts'][0]['text'] ?? null,
                error: null
            );
            
        } catch (Exception $e) {
            return new LLMResponse(
                success: false,
                content: null,
                error: $e->getMessage()
            );
        }
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function getProviderName(): string
    {
        return 'gemini';
    }

    public function getRemainingQuota(): ?int
    {
        return null; // Not tracked by this service
    }

    private function buildPrompt(string $context, string $userQuestion): string
    {
        return "Contexte:\n{$context}\n\nQuestion:\n{$userQuestion}";
    }
}