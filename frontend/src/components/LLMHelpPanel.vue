<template>
  <div v-if="show" class="fixed inset-0 bg-neutral-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50" @click.self="closePanel">
    <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-neutral-100 dark:bg-neutral-800 dark:border-neutral-700">
      <div class="mt-3 text-center">
        <h3 class="text-lg leading-6 font-medium text-accent-700 dark:text-accent-300">🤖 Aide contextuelle</h3>
        <div class="mt-4 px-7 py-3 text-left">
          <p class="text-sm text-neutral-600 dark:text-neutral-300 mb-4">Contexte de la question :</p>
          <div class="bg-neutral-100 p-3 rounded-md text-sm text-neutral-800 mb-4 whitespace-pre-wrap dark:bg-neutral-700 dark:text-neutral-200">{{ questionContext }}</div>

          <label for="userQuestion" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200 mb-2">Votre question spécifique (optionnel) :</label>
          <textarea id="userQuestion" v-model="userQuestion" rows="3" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:ring-accent-500 focus:border-accent-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-100" :disabled="loading"></textarea>

          <div class="flex justify-between items-center mt-4">
            <button @click="requestHelp" :disabled="loading || quotaExceeded" class="px-4 py-2 bg-accent-500 text-white rounded-md hover:bg-accent-600 disabled:bg-neutral-400 transition-colors duration-200">
              <span v-if="loading" class="flex items-center"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>Chargement...</span>
              <span v-else>Demander de l'aide</span>
            </button>
          </div>

          <div v-if="llmResponse" class="mt-6 p-4 bg-accent-50 border border-accent-200 rounded-md text-left dark:bg-accent-900 dark:border-accent-800">
            <h4 class="font-semibold text-accent-800 dark:text-accent-200 mb-2">Réponse de l'IA :</h4>
            <p class="text-sm text-accent-700 dark:text-accent-300 whitespace-pre-wrap">{{ llmResponse }}</p>
          </div>
          <div v-if="error" class="mt-6 p-4 bg-error-50 border border-error-200 rounded-md text-left text-error-700 text-sm dark:bg-error-900 dark:border-error-800 dark:text-error-100">
            <p>Erreur : {{ error }}</p>
            <button v-if="retryAvailable" @click="requestHelp" class="mt-2 text-error-600 hover:underline dark:text-error-400">Réessayer</button>
          </div>
        </div>
        <div class="items-center px-4 py-3">
          <button @click="closePanel" class="px-4 py-2 bg-neutral-200 text-neutral-800 rounded hover:bg-neutral-300 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import api from '@/api';
import { useProjectStore } from '@/stores/projectStore';

const props = defineProps({
  show: Boolean,
  projectId: Number,
  sectionKey: String,
  questionKey: String,
});

const emit = defineEmits(['close']);

const projectStore = useProjectStore();
const userQuestion = ref('');
const questionContext = ref('');
const llmResponse = ref(null);
const loading = ref(false);
const error = ref(null);
const remainingRequests = ref(10); // Initial value, will be updated by API
const retryAvailable = ref(false);

const quotaExceeded = ref(false);

const fetchQuestionContext = () => {
  const section = projectStore.sections.find(s => s.key === props.sectionKey);
  if (section) {
    const question = section.questions[props.questionKey];
    if (question && question.help_context) {
      questionContext.value = question.help_context;
    } else {
      questionContext.value = 'Aucun contexte spécifique défini pour cette question.';
    }
  }
};

const requestHelp = async () => {
  loading.value = true;
  error.value = null;
  llmResponse.value = null;
  retryAvailable.value = false;

  try {
    const response = await api.post('/api/help', {
      project_id: props.projectId,
      section_key: props.sectionKey,
      question_key: props.questionKey,
      user_question: userQuestion.value,
    });

    llmResponse.value = response.data.content;
    remainingRequests.value = response.data.remaining_requests;
    quotaExceeded.value = remainingRequests.value <= 0;

  } catch (e) {
    error.value = e.response?.data?.error || 'Une erreur est survenue.';
    retryAvailable.value = e.response?.data?.retry_available || false;
    if (e.response?.data?.error_type === 'quota_exceeded') {
      quotaExceeded.value = true;
    }
  } finally {
    loading.value = false;
  }
};

const closePanel = () => {
  llmResponse.value = null; // Clear previous response
  userQuestion.value = ''; // Clear user question
  error.value = null; // Clear error
  emit('close');
};

watch(() => props.show, (newVal) => {
  if (newVal) {
    fetchQuestionContext();
    // Optionally fetch initial remaining requests if not already done
    // This assumes the backend sends remaining_requests on each help call
  }
});
</script>

<style scoped>
/* Add any specific styles for the modal here if needed */
</style>
