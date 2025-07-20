<template>
  <div class="bg-neutral-50 rounded-lg p-4 sm:p-6 border border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700">
    <div class="flex flex-col sm:flex-row items-start justify-between">
      <div class="flex-1 w-full">
        <h4 class="font-medium text-neutral-900 dark:text-neutral-100 mb-3">{{ question.text }}</h4>
        
        <div v-if="question.type === 'textarea'">
          <textarea 
            :value="answerValue"
            @input="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-transparent transition-all duration-200 resize-none dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-100"
            rows="4"
            :placeholder="question.placeholder"
          ></textarea>
        </div>

        <div v-else-if="question.type === 'text'">
          <input 
            :value="answerValue"
            @input="updateValue($event.target.value)"
            type="text"
            class="w-full px-3 py-2 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-100"
            :placeholder="question.placeholder"
          />
        </div>

        <div v-else-if="question.type === 'radio'" class="space-y-2">
          <label v-for="option in question.options" :key="option" class="flex items-center space-x-3 cursor-pointer">
            <input 
              type="radio"
              :value="option"
              :checked="answerValue === option"
              class="w-5 h-5 text-accent-600 border-neutral-300 rounded-full focus:ring-accent-600 dark:border-neutral-600"
            />
            <span class="text-neutral-700 dark:text-neutral-200">{{ option }}</span>
          </label>
        </div>

        <div v-else-if="question.type === 'checkbox'" class="space-y-2">
          <label v-for="option in question.options" :key="option" class="flex items-center space-x-3 cursor-pointer">
            <input 
              type="checkbox"
              :value="option"
              :checked="answerValue && answerValue.includes(option)"
              @change="handleCheckboxChange(option, $event.target.checked)"
              class="w-5 h-5 text-accent-600 border-neutral-300 rounded focus:ring-accent-600 dark:border-neutral-600"
            />
            <span class="text-neutral-700 dark:text-neutral-200">{{ option }}</span>
          </label>
        </div>
      </div>
      <div class="flex justify-end sm:justify-start mt-4 sm:mt-0 sm:ml-4 w-full sm:w-auto">
        <button @click="$emit('help', questionKey)" class="px-3 py-1 bg-accent-500 text-white rounded-md hover:bg-accent-600 transition-colors duration-200 text-sm font-medium">
          🤖 Aide
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  question: Object,
  questionKey: String,
  answer: Object
})

const emit = defineEmits(['update', 'help'])

const answerValue = ref(props.answer?.answer_value?.value || (props.question.type === 'checkbox' ? [] : ''))

watch(() => props.answer, (newAnswer) => {
  answerValue.value = newAnswer?.answer_value?.value || (props.question.type === 'checkbox' ? [] : '')
})

const updateValue = (value) => {
  answerValue.value = value
  emit('update', props.questionKey, value, false)
}

const handleCheckboxChange = (option, checked) => {
  let newValue = Array.isArray(answerValue.value) ? [...answerValue.value] : []
  if (checked) {
    newValue.push(option)
  } else {
    newValue = newValue.filter(item => item !== option)
  }
  updateValue(newValue)
}
</script>
