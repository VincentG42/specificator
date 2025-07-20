# 🏗️ SPÉCIFICATIONS TECHNIQUES

## 3.1 Architecture générale

### Type d'application
- **Architecture** : SPA (Single Page Application)
- **Pattern** : MVVM avec Vue.js
- **Décomposition** :
  - Frontend Vue.js (interface utilisateur)
  - Backend Laravel API (gestion données + LLM)
  - Base SQLite (persistance locale)

### Structure des modules

frontend/
├── components/
│ ├── QuestionnaireSection.vue
│ ├── QuestionItem.vue
│ ├── HelpPanel.vue
│ ├── ProjectManager.vue
│ └── ExportManager.vue
├── views/
│ ├── Home.vue
│ ├── Questionnaire.vue
│ └── Export.vue
├── stores/
│ ├── projectStore.js
│ ├── questionnaireStore.js
│ └── helpStore.js
└── utils/
├── exportGenerator.js
└── apiClient.js

backend/
├── app/Http/Controllers/
│ ├── ProjectController.php
│ ├── QuestionnaireController.php
│ └── LLMController.php
├── app/Models/
│ ├── Project.php
│ ├── Section.php
│ └── Answer.php
└── app/Services/
├── LLMService.php
└── ExportService.php


## 3.2 Stack technologique

### Frontend
- **Framework** : Vue.js 3 (Composition API)
- **CSS Framework** : Tailwind CSS
- **State Management** : Pinia
- **Router** : Vue Router 4
- **Build Tool** : Vite
- **HTTP Client** : Axios

### Backend
- **Framework** : Laravel 10
- **Serveur** : PHP 8.2 + Laravel Artisan serve (dev)
- **API** : RESTful API avec Laravel Sanctum (si auth future)

### Base de données
- **SGBD** : SQLite (fichier local)
- **ORM** : Eloquent (Laravel)
- **Migrations** : Laravel migrations
- **Seeding** : Questions prédéfinies via seeders

### Outils de développement
- **Package Manager** : npm (frontend) + Composer (backend)
- **Linting** : ESLint + Prettier (frontend)
- **Testing** : Vitest (frontend) + PHPUnit (backend)

## 3.3 APIs et intégrations

### API interne (Laravel → Vue)

#### Endpoints principaux
```php
// Projets
GET    /api/projects           // Liste des projets
POST   /api/projects           // Créer projet
GET    /api/projects/{id}      // Détails projet
PUT    /api/projects/{id}      // Modifier projet
DELETE /api/projects/{id}      // Supprimer projet

// Questionnaire
GET    /api/sections           // Liste des sections
GET    /api/sections/{id}/questions  // Questions d'une section
POST   /api/answers            // Sauvegarder réponse
PUT    /api/answers/{id}       // Modifier réponse

// Export
POST   /api/projects/{id}/export     // Générer fichiers MD
GET    /api/projects/{id}/download   // Télécharger ZIP

// Aide LLM
POST   /api/help               // Demander aide contextuelle

Format des réponses

json 

{
  "success": true,
  "data": {
    "project": {
      "id": 1,
      "name": "Mon App",
      "progress": 65,
      "created_at": "2024-01-15T10:00:00Z"
    }
  },
  "message": "Projet créé avec succès"
}

API externe (LLM)
Intégration Claude/Gemini

php 

// Service LLM abstrait
interface LLMServiceInterface {
    public function getHelp(string $context, string $question): string;
}

// Implémentations
class ClaudeService implements LLMServiceInterface
class GeminiService implements LLMServiceInterface

Configuration

php 

// .env
LLM_PROVIDER=claude  // ou gemini
CLAUDE_API_KEY=sk-...
GEMINI_API_KEY=...
LLM_MAX_TOKENS=1000
LLM_TIMEOUT=30

3.4 Performance et scalabilité
Temps de réponse cibles

    Chargement initial : < 2 secondes
    Navigation entre sections : < 200ms
    Sauvegarde automatique : < 500ms
    Aide LLM : < 10 secondes
    Export fichiers : < 3 secondes

Optimisations frontend

    Lazy loading : Sections chargées à la demande
    Debouncing : Sauvegarde automatique avec délai 500ms
    Caching : Questions en cache navigateur
    Compression : Assets minifiés par Vite

Optimisations backend

    Database : Index sur project_id, section_id
    Caching : Cache Laravel pour questions statiques
    Queue : Jobs asynchrones pour export volumineux
    Rate limiting : 60 requêtes/minute par IP

Limites techniques

    Projets simultanés : 10 par utilisateur max
    Taille réponses : 10KB par réponse max
    Fichiers export : 50MB max par projet
    Sessions LLM : 10 demandes/session max

3.5 Sécurité
Authentification (MVP = aucune)

    Accès : Libre, pas d'auth pour MVP
    Sessions : Gestion par navigateur uniquement
    Future : Laravel Sanctum pour auth simple

Protection des données

    Stockage local : SQLite avec permissions fichier restrictives
    Transmission : HTTPS en production
    Validation : Sanitisation inputs côté serveur
    LLM : Pas de données sensibles dans prompts

Sécurité API

    CORS : Configuration restrictive
    Rate limiting : Middleware Laravel
    Validation : Form Requests Laravel
    Sanitisation : HTMLPurifier pour textes libres

Vulnérabilités communes

    XSS : Échappement automatique Vue.js + validation serveur
    CSRF : Token CSRF Laravel (si auth future)
    SQL Injection : Eloquent ORM (requêtes préparées)
    File Upload : Validation stricte types MIME

3.6 Base de données
Schéma SQLite

sql 

-- Projets
CREATE TABLE projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    progress INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sections (données statiques)
CREATE TABLE sections (
    id INTEGER PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    order_index INTEGER NOT NULL
);

-- Questions (données statiques)
CREATE TABLE questions (
    id INTEGER PRIMARY KEY,
    section_id INTEGER NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('text', 'textarea', 'radio', 'checkbox') NOT NULL,
    options JSON, -- Pour radio/checkbox
    help_context TEXT, -- Contexte pour LLM
    is_required BOOLEAN DEFAULT FALSE,
    order_index INTEGER NOT NULL,
    FOREIGN KEY (section_id) REFERENCES sections(id)
);

-- Réponses utilisateur
CREATE TABLE answers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    project_id INTEGER NOT NULL,
    question_id INTEGER NOT NULL,
    answer_value TEXT,
    is_not_applicable BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id),
    UNIQUE(project_id, question_id)
);

-- Historique aide LLM (optionnel)
CREATE TABLE help_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    project_id INTEGER NOT NULL,
    question_id INTEGER NOT NULL,
    user_question TEXT,
    llm_response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

Index pour performance

sql 

CREATE INDEX idx_answers_project_id ON answers(project_id);
CREATE INDEX idx_questions_section_id ON questions(section_id);
CREATE INDEX idx_help_requests_project_id ON help_requests(project_id);

3.7 Configuration environnements
Développement

bash 

# Frontend (Vue)
npm run dev  # Port 5173

# Backend (Laravel)
php artisan serve  # Port 8000

# Base de données
database/database.sqlite (auto-créé)

Variables d'environnement

bash 

# Laravel .env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# LLM Configuration
LLM_PROVIDER=claude
CLAUDE_API_KEY=
GEMINI_API_KEY=
LLM_MAX_REQUESTS_PER_SESSION=10

# Frontend .env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME="Générateur de Specs"

Architecture définie :

    ✅ SPA Vue.js + API Laravel
    ✅ SQLite pour persistance locale
    ✅ Intégration LLM abstraite
    ✅ Sécurité de base
    ✅ Performance optimisée

Status : ✅ Validé
Prochaine étape : Spécifications UX/UI
