<?php

namespace App\DTOs;

class LLMResponse 
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $content,
        public readonly ?string $error,
        public readonly array $metadata = []
    ) {}
}