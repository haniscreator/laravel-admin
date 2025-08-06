<template>
  <div>
    <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold dark:text-gray-200">Albums</h1>
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

  <!-- Reset Button -->
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
      <table class="min-w-full bg-white border border-gray-200">
        <!-- Add this inside your table <thead> -->

    <thead>
      <tr>
        <th @click="sortBy('id')" class="cursor-pointer px-4 py-2 whitespace-nowrap text-left">
          ID
          <span class="ml-1">
            <span v-if="sort.field === 'id'">
              {{ sort.direction === 'asc' ? '▲' : '▼' }}
            </span>
            <span v-else class="text-gray-400">▲▼</span>
          </span>
        </th>
        <th @click="sortBy('name')" class="cursor-pointer px-4 py-2 whitespace-nowrap text-left">
          Name
          <span class="ml-1">
            <span v-if="sort.field === 'name'">
              {{ sort.direction === 'asc' ? '▲' : '▼' }}
            </span>
            <span v-else class="text-gray-400">▲▼</span>
          </span>
        </th>
        <th @click="sortBy('description')" class="cursor-pointer px-4 py-2 whitespace-nowrap text-left">
          Description
          <span class="ml-1">
            <span v-if="sort.field === 'description'">
              {{ sort.direction === 'asc' ? '▲' : '▼' }}
            </span>
            <span v-else class="text-gray-400">▲▼</span>
          </span>
        </th>
        <th @click="sortBy('location')" class="cursor-pointer px-4 py-2 whitespace-nowrap text-left">
          Location
          <span class="ml-1">
            <span v-if="sort.field === 'location'">
              {{ sort.direction === 'asc' ? '▲' : '▼' }}
            </span>
            <span v-else class="text-gray-400">▲▼</span>
          </span>
        </th>
        <th class="text-left py-2 px-4 border-b">Keywords</th>
        <th class="text-left py-2 px-4 border-b">Status</th>
        <th class="px-4 py-2">Actions</th>
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
            <td class="py-2 px-4 border">{{ album.description.length > 100 ? album.description.slice(0, 100) + '...' : album.description }}</td>
            <td class="py-2 px-4 border">{{ album.location }}</td>
            <td class="py-2 px-4 border">{{ album.keyword }}</td>
            <!-- <td class="py-2 px-4 border">
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
            </td> -->

            <td class="py-2 px-4 border text-center">
              <input
                type="checkbox"
                :checked="album.status === 1"
                @change="toggleStatus(album.id, $event.target.checked)"
                class="toggle-checkbox hidden"
                :id="'toggle-' + album.id"
              />
              <label
                :for="'toggle-' + album.id"
                class="toggle-label block w-14 h-8 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer relative"
              >
                <span
                  class="dot absolute left-1 top-1 w-6 h-6 bg-white rounded-full transition"
                  :class="{ 'translate-x-6': album.status === 1 }"
                ></span>
              </label>
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

  import { ref, watch, computed, reactive } from 'vue'
  import { Inertia } from '@inertiajs/inertia'
  import { usePage } from '@inertiajs/inertia-vue3'
  

  const props = usePage().props.value
  const filters = ref(props.filters || { keyword: '' })
  const page = usePage()
  const albums = computed(() => usePage().props.value.albums)

  const sort = reactive({
    field: page.props.filters?.sort || 'id',
    direction: page.props.filters?.direction || 'desc',
  })

  function sortBy(field) {
    if (sort.field === field) {
      sort.direction = sort.direction === 'asc' ? 'desc' : 'asc'
    } else {
      sort.field = field
      sort.direction = 'asc'
    }

    Inertia.get('/albums', {
      keyword: filters.value.keyword,
      sort: sort.field,
      direction: sort.direction,
      page: albums.value.current_page,
    }, {
      preserveState: true,
      preserveScroll: true,
    })
  }


  function goToPage(page) {
    Inertia.get('/albums', {
      page,
      sort: sort.field,
      direction: sort.direction,
    }, {
      preserveState: true,
      preserveScroll: true,
    })
  }


  function search() {
    Inertia.get('/albums', {
      keyword: filters.value.keyword,
      sort: sort.field,
      direction: sort.direction,
    }, {
      preserveState: true,
      replace: true,
    })
  }

  function resetSearch() {
    filters.value.keyword = ''
    search()
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

  function toggleStatus(albumId) {
    Inertia.put(`/albums/${albumId}/toggle-status`, {}, {
      preserveScroll: true,
      preserveState: true,
    })
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


<style scoped>
.toggle-checkbox:checked + .toggle-label {
  background-color: #10b981; /* green */
}

.toggle-label .dot {
  transition: transform 0.3s ease;
}

.translate-x-6 {
  transform: translateX(1.5rem); /* move dot right */
}
</style>
