# Backend - Générateur de Spécifications

Ce répertoire contient le backend de l'application "Générateur de Spécifications pour Développement Assisté par IA". Il est construit avec Laravel et gère la logique métier, la persistance des données et l'intégration avec les modèles de langage (LLM).

## Technologies Utilisées

*   **Framework**: Laravel 10
*   **Langage**: PHP 8.2+
*   **Base de données**: SQLite (fichier local)
*   **API**: RESTful API
*   **LLM Integration**: Abstraction pour Claude/Gemini

## Installation et Démarrage

Suivez ces étapes pour configurer et démarrer le serveur backend :

1.  **Naviguez vers le répertoire `backend`** :
    ```bash
    cd backend
    ```

2.  **Installez les dépendances Composer** :
    ```bash
    composer install
    ```

3.  **Créez le fichier d'environnement et générez la clé d'application** :
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Exécutez les migrations de base de données** (cela créera le fichier `database/database.sqlite` si ce n'est pas déjà fait) :
    ```bash
    php artisan migrate
    ```

5.  **Démarrez le serveur de développement Laravel** :
    ```bash
    php artisan serve
    ```
    Le serveur sera accessible par défaut sur `http://localhost:8000`.

## API Endpoints

Le backend expose une API RESTful. Voici quelques endpoints clés :

*   `GET /api/projects`: Liste tous les projets.
*   `POST /api/projects`: Crée un nouveau projet.
*   `GET /api/projects/{id}`: Récupère les détails d'un projet spécifique.
*   `POST /api/answers`: Sauvegarde une réponse à une question.
*   `PUT /api/answers/{id}`: Met à jour une réponse existante.
*   `POST /api/help`: Demande de l'aide contextuelle à un LLM.

## Configuration LLM

Les clés API pour les services LLM (Claude, Gemini) doivent être configurées dans le fichier `.env` :

```dotenv
LLM_PROVIDER=claude # ou gemini
CLAUDE_API_KEY=votre_cle_api_claude
GEMINI_API_KEY=votre_cle_api_gemini
```

## Structure du Projet

*   `app/Http/Controllers`: Contrôleurs API.
*   `app/Models`: Modèles Eloquent pour la base de données.
*   `app/Services`: Services métier, y compris l'intégration LLM et l'export.
*   `config`: Fichiers de configuration, y compris `questionnaire.php` pour la structure des questions.
*   `database`: Migrations et seeders.
*   `routes/api.php`: Définition des routes API.