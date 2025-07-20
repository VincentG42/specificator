# 🎪 CONTEXTE & VISION PROJET

## 1.1 Définition du projet

### Objectif principal
Créer un **Générateur de Spécifications pour Développement Assisté par IA** qui guide méthodiquement les développeurs pour produire des spécifications complètes et exploitables par les assistants de code (Cursor, Claude, etc.).

**Problème résolu :** 
- Éviter les oublis critiques dans les spécifications
- Améliorer la qualité du code généré par l'IA
- Standardiser le processus de spécification en équipe

### Périmètre

**✅ Ce que fait l'application :**
- Interface web de questionnaire guidé basé sur le guide universel
- Génération de fichiers .md structurés par section
- Intégration LLM pour aide contextuelle sur les questions complexes
- Export des spécifications complètes
- Sauvegarde locale des projets en cours

**❌ Ce que ne fait PAS l'application :**
- Ne génère pas de code directement
- Ne remplace pas les outils de développement (IDE, Git, etc.)
- Ne gère pas le versioning des projets
- Pas de collaboration temps réel (pour le MVP)

### Contraintes temporelles
- **MVP** : Développement via CLI Gemini (délai flexible)
- **Évolution** : Itérative selon retours d'usage équipe
- **Priorité** : Fonctionnalité avant esthétique

### Budget/Ressources
- **Équipe** : Développeur solo (toi) + retours collègues
- **Infrastructure** : Locale (pas de serveur pour MVP)
- **Outils** : Stack web moderne, base de données simple locale
- **API** : LLM (Claude/Gemini) - clé à obtenir

## 1.2 Utilisateurs cibles

### Profils utilisateurs
- **Primaire** : Développeurs juniors/seniors de ton équipe
- **Compétences** : Bonnes connaissances techniques, familiers avec l'IA
- **Contexte d'usage** : 
  - Préparation de nouveaux projets
  - Amélioration des prompts pour assistants IA
  - Standardisation des specs en équipe

### Volume
- **Utilisateurs** : Toi + collègues (~5-20 personnes max)
- **Fréquence** : Usage ponctuel (début projet, nouvelles features)
- **Sessions** : Probablement 1-3 projets par mois par utilisateur

### Géographie
- **Portée** : Locale/équipe
- **Langue** : Français (interface) + Anglais (exports techniques)
- **Fuseaux** : Non applicable (usage local)

## 1.3 Environnement existant

### Systèmes actuels
- **Outils de dev** : IDE (VS Code/Cursor), Git
- **IA actuels** : Assistants de code (Cursor, Claude, etc.)
- **Documentation** : Markdown, README existants
- **Gestion projet** : Outils équipe existants (à identifier)

### Données existantes
- **Migration** : Aucune (nouveau projet)
- **Intégration** : Export vers outils existants
- **Format** : Compatible avec workflows actuels

### Contraintes organisationnelles
- **Processus** : Intégration dans workflow de développement
- **Validation** : Tests avec collègues avant déploiement
- **Adoption** : Doit être plus efficace que méthodes actuelles

---

**Status** : ✅ Validé
**Prochaine étape** : Spécifications fonctionnelles

