<?php

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
                    'placeholder' => 'Ex: Créer une application de gestion de tâches pour les équipes de développement afin de centraliser les projets, améliorer le suivi et réduire les oublis.'
                ],
                'project-scope' => [
                    'text' => 'Périmètre : Que fait-il ? Que ne fait-il PAS ?',
                    'type' => 'textarea',
                    'required' => true,
                    'help_context' => 'Délimitation précise des fonctionnalités incluses et exclues',
                    'placeholder' => "Ex: ✅ Fait : gestion de projets, tâches, commentaires, pièces jointes.
❌ Ne fait PAS : facturation, gestion RH, chat en temps réel."
                ],
                'time-constraints' => [
                    'text' => 'Contraintes temporelles : MVP en combien de temps ? Versions suivantes ?',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Planning de développement et jalons importants',
                    'placeholder' => 'Ex: MVP en 3 mois, V2 avec intégrations à 6 mois.'
                ],
                'target-users' => [
                    'text' => "Profils utilisateurs : Rôles, compétences techniques, contexte d'usage",
                    'type' => 'textarea',
                    'required' => true,
                    'help_context' => 'Personas détaillés des utilisateurs finaux',
                    'placeholder' => "Ex: Chef de projet (non-technique, besoin de reporting), Développeur (technique, besoin d'intégration IDE), Client (accès limité, besoin de suivi simple)."
                ],
                'user-volume' => [
                    'text' => "Volume : Combien d'utilisateurs simultanés/jour/mois ?",
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Estimation de la charge utilisateur pour dimensionnement',
                    'placeholder' => 'Ex: 50 utilisateurs actifs/jour, 10 simultanés en pic.'
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
                    'help_context' => "Liste des fonctionnalités avec Must/Should/Could/Won't have",
                    'placeholder' => "Must: Authentification utilisateur
Must: Création de projets et tâches
Should: Notifications email
Could: Export PDF des rapports
Won't: Intégration calendrier"
                ],
                'user-stories' => [
                    'text' => 'User Stories principales (format: En tant que [rôle], je veux [action] pour [bénéfice])',
                    'type' => 'textarea',
                    'required' => true,
                    'help_context' => "Stories utilisateur avec critères d'acceptation",
                    'placeholder' => "Ex: En tant que chef de projet, je veux voir un dashboard de l'avancement pour identifier les blocages."
                ],
                'business-rules' => [
                    'text' => 'Règles métier : Validations, calculs, permissions',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Logique métier spécifique et contraintes',
                    'placeholder' => "Ex: Une tâche ne peut être marquée 'terminée' que si toutes ses sous-tâches le sont. Le format d'un email doit être validé. Un utilisateur 'lecteur' ne peut pas modifier de tâches."
                ],
                'workflows' => [
                    'text' => 'Workflows principaux : Étapes, décisions, validations',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Processus métier et parcours utilisateur détaillés',
                    'placeholder' => "Ex: Workflow de validation d'une tâche : Création -> Assignation -> En cours -> En revue -> Validée/Refusée -> Terminée."
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
                    'text' => "Type d'application",
                    'type' => 'radio',
                    'required' => true,
                    'options' => ['Web SPA', 'Web Multi-pages', 'Mobile', 'Desktop', 'API/Microservice', 'Autre'],
                    'help_context' => "Architecture générale de l'application"
                ],
                'frontend-stack' => [
                    'text' => 'Stack Frontend : Framework, librairies, outils',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Technologies frontend préférées ou imposées',
                    'placeholder' => 'Ex: Vue.js 3 (Composition API), Pinia, Tailwind CSS, Vite'
                ],
                'backend-stack' => [
                    'text' => 'Stack Backend : Langage, framework, serveur',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Technologies backend et infrastructure',
                    'placeholder' => 'Ex: Laravel 11, PHP 8.3, Nginx, base de données PostgreSQL'
                ],
                'database-type' => [
                    'text' => 'Base de données : Type, SGBD, particularités',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Choix de stockage des données',
                    'placeholder' => 'Ex: PostgreSQL pour les données relationnelles, Redis pour le cache et les files d\'attente.'
                ],
                'external-apis' => [
                    'text' => 'APIs externes : Services tiers, authentification',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Intégrations avec services externes',
                    'placeholder' => "Ex: API Google pour l'authentification, API Stripe pour les paiements, API Mailgun pour les emails."
                ],
                'performance-targets' => [
                    'text' => 'Cibles de performance : Temps de réponse, charge',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Objectifs de performance mesurables',
                    'placeholder' => "Ex: Temps de chargement initial < 2s. Temps de réponse API < 200ms pour 95% des requêtes. Supporte 100 req/s."
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
                    'help_context' => 'Charte graphique et identité visuelle',
                    'placeholder' => 'Ex: Primaire: #3b82f6 (bleu), Secondaire: #10b981 (vert), Neutres: gris froids.'
                ],
                'responsive-priority' => [
                    'text' => 'Priorité responsive',
                    'type' => 'radio',
                    'required' => false,
                    'options' => ['Desktop first', 'Mobile first', 'Égale'],
                    'help_context' => "Stratégie d'adaptation multi-écrans"
                ],
                'navigation-type' => [
                    'text' => 'Type de navigation principale',
                    'type' => 'radio',
                    'required' => false,
                    'options' => ['Menu horizontal', 'Sidebar', 'Navigation par onglets', 'Autre'],
                    'help_context' => "Structure de navigation de l'interface"
                ],
                'user-journey' => [
                    'text' => 'Parcours utilisateur principal : Étapes clés',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => "Flow principal d'utilisation de l'application",
                    'placeholder' => "Ex: 1. Inscription -> 2. Création du premier projet -> 3. Ajout de tâches -> 4. Invitation de collaborateurs -> 5. Suivi de l'avancement."
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
                    'placeholder' => "Ex: Utilisateur (id, nom, email, mdp, rôle)
Projet (id, nom, description)
Tâche (id, titre, statut, projet_id, assigne_a)"
                ],
                'data-relationships' => [
                    'text' => 'Relations entre entités : Associations, cardinalités',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Liens entre les objets métier',
                    'placeholder' => "Ex: Un Utilisateur peut avoir plusieurs Projets (1-N). Un Projet a plusieurs Tâches (1-N). Une Tâche est assignée à un seul Utilisateur (1-1)."
                ],
                'data-sources' => [
                    'text' => 'Sources de données : Saisie, import, APIs, calculs',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Origine et alimentation des données',
                    'placeholder' => "Ex: Données utilisateurs saisies au formulaire. Données de projets importées via CSV. Taux de change récupéré via une API externe."
                ],
                'data-validation' => [
                    'text' => 'Règles de validation : Formats, contraintes, plages',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Contrôles de qualité des données',
                    'placeholder' => "Ex: L'email doit être un format valide. Le nom du projet doit faire entre 3 et 50 caractères. Le statut d'une tâche doit être l'une des valeurs suivantes : 'à faire', 'en cours', 'terminé'."
                ]
            ]
        ],

        'integration-specs' => [
            'title' => "Spécifications d'Intégration",
            'description' => 'APIs, systèmes externes, échanges',
            'icon' => '🔗',
            'order' => 6,
            'questions' => [
                'external-systems' => [
                    'text' => 'Systèmes externes : APIs, services, legacy',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => "Intégrations avec l'existant",
                    'placeholder' => "Ex: Intégration avec l'annuaire d'entreprise (LDAP) pour l'authentification. Connexion à l'API de Slack pour les notifications."
                ],
                'data-formats' => [
                    'text' => "Formats d'échange : JSON, XML, CSV, autres",
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Standards de communication',
                    'placeholder' => 'Ex: API interne en JSON:API, import/export en CSV, communication avec le legacy en XML.'
                ],
                'sync-frequency' => [
                    'text' => 'Fréquence de synchronisation : Temps réel, batch, événementiel',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Stratégie de mise à jour des données',
                    'placeholder' => 'Ex: Synchronisation des utilisateurs avec LDAP toutes les nuits (batch). Notifications Slack en temps réel (événementiel).'
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
                    'help_context' => 'Infrastructure et configuration par environnement',
                    'placeholder' => "Ex: Dev (local, SQLite), Staging (Scalingo, clone de la prod), Prod (Scalingo, PostgreSQL + Redis)."
                ],
                'deployment-strategy' => [
                    'text' => 'Stratégie de déploiement : CI/CD, rollback, validation',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Processus de mise en production',
                    'placeholder' => "Ex: CI/CD avec Github Actions. Déploiement automatique sur staging à chaque push sur 'develop'. Déploiement manuel sur prod (blue-green) après validation."
                ],
                'monitoring-needs' => [
                    'text' => 'Besoins de monitoring : Métriques, logs, alertes',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Observabilité et surveillance',
                    'placeholder' => "Ex: Monitoring des temps de réponse et du taux d'erreur avec Datadog. Centralisation des logs sur Papertrail. Alertes sur Slack si le taux d'erreur > 1%."
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
                    'help_context' => 'Conformité réglementaire',
                    'placeholder' => "Ex: Données personnelles (nom, email) stockées en Europe. Anonymisation des données pour les statistiques. Droit à l'oubli implémenté."
                ],
                'security-requirements' => [
                    'text' => 'Exigences de sécurité : Authentification, autorisation, chiffrement',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Mesures de sécurité nécessaires',
                    'placeholder' => "Ex: Mots de passe hashés (bcrypt). Protection contre les attaques OWASP Top 10. Connexions en HTTPS uniquement. Tests d'intrusion prévus."
                ],
                'compliance-standards' => [
                    'text' => 'Standards à respecter : ISO, SOC, sectoriels',
                    'type' => 'text',
                    'required' => false,
                    'help_context' => 'Certifications et normes applicables',
                    'placeholder' => 'Ex: Si application médicale, conformité HDS. Si paiement, conformité PCI-DSS.'
                ]
            ]
        ],

        'quality-specs' => [
            'title' => 'Spécifications de Qualité',
            'description' => "Tests, métriques, critères d'acceptation",
            'icon' => '📈',
            'order' => 9,
            'questions' => [
                'testing-strategy' => [
                    'text' => 'Stratégie de tests : Types, couverture, automatisation',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Plan de validation et tests',
                    'placeholder' => "Ex: Tests unitaires (PHPUnit) > 80% de couverture. Tests d'intégration pour les workflows critiques. Tests E2E (Cypress) pour le parcours principal."
                ],
                'quality-metrics' => [
                    'text' => 'Métriques de qualité : Performance, fiabilité, utilisabilité',
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Indicateurs de succès mesurables',
                    'placeholder' => "Ex: Taux de complétion des tâches > 90%. Score de satisfaction utilisateur > 4/5. Taux d'erreur < 0.5%."
                ],
                'acceptance-criteria' => [
                    'text' => "Critères d'acceptation globaux : Conditions de livraison",
                    'type' => 'textarea',
                    'required' => false,
                    'help_context' => 'Définition of Done du projet',
                    'placeholder' => "Ex: Le projet est livrable si toutes les fonctionnalités 'Must-have' sont implémentées, les tests passent, et la documentation est à jour."
                ]
            ]
        ]
    ]
];
