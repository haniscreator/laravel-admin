<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold dark:text-gray-200">Items</h1>

      <div v-if="$page.props.flash.success" class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-3 rounded mb-4">
        {{ $page.props.flash.success }}
    </div>

      <div class="flex items-center space-x-2">
        
         <!-- Album dropdown -->
        <select
            v-model="filters.album_id"
            @change="search"
            class="border px-3 py-2 rounded max-w-xs"
          >
          <option value="">All Albums</option>
          <option v-for="album in albums" :key="album.id" :value="album.id">
            {{ album.name }}
          </option>
        </select>
        
        <input
          v-model="filters.keyword"
          @input="search"
          type="text"
          placeholder="Search items..."
          class="border px-3 py-2 rounded w-64"
        />

        <button
          @click="resetSearch"
          class="text-white px-4 py-2 rounded bg-gray-700 hover:bg-gray-900 border-transparent"
        >
          Reset
        </button>

        <button
          @click="goToCreateForm"
          class="text-white px-4 py-2 rounded bg-gray-700 hover:bg-gray-900 border-transparent"
        >
          + Add New
        </button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200">
        <thead>
          <tr class="bg-gray-100 dark:bg-gray-700 text-left text-gray-800 dark:text-white">
            <th @click="sortBy('id')" class="cursor-pointer px-4 py-2">ID</th>
            <th @click="sortBy('name')" class="cursor-pointer px-4 py-2">Name</th>
            <th @click="sortBy('description')" class="cursor-pointer px-4 py-2">Description</th>
            <th class="px-4 py-2">Album</th>
            <th class="px-4 py-2">Keyword</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items.data" :key="item.id" class="hover:bg-gray-50 text-gray-800 dark:text-white">
            <td class="py-2 px-4 border">{{ item.id }}</td>
            <td class="py-2 px-4 border">{{ item.name }}</td>
            <td class="py-2 px-4 border">{{ truncate(item.description) }}</td>
            <td class="py-2 px-4 border">{{ item.album_name }}</td>
            <td class="py-2 px-4 border">
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="(tag, index) in (item.keyword || '').split(',')"
                  :key="index"
                  class="inline-block bg-yellow-200 dark:bg-yellow-600 text-gray-800 dark:text-white text-xs font-medium px-2 py-0.5 rounded"
                >
                  {{ tag.trim() }}
                </span>
              </div>
            </td>
            <td class="py-2 px-4 border text-center">
              <input
                type="checkbox"
                :checked="item.status === 1"
                @change="toggleStatus(item.id, $event.target.checked)"
                class="toggle-checkbox hidden"
                :id="'toggle-' + item.id"
              />
              <label
                :for="'toggle-' + item.id"
                class="toggle-label block w-10 h-5 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer relative"
              >
                <span
                  class="dot absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition"
                  :class="{ 'translate-x-5': item.status === 1 }"
                ></span>
              </label>
            </td>

            <td class="px-3 py-2 border text-center">
              <button
                @click="editItem(item.id)"
                class="text-blue-500 hover:text-blue-700 mr-2"
                title="Edit"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                @click="deleteItem(item.id)"
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
        :disabled="!items.prev_page_url"
        @click="goToPage(items.current_page - 1)"
        class="mr-2 px-4 py-2 rounded border text-sm font-medium bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Prev
      </button>
      <button
        :disabled="!items.next_page_url"
        @click="goToPage(items.current_page + 1)"
        class="px-4 py-2 rounded border text-sm font-medium bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'

const page = usePage()
const items = computed(() => page.props.value.items)

const props = defineProps({
  items: Object,
  albums: Array,
  filters: Object,
})

const filters = ref({
  keyword: page.props.value.filters?.keyword || '',
  album_id: props.filters.album_id || '',
})

const sort = reactive({
  field: page.props.value.filters?.sort || 'id',
  direction: page.props.value.filters?.direction || 'desc',
})

function truncate(text, length = 100) {
  return text.length > length ? text.slice(0, length) + '...' : text
}

function search() {
  Inertia.get('/items', {
    keyword: filters.value.keyword,
    album_id: filters.value.album_id,
    sort: sort.field,
    direction: sort.direction,
  }, {
    preserveState: true,
    replace: true,
  })
}

function resetSearch() {
  filters.value.keyword = ''
  filters.value.album_id = ''
  search()
}

function goToPage(page) {
  Inertia.get('/items', {
    page,
    sort: sort.field,
    direction: sort.direction,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function sortBy(field) {
  if (sort.field === field) {
    sort.direction = sort.direction === 'asc' ? 'desc' : 'asc'
  } else {
    sort.field = field
    sort.direction = 'asc'
  }

  search()
}

function goToCreateForm() {
  Inertia.get('/items/create')
}

function editItem(id) {
  Inertia.get(`/items/${id}/edit`)
}

function deleteItem(id) {
  if (confirm('Are you sure you want to delete this item?')) {
    Inertia.delete(`/items/${id}`, {
      preserveScroll: true,
    })
  }
}

function toggleStatus(itemId) {
  Inertia.put(`/items/${itemId}/toggle-status`, {}, {
    preserveScroll: true,
    preserveState: true,
  })
}
</script>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
  layout: MainLayout,
}
</script>

<style scoped>
.toggle-checkbox:checked + .toggle-label {
  background-color: #10b981; /* green */
}

.toggle-label .dot {
  transition: transform 0.3s ease;
}

.translate-x-5 {
  transform: translateX(1.25rem); /* move dot right */
}
</style>