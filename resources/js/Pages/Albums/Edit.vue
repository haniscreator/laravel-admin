<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  album: Object
})

const form = useForm({
  name: props.album.name,
  description: props.album.description,
  location: props.album.location,
  keyword: props.album.keyword,
  status: props.album.status,
})

function updateAlbum() {
    form.put(`/albums/${props.album.id}`)
}

function goBack() {
  Inertia.get('/albums')
}
</script>

<template>
  <MainLayout>
    <div class="max-w-2xl mx-auto p-6 bg-white dark:bg-gray-800 shadow rounded">
      <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
        Edit Album
      </h1>

      <form @submit.prevent="updateAlbum" class="space-y-4">
        <div>
          <label class="block text-gray-700 dark:text-gray-300">Name</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
          />
           <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
        </div>

        <div>
          <label class="block text-gray-700 dark:text-gray-300">Description</label>
          <textarea
            v-model="form.description"
            class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
          ></textarea>
        </div>

        <div>
          <label class="block text-gray-700 dark:text-gray-300">Location</label>
          <input
            v-model="form.location"
            type="text"
            class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
          />
        </div>

        <div>
          <label class="block text-gray-700 dark:text-gray-300">Keyword</label>
          <input
            v-model="form.keyword"
            type="text"
            class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
          />
        </div>

        <div>
          <label class="block text-gray-700 dark:text-gray-300">Status</label>
          <select
            v-model="form.status"
            class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
          >
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>

        <div class="flex space-x-2">
          <button
            type="submit"
            class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-900 border border-transparent dark:border-white"
          >
            Update
          </button>
          <button
            type="button"
            @click="goBack"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  </MainLayout>
</template>
