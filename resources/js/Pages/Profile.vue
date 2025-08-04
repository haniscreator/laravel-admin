<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Profile Page</h1>
    <p>This is your profile content inside the layout.</p>
  </div>
</template>

<script setup>
// Your setup logic here (if needed)
</script>

<script>
import AuthenticatedLayout from '@/Layouts/MainLayout.vue'

export default {
  layout: AuthenticatedLayout,
}
</script>
