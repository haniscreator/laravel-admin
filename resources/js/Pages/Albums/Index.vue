<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Albums</h1>

    <input
      v-model="filters.keyword"
      @input="search"
      type="text"
      placeholder="Search albums..."
      class="border px-3 py-2 rounded mb-4"
    />

    <ul>
      <li v-for="album in albums.data" :key="album.id" class="mb-2">
        <h3 class="font-semibold">{{ album.name }}</h3>
        <p>{{ album.description }}</p>
      </li>
    </ul>

    <div class="mt-4">
      <button
        :disabled="!albums.prev_page_url"
        @click="goToPage(albums.current_page - 1)"
        class="mr-2 px-3 py-1 border rounded"
      >
        Prev
      </button>

      <button
        :disabled="!albums.next_page_url"
        @click="goToPage(albums.current_page + 1)"
        class="px-3 py-1 border rounded"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePage, Inertia } from '@inertiajs/inertia-vue3'

const props = usePage().props.value
const albums = ref(props.albums)
const filters = ref(props.filters || { keyword: '' })

function search() {
  Inertia.get('/albums', { keyword: filters.value.keyword }, { preserveState: true, replace: true })
}

function goToPage(page) {
  Inertia.get('/albums', { page, keyword: filters.value.keyword }, { preserveState: true, replace: true })
}

watch(() => props.albums, (newVal) => {
  albums.value = newVal
})
</script>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
  layout: MainLayout,
}
</script>
