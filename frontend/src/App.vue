<template>
  <div id="app" class="min-h-screen bg-neutral-50 font-sans text-base text-neutral-700 dark:bg-neutral-900 dark:text-neutral-200">
    <!-- Header avec steps -->
    <header class="bg-neutral-100 shadow-sm border-b border-neutral-200 sticky top-0 z-50 dark:bg-neutral-900 dark:border-neutral-700">
      <div class="max-w-7xl mx-auto px-6 py-4">
        <nav class="flex items-center justify-between">
          <!-- Logo -->
          <router-link to="/" class="flex items-center space-x-3 cursor-pointer">
            <div class="w-8 h-8 bg-accent-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-sm">📋</span>
            </div>
            <h1 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">Générateur de Specs</h1>
          </router-link>
          
          <!-- Steps progression (simplified for now) -->
          <div class="flex items-center space-x-2">
            <!-- Will be dynamic later -->
          </div>
          
          <!-- Actions -->
          <div class="flex items-center space-x-3">
            <div v-if="isQuestionnaireRoute" class="flex items-center space-x-3">
              <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ projectProgress }}% complété</span>
              <button @click="saveProject" class="px-4 py-2 bg-neutral-100 text-neutral-700 rounded-md hover:bg-neutral-200 transition-colors duration-200 text-sm font-medium dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                💾 Sauvegarder
              </button>
            </div>
            <ThemeSwitcher />
          </div>
        </nav>
      </div>
    </header>

    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-[-10px]"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-[-10px]"
    >
      <div v-if="showSaveConfirmation" class="fixed top-4 right-4 bg-success-50 rounded-lg border border-success-200 p-4 flex items-center space-x-3 shadow-lg dark:bg-success-900 dark:border-success-800 dark:text-success-100">
        <span class="w-5 h-5 bg-success-500 rounded-full flex items-center justify-center">
          <span class="text-white text-xs">✓</span>
        </span>
        <span class="text-sm text-success-700 font-medium dark:text-success-100">Sauvegardé !</span>
      </div>
    </transition>
    
    <!-- Contenu principal -->
    <main class="max-w-4xl mx-auto px-6 py-8">
      <router-view />
    </main>
    
    <!-- Footer (optionnel) -->
    <footer class="bg-white border-t border-neutral-200 mt-16 dark:bg-neutral-800 dark:border-neutral-700">
      <div class="max-w-7xl mx-auto px-6 py-4 text-center text-neutral-500 text-sm dark:text-neutral-400">
        <p>Avertissement : Cet outil génère des spécifications basées sur vos réponses. Vous êtes responsable de la validation et de l'exactitude du contenu produit. Les spécifications générées vous appartiennent entièrement.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useProjectStore } from './stores/projectStore';
import ThemeSwitcher from './components/ThemeSwitcher.vue';

const route = useRoute();
const projectStore = useProjectStore();
const showSaveConfirmation = ref(false);

const isQuestionnaireRoute = computed(() => route.name === 'Questionnaire');

const projectProgress = computed(() => {
  if (!projectStore.project || !projectStore.sections.length) return 0;
  const totalQuestions = projectStore.sections.reduce((acc, section) => acc + Object.keys(section.questions).length, 0);
  const answeredQuestions = projectStore.answers.filter(answer => !answer.is_not_applicable && answer.answer_value && answer.answer_value.value !== null && answer.answer_value.value !== '').length;
  return totalQuestions > 0 ? Math.round((answeredQuestions / totalQuestions) * 100) : 0;
});

const saveProject = () => {
  projectStore.saveProject();
  showSaveConfirmation.value = true;
  setTimeout(() => {
    showSaveConfirmation.value = false;
  }, 2000);
};
</script>

<style scoped>
/* No specific styles needed here, Tailwind handles most of it */
</style>