<template>
  <div class="flex h-screen bg-white dark:bg-gray-800">
    <!-- Mobile Sidebar -->
    <div v-if="isMobileOpen" class="fixed inset-0 z-40 flex">
      <Sidebar />
      <div class="fixed inset-0 bg-black opacity-50" @click="isMobileOpen = false"></div>
    </div>

    <!-- Desktop Sidebar -->
    <div class="hidden md:flex">
      <Sidebar />
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <header class="flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-900 shadow-md">
        <button @click="isMobileOpen = true" class="md:hidden">
          <i class="fas fa-bars text-2xl text-gray-700 dark:text-white"></i>
        </button>

        <div class="ml-auto">
          <UserDropdown />
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'
import UserDropdown from '@/Components/UserDropdown.vue'

const isMobileOpen = ref(false)
</script>
