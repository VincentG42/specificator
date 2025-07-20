# ⚖️ SPÉCIFICATIONS LÉGALES & CONFORMITÉ

## 8.1 Protection des données

### Données utilisateur (stockage local)
- **Principe** : Stockage local SQLite = pas de transmission de données personnelles
- **Portée** : Données projet uniquement (noms, descriptions, réponses techniques)
- **Responsabilité** : Utilisateur responsable de ses propres données
- **Accès** : Fichier local accessible uniquement par l'utilisateur

### Anonymisation pour LLM
```php
// Service d'anonymisation avant envoi LLM
class DataAnonymizer {
    private array $sensitivePatterns = [
        'email' => '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/',
        'phone' => '/\b(?:\+33|0)[1-9](?:[0-9]{8})\b/',
        'ip' => '/\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b/',
        'url' => '/https?:\/\/[^\s]+/',
        'company_specific' => '/\b(ACME|MonEntreprise|ClientX)\b/i'
    ];
    
    public function anonymizeForLLM(string $content): string {
        $anonymized = $content;
        
        // Remplacer emails
        $anonymized = preg_replace(
            $this->sensitivePatterns['email'], 
            '[EMAIL]', 
            $anonymized
        );
        
        // Remplacer téléphones
        $anonymized = preg_replace(
            $this->sensitivePatterns['phone'], 
            '[TELEPHONE]', 
            $anonymized
        );
        
        // Remplacer IPs
        $anonymized = preg_replace(
            $this->sensitivePatterns['ip'], 
            '[ADRESSE_IP]', 
            $anonymized
        );
        
        // Remplacer URLs complètes
        $anonymized = preg_replace(
            $this->sensitivePatterns['url'], 
            '[URL]', 
            $anonymized
        );
        
        // Remplacer noms d'entreprise spécifiques
        $anonymized = preg_replace(
            $this->sensitivePatterns['company_specific'], 
            '[ENTREPRISE]', 
            $anonymized
        );
        
        return $anonymized;
    }
    
    public function shouldAnonymize(string $content): bool {
        foreach ($this->sensitivePatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        return false;
    }
}

Intégration dans le service LLM

php 

// Modification du LLMService
class LLMService {
    private DataAnonymizer $anonymizer;
    
    public function __construct(DataAnonymizer $anonymizer) {
        $this->anonymizer = $anonymizer;
    }
    
    public function getContextualHelp(Project $project, string $sectionKey, string $questionKey, ?string $userQuestion = null): LLMResponse {
        $context = $this->buildContext($project, $sectionKey, $questionKey);
        
        // Anonymisation avant envoi
        $anonymizedContext = $this->anonymizer->anonymizeForLLM($context);
        $anonymizedQuestion = $userQuestion ? $this->anonymizer->anonymizeForLLM($userQuestion) : null;
        
        // Log si anonymisation appliquée
        if ($this->anonymizer->shouldAnonymize($context) || 
            ($userQuestion && $this->anonymizer->shouldAnonymize($userQuestion))) {
            Log::info('Data anonymized before LLM request', [
                'project_id' => $project->id,
                'section' => $sectionKey,
                'question' => $questionKey
            ]);
        }
        
        return $this->provider->getHelp($anonymizedContext, $anonymizedQuestion ?? "Aide-moi à répondre à cette question");
    }
}

8.2 Sécurité des données
Bonnes pratiques implémentées

    Validation inputs : Sanitisation côté serveur (Laravel Form Requests)
    Pas de données sensibles : Pas de mots de passe, données bancaires, etc.
    Logs sécurisés : Pas de données utilisateur dans les logs
    Transmission chiffrée : HTTPS pour API LLM

Avertissements utilisateur

vue 

<!-- Composant d'avertissement -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
  <div class="flex items-start space-x-3">
    <span class="text-blue-500 text-xl">🔒</span>
    <div>
      <h4 class="font-medium text-blue-900 mb-2">
        Protection de vos données
      </h4>
      <ul class="text-sm text-blue-800 space-y-1">
        <li>• Vos données sont stockées localement sur votre machine</li>
        <li>• Les demandes d'aide sont anonymisées avant envoi au LLM</li>
        <li>• Évitez de saisir des informations confidentielles (mots de passe, données clients)</li>
        <li>• Vous êtes responsable de la sauvegarde de vos projets</li>
      </ul>
    </div>
  </div>
</div>

8.3 Propriété intellectuelle
Code source

    Statut : Propriété de l'auteur (toi)
    Licence : Non définie pour le MVP
    Usage : Interne équipe pour le moment

Spécifications générées

    Propriété : Appartiennent à l'utilisateur/entreprise
    Responsabilité : L'utilisateur est responsable du contenu généré
    Usage LLM : Pas de droits revendiqués par les fournisseurs LLM sur les specs

Avertissement légal

markdown 

<!-- Footer de l'application -->
**Avertissement :** Cet outil génère des spécifications basées sur vos réponses. 
Vous êtes responsable de la validation et de l'exactitude du contenu produit. 
Les spécifications générées vous appartiennent entièrement.

8.4 Conformité technique
Standards respectés

    Sécurité web : Validation OWASP de base
    Accessibilité : Bonnes pratiques WCAG (contraste, navigation clavier)
    Performance : Pas de contraintes réglementaires spécifiques

Audit de conformité (future)

php 

// Commande pour audit basique
class ComplianceAudit extends Command {
    protected $signature = 'audit:compliance';
    
    public function handle() {
        $this->info('🔍 Audit de conformité');
        
        // Vérifier anonymisation
        $this->checkAnonymization();
        
        // Vérifier logs
        $this->checkLogs();
        
        // Vérifier stockage
        $this->checkStorage();
        
        $this->info('✅ Audit terminé');
    }
    
    private function checkAnonymization() {
        // Tester les patterns d'anonymisation
        $anonymizer = app(DataAnonymizer::class);
        $testData = "Contact: john@example.com, Tel: 0123456789";
        $result = $anonymizer->anonymizeForLLM($testData);
        
        if (str_contains($result, '@') || preg_match('/\d{10}/', $result)) {
            $this->error('❌ Anonymisation défaillante');
        } else {
            $this->info('✅ Anonymisation fonctionnelle');
        }
    }
}

Conformité définie :

    ✅ Protection données avec stockage local
    ✅ Anonymisation automatique pour LLM
    ✅ Bonnes pratiques sécurité
    ✅ Avertissements utilisateur
    ✅ Propriété intellectuelle clarifiée

Status : ✅ Validé


---

## 📄 `09-specifications-qualite.md`

```markdown
# 📈 SPÉCIFICATIONS DE QUALITÉ

## 9.1 Stratégie de tests

### Tests automatisés backend (Laravel)
```php
// Tests unitaires - Questions Analytics
class AnalyticsServiceTest extends TestCase {
    public function test_track_answer_given_updates_analytics() {
        $project = Project::factory()->create();
        $service = app(AnalyticsService::class);
        
        $service->trackAnswerGiven($project, 'context-vision', 'project-objective', 'Test answer', false);
        
        $this->assertDatabaseHas('question_analytics', [
            'section_key' => 'context-vision',
            'question_key' => 'project-objective',
            'answer_count' => 1,
            'not_applicable_count' => 0
        ]);
    }
    
    public function test_anonymization_removes_sensitive_data() {
        $anonymizer = app(DataAnonymizer::class);
        $sensitiveData = "Contact john@example.com ou 0123456789";
        
        $result = $anonymizer->anonymizeForLLM($sensitiveData);
        
        $this->assertEquals("Contact [EMAIL] ou [TELEPHONE]", $result);
    }
}

// Tests d'intégration - LLM Service
class LLMServiceTest extends TestCase {
    public function test_llm_service_handles_api_failure() {
        // Mock du service LLM pour simuler échec
        $this->mock(ClaudeService::class, function ($mock) {
            $mock->shouldReceive('getHelp')
                 ->andReturn(new LLMResponse(false, null, 'API Error'));
        });
        
        $project = Project::factory()->create();
        $service = app(LLMService::class);
        
        $response = $service->getContextualHelp($project, 'context-vision', 'project-objective');
        
        $this->assertFalse($response->success);
        $this->assertNotNull($response->error);
    }
}

// Tests fonctionnels - Export
class ExportServiceTest extends TestCase {
    public function test_export_generates_all_section_files() {
        $project = Project::factory()->create(['name' => 'Test Project']);
        
        // Créer quelques réponses
        Answer::factory()->create([
            'project_id' => $project->id,
            'section_key' => 'context-vision',
            'question_key' => 'project-objective',
            'answer_value' => ['value' => 'Test objective']
        ]);
        
        $service = app(ExportService::class);
        $files = $service->generateAllFiles($project);
        
        $this->assertCount(10, $files); // 9 sections + index
        $this->assertArrayHasKey('00-index.md', $files);
        $this->assertArrayHasKey('01-contexte-vision.md', $files);
        $this->assertStringContainsString('Test objective', $files['01-contexte-vision.md']);
    }
}

Tests frontend (Vitest + Vue Test Utils)

javascript 

// tests/components/QuestionItem.test.js
import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import QuestionItem from '@/components/QuestionItem.vue'

describe('QuestionItem', () => {
  const mockQuestion = {
    text: 'Quel est l\'objectif du projet ?',
    type: 'textarea',
    required: true,
    help_context: 'Définition claire du problème'
  }
  
  it('renders question text correctly', () => {
    const wrapper = mount(QuestionItem, {
      props: { question: mockQuestion, answer: null }
    })
    
    expect(wrapper.text()).toContain('Quel est l\'objectif du projet ?')
  })
  
  it('emits update event when answer changes', async () => {
    const wrapper = mount(QuestionItem, {
      props: { question: mockQuestion, answer: null }
    })
    
    const textarea = wrapper.find('textarea')
    await textarea.setValue('Mon objectif de projet')
    
    expect(wrapper.emitted('update')).toBeTruthy()
    expect(wrapper.emitted('update')[0][0]).toBe('Mon objectif de projet')
  })
  
  it('shows help button and emits help event', async () => {
    const wrapper = mount(QuestionItem, {
      props: { question: mockQuestion, answer: null }
    })
    
    const helpButton = wrapper.find('[data-test="help-button"]')
    expect(helpButton.exists()).toBe(true)
    
    await helpButton.trigger('click')
    expect(wrapper.emitted('help')).toBeTruthy()
  })
})

// tests/stores/projectStore.test.js
import { describe, it, expect, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useProjectStore } from '@/stores/projectStore'

describe('Project Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })
  
  it('creates new project correctly', () => {
    const store = useProjectStore()
    
    store.createProject('Test Project', 'Description test')
    
    expect(store.currentProject).toBeDefined()
    expect(store.currentProject.name).toBe('Test Project')
    expect(store.currentProject.description).toBe('Description test')
  })
  
  it('calculates progress correctly', () => {
    const store = useProjectStore()
    store.createProject('Test')
    
    // Simuler quelques réponses
    store.updateAnswer('context-vision', 'project-objective', 'Test answer')
    store.updateAnswer('context-vision', 'project-scope', 'Test scope')
    
    const progress = store.calculateProgress()
    expect(progress).toBeGreaterThan(0)
    expect(progress).toBeLessThanOrEqual(100)
  })
})

Tests end-to-end (Cypress - optionnel)

javascript 

// cypress/e2e/questionnaire-flow.cy.js
describe('Questionnaire Flow', () => {
  it('completes full questionnaire workflow', () => {
    cy.visit('/')
    
    // Créer nouveau projet
    cy.get('[data-test="new-project-btn"]').click()
    cy.get('[data-test="project-name"]').type('Mon Projet E2E')
    cy.get('[data-test="create-project"]').click()
    
    // Remplir première section
    cy.get('[data-test="question-project-objective"]').type('Objectif de test E2E')
    cy.get('[data-test="question-project-scope"]').type('Scope de test')
    
    // Passer à la section suivante
    cy.get('[data-test="next-section"]').click()
    
    // Vérifier progression
    cy.get('[data-test="progress-bar"]').should('contain', '%')
    
    // Tester aide LLM (si API disponible)
    cy.get('[data-test="help-button"]').first().click()
    cy.get('[data-test="help-panel"]').should('be.visible')
    
    // Aller jusqu'à l'export
    // ... navigation sections
    
    // Export final
    cy.get('[data-test="export-btn"]').click()
    cy.get('[data-test="download-link"]').should('be.visible')
  })
})

9.2 Métriques de qualité
Métriques techniques

php 

// Commande pour métriques qualité
class QualityMetrics extends Command {
    protected $signature = 'metrics:quality';
    
    public function handle() {
        $this->info('📊 Métriques de qualité');
        
        $metrics = [
            'code_coverage' => $this->getCodeCoverage(),
            'response_times' => $this->getResponseTimes(),
            'error_rates' => $this->getErrorRates(),
            'user_satisfaction' => $this->getUserSatisfaction()
        ];
        
        $this->table(
            ['Métrique', 'Valeur', 'Cible', 'Status'],
            [
                ['Couverture tests', $metrics['code_coverage'] . '%', '> 80%', $metrics['code_coverage'] > 80 ? '✅' : '❌'],
                ['Temps réponse API', $metrics['response_times'] . 'ms', '< 500ms', $metrics['response_times'] < 500 ? '✅' : '❌'],
                ['Taux d\'erreur', $metrics['error_rates'] . '%', '< 1%', $metrics['error_rates'] < 1 ? '✅' : '❌'],
                ['Satisfaction', $metrics['user_satisfaction'] . '/5', '> 4.0', $metrics['user_satisfaction'] > 4 ? '✅' : '❌']
            ]
        );
    }
    
    private function getCodeCoverage(): float {
        // Intégration avec PHPUnit coverage
        $coverageFile = base_path('coverage/coverage.xml');
        if (!file_exists($coverageFile)) return 0;
        
        $xml = simplexml_load_file($coverageFile);
        $metrics = $xml->project->metrics;
        
        return round(($metrics['coveredstatements'] / $metrics['statements']) * 100, 1);
    }
    
    private function getResponseTimes(): float {
        // Moyenne des temps de réponse des 24 dernières heures
        return DB::table('usage_events')
            ->where('created_at', '>=', now()->subDay())
            ->where('event_data->response_time', '>', 0)
            ->avg('event_data->response_time') ?? 0;
    }
    
    private function getErrorRates(): float {
        $total = DB::table('usage_events')->where('created_at', '>=', now()->subDay())->count();
        $errors = DB::table('usage_events')
            ->where('created_at', '>=', now()->subDay())
            ->where('event_type', 'error')
            ->count();
            
        return $total > 0 ? round(($errors / $total) * 100, 2) : 0;
    }
    
    private function getUserSatisfaction(): float {
        // Basé sur les métriques d'usage (taux de completion, etc.)
        $completionRate = $this->getCompletionRate();
        $helpSuccessRate = $this->getHelpSuccessRate();
        
        // Score composite simple
        return round(($completionRate + $helpSuccessRate) / 2, 1);
    }
}

Monitoring en temps réel

php 

// Middleware pour tracking performance
class PerformanceMiddleware {
    public function handle($request, Closure $next) {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $duration = (microtime(true) - $startTime) * 1000; // en ms
        
        // Log performance si > seuil
        if ($duration > 1000) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'duration_ms' => round($duration, 2),
                'memory_mb' => round(memory_get_peak_usage() / 1024 / 1024, 2)
            ]);
        }
        
        // Ajouter header performance
        $response->headers->set('X-Response-Time', round($duration, 2) . 'ms');
        
        return $response;
    }
}

9.3 Critères d'acceptation globaux
Critères fonctionnels

    ✅ Questionnaire complet : 9 sections avec toutes les questions définies
    ✅ Sauvegarde automatique : Aucune perte de données utilisateur
    ✅ Export multi-fichiers : Génération correcte des .md par section
    ✅ Aide LLM : Réponses contextuelles pertinentes (quand API disponible)
    ✅ Navigation fluide : Passage entre sections sans blocage

Critères techniques

    ✅ Performance : Temps de réponse < 500ms pour 95% des requêtes
    ✅ Fiabilité : Uptime > 99% (pour usage local)
    ✅ Compatibilité : Fonctionne sur navigateurs modernes (Chrome, Firefox, Safari)
    ✅ Sécurité : Anonymisation automatique des données sensibles
    ✅ Scalabilité : Support jusqu'à 100 projets par utilisateur

Critères qualité

    ✅ Tests : Couverture > 80% du code critique
    ✅ Documentation : README complet + commentaires code
    ✅ Maintenabilité : Code structuré et modulaire
    ✅ Utilisabilité : Interface intuitive sans formation
    ✅ Analytics : Métriques d'amélioration disponibles

Critères de livraison MVP

markdown 

## Definition of Done - MVP

### Fonctionnalités core ✅
- [ ] Création/gestion projets
- [ ] Questionnaire 9 sections complet
- [ ] Sauvegarde automatique
- [ ] Export fichiers .md
- [ ] Aide LLM (avec fallback si indisponible)

### Qualité technique ✅
- [ ] Tests unitaires > 70%
- [ ] Anonymisation fonctionnelle
- [ ] Performance acceptable (< 2s chargement)
- [ ] Gestion d'erreurs robuste
- [ ] Analytics de base implémentées

### Documentation ✅
- [ ] README installation/usage
- [ ] Spécifications complètes (ces 9 fichiers)
- [ ] Commentaires code critique
- [ ] Guide utilisateur basique

### Validation utilisateur ✅
- [ ] Test avec 2-3 collègues
- [ ] Génération specs réelles fonctionnelle
- [ ] Feedback positif sur utilisabilité
- [ ] Amélioration identifiée via analytics

9.4 Plan de tests de validation
Phase 1 : Tests développeur

    Tests automatisés : Lancer suite complète
    Tests manuels : Parcours utilisateur complet
    Tests performance : Charge avec 10 projets simultanés
    Tests sécurité : Vérification anonymisation

Phase 2 : Tests utilisateurs internes

    Onboarding : 3 collègues testent sans formation
    Projets réels : Génération specs pour vrais projets
    Feedback structuré : Questionnaire satisfaction
    Analytics : Analyse des métriques d'usage

Phase 3 : Amélioration continue

    Rapport analytics : Identification points faibles
    Priorisation : Roadmap améliorations
    Itération : Cycles courts d'amélioration
    Validation : Tests régression après modifications

Qualité définie :

    ✅ Stratégie de tests complète (unit, integration, e2e)
    ✅ Métriques techniques et business
    ✅ Critères d'acceptation MVP
    ✅ Plan de validation utilisateur
    ✅ Monitoring performance et erreurs
    ✅ Process d'amélioration continue

Status : ✅ Validé
