<template>
  <div class="relative inline-block text-left">
    <div>
      <button type="button" @click="isOpen = !isOpen" class="inline-flex justify-center w-full rounded-md border border-neutral-300 shadow-sm px-4 py-2 bg-neutral-100 text-sm font-medium text-neutral-700 hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700">
        <span v-if="currentTheme === 'light'">☀️ Clair</span>
        <span v-else-if="currentTheme === 'dark'">🌙 Sombre</span>
        <span v-else>💻 Système</span>
        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>

    <div v-if="isOpen" class="origin-top-right absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-neutral-100 ring-1 ring-black ring-opacity-5 focus:outline-none z-10 dark:bg-neutral-800">
      <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
        <button @click="setTheme('light')" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-200 hover:text-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-700" role="menuitem">☀️ Clair</button>
        <button @click="setTheme('dark')" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-200 hover:text-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-700" role="menuitem">🌙 Sombre</button>
        <button @click="setTheme('system')" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-200 hover:text-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-700" role="menuitem">💻 Système</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

const isOpen = ref(false);
const currentTheme = ref('system'); // Default to system

const applyTheme = (theme) => {
  const root = document.documentElement;
  if (theme === 'dark') {
    root.classList.add('dark');
    localStorage.theme = 'dark';
  } else if (theme === 'light') {
    root.classList.remove('dark');
    localStorage.theme = 'light';
  } else { // system
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      root.classList.add('dark');
    } else {
      root.classList.remove('dark');
    }
    localStorage.removeItem('theme');
  }
  currentTheme.value = theme;
};

const setTheme = (theme) => {
  applyTheme(theme);
  isOpen.value = false; // Close dropdown after selection
};

onMounted(() => {
  // Initialize theme based on localStorage or system preference
  if (localStorage.theme === 'dark') {
    applyTheme('dark');
  } else if (localStorage.theme === 'light') {
    applyTheme('light');
  } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
    applyTheme('system'); // System is dark
  } else {
    applyTheme('system'); // System is light
  }

  // Listen for system theme changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.theme) { // Only apply if user hasn't manually set a theme
      applyTheme('system');
    }
  });
});
</script>
