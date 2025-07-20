<?php

namespace App\Contracts;

use App\DTOs\LLMResponse;

interface LLMServiceInterface 
{
    public function getHelp(string $context, string $userQuestion): LLMResponse;
    public function isAvailable(): bool;
    public function getProviderName(): string;
    public function getRemainingQuota(): ?int;
}