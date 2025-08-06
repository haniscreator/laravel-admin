<template>
  <div class="max-w-2xl mx-auto py-8 shadow p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Create New Album</h1>


    <form @submit.prevent="submit">
      <div class="mb-4">
        <label class="block font-medium text-gray-800 dark:text-gray-200">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full border rounded px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
        />
        <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
       
      </div>

      <div class="mb-4">
        <label class="block font-medium text-gray-800 dark:text-gray-200">Description</label>
        <textarea
          v-model="form.description"
          class="w-full border rounded px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
        ></textarea>
        <div v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</div>
      </div>

      <div class="mb-4">
        <label class="block font-medium text-gray-800 dark:text-gray-200">Location</label>
        <input
          v-model="form.location"
          type="text"
          class="w-full border rounded px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
        />
        <div v-if="form.errors.location" class="text-red-500">{{ form.errors.location }}</div>

      </div>

      <div class="mb-4">
        <label class="block font-medium text-gray-800 dark:text-gray-200">Keyword</label>
        <input
          v-model="form.keyword"
          type="text"
          class="w-full border rounded px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
        />
        <div v-if="form.errors.keyword" class="text-red-500">{{ form.errors.keyword }}</div>

      </div>

      <div class="mb-4 flex items-center">
        <input v-model="form.status" type="checkbox" id="status" class="mr-2" />
        <label for="status" class="font-medium text-gray-800 dark:text-gray-200">Active</label>
      </div>


      <div class="flex space-x-2">
        <button
        type="submit"
        class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-900 border border-transparent"
        >
        Save
        </button>
        <button type="button" @click="goBack" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Cancel</button>
    </div>
    </form>
  </div>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'

const form = useForm({
  name: '',
  description: '',
  location: '',
  keyword: '',
  status: false,
})

function submit() {
  form.post('/albums')
}

function goBack() {
  Inertia.get('/albums')
}
</script>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
  layout: MainLayout,
}
</script>
