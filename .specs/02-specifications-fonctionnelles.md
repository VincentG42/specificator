# 🔧 SPÉCIFICATIONS FONCTIONNELLES

## 2.1 Fonctionnalités principales

### F1 - Questionnaire guidé interactif
**User Story :** "En tant que développeur, je veux répondre à un questionnaire structuré pour générer des spécifications complètes sans oublier d'éléments critiques"

**Critères d'acceptation :**
- Navigation séquentielle par sections (9 sections du guide)
- Possibilité de revenir en arrière sans perdre les données
- Sauvegarde automatique à chaque réponse
- Indicateur de progression (section X/9)
- **Priorité :** Must-have

### F2 - Types de réponses flexibles
**User Story :** "En tant qu'utilisateur, je veux pouvoir répondre de différentes manières selon le type de question"

**Critères d'acceptation :**
- Texte libre (court et long)
- Cases à cocher multiples
- Boutons radio (choix unique)
- Option "Non applicable" sur toutes les questions
- Option "Besoin d'aide" sur toutes les questions
- **Priorité :** Must-have

### F3 - Aide contextuelle via LLM
**User Story :** "En tant que développeur junior, je veux obtenir de l'aide sur les questions complexes pour mieux comprendre ce qui est attendu"

**Critères d'acceptation :**
- Section dédiée "Aide" accessible depuis chaque question
- Prompt système adapté au contexte de la question
- Réponse de l'IA intégrée dans l'interface
- Possibilité de reformuler la question à l'IA
- **Priorité :** Should-have

### F4 - Gestion de projets simple
**User Story :** "En tant qu'utilisateur, je veux sauvegarder mes projets en cours et les reprendre plus tard"

**Critères d'acceptation :**
- Création d'un nouveau projet avec nom
- Sauvegarde automatique des réponses
- Liste des projets existants
- Chargement d'un projet existant
- Suppression de projets
- **Priorité :** Must-have

### F5 - Export multi-fichiers
**User Story :** "En tant que développeur, je veux exporter mes spécifications sous forme de fichiers .md structurés pour les utiliser avec mes outils IA"

**Critères d'acceptation :**
- Génération d'un fichier .md par section (9 fichiers)
- Fichier index/sommaire avec liens
- Format markdown propre et lisible
- Téléchargement en archive ZIP
- **Priorité :** Must-have

## 2.2 Workflows et processus

### Workflow principal : Création de spécifications

    Accueil → Nouveau projet OU Charger projet existant
    Saisie nom du projet
    Section 1/9 : Contexte & Vision
    ├── Questions avec types variés
    ├── Option "Non applicable"
    ├── Option "Besoin d'aide" → Aide LLM
    └── Sauvegarde auto
    Navigation → Section suivante (2/9)
    ... Répéter pour les 9 sections
    Récapitulatif final
    Export → Génération fichiers .md


### Workflow secondaire : Aide LLM

    Clic "Besoin d'aide" sur une question
    Ouverture panneau aide
    Affichage contexte de la question
    Saisie question spécifique (optionnel)
    Appel API LLM avec prompt système
    Affichage réponse formatée
    Possibilité de reformuler
    Fermeture aide → Retour questionnaire


## 2.3 États des objets

### Projet
- **Créé** : Nouveau projet, aucune réponse
- **En cours** : Réponses partielles sauvegardées
- **Complété** : Toutes les sections remplies
- **Exporté** : Fichiers .md générés

### Question
- **Non répondue** : État initial
- **Répondue** : Valeur saisie
- **Non applicable** : Marquée comme N/A
- **Avec aide** : Aide LLM consultée

### Session d'aide
- **Initiée** : Demande d'aide lancée
- **En cours** : Échange avec LLM actif
- **Terminée** : Aide fermée

## 2.4 Règles métier

### Validation des données
- **Nom de projet** : Obligatoire, 3-50 caractères, caractères alphanumériques + espaces
- **Réponses texte** : Pas de limite stricte, mais warning si > 1000 caractères
- **Progression** : Minimum 70% des questions répondues pour export
- **Sauvegarde** : Automatique toutes les 30 secondes + à chaque changement de section

### Permissions
- **Accès** : Tous les utilisateurs (pas d'authentification pour MVP)
- **Projets** : Stockage local par navigateur
- **Export** : Libre, pas de restrictions

### Gestion LLM
- **Rate limiting** : Maximum 10 demandes d'aide par session
- **Timeout** : 30 secondes max par requête
- **Fallback** : Message d'erreur si API indisponible
- **Coût** : Tracking basique du nombre d'appels

## 2.5 Gestion des erreurs fonctionnelles

### Erreurs utilisateur
- **Projet sans nom** : "Veuillez saisir un nom de projet"
- **Export incomplet** : "Complétez au moins 70% des questions pour exporter"
- **Nom projet existant** : "Ce nom existe déjà, choisissez-en un autre"

### Erreurs techniques
- **Sauvegarde échouée** : "Impossible de sauvegarder, vérifiez l'espace disque"
- **LLM indisponible** : "Service d'aide temporairement indisponible"
- **Export échoué** : "Erreur lors de la génération des fichiers"

### Actions de récupération
- **Perte de données** : Récupération automatique depuis la dernière sauvegarde
- **Erreur réseau** : Retry automatique + possibilité de retry manuel
- **Corruption projet** : Sauvegarde de sécurité + possibilité de restauration

## 2.6 Notifications

### Feedback utilisateur
- **Sauvegarde** : Indicateur discret "Sauvegardé" (disparaît après 2s)
- **Progression** : Barre de progression + "Section X/9 complétée"
- **Export** : "Fichiers générés avec succès" + lien de téléchargement
- **Aide LLM** : Spinner pendant chargement + confirmation réception

### Alertes
- **Données non sauvées** : Warning si tentative de quitter sans sauvegarde
- **Session longue** : Suggestion de sauvegarde après 30min d'inactivité
- **Quota LLM** : Warning à 8/10 demandes d'aide

---

**Sections couvertes :**
- ✅ Fonctionnalités principales avec user stories
- ✅ Workflows détaillés
- ✅ États des objets
- ✅ Règles métier et validations
- ✅ Gestion d'erreurs et notifications

**Status :** ✅ Validé
**Prochaine étape :** Spécifications techniques

