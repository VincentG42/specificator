# 🎨 SPÉCIFICATIONS UX/UI

## 4.1 Design et ergonomie

### Charte graphique

#### Palette de couleurs
```css
/* Couleurs principales */
--primary-50: #eff6ff    /* Bleu très clair */
--primary-500: #3b82f6   /* Bleu principal */
--primary-600: #2563eb   /* Bleu foncé */
--primary-700: #1d4ed8   /* Bleu très foncé */

/* Couleurs secondaires */
--emerald-500: #10b981   /* Vert succès */
--amber-500: #f59e0b     /* Orange warning */
--red-500: #ef4444       /* Rouge erreur */
--purple-500: #8b5cf6    /* Violet aide LLM */

/* Couleurs neutres */
--gray-50: #f9fafb       /* Background clair */
--gray-100: #f3f4f6      /* Background cards */
--gray-200: #e5e7eb      /* Bordures */
--gray-400: #9ca3af      /* Texte secondaire */
--gray-700: #374151      /* Texte principal */
--gray-900: #111827      /* Texte titres */

Typographie

css 

/* Fonts */
font-family: 'Inter', system-ui, sans-serif

/* Tailles */
--text-xs: 0.75rem      /* Labels, badges */
--text-sm: 0.875rem     /* Texte secondaire */
--text-base: 1rem       /* Texte principal */
--text-lg: 1.125rem     /* Sous-titres */
--text-xl: 1.25rem      /* Titres sections */
--text-2xl: 1.5rem      /* Titre principal */
--text-3xl: 1.875rem    /* Titre page */

Espacements et grilles

css 

/* Espacements système */
--space-1: 0.25rem   /* 4px */
--space-2: 0.5rem    /* 8px */
--space-4: 1rem      /* 16px */
--space-6: 1.5rem    /* 24px */
--space-8: 2rem      /* 32px */
--space-12: 3rem     /* 48px */

/* Layout */
--container-max: 1200px
--sidebar-width: 280px
--header-height: 80px

Composants de base
Boutons

html 

<!-- Bouton principal -->
<button class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 shadow-sm hover:shadow-md">
  Action principale
</button>

<!-- Bouton secondaire -->
<button class="bg-white hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-lg font-medium border border-gray-200 transition-colors duration-200">
  Action secondaire
</button>

<!-- Bouton aide -->
<button class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm transition-colors duration-200">
  🤖 Besoin d'aide
</button>

Formulaires

html 

<!-- Input texte -->
<div class="space-y-2">
  <label class="block text-sm font-medium text-gray-700">
    Nom du projet
  </label>
  <input class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200" 
         type="text" placeholder="Mon super projet">
</div>

<!-- Textarea -->
<textarea class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 resize-none" 
          rows="4" placeholder="Description détaillée..."></textarea>

<!-- Checkbox -->
<label class="flex items-center space-x-3 cursor-pointer">
  <input type="checkbox" class="w-5 h-5 text-primary-500 border-gray-300 rounded focus:ring-primary-500">
  <span class="text-gray-700">Option à cocher</span>
</label>

Cards et conteneurs

html 

<!-- Card principale -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
  <h3 class="text-lg font-semibold text-gray-900 mb-4">Titre de la card</h3>
  <p class="text-gray-600">Contenu de la card</p>
</div>

<!-- Card question -->
<div class="bg-gray-50 rounded-lg p-6 border-l-4 border-primary-500">
  <h4 class="font-medium text-gray-900 mb-3">Question</h4>
  <!-- Contenu question -->
</div>

4.2 Layout et navigation
Structure générale

html 

<div id="app" class="min-h-screen bg-gray-50">
  <!-- Header avec steps -->
  <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4">
      <!-- Logo + Navigation steps -->
    </div>
  </header>
  
  <!-- Contenu principal -->
  <main class="max-w-4xl mx-auto px-6 py-8">
    <!-- Vue courante -->
  </main>
  
  <!-- Footer (optionnel) -->
  <footer class="bg-white border-t border-gray-200 mt-16">
    <!-- Liens utiles -->
  </footer>
</div>

Navigation par steps

html 

<nav class="flex items-center justify-between">
  <!-- Logo -->
  <div class="flex items-center space-x-3">
    <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
      <span class="text-white font-bold text-sm">📋</span>
    </div>
    <h1 class="text-xl font-bold text-gray-900">Générateur de Specs</h1>
  </div>
  
  <!-- Steps progression -->
  <div class="flex items-center space-x-2">
    <div v-for="(section, index) in sections" :key="index" 
         class="flex items-center">
      <!-- Step circle -->
      <div :class="[
        'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-200',
        currentSection === index ? 'bg-primary-500 text-white' :
        completedSections.includes(index) ? 'bg-emerald-500 text-white' :
        'bg-gray-200 text-gray-500'
      ]">
        {{ index + 1 }}
      </div>
      
      <!-- Step label -->
      <span :class="[
        'ml-2 text-sm font-medium',
        currentSection === index ? 'text-primary-600' :
        completedSections.includes(index) ? 'text-emerald-600' :
        'text-gray-400'
      ]">
        {{ section.name }}
      </span>
      
      <!-- Connector -->
      <div v-if="index < sections.length - 1" 
           class="w-8 h-0.5 bg-gray-200 mx-4"></div>
    </div>
  </div>
  
  <!-- Actions -->
  <div class="flex items-center space-x-3">
    <span class="text-sm text-gray-500">{{ progress }}% complété</span>
    <button class="text-primary-500 hover:text-primary-600">
      💾 Sauvegarder
    </button>
  </div>
</nav>

Responsive design

css 

/* Desktop first (priorité) */
@media (max-width: 1024px) {
  /* Tablette - pas prioritaire mais géré */
  .step-labels { display: none; }
  .container { padding: 1rem; }
}

@media (max-width: 768px) {
  /* Mobile - fonctionnel mais pas optimisé */
  .steps-nav { flex-direction: column; }
  .step-circles { justify-content: center; }
  .main-content { padding: 0.5rem; }
}

4.3 Parcours utilisateur
Page d'accueil

html 

<div class="text-center py-16">
  <!-- Hero section -->
  <div class="max-w-3xl mx-auto">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">
      Générez des spécifications complètes pour vos projets
    </h1>
    <p class="text-xl text-gray-600 mb-8">
      Un questionnaire guidé pour créer des specs exploitables par l'IA
    </p>
    
    <!-- Actions principales -->
    <div class="flex justify-center space-x-4 mb-12">
      <button class="bg-primary-500 hover:bg-primary-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
        🚀 Nouveau projet
      </button>
      <button class="bg-white hover:bg-gray-50 text-gray-700 px-8 py-4 rounded-lg font-semibold text-lg border border-gray-200 transition-colors duration-200">
        📂 Charger un projet
      </button>
    </div>
  </div>
  
  <!-- Projets récents -->
  <div v-if="recentProjects.length" class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Projets récents</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Cards projets -->
    </div>
  </div>
</div>

Page questionnaire

html 

<div class="max-w-4xl mx-auto">
  <!-- En-tête section -->
  <div class="mb-8">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-bold text-gray-900">
        {{ currentSection.title }}
      </h2>
      <span class="text-sm text-gray-500">
        Section {{ currentSectionIndex + 1 }}/9
      </span>
    </div>
    <p class="text-gray-600">{{ currentSection.description }}</p>
    
    <!-- Barre de progression -->
    <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
      <div class="bg-primary-500 h-2 rounded-full transition-all duration-300" 
           :style="{ width: sectionProgress + '%' }"></div>
    </div>
  </div>
  
  <!-- Questions -->
  <div class="space-y-8">
    <QuestionItem 
      v-for="question in currentQuestions" 
      :key="question.id"
      :question="question"
      :answer="getAnswer(question.id)"
      @update="updateAnswer"
      @help="showHelp"
    />
  </div>
  
  <!-- Navigation -->
  <div class="flex justify-between items-center mt-12 pt-8 border-t border-gray-200">
    <button v-if="currentSectionIndex > 0"
            @click="previousSection"
            class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
      <span>←</span>
      <span>Section précédente</span>
    </button>
    <div v-else></div>
    
    <button @click="nextSection"
            class="flex items-center space-x-2 bg-primary-500 hover:bg-primary-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
      <span>{{ isLastSection ? 'Terminer' : 'Section suivante' }}</span>
      <span v-if="!isLastSection">→</span>
    </button>
  </div>
</div>

4.4 États des composants
États des questions

html 

<!-- Question non répondue -->
<div class="bg-white rounded-lg p-6 border border-gray-200">
  <div class="flex items-start justify-between">
    <div class="flex-1">
      <h4 class="font-medium text-gray-900 mb-3">{{ question.text }}</h4>
      <!-- Input selon type -->
    </div>
    <div class="flex space-x-2 ml-4">
      <button class="text-purple-500 hover:text-purple-600 text-sm">
        🤖 Aide
      </button>
    </div>
  </div>
</div>

<!-- Question répondue -->
<div class="bg-white rounded-lg p-6 border border-emerald-200 bg-emerald-50">
  <div class="flex items-start justify-between">
    <div class="flex-1">
      <div class="flex items-center space-x-2 mb-2">
        <span class="w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center">
          <span class="text-white text-xs">✓</span>
        </span>
        <h4 class="font-medium text-gray-900">{{ question.text }}</h4>
      </div>
      <!-- Réponse affichée -->
    </div>
  </div>
</div>

<!-- Question non applicable -->
<div class="bg-gray-100 rounded-lg p-6 border border-gray-200 opacity-75">
  <div class="flex items-center space-x-2">
    <span class="text-gray-500 text-sm">N/A</span>
    <h4 class="font-medium text-gray-600">{{ question.text }}</h4>
  </div>
</div>

États de chargement

html 

<!-- Sauvegarde en cours -->
<div class="fixed top-4 right-4 bg-white rounded-lg shadow-lg border border-gray-200 p-4 flex items-center space-x-3 transition-all duration-200">
  <div class="animate-spin w-4 h-4 border-2 border-primary-500 border-t-transparent rounded-full"></div>
  <span class="text-sm text-gray-700">Sauvegarde...</span>
</div>

<!-- Sauvegarde réussie -->
<div class="fixed top-4 right-4 bg-emerald-50 rounded-lg border border-emerald-200 p-4 flex items-center space-x-3 transition-all duration-200">
  <span class="w-4 h-4 bg-emerald-500 rounded-full flex items-center justify-center">
    <span class="text-white text-xs">✓</span>
  </span>
  <span class="text-sm text-emerald-700">Sauvegardé</span>
</div>

4.5 Animations et transitions
Transitions de base

css 

/* Transitions globales */
.transition-base {
  transition: all 0.2s ease-in-out;
}

.transition-colors {
  transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
}

.transition-shadow {
  transition: box-shadow 0.2s ease-in-out;
}

/* Animations subtiles */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideIn {
  from { transform: translateX(-20px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}

.animate-slideIn {
  animation: slideIn 0.3s ease-out;
}

Micro-interactions

html 

<!-- Bouton avec effet hover -->
<button class="group bg-primary-500 hover:bg-primary-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5">
  <span class="group-hover:scale-105 transition-transform duration-200">
    Action
  </span>
</button>

<!-- Card avec effet hover -->
<div class="bg-white rounded-lg p-6 border border-gray-200 hover:border-primary-200 hover:shadow-md transition-all duration-200 cursor-pointer">
  <!-- Contenu -->
</div>

4.6 Accessibilité
Navigation clavier

html 

<!-- Focus visible -->
<style>
.focus-visible:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}
</style>

<!-- Tabindex logique -->
<div class="questionnaire">
  <input tabindex="1" />
  <button tabindex="2">Aide</button>
  <button tabindex="3">Non applicable</button>
  <button tabindex="4">Suivant</button>
</div>

Lecteurs d'écran

html 

<!-- Labels explicites -->
<label for="project-name" class="sr-only">Nom du projet</label>
<input id="project-name" aria-describedby="project-name-help" />
<div id="project-name-help" class="text-sm text-gray-500">
  Choisissez un nom descriptif pour votre projet
</div>

<!-- États ARIA -->
<div role="progressbar" 
     aria-valuenow="65" 
     aria-valuemin="0" 
     aria-valuemax="100"
     aria-label="Progression du questionnaire">
  <!-- Barre de progression -->
</div>

Contrastes et lisibilité

css 

/* Ratios de contraste respectés */
.text-primary { color: #1d4ed8; } /* 4.5:1 sur blanc */
.text-secondary { color: #374151; } /* 7:1 sur blanc */
.bg-error { background: #ef4444; } /* 4.5:1 avec texte blanc */

/* Tailles minimales */
.btn { min-height: 44px; min-width: 44px; } /* Touch target */
.text-readable { font-size: 16px; line-height: 1.5; }

Interface définie :

    ✅ Design moderne et coloré avec Tailwind
    ✅ Navigation par steps en header
    ✅ Layout responsive desktop-first
    ✅ Composants réutilisables
    ✅ Animations subtiles et feedback visuel
    ✅ Accessibilité de base

Status : ✅ Validé
