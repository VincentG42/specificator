<template>
  <div class="text-center py-16">
    <!-- Hero section -->
    <div class="max-w-3xl mx-auto">
      <h1 class="text-4xl font-bold text-neutral-900 dark:text-neutral-100 mb-6">
        Générez des spécifications complètes pour vos projets
      </h1>
      <p class="text-xl text-neutral-600 dark:text-neutral-300 mb-8">
        Un questionnaire guidé pour créer des specs exploitables par l'IA
      </p>
      
      <!-- Actions principales -->
      <div class="flex justify-center space-x-4 mb-12">
        <button @click="showNewProjectModal = true" class="bg-accent-500 hover:bg-accent-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
          🚀 Nouveau projet
        </button>
        <button @click="showProjectList = true" class="bg-neutral-50 hover:bg-neutral-100 text-neutral-700 px-8 py-4 rounded-lg font-semibold text-lg border border-neutral-200 transition-colors duration-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:text-neutral-200 dark:border-neutral-700">
          📂 Charger un projet
        </button>
      </div>
    </div>
    
    <!-- Projets récents -->
    <div v-if="recentProjects.length" class="max-w-4xl mx-auto">
      <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-6">Projets récents</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Cards projets -->
      </div>
    </div>

    <ProjectListModal :show="showProjectList" @close="showProjectList = false" />
    <NewProjectModal :show="showNewProjectModal" @close="showNewProjectModal = false" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ProjectListModal from '@/components/ProjectListModal.vue'
import NewProjectModal from '@/components/NewProjectModal.vue'

const recentProjects = ref([])
const showProjectList = ref(false)
const showNewProjectModal = ref(false)
</script>
