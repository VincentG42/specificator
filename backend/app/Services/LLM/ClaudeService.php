<?php

namespace App\Services\LLM;

use App\Contracts\LLMServiceInterface;
use App\DTOs\LLMResponse;
use GuzzleHttp\Client as HttpClient;
use Exception;

class ClaudeService implements LLMServiceInterface 
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
            $response = $this->client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                    'anthropic-version' => '2023-06-01'
                ],
                'json' => [
                    'model' => 'claude-3-sonnet-20240229',
                    'max_tokens' => $this->maxTokens,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $this->buildPrompt($context, $userQuestion)
                        ]
                    ]
                ],
                'timeout' => $this->timeout
            ]);
            
            $data = $response->json();
            return new LLMResponse(
                success: true,
                content: $data['content'][0]['text'] ?? null,
                error: null,
                metadata: ['tokens_used' => $data['usage']['output_tokens'] ?? 0]
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
        return 'claude';
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