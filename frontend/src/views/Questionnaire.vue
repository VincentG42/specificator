<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- En-tête section -->
    <div class="mb-8">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-neutral-900">
          {{ currentSection.title }}
        </h2>
        <span class="text-sm text-neutral-500">
          Section {{ currentSectionIndex + 1 }}/{{ sections.length }}
        </span>
      </div>
      <p class="text-neutral-600">{{ currentSection.description }}</p>
      
      <!-- Barre de progression -->
      <div class="w-full bg-neutral-200 rounded-full h-2 mt-4">
        <div class="bg-accent-500 h-2 rounded-full transition-all duration-300" 
             :style="{ width: sectionProgress + '%' }"></div>
      </div>
    </div>
    
    <!-- Questions -->
    <div class="space-y-8">
      <QuestionItem 
        v-for="(question, key) in currentSection.questions" 
        :key="key"
        :question="question"
        :questionKey="key"
        :answer="store.getAnswer(currentSectionKey, key)"
        @update="updateAnswer"
        @help="showHelp"
      />
    </div>
    
    <!-- Navigation -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-12 pt-8 border-t border-neutral-200 bg-neutral-100 min-h-[100px] p-4 rounded-lg space-y-4 sm:space-y-0">
      <button v-if="currentSectionIndex > 0"
              @click="previousSection"
              class="flex items-center justify-center space-x-2 w-full sm:w-auto px-4 py-2 sm:px-8 sm:py-4 text-neutral-700 font-semibold rounded-lg border-2 border-neutral-500 hover:bg-neutral-200 transition-colors duration-200 text-base sm:text-lg shadow-md">
        <span>←</span>
        <span>Section précédente</span>
      </button>
      <div v-else class="w-full sm:w-auto"></div>
      
      <button @click="nextSection"
              class="flex items-center justify-center space-x-2 w-full sm:w-auto px-4 py-2 sm:px-8 sm:py-4 bg-accent-500 hover:bg-accent-600 text-white rounded-lg font-semibold text-base sm:text-lg shadow-md transition-all duration-200">
        <span>{{ isLastSection ? 'Terminer' : 'Section suivante' }}</span>
        <span v-if="!isLastSection">→</span>
      </button>
    </div>

    <LLMHelpPanel 
      v-if="project"
      :show="showHelpPanel"
      :projectId="project.id"
      :sectionKey="currentSectionKey"
      :questionKey="currentQuestionKeyForHelp"
      @close="showHelpPanel = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjectStore } from '@/stores/projectStore'
import QuestionItem from '../components/QuestionItem.vue'
import LLMHelpPanel from '../components/LLMHelpPanel.vue'

const route = useRoute()
const router = useRouter()
const store = useProjectStore()

const currentSectionIndex = ref(0)
const showHelpPanel = ref(false)
const currentQuestionKeyForHelp = ref(null)

const sections = computed(() => store.sections)
const project = computed(() => store.project)
const currentSection = computed(() => store.sections[currentSectionIndex.value] || { questions: {} })
const currentSectionKey = computed(() => currentSection.value.key)

const sectionProgress = computed(() => {
  if (!currentSection.value.questions) return 0;
  const questions = Object.keys(currentSection.value.questions)
  if (questions.length === 0) return 0;
  const answered = questions.filter(key => store.getAnswer(currentSectionKey.value, key))
  return (answered.length / questions.length) * 100
})

const isLastSection = computed(() => currentSectionIndex.value === store.sections.length - 1)

onMounted(() => {
  store.fetchProject(route.params.id)
  store.fetchSections()
})

const updateAnswer = (questionKey, answerValue, isNotApplicable) => {
  store.updateAnswer(currentSectionKey.value, questionKey, answerValue, isNotApplicable)
}

const previousSection = () => {
  if (currentSectionIndex.value > 0) {
    currentSectionIndex.value--
  }
}

const nextSection = () => {
  if (!isLastSection.value) {
    currentSectionIndex.value++
  } else {
    router.push({ name: 'Export', params: { id: project.value.id } })
  }
}

const showHelp = (questionKey) => {
  currentQuestionKeyForHelp.value = questionKey;
  showHelpPanel.value = true;
}
</script>