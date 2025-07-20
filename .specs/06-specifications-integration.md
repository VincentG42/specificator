# 🔗 SPÉCIFICATIONS D'INTÉGRATION

## 6.1 Intégration LLM (Large Language Model)

### Architecture d'abstraction

#### Interface commune
```php
// app/Contracts/LLMServiceInterface.php
interface LLMServiceInterface 
{
    public function getHelp(string $context, string $userQuestion): LLMResponse;
    public function isAvailable(): bool;
    public function getProviderName(): string;
    public function getRemainingQuota(): ?int;
}

// app/DTOs/LLMResponse.php
class LLMResponse 
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $content,
        public readonly ?string $error,
        public readonly array $metadata = []
    ) {}
}

Implémentations spécifiques

php 

// app/Services/LLM/ClaudeService.php
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
}

// app/Services/LLM/GeminiService.php  
class GeminiService implements LLMServiceInterface 
{
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
}

Service Manager

php 

// app/Services/LLMManager.php
class LLMManager 
{
    private LLMServiceInterface $activeService;
    
    public function __construct() 
    {
        $provider = config('llm.default_provider', 'claude');
        $this->activeService = $this->createService($provider);
    }
    
    private function createService(string $provider): LLMServiceInterface 
    {
        return match($provider) {
            'claude' => new ClaudeService(
                client: new HttpClient(),
                apiKey: config('llm.claude.api_key'),
                maxTokens: config('llm.max_tokens', 1000),
                timeout: config('llm.timeout', 30)
            ),
            'gemini' => new GeminiService(
                client: new HttpClient(),
                apiKey: config('llm.gemini.api_key'),
                maxTokens: config('llm.max_tokens', 1000),
                timeout: config('llm.timeout', 30)
            ),
            default => throw new InvalidArgumentException("Provider {$provider} not supported")
        };
    }
    
    public function getHelp(Project $project, string $questionKey, string $userQuestion = ''): LLMResponse 
    {
        $context = $this->buildProjectContext($project, $questionKey);
        return $this->activeService->getHelp($context, $userQuestion);
    }
}

6.2 Système de prompts prédéfinis
Prompt système principal

php 

// config/llm_prompts.php
return [
    'system_prompt' => "Tu es un architecte logiciel senior spécialisé en formalisation de spécifications techniques. 

Ton rôle est d'aider les développeurs à compléter leurs spécifications de projet en :
- Posant des questions pertinentes pour clarifier les besoins
- Suggérant des bonnes pratiques selon le contexte
- Identifiant les points critiques souvent oubliés
- Proposant des exemples concrets et exploitables

Réponds de manière :
- Concise mais complète (200-400 mots max)
- Structurée avec des listes à puces si pertinent
- Pratique avec des exemples concrets
- Adaptée au niveau technique du projet

Contexte du projet ci-dessous :",

    'question_contexts' => [
        'project-objective' => "L'utilisateur doit définir l'objectif principal de son projet. Aide-le à :
- Identifier clairement le problème métier résolu
- Définir les bénéficiaires principaux
- Formuler un objectif SMART (Spécifique, Mesurable, Atteignable, Réaliste, Temporel)
- Éviter les objectifs trop vagues ou trop larges",

        'project-scope' => "L'utilisateur doit délimiter le périmètre de son projet. Aide-le à :
- Lister les fonctionnalités incluses dans le MVP
- Identifier explicitement ce qui est exclu
- Éviter le scope creep en définissant des limites claires
- Prioriser les fonctionnalités essentielles",

        'main-features' => "L'utilisateur doit lister ses fonctionnalités principales. Aide-le à :
- Utiliser la méthode MoSCoW (Must/Should/Could/Won't have)
- Formuler des fonctionnalités testables et mesurables
- Identifier les dépendances entre fonctionnalités
- Éviter les fonctionnalités trop techniques ou trop vagues",

        'technical-stack' => "L'utilisateur doit choisir sa stack technique. Aide-le à :
- Considérer les contraintes de l'équipe (compétences, préférences)
- Évaluer la maturité et l'écosystème des technologies
- Anticiper la scalabilité et la maintenance
- Justifier ses choix techniques",

        'data-model' => "L'utilisateur doit définir son modèle de données. Aide-le à :
- Identifier les entités métier principales
- Définir les relations et cardinalités
- Anticiper les besoins de performance (index, requêtes)
- Considérer l'évolutivité du schéma",

        'user-experience' => "L'utilisateur doit spécifier l'expérience utilisateur. Aide-le à :
- Définir les parcours utilisateur principaux
- Identifier les points de friction potentiels
- Considérer l'accessibilité et l'inclusivité
- Adapter l'interface aux compétences des utilisateurs"
    ]
];

Construction du contexte projet

php 

// app/Services/LLMManager.php (suite)
private function buildProjectContext(Project $project, string $questionKey): string 
{
    $context = config('llm_prompts.system_prompt') . "\n\n";
    
    // Informations projet
    $context .= "## PROJET : {$project->name}\n";
    if ($project->description) {
        $context .= "Description : {$project->description}\n";
    }
    $context .= "Progression : {$project->progress}%\n\n";
    
    // Réponses existantes par section
    $sections = config('questionnaire.sections');
    $answers = $project->answers()->get()->groupBy('section_key');
    
    foreach ($sections as $sectionKey => $section) {
        if (!$answers->has($sectionKey)) continue;
        
        $context .= "## {$section['title']}\n";
        
        foreach ($section['questions'] as $qKey => $question) {
            $answer = $answers[$sectionKey]->where('question_key', $qKey)->first();
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
    $questionContext = config("llm_prompts.question_contexts.{$questionKey}");
    if ($questionContext) {
        $context .= "## AIDE DEMANDÉE\n{$questionContext}\n\n";
    }
    
    return $context;
}

6.3 Gestion des erreurs et retry manuel
Gestion des erreurs LLM

php 

// app/Http/Controllers/LLMController.php
class LLMController extends Controller 
{
    public function getHelp(Request $request, LLMManager $llmManager) 
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'question_key' => 'required|string',
            'user_question' => 'nullable|string|max:500'
        ]);
        
        $project = Project::findOrFail($request->project_id);
        
        // Vérification quota session
        $sessionRequests = session('llm_requests', 0);
        if ($sessionRequests >= config('llm.max_requests_per_session', 10)) {
            return response()->json([
                'success' => false,
                'error' => 'Limite de demandes d\'aide atteinte pour cette session (10 max)',
                'error_type' => 'quota_exceeded'
            ], 429);
        }
        
        try {
            $response = $llmManager->getHelp(
                $project, 
                $request->question_key, 
                $request->user_question ?? ''
            );
            
            if ($response->success) {
                // Incrémenter compteur session
                session(['llm_requests' => $sessionRequests + 1]);
                
                // Optionnel : Sauvegarder l'historique
                $this->saveHelpRequest($project, $request, $response);
                
                return response()->json([
                    'success' => true,
                    'content' => $response->content,
                    'metadata' => $response->metadata,
                    'remaining_requests' => config('llm.max_requests_per_session', 10) - $sessionRequests - 1
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $this->formatLLMError($response->error),
                    'error_type' => 'llm_error',
                    'retry_available' => true
                ], 500);
            }
            
        } catch (Exception $e) {
            Log::error('LLM Help Request Failed', [
                'project_id' => $project->id,
                'question_key' => $request->question_key,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Service d\'aide temporairement indisponible',
                'error_type' => 'service_unavailable',
                'retry_available' => true
            ], 503);
        }
    }
    
    private function formatLLMError(string $error): string 
    {
        // Simplifier les erreurs techniques pour l'utilisateur
        if (str_contains($error, 'timeout')) {
            return 'Le service d\'aide met trop de temps à répondre. Réessayez avec une question plus courte.';
        }
        
        if (str_contains($error, 'rate limit')) {
            return 'Trop de demandes simultanées. Attendez quelques secondes avant de réessayer.';
        }
        
        if (str_contains($error, 'invalid_api_key')) {
            return 'Configuration du service d\'aide incorrecte. Contactez l\'administrateur.';
        }
        
        return 'Erreur temporaire du service d\'aide. Réessayez dans quelques instants.';
    }
}

Interface utilisateur pour retry

vue 

<!-- components/HelpPanel.vue -->
<template>
  <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-purple-700">
        🤖 Aide IA
      </h3>
      <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
        ✕
      </button>
    </div>
    
    <!-- Zone de saisie question optionnelle -->
    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Question spécifique (optionnel)
      </label>
      <textarea 
        v-model="userQuestion"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
        rows="3"
        placeholder="Précisez votre question ou laissez vide pour une aide générale..."
        :disabled="loading"
      ></textarea>
    </div>
    
    <!-- Bouton demande aide -->
    <div class="flex justify-between items-center mb-4">
      <button 
        @click="requestHelp"
        :disabled="loading || quotaExceeded"
        class="bg-purple-500 hover:bg-purple-600 disabled:bg-gray-300 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200"
      >
        <span v-if="loading" class="flex items-center space-x-2">
          <div class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></div>
          <span>Réflexion en cours...</span>
        </span>
        <span v-else>Demander de l'aide</span>
      </button>
      
      <span class="text-sm text-gray-500">
        {{ remainingRequests }}/10 demandes restantes
      </span>
    </div>
    
    <!-- Zone de réponse -->
    <div v-if="response || error" class="border-t border-gray-200 pt-4">
      <!-- Succès -->
      <div v-if="response" class="prose prose-sm max-w-none">
        <div class="bg-purple-50 rounded-md p-4 mb-3">
          <h4 class="text-purple-800 font-medium mb-2">💡 Suggestion :</h4>
          <div class="text-purple-700 whitespace-pre-wrap">{{ response }}</div>
        </div>
      </div>
      
      <!-- Erreur avec retry -->
      <div v-if="error" class="bg-red-50 rounded-md p-4">
        <div class="flex items-start justify-between">
          <div>
            <h4 class="text-red-800 font-medium mb-1">❌ Erreur</h4>
            <p class="text-red-700 text-sm">{{ error }}</p>
          </div>
          <button 
            v-if="retryAvailable"
            @click="requestHelp"
            class="text-red-600 hover:text-red-800 text-sm font-medium"
          >
            🔄 Réessayer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    projectId: Number,
    questionKey: String
  },
  
  data() {
    return {
      userQuestion: '',
      loading: false,
      response: null,
      error: null,
      retryAvailable: false,
      remainingRequests: 10
    }
  },
  
  computed: {
    quotaExceeded() {
      return this.remainingRequests <= 0;
    }
  },
  
  methods: {
    async requestHelp() {
      this.loading = true;
      this.error = null;
      this.response = null;
      
      try {
        const response = await this.$http.post('/api/help', {
          project_id: this.projectId,
          question_key: this.questionKey,
          user_question: this.userQuestion
        });
        
        this.response = response.data.content;
        this.remainingRequests = response.data.remaining_requests;
        
      } catch (error) {
        this.error = error.response?.data?.error || 'Erreur inconnue';
        this.retryAvailable = error.response?.data?.retry_available || false;
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

6.4 Configuration et limites
Configuration LLM

php 

// config/llm.php
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
        'max_context_length' => 8000 // Caractères max du contexte projet
    ],
    
    'fallback' => [
        'enabled' => true,
        'message' => 'Service d\'aide temporairement indisponible. Consultez la documentation ou contactez l\'équipe.'
    ]
];

Variables d'environnement

bash 

# .env
LLM_PROVIDER=claude
CLAUDE_API_KEY=sk-ant-...
GEMINI_API_KEY=...

LLM_MAX_TOKENS=1000
LLM_TIMEOUT=30
LLM_MAX_REQUESTS_SESSION=10

Intégrations définies :

    ✅ Abstraction LLM avec support Claude/Gemini
    ✅ Système de prompts prédéfinis avec contexte projet complet
    ✅ Gestion d'erreurs avec retry manuel
    ✅ Quotas et limites par session
    ✅ Interface utilisateur pour demandes d'aide

Status : ✅ Validé
Prochaine étape : Spécifications opérationnelles
