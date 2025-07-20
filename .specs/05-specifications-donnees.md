# 📊 SPÉCIFICATIONS DE DONNÉES

## 5.1 Modèle de données

### Entités principales

#### Project (Projet)
```php
// Model Laravel
class Project extends Model {
    protected $fillable = [
        'name',
        'description', 
        'progress',
        'metadata'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'progress' => 'integer'
    ];
}

Attributs :

    id : INTEGER PRIMARY KEY (auto-increment)
    name : VARCHAR(255) NOT NULL (nom du projet)
    description : TEXT NULLABLE (description optionnelle)
    progress : INTEGER DEFAULT 0 (pourcentage 0-100)
    metadata : JSON (infos export, stats, etc.)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP

Contraintes :

    name : Unique par utilisateur (pour MVP = global)
    progress : Entre 0 et 100
    metadata : Structure flexible pour évolutions futures

Answer (Réponse)

php 

class Answer extends Model {
    protected $fillable = [
        'project_id',
        'section_key',
        'question_key', 
        'answer_value',
        'is_not_applicable'
    ];
    
    protected $casts = [
        'answer_value' => 'array', // Pour réponses multiples
        'is_not_applicable' => 'boolean'
    ];
}

Attributs :

    id : INTEGER PRIMARY KEY
    project_id : INTEGER NOT NULL (FK vers projects)
    section_key : VARCHAR(50) NOT NULL (ex: 'context-vision')
    question_key : VARCHAR(100) NOT NULL (ex: 'project-objective')
    answer_value : JSON (valeur(s) de la réponse)
    is_not_applicable : BOOLEAN DEFAULT FALSE
    created_at : TIMESTAMP
    updated_at : TIMESTAMP

Contraintes :

    Index unique : (project_id, section_key, question_key)
    section_key : Valeurs prédéfinies (9 sections)
    answer_value : Peut être string, array, boolean selon type question

Relations

php 

// Project.php
public function answers() {
    return $this->hasMany(Answer::class);
}

public function getAnswerForQuestion($sectionKey, $questionKey) {
    return $this->answers()
        ->where('section_key', $sectionKey)
        ->where('question_key', $questionKey)
        ->first();
}

// Answer.php  
public function project() {
    return $this->belongsTo(Project::class);
}

5.2 Structure des questions (en dur dans le code)
Configuration des sections

php 

// config/questionnaire.php
return [
    'sections' => [
        'context-vision' => [
            'title' => 'Contexte & Vision Projet',
            'description' => 'Définition du projet, utilisateurs cibles, environnement',
            'icon' => '🎪',
            'order' => 1,
            'questions' => [
                'project-objective' => [
                    'text' => 'Objectif principal : Que résout ce projet ? Pour qui ?',
                    'type' => 'textarea',
                    'required' => true,
                    'help_context' => 'Définition claire du problème métier et des bénéficiaires',
                    'placeholder' => 'Ex: Créer une application de gestion de tâches pour les équipes de développement...'
                ],
                'project-scope' => [
                    'text' => 'Périmètre : Que fait-il ? Que ne fait-il PAS ?',
                    'type' => 'textarea',
                    'required' => true,
                    'help_context' => 'Délimitation précise des fonctionnalités incluses et exclues'
                ],
                'time-constraints' => [
                    'text' => 'Contraintes temporelles : MVP en combien de temps ? Versions suivantes ?',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Planning de développement et jalons importants'
                ],
                'target-users' => [
                    'text' => 'Profils utilisateurs : Rôles, compétences techniques, contexte d\'usage',
                    'type' => 'textarea',
                    'required' => true,
                    'help_context' => 'Personas détaillés des utilisateurs finaux'
                ],
                'user-volume' => [
                    'text' => 'Volume : Combien d\'utilisateurs simultanés/jour/mois ?',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Estimation de la charge utilisateur pour dimensionnement'
                ]
            ]
        ],
        
        'functional-specs' => [
            'title' => 'Spécifications Fonctionnelles',
            'description' => 'Fonctionnalités, workflows, règles métier',
            'icon' => '🔧',
            'order' => 2,
            'questions' => [
                'main-features' => [
                    'text' => 'Fonctionnalités principales (une par ligne avec priorité MoSCoW)',
                    'type' => 'textarea',
                    'required' => true,
                    'help_context' => 'Liste des fonctionnalités avec Must/Should/Could/Won\'t have',
                    'placeholder' => 'Must: Authentification utilisateur\nMust: Création de tâches\nShould: Notifications email...'
                ],
                'user-stories' => [
                    'text' => 'User Stories principales (format: En tant que [rôle], je veux [action] pour [bénéfice])',
                    'type' => 'textarea',
                    'required' => true,
                    'help_context' => 'Stories utilisateur avec critères d\'acceptation'
                ],
                'business-rules' => [
                    'text' => 'Règles métier : Validations, calculs, permissions',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Logique métier spécifique et contraintes'
                ],
                'workflows' => [
                    'text' => 'Workflows principaux : Étapes, décisions, validations',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Processus métier et parcours utilisateur détaillés'
                ]
            ]
        ],
        
        'technical-specs' => [
            'title' => 'Spécifications Techniques',
            'description' => 'Architecture, stack, APIs, performance',
            'icon' => '🏗️',
            'order' => 3,
            'questions' => [
                'app-type' => [
                    'text' => 'Type d\'application',
                    'type' => 'radio',
                    'required' => true,
                    'options' => ['Web SPA', 'Web Multi-pages', 'Mobile', 'Desktop', 'API/Microservice', 'Autre'],
                    'help_context' => 'Architecture générale de l\'application'
                ],
                'frontend-stack' => [
                    'text' => 'Stack Frontend : Framework, librairies, outils',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Technologies frontend préférées ou imposées',
                    'placeholder' => 'Ex: Vue.js 3, Tailwind CSS, Vite'
                ],
                'backend-stack' => [
                    'text' => 'Stack Backend : Langage, framework, serveur',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Technologies backend et infrastructure',
                    'placeholder' => 'Ex: Laravel, PHP 8.2, MySQL'
                ],
                'database-type' => [
                    'text' => 'Base de données : Type, SGBD, particularités',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Choix de stockage des données'
                ],
                'external-apis' => [
                    'text' => 'APIs externes : Services tiers, authentification',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Intégrations avec services externes'
                ],
                'performance-targets' => [
                    'text' => 'Cibles de performance : Temps de réponse, charge',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Objectifs de performance mesurables'
                ]
            ]
        ],
        
        'ux-ui-specs' => [
            'title' => 'Spécifications UX/UI',
            'description' => 'Design, ergonomie, parcours utilisateur',
            'icon' => '🎨',
            'order' => 4,
            'questions' => [
                'design-style' => [
                    'text' => 'Style visuel souhaité',
                    'type' => 'radio',
                    'required' => false,
                    'options' => ['Moderne/Minimaliste', 'Classique/Corporate', 'Créatif/Coloré', 'Autre'],
                    'help_context' => 'Direction artistique générale'
                ],
                'color-palette' => [
                    'text' => 'Palette de couleurs : Couleurs principales, secondaires',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Charte graphique et identité visuelle'
                ],
                'responsive-priority' => [
                    'text' => 'Priorité responsive',
                    'type' => 'radio',
                    'required' => false,
                    'options' => ['Desktop first', 'Mobile first', 'Égale'],
                    'help_context' => 'Stratégie d\'adaptation multi-écrans'
                ],
                'navigation-type' => [
                    'text' => 'Type de navigation principale',
                    'type' => 'radio',
                    'required' => false,
                    'options' => ['Menu horizontal', 'Sidebar', 'Navigation par onglets', 'Autre'],
                    'help_context' => 'Structure de navigation de l\'interface'
                ],
                'user-journey' => [
                    'text' => 'Parcours utilisateur principal : Étapes clés',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Flow principal d\'utilisation de l\'application'
                ]
            ]
        ],
        
        'data-specs' => [
            'title' => 'Spécifications de Données',
            'description' => 'Modèle, formats, sources, cycle de vie',
            'icon' => '📊',
            'order' => 5,
            'questions' => [
                'main-entities' => [
                    'text' => 'Entités principales : Objets métier et leurs propriétés',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Modèle de données conceptuel',
                    'placeholder' => 'Ex: Utilisateur (nom, email, rôle)\nTâche (titre, description, statut, assigné)'
                ],
                'data-relationships' => [
                    'text' => 'Relations entre entités : Associations, cardinalités',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Liens entre les objets métier'
                ],
                'data-sources' => [
                    'text' => 'Sources de données : Saisie, import, APIs, calculs',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Origine et alimentation des données'
                ],
                'data-validation' => [
                    'text' => 'Règles de validation : Formats, contraintes, plages',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Contrôles de qualité des données'
                ]
            ]
        ],
        
        'integration-specs' => [
            'title' => 'Spécifications d\'Intégration',
            'description' => 'APIs, systèmes externes, échanges',
            'icon' => '🔗',
            'order' => 6,
            'questions' => [
                'external-systems' => [
                    'text' => 'Systèmes externes : APIs, services, legacy',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Intégrations avec l\'existant'
                ],
                'data-formats' => [
                    'text' => 'Formats d\'échange : JSON, XML, CSV, autres',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Standards de communication'
                ],
                'sync-frequency' => [
                    'text' => 'Fréquence de synchronisation : Temps réel, batch, événementiel',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Stratégie de mise à jour des données'
                ]
            ]
        ],
        
        'operational-specs' => [
            'title' => 'Spécifications Opérationnelles',
            'description' => 'Déploiement, monitoring, maintenance',
            'icon' => '🚀',
            'order' => 7,
            'questions' => [
                'environments' => [
                    'text' => 'Environnements : Développement, test, production',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Infrastructure et configuration par environnement'
                ],
                'deployment-strategy' => [
                    'text' => 'Stratégie de déploiement : CI/CD, rollback, validation',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Processus de mise en production'
                ],
                'monitoring-needs' => [
                    'text' => 'Besoins de monitoring : Métriques, logs, alertes',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Observabilité et surveillance'
                ]
            ]
        ],
        
        'legal-compliance' => [
            'title' => 'Légal & Conformité',
            'description' => 'RGPD, sécurité, standards',
            'icon' => '⚖️',
            'order' => 8,
            'questions' => [
                'data-protection' => [
                    'text' => 'Protection des données : RGPD, données sensibles',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Conformité réglementaire'
                ],
                'security-requirements' => [
                    'text' => 'Exigences de sécurité : Authentification, autorisation, chiffrement',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Mesures de sécurité nécessaires'
                ],
                'compliance-standards' => [
                    'text' => 'Standards à respecter : ISO, SOC, sectoriels',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Certifications et normes applicables'
                ]
            ]
        ],
        
        'quality-specs' => [
            'title' => 'Spécifications de Qualité',
            'description' => 'Tests, métriques, critères d\'acceptation',
            'icon' => '📈',
            'order' => 9,
            'questions' => [
                'testing-strategy' => [
                    'text' => 'Stratégie de tests : Types, couverture, automatisation',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Plan de validation et tests'
                ],
                'quality-metrics' => [
                    'text' => 'Métriques de qualité : Performance, fiabilité, utilisabilité',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Indicateurs de succès mesurables'
                ],
                'acceptance-criteria' => [
                    'text' => 'Critères d\'acceptation globaux : Conditions de livraison',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Définition of Done du projet'
                ]
            ]
        ]
    ]
];

5.3 Formats et validation
Types de questions supportés

php 

// Types de réponses
const QUESTION_TYPES = [
    'text' => 'Texte court (< 255 caractères)',
    'textarea' => 'Texte long (illimité)',
    'radio' => 'Choix unique parmi options',
    'checkbox' => 'Choix multiples',
    'select' => 'Liste déroulante'
];

// Validation par type
class AnswerValidator {
    public function validate($questionType, $value, $options = []) {
        switch($questionType) {
            case 'text':
                return $this->validateText($value, 255);
            case 'textarea':
                return $this->validateText($value, 10000); // Limite raisonnable
            case 'radio':
                return $this->validateChoice($value, $options, false);
            case 'checkbox':
                return $this->validateChoice($value, $options, true);
            default:
                return true;
        }
    }
    
    private function validateText($value, $maxLength) {
        return is_string($value) && strlen($value) <= $maxLength;
    }
    
    private function validateChoice($value, $options, $multiple) {
        if ($multiple) {
            return is_array($value) && 
                   array_diff($value, $options) === [];
        }
        return in_array($value, $options);
    }
}

Structure JSON des réponses

json 

{
  "answer_value": {
    "type": "text|textarea|radio|checkbox",
    "value": "string|array",
    "metadata": {
      "help_requested": true,
      "help_response": "Réponse de l'IA...",
      "last_modified": "2024-01-15T10:30:00Z"
    }
  }
}

5.4 Export des données
Structure des fichiers générés

project-name-specs/
├── 00-index.md                    # Sommaire avec métadonnées
├── 01-contexte-vision.md          # Section 1
├── 02-specifications-fonctionnelles.md
├── 03-specifications-techniques.md
├── 04-specifications-ux-ui.md
├── 05-specifications-donnees.md
├── 06-specifications-integration.md
├── 07-specifications-operationnelles.md
├── 08-legal-conformite.md
├── 09-specifications-qualite.md
└── metadata.json                  # Données techniques

Template de fichier de section

php 

// ExportService.php
public function generateSectionFile($project, $sectionKey) {
    $section = config("questionnaire.sections.{$sectionKey}");
    $answers = $project->answers()->where('section_key', $sectionKey)->get();
    
    $content = "# {$section['icon']} {$section['title']}\n\n";
    $content .= "> {$section['description']}\n\n";
    $content .= "**Projet :** {$project->name}\n";
    $content .= "**Généré le :** " . now()->format('d/m/Y à H:i') . "\n\n";
    $content .= "---\n\n";
    
    foreach ($section['questions'] as $questionKey => $question) {
        $answer = $answers->where('question_key', $questionKey)->first();
        
        $content .= "## {$question['text']}\n\n";
        
        if ($answer && !$answer->is_not_applicable) {
            $value = $answer->answer_value['value'] ?? '';
            if (is_array($value)) {
                $content .= "- " . implode("\n- ", $value) . "\n\n";
            } else {
                $content .= $value . "\n\n";
            }
        } elseif ($answer && $answer->is_not_applicable) {
            $content .= "*Non applicable*\n\n";
        } else {
            $content .= "*Non renseigné*\n\n";
        }
        
        $content .= "---\n\n";
    }
    
    return $content;
}

Métadonnées d'export

json 

{
  "project": {
    "name": "Mon Super Projet",
    "id": 123,
    "created_at": "2024-01-15T09:00:00Z",
    "exported_at": "2024-01-15T14:30:00Z"
  },
  "questionnaire": {
    "version": "1.0",
    "completion_rate": 85,
    "sections_completed": 7,
    "total_sections": 9
  },
  "export": {
    "format": "markdown",
    "files_generated": 10,
    "total_size": "156KB",
    "generator": "Générateur de Specs v1.0"
  },
  "stats": {
    "total_questions": 45,
    "answered_questions": 38,
    "not_applicable": 3,
    "help_requests": 12
  }
}

5.5 Cycle de vie des données
Création

    Nouveau projet : Création avec nom uniquement
    Première réponse : Initialisation des answers au fur et à mesure
    Sauvegarde auto : Toutes les 30 secondes + changement section

Modification

    Update en place : Pas d'historique (MVP)
    Timestamp : updated_at automatique
    Validation : Avant sauvegarde selon type question

Suppression

    Soft delete : Non implémenté (MVP)
    Cascade : Suppression projet → suppression answers
    Confirmation : Dialog de confirmation utilisateur

Archivage/Export

    Export : Génération fichiers sans modification BDD
    Archivage : Non implémenté (MVP)
    Backup : Responsabilité utilisateur (fichiers locaux)

Modèle de données défini :

    ✅ Entités Project et Answer avec relations
    ✅ Questions en configuration PHP (9 sections)
    ✅ Validation par type de question
    ✅ Export multi-fichiers .md avec métadonnées
    ✅ Cycle de vie simple (création/modification/suppression)

Status : ✅ Validé
Prochaine étape : Spécifications d'intégration
