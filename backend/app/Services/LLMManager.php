<?php

namespace App\Services;

use App\Contracts\LLMServiceInterface;
use App\Models\Project;
use App\Services\LLM\ClaudeService;
use App\Services\LLM\GeminiService;
use GuzzleHttp\Client as HttpClient;
use InvalidArgumentException;
use Illuminate\Support\Facades\Config;

class LLMManager 
{
    private LLMServiceInterface $activeService;
    
    public function __construct() 
    {
        $provider = Config::get('llm.default_provider', 'claude');
        $this->activeService = $this->createService($provider);
    }
    
    private function createService(string $provider): LLMServiceInterface 
    {
        $config = Config::get("llm.providers.{$provider}");

        return match($provider) {
            'claude' => new ClaudeService(
                client: new HttpClient(),
                apiKey: $config['api_key'],
                maxTokens: Config::get('llm.limits.max_tokens', 1000),
                timeout: Config::get('llm.limits.timeout', 30)
            ),
            'gemini' => new GeminiService(
                client: new HttpClient(),
                apiKey: $config['api_key'],
                maxTokens: Config::get('llm.limits.max_tokens', 1000),
                timeout: Config::get('llm.limits.timeout', 30)
            ),
            default => throw new InvalidArgumentException("Provider {$provider} not supported")
        };
    }
    
    public function getHelp(Project $project, string $questionKey, string $userQuestion = ''): \App\DTOs\LLMResponse 
    {
        $context = $this->buildProjectContext($project, $questionKey);
        return $this->activeService->getHelp($context, $userQuestion);
    }

    private function buildProjectContext(Project $project, string $questionKey): string 
    {
        $context = Config::get('llm_prompts.system_prompt') . "\n\n";
        
        // Informations projet
        $context .= "## PROJET : {$project->name}\n";
        if ($project->description) {
            $context .= "Description : {$project->description}\n";
        }
        $context .= "Progression : {$project->progress}%\n\n";
        
        // Réponses existantes par section
        $sections = Config::get('questionnaire.sections');
        $answers = $project->answers()->get()->groupBy('section_key');
        
        foreach ($sections as $sectionKeyItem => $section) {
            if (!$answers->has($sectionKeyItem)) continue;
            
            $context .= "## {$section['title']}\n";
            
            foreach ($section['questions'] as $qKey => $question) {
                $answer = $answers[$sectionKeyItem]->where('question_key', $qKey)->first();
                if (!$answer || $answer->is_not_applicable) continue;
                
                $value = $answer->answer_value['value'] ?? '';
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                
                $context .= "- {$question['text']} : {$value}\n";
            }
            $context .= "\n";
        }
        
        // Contexte spécifique à la question
        $questionContext = Config::get("llm_prompts.question_contexts.{$questionKey}");
        if ($questionContext) {
            $context .= "## AIDE DEMANDÉE\n{$questionContext}\n\n";
        }
        
        return $context;
    }
}