<template>
  <div v-if="show" class="fixed inset-0 bg-neutral-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" @click.self="$emit('close')">
    <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-neutral-100 dark:bg-neutral-800 dark:border-neutral-700">
      <div class="mt-3 text-center">
        <h3 class="text-lg leading-6 font-medium text-neutral-900 dark:text-neutral-100">Nouveau projet</h3>
        <form @submit.prevent="createProject" class="mt-4 px-7 py-3">
          <input type="text" v-model="projectName" placeholder="Nom du projet" 
                 class="w-full px-3 py-2 text-neutral-700 border rounded-lg focus:outline-none focus:shadow-outline dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-100" required>
          <div class="items-center px-4 py-3">
            <button type="submit" class="px-4 py-2 bg-accent-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-accent-600 focus:outline-none focus:ring-2 focus:ring-accent-500">
              Créer le projet
            </button>
            <button @click="$emit('close')" type="button" class="mt-2 px-4 py-2 bg-neutral-200 text-neutral-800 rounded hover:bg-neutral-300 w-full dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
              Annuler
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api';

defineProps({
  show: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(['close']);

const projectName = ref('');
const router = useRouter();

async function createProject() {
  if (!projectName.value.trim()) return;
  try {
    const response = await api.post('/api/projects', { name: projectName.value });
    router.push({ name: 'Questionnaire', params: { id: response.data.id } });
    emit('close');
  } catch (error) {
    console.error("Erreur lors de la création du projet:", error);
    // Handle error (e.g., show a notification)
  }
}
</script>
