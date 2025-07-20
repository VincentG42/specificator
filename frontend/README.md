# Frontend - Générateur de Spécifications

Ce répertoire contient l'interface utilisateur (frontend) de l'application "Générateur de Spécifications pour Développement Assisté par IA". Il est construit avec Vue.js et utilise Tailwind CSS v4 pour le stylisme.

## Technologies Utilisées

*   **Framework**: Vue.js 3 (Composition API)
*   **Gestion d'état**: Pinia
*   **Routing**: Vue Router 4
*   **Build Tool**: Vite
*   **Styling**: Tailwind CSS v4
*   **HTTP Client**: Axios

## Installation et Démarrage

Suivez ces étapes pour configurer et démarrer l'application frontend :

1.  **Naviguez vers le répertoire `frontend`** :
    ```bash
    cd frontend
    ```

2.  **Installez les dépendances Node.js** :
    ```bash
    npm install
    ```

3.  **Configurez l'URL de l'API backend** :
    Créez un fichier `.env` à la racine du répertoire `frontend` si ce n'est pas déjà fait, et ajoutez la ligne suivante :
    ```dotenv
    VITE_API_BASE_URL=http://localhost:8000
    ```
    Assurez-vous que cette URL correspond à l'adresse de votre serveur backend Laravel.

4.  **Démarrez le serveur de développement frontend** :
    ```bash
    npm run dev
    ```
    L'application sera accessible par défaut sur `http://localhost:5173`.

## Structure du Projet

*   `src/assets/css/tailwind.css`: Fichier CSS principal avec la configuration `@theme` de Tailwind CSS v4.
*   `src/components`: Composants Vue réutilisables (modals, items de question, panneau d'aide LLM).
*   `src/stores`: Stores Pinia pour la gestion d'état (ex: `projectStore.js`).
*   `src/views`: Composants Vue représentant les pages principales de l'application (Accueil, Questionnaire).
*   `src/api.js`: Configuration centralisée d'Axios pour les appels API.

## Theming et Couleurs Personnalisées

Ce projet utilise la nouvelle approche de theming de Tailwind CSS v4 avec la directive `@theme`. Les couleurs personnalisées sont définies comme des variables CSS dans `src/assets/css/tailwind.css` et référencées dans `tailwind.config.js`.

**Palette de Couleurs :**

*   **Neutre**: Tons de gris/beige pour la base.
*   **Accent**: Un cyan vibrant pour les éléments interactifs et les actions principales.
*   **Utilité**: Vert pour le succès, orange pour les avertissements, rouge pour les erreurs.

Pour modifier la palette, éditez les variables CSS dans `src/assets/css/tailwind.css`.