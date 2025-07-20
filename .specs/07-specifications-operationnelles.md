# 🚀 SPÉCIFICATIONS OPÉRATIONNELLES

## 7.1 Environnements

### Développement local
```bash
# Installation simple
git clone [repo]
cd spec-generator

# Backend Laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve  # Port 8000

# Frontend Vue
cd frontend
npm install
npm run dev  # Port 5173

# Base de données
database/database.sqlite (auto-créé)

Configuration minimale

bash 

# .env requis
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# LLM (optionnel pour dev)
LLM_PROVIDER=claude
CLAUDE_API_KEY=  # Vide = mode dégradé

7.2 Analytics et amélioration continue
Métriques de pertinence des questions

php 

// Migration pour analytics
Schema::create('question_analytics', function (Blueprint $table) {
    $table->id();
    $table->string('section_key');
    $table->string('question_key');
    $table->integer('answer_count')->default(0);
    $table->integer('not_applicable_count')->default(0);
    $table->integer('help_request_count')->default(0);
    $table->decimal('avg_answer_length', 8, 2)->nullable();
    $table->json('feedback_scores')->nullable(); // Future: notation pertinence
    $table->timestamps();
    
    $table->unique(['section_key', 'question_key']);
});

// Tracking des interactions
Schema::create('usage_events', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('project_id');
    $table->string('event_type'); // answer_given, help_requested, section_completed, etc.
    $table->string('section_key');
    $table->string('question_key')->nullable();
    $table->json('event_data')->nullable();
    $table->timestamp('created_at');
    
    $table->index(['event_type', 'created_at']);
    $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
});

Service d'analytics

php 

// app/Services/AnalyticsService.php
class AnalyticsService {
    
    public function trackAnswerGiven(Project $project, string $sectionKey, string $questionKey, $answerValue, bool $isNotApplicable) {
        // Event tracking
        DB::table('usage_events')->insert([
            'project_id' => $project->id,
            'event_type' => $isNotApplicable ? 'answer_not_applicable' : 'answer_given',
            'section_key' => $sectionKey,
            'question_key' => $questionKey,
            'event_data' => json_encode([
                'answer_length' => is_string($answerValue) ? strlen($answerValue) : null,
                'answer_type' => gettype($answerValue)
            ]),
            'created_at' => now()
        ]);
        
        // Agrégation pour analytics
        $this->updateQuestionAnalytics($sectionKey, $questionKey, $answerValue, $isNotApplicable);
    }
    
    public function trackHelpRequested(Project $project, string $sectionKey, string $questionKey, ?string $userQuestion) {
        DB::table('usage_events')->insert([
            'project_id' => $project->id,
            'event_type' => 'help_requested',
            'section_key' => $sectionKey,
            'question_key' => $questionKey,
            'event_data' => json_encode([
                'has_custom_question' => !empty($userQuestion),
                'question_length' => strlen($userQuestion ?? '')
            ]),
            'created_at' => now()
        ]);
        
        // Incrémenter compteur aide
        DB::table('question_analytics')
            ->where('section_key', $sectionKey)
            ->where('question_key', $questionKey)
            ->increment('help_request_count');
    }
    
    public function trackSectionCompleted(Project $project, string $sectionKey) {
        DB::table('usage_events')->insert([
            'project_id' => $project->id,
            'event_type' => 'section_completed',
            'section_key' => $sectionKey,
            'created_at' => now()
        ]);
    }
    
    public function trackProjectExported(Project $project, array $exportStats) {
        DB::table('usage_events')->insert([
            'project_id' => $project->id,
            'event_type' => 'project_exported',
            'section_key' => 'all',
            'event_data' => json_encode($exportStats),
            'created_at' => now()
        ]);
    }
    
    private function updateQuestionAnalytics(string $sectionKey, string $questionKey, $answerValue, bool $isNotApplicable) {
        $analytics = DB::table('question_analytics')
            ->where('section_key', $sectionKey)
            ->where('question_key', $questionKey)
            ->first();
            
        if (!$analytics) {
            DB::table('question_analytics')->insert([
                'section_key' => $sectionKey,
                'question_key' => $questionKey,
                'answer_count' => $isNotApplicable ? 0 : 1,
                'not_applicable_count' => $isNotApplicable ? 1 : 0,
                'avg_answer_length' => is_string($answerValue) ? strlen($answerValue) : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $newAnswerCount = $analytics->answer_count + ($isNotApplicable ? 0 : 1);
            $newNACount = $analytics->not_applicable_count + ($isNotApplicable ? 1 : 0);
            
            // Calcul moyenne longueur réponses
            $newAvgLength = null;
            if (!$isNotApplicable && is_string($answerValue)) {
                $currentTotal = ($analytics->avg_answer_length ?? 0) * $analytics->answer_count;
                $newAvgLength = ($currentTotal + strlen($answerValue)) / $newAnswerCount;
            }
            
            DB::table('question_analytics')
                ->where('section_key', $sectionKey)
                ->where('question_key', $questionKey)
                ->update([
                    'answer_count' => $newAnswerCount,
                    'not_applicable_count' => $newNACount,
                    'avg_answer_length' => $newAvgLength ?? $analytics->avg_answer_length,
                    'updated_at' => now()
                ]);
        }
    }
}

Rapports d'amélioration

php 

// app/Http/Controllers/AnalyticsController.php
class AnalyticsController extends Controller {
    
    public function getImprovementReport() {
        $report = [
            'questions_analysis' => $this->analyzeQuestions(),
            'sections_analysis' => $this->analyzeSections(),
            'usage_patterns' => $this->getUsagePatterns(),
            'recommendations' => $this->generateRecommendations()
        ];
        
        return response()->json($report);
    }
    
    private function analyzeQuestions() {
        $analytics = DB::table('question_analytics')
            ->select('*')
            ->get()
            ->map(function ($item) {
                $totalInteractions = $item->answer_count + $item->not_applicable_count;
                $naRate = $totalInteractions > 0 ? ($item->not_applicable_count / $totalInteractions) * 100 : 0;
                $helpRate = $item->answer_count > 0 ? ($item->help_request_count / $item->answer_count) * 100 : 0;
                
                return [
                    'section_key' => $item->section_key,
                    'question_key' => $item->question_key,
                    'total_interactions' => $totalInteractions,
                    'na_rate' => round($naRate, 1),
                    'help_rate' => round($helpRate, 1),
                    'avg_answer_length' => $item->avg_answer_length,
                    'relevance_score' => $this->calculateRelevanceScore($naRate, $helpRate, $totalInteractions)
                ];
            });
            
        return [
            'most_problematic' => $analytics->sortByDesc('help_rate')->take(10)->values(),
            'least_relevant' => $analytics->sortByDesc('na_rate')->take(10)->values(),
            'most_relevant' => $analytics->sortBy('na_rate')->take(10)->values(),
            'underused' => $analytics->sortBy('total_interactions')->take(10)->values()
        ];
    }
    
    private function analyzeSections() {
        $sectionStats = DB::table('usage_events')
            ->select('section_key')
            ->selectRaw('COUNT(*) as total_events')
            ->selectRaw('COUNT(DISTINCT project_id) as unique_projects')
            ->selectRaw('AVG(CASE WHEN event_type = "section_completed" THEN 1 ELSE 0 END) as completion_rate')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('section_key')
            ->get();
            
        return $sectionStats->map(function ($section) {
            return [
                'section_key' => $section->section_key,
                'engagement_score' => $section->total_events / max($section->unique_projects, 1),
                'completion_rate' => round($section->completion_rate * 100, 1),
                'unique_projects' => $section->unique_projects
            ];
        })->sortByDesc('engagement_score')->values();
    }
    
    private function getUsagePatterns() {
        return [
            'peak_usage_hours' => $this->getPeakUsageHours(),
            'average_session_duration' => $this->getAverageSessionDuration(),
            'most_exported_sections' => $this->getMostExportedSections(),
            'abandonment_points' => $this->getAbandonmentPoints()
        ];
    }
    
    private function generateRecommendations() {
        $recommendations = [];
        
        // Questions avec taux N/A élevé
        $highNAQuestions = DB::table('question_analytics')
            ->selectRaw('section_key, question_key, (not_applicable_count * 100.0 / (answer_count + not_applicable_count)) as na_rate')
            ->havingRaw('(answer_count + not_applicable_count) >= 5 AND na_rate > 50')
            ->get();
            
        if ($highNAQuestions->count() > 0) {
            $recommendations[] = [
                'type' => 'high_na_rate',
                'priority' => 'high',
                'title' => 'Questions peu pertinentes détectées',
                'description' => 'Certaines questions ont un taux "Non applicable" > 50%',
                'affected_questions' => $highNAQuestions->count(),
                'action' => 'Revoir la formulation ou rendre optionnelles'
            ];
        }
        
        // Questions avec beaucoup de demandes d'aide
        $highHelpQuestions = DB::table('question_analytics')
            ->selectRaw('section_key, question_key, (help_request_count * 100.0 / answer_count) as help_rate')
            ->havingRaw('answer_count >= 3 AND help_rate > 40')
            ->get();
            
        if ($highHelpQuestions->count() > 0) {
            $recommendations[] = [
                'type' => 'high_help_rate',
                'priority' => 'medium',
                'title' => 'Questions difficiles à comprendre',
                'description' => 'Certaines questions génèrent beaucoup de demandes d\'aide',
                'affected_questions' => $highHelpQuestions->count(),
                'action' => 'Améliorer la formulation ou ajouter des exemples'
            ];
        }
        
        // Sections peu utilisées
        $underusedSections = DB::table('usage_events')
            ->select('section_key')
            ->selectRaw('COUNT(DISTINCT project_id) as unique_projects')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('section_key')
            ->havingRaw('unique_projects < 3')
            ->get();
            
        if ($underusedSections->count() > 0) {
            $recommendations[] = [
                'type' => 'underused_sections',
                'priority' => 'low',
                'title' => 'Sections peu utilisées',
                'description' => 'Certaines sections sont rarement complétées',
                'affected_sections' => $underusedSections->count(),
                'action' => 'Évaluer la pertinence ou améliorer la présentation'
            ];
        }
        
        return $recommendations;
    }
    
    private function calculateRelevanceScore($naRate, $helpRate, $totalInteractions) {
        // Score de pertinence : plus c'est bas, moins c'est pertinent
        $baseScore = 100;
        $baseScore -= $naRate; // Pénalité pour taux N/A élevé
        $baseScore -= ($helpRate * 0.5); // Pénalité modérée pour aide fréquente
        $baseScore += min($totalInteractions * 2, 20); // Bonus pour usage fréquent
        
        return max(0, round($baseScore, 1));
    }
}

Interface d'analytics (Vue)

vue 

<!-- components/AnalyticsReport.vue -->
<template>
  <div class="analytics-report">
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-4">
        📊 Rapport d'amélioration du questionnaire
      </h2>
      <p class="text-gray-600">
        Analyse de l'usage pour identifier les points d'amélioration
      </p>
    </div>
    
    <!-- Recommandations prioritaires -->
    <div v-if="report.recommendations?.length" class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">
        🎯 Recommandations d'amélioration
      </h3>
      <div class="space-y-4">
        <div v-for="rec in report.recommendations" :key="rec.type"
             :class="[
               'p-4 rounded-lg border-l-4',
               rec.priority === 'high' ? 'bg-red-50 border-red-400' :
               rec.priority === 'medium' ? 'bg-amber-50 border-amber-400' :
               'bg-blue-50 border-blue-400'
             ]">
          <div class="flex justify-between items-start">
            <div>
              <h4 class="font-medium text-gray-900">{{ rec.title }}</h4>
              <p class="text-sm text-gray-600 mt-1">{{ rec.description }}</p>
              <p class="text-sm font-medium mt-2">
                Action recommandée : {{ rec.action }}
              </p>
            </div>
            <span :class="[
              'px-2 py-1 text-xs font-medium rounded',
              rec.priority === 'high' ? 'bg-red-100 text-red-800' :
              rec.priority === 'medium' ? 'bg-amber-100 text-amber-800' :
              'bg-blue-100 text-blue-800'
            ]">
              {{ rec.priority }}
            </span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Questions les plus problématiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <div class="bg-white rounded-lg p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          ❓ Questions générant le plus d'aide
        </h3>
        <div class="space-y-3">
          <div v-for="q in report.questions_analysis?.most_problematic?.slice(0, 5)" 
               :key="`${q.section_key}-${q.question_key}`"
               class="flex justify-between items-center p-3 bg-gray-50 rounded">
            <div>
              <p class="text-sm font-medium text-gray-900">
                {{ getSectionTitle(q.section_key) }}
              </p>
              <p class="text-xs text-gray-600">
                {{ getQuestionText(q.section_key, q.question_key) }}
              </p>
            </div>
            <span class="text-sm font-bold text-red-600">
              {{ q.help_rate }}% aide
            </span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          🚫 Questions souvent "Non applicable"
        </h3>
        <div class="space-y-3">
          <div v-for="q in report.questions_analysis?.least_relevant?.slice(0, 5)" 
               :key="`${q.section_key}-${q.question_key}`"
               class="flex justify-between items-center p-3 bg-gray-50 rounded">
            <div>
              <p class="text-sm font-medium text-gray-900">
                {{ getSectionTitle(q.section_key) }}
              </p>
              <p class="text-xs text-gray-600">
                {{ getQuestionText(q.section_key, q.question_key) }}
              </p>
            </div>
            <span class="text-sm font-bold text-amber-600">
              {{ q.na_rate }}% N/A
            </span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Engagement par section -->
    <div class="bg-white rounded-lg p-6 border border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">
        📈 Engagement par section
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="section in report.sections_analysis?.slice(0, 9)" 
             :key="section.section_key"
             class="p-4 bg-gray-50 rounded-lg">
          <h4 class="font-medium text-gray-900 mb-2">
            {{ getSectionTitle(section.section_key) }}
          </h4>
          <div class="space-y-1 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Engagement:</span>
              <span class="font-medium">{{ section.engagement_score?.toFixed(1) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Completion:</span>
              <span class="font-medium">{{ section.completion_rate }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Projets:</span>
              <span class="font-medium">{{ section.unique_projects }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuestionnaireStore } from '@/stores/questionnaireStore'

const questionnaireStore = useQuestionnaireStore()
const report = ref({})

onMounted(async () => {
  try {
    const response = await axios.get('/api/analytics/improvement-report')
    report.value = response.data
  } catch (error) {
    console.error('Erreur chargement analytics:', error)
  }
})

const getSectionTitle = (sectionKey) => {
  return questionnaireStore.sections[sectionKey]?.title || sectionKey
}

const getQuestionText = (sectionKey, questionKey) => {
  const question = questionnaireStore.sections[sectionKey]?.questions[questionKey]
  return question?.text?.substring(0, 60) + '...' || questionKey
}
</script>

7.3 Déploiement et maintenance
Déploiement simple

bash 

# Script de déploiement local/serveur
#!/bin/bash
# deploy.sh

echo "🚀 Déploiement Générateur de Specs"

# Backend
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Frontend
npm ci
npm run build

# Base de données
php artisan migrate --force

# Permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Déploiement terminé"

Monitoring basique

php 

// Logs structurés
Log::channel('analytics')->info('Question answered', [
    'project_id' => $project->id,
    'section' => $sectionKey,
    'question' => $questionKey,
    'answer_length' => strlen($answerValue),
    'help_requested' => false
]);

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'database' => DB::connection()->getPdo() ? 'connected' : 'error',
        'llm_service' => app(LLMService::class)->isAvailable(),
        'version' => config('app.version', '1.0.0')
    ]);
});

Sauvegarde automatique

bash 

# Cron job pour sauvegarde quotidienne
0 2 * * * cd /path/to/app && php artisan backup:database

php 

// Commande artisan personnalisée
class BackupDatabase extends Command {
    protected $signature = 'backup:database';
    
    public function handle() {
        $backupPath = storage_path('backups');
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sqlite';
        
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }
        
        File::copy(
            database_path('database.sqlite'),
            $backupPath . '/' . $filename
        );
        
        $this->info("Sauvegarde créée: {$filename}");
        
        // Nettoyer les anciennes sauvegardes (garder 30 jours)
        $this->cleanOldBackups($backupPath);
    }
}

Opérationnel défini avec focus analytics :

    ✅ Installation et déploiement simples
    ✅ Tracking complet des interactions utilisateur
    ✅ Analytics de pertinence des questions
    ✅ Rapports d'amélioration automatiques
    ✅ Recommandations basées sur l'usage
    ✅ Interface d'analytics pour visualisation
    ✅ Monitoring et sauvegarde de base

Status : ✅ Validé
Prochaine étape : Spécifications légales & conformité
