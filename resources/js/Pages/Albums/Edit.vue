<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { ref } from 'vue'




const props = defineProps({
  album: Object,
  image: Object,
})

const newImage = ref(null)
const selectedImageName = ref('')

function handleFileChange(event) {
  newImage.value = event.target.files[0]
  const file = event.target.files[0]
  if (file) {
    newImage.value = file
    selectedImageName.value = file.name
  } else {
    selectedImageName.value = ''
  }
}


function updateAlbum() {
  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('description', form.description)
  formData.append('location', form.location)
  formData.append('keyword', form.keyword)
  formData.append('status', form.status ? 1 : 0)

  if (newImage.value) {
    formData.append('image', newImage.value)
  }

  formData.append('_method', 'put') // important for Laravel to treat it as PUT

  Inertia.post(`/albums/${props.album.id}`, formData, {
    forceFormData: true,
  })
}


const form = useForm({
  name: props.album.name,
  description: props.album.description,
  location: props.album.location,
  keyword: props.album.keyword,
  status: props.album.status,
})


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
          <div v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</div>

        </div>

        <div>
          <label class="block text-gray-700 dark:text-gray-300">Location</label>
          <input
            v-model="form.location"
            type="text"
            class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
          />
          <div v-if="form.errors.location" class="text-red-500">{{ form.errors.location }}</div>

        </div>

        <div>
          <label class="block text-gray-700 dark:text-gray-300">Keyword</label>
          <input
            v-model="form.keyword"
            type="text"
            class="w-full px-4 py-2 border rounded dark:bg-gray-700 dark:text-white"
          />
           <div v-if="form.errors.keyword" class="text-red-500">{{ form.errors.keyword }}</div>

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

        <div v-if="props.image">
      <img :src="`/storage/${props.image.path}`" class="max-w-md h-auto mb-4" />
    </div>

    <label
      class="inline-flex items-center gap-2 cursor-pointer 
            px-4 py-2 rounded text-sm font-semibold 
            bg-gray-700 text-white hover:bg-gray-900
            dark:bg-gray-700 dark:hover:bg-gray-900"
    >
      <i class="fas fa-upload"></i> <!-- Font Awesome icon -->
      Upload Image
      <input
        type="file"
        @change="handleFileChange"
        accept=".jpg,.jpeg,.png"
        class="hidden"
      />
    </label>


<span class="text-sm text-gray-700 dark:text-gray-300" v-if="selectedImageName">
   {{ selectedImageName }}
  </span>

    <!-- <input type="file" @change="handleFileChange" accept="image/*" class="block w-full text-sm text-gray-900 dark:text-gray-200 
         file:mr-4 file:py-2 file:px-4
         file:rounded file:border-0
         file:text-sm file:font-semibold
         file:bg-gray-700 file:text-white
         hover:file:bg-gray-900" /> -->

    <!-- <button @click="updateAlbum" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Update Image</button> -->
  

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
