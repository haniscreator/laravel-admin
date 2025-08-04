<script setup>
import AuthenticatedLayout from '@/Layouts/MainLayout.vue'
import { Head } from '@inertiajs/vue3'

defineProps({ albums: Number, items: Number })
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #default>
      <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-4 rounded shadow">
          <h2 class="text-lg font-semibold">Total Albums</h2>
          <p class="text-3xl mt-2">{{ albums }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <h2 class="text-lg font-semibold">Total Items</h2>
          <p class="text-3xl mt-2">{{ items }}</p>
        </div>
      </div>
    </template>
  </AuthenticatedLayout>
</template>

