<template>
  <div v-if="show" class="fixed inset-0 bg-neutral-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" @click.self="$emit('close')">
    <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <h3 class="text-lg leading-6 font-medium text-neutral-900">Charger un projet existant</h3>
        <div class="mt-4 px-7 py-3">
          <div v-if="loading" class="text-center">
            <p>Chargement des projets...</p>
          </div>
          <div v-else-if="error" class="text-center text-error-500">
            <p>Erreur lors du chargement des projets : {{ error }}</p>
          </div>
          <ul v-else-if="projects.length" class="space-y-3">
            <li v-for="project in projects" :key="project.id" @click="selectProject(project.id)"
                class="p-4 bg-neutral-50 rounded-lg hover:bg-neutral-100 cursor-pointer border border-neutral-200 text-left">
              <p class="font-semibold text-neutral-700">{{ project.name }}</p>
              <p class="text-sm text-neutral-500">Dernière modification : {{ new Date(project.updated_at).toLocaleDateString() }}</p>
            </li>
          </ul>
          <div v-else>
            <p class="text-neutral-500">Aucun projet trouvé. Créez-en un nouveau pour commencer.</p>
          </div>
        </div>
        <div class="items-center px-4 py-3">
          <button @click="$emit('close')" class="px-4 py-2 bg-neutral-200 text-neutral-800 rounded hover:bg-neutral-300">
            Annuler
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

defineProps({
  show: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(['close']);

const projects = ref([]);
const loading = ref(true);
const error = ref(null);
const router = useRouter();

import api from '@/api';

async function fetchProjects() {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.get('/api/projects');
    projects.value = response.data;
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
}

function selectProject(projectId) {
  router.push({ name: 'Questionnaire', params: { id: projectId } });
  emit('close');
}

onMounted(() => {
  fetchProjects();
});
</script>
