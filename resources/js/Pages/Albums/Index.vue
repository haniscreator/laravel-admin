<template>
  <div>
    <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Albums</h1>
    <div v-if="$page.props.flash.success" class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-3 rounded mb-4">
        {{ $page.props.flash.success }}
    </div>

    <div class="flex items-center space-x-2">
        <input
        v-model="filters.keyword"
        @input="search"
        type="text"
        placeholder="Search albums..."
        class="border px-3 py-2 rounded w-64"
    />

    <button
      @click="goToCreateForm"
      class="text-white px-4 py-2 rounded bg-gray-700 hover:bg-gray-900 border-transparent"
    >
      + Add New
    </button>
  </div>
</div>


    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="text-left py-2 px-4 border-b">ID</th>
            <th class="text-left py-2 px-4 border-b">Name</th>
            <th class="text-left py-2 px-4 border-b">Description</th>
            <th class="text-left py-2 px-4 border-b">Location</th>
            <th class="text-left py-2 px-4 border-b">Status</th>
            <th class="text-left py-2 px-4 border-b">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="album in albums.data"
            :key="album.id"
            class="hover:bg-gray-50"
          >
            <td class="py-2 px-4 border">{{ album.id }}</td>
            <td class="py-2 px-4 border">{{ album.name }}</td>
            <td class="py-2 px-4 border">{{ album.description }}</td>
            <td class="py-2 px-4 border">{{ album.location }}</td>
            <td class="py-2 px-4 border">
              <span
                :class="[
                  'px-2 py-1 rounded-full text-sm font-semibold',
                  album.status === 1
                    ? 'bg-green-100 text-green-800'
                    : 'bg-red-100 text-red-800'
                ]"
              >
                {{ album.status === 1 ? 'Active' : 'In-Active' }}
              </span>
            </td>
            <td class="px-4 py-2 border text-center">
              <button
                @click="editAlbum(album.id)"
                class="text-blue-500 hover:text-blue-700 mr-2"
                title="Edit"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                @click="deleteAlbum(album.id)"
                class="text-red-500 hover:text-red-700"
                title="Delete"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex justify-end mt-4">
    <button
        :disabled="!albums.prev_page_url"
        @click="goToPage(albums.current_page - 1)"
        class="mr-2 px-4 py-2 rounded border text-sm font-medium
            bg-white dark:bg-gray-800 
            text-gray-800 dark:text-gray-200 
            hover:bg-gray-100 dark:hover:bg-gray-700 
            disabled:opacity-50 disabled:cursor-not-allowed"
    >
        Prev
    </button>

    <button
        :disabled="!albums.next_page_url"
        @click="goToPage(albums.current_page + 1)"
        class="px-4 py-2 rounded border text-sm font-medium
            bg-white dark:bg-gray-800 
            text-gray-800 dark:text-gray-200 
            hover:bg-gray-100 dark:hover:bg-gray-700 
            disabled:opacity-50 disabled:cursor-not-allowed"
    >
        Next
    </button>
    </div>

  </div>
</template>

<script setup>

import { ref, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
import { computed } from 'vue'
const albums = computed(() => usePage().props.value.albums)

const props = usePage().props.value
const filters = ref(props.filters || { keyword: '' })

function search() {
  Inertia.get('/albums', { keyword: filters.value.keyword }, { preserveState: true, replace: true })
}

function goToPage(page) {

  Inertia.get('/albums', { page, keyword: filters.value.keyword }, { preserveState: true, replace: true })
}

function editAlbum(id) {
    Inertia.get(`/albums/${id}/edit`)
}

function deleteAlbum(id) {
  if (confirm('Are you sure you want to delete this album?')) {
    Inertia.delete(`/albums/${id}`)
  }
}

function goToCreateForm() {
  Inertia.get('/albums/create')
}

watch(() => usePage().props.value.albums, (newVal) => {
  albums.value = newVal
})
</script>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
  layout: MainLayout,
}
</script>
