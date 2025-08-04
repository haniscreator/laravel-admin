<template>
  <div class="relative w-16 h-8 bg-gray-700 rounded-full p-1 flex items-center cursor-pointer" @click="toggle">
    <div :class="{'translate-x-0': !darkMode, 'translate-x-8': darkMode}" class="absolute transition-transform duration-300 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
      <i :class="darkMode ? 'fas fa-moon text-white' : 'fas fa-sun text-yellow-400'"></i>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const darkMode = ref(localStorage.theme === 'dark')

const toggle = () => {
  darkMode.value = !darkMode.value
  localStorage.theme = darkMode.value ? 'dark' : 'light'
  document.documentElement.classList.toggle('dark', darkMode.value)
}
</script>
