<template>
  <div class="max-w-2xl mx-auto py-8 shadow p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Edit Profile</h1>

    <!-- Flash message -->
    <div v-if="flash.success" class="mb-4 p-3 bg-green-100 text-green-700 rounded">
      {{ flash.success }}
    </div>

    <form @submit.prevent="updateProfile" enctype="multipart/form-data">
      <!-- Name -->
      <div class="mb-4">
        <label class="block font-medium text-gray-800 dark:text-gray-200">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700"
        />
        <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
      </div>

      <!-- Email -->
      <div class="mb-4">
        <label class="block font-medium text-gray-800 dark:text-gray-200">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700"
        />
        <div v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</div>
      </div>

      <!-- Password -->
      <div class="mb-4">
        <label class="block font-medium text-gray-800 dark:text-gray-200">Password</label>
        <input
          v-model="form.password"
          type="password"
          placeholder="Leave blank to keep current"
          class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700"
        />
        <div v-if="form.errors.password" class="text-red-500">{{ form.errors.password }}</div>
      </div>

      <!-- Confirm Password -->
      <div class="mb-4">
        <label class="block font-medium text-gray-800 dark:text-gray-200">Confirm Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700"
        />
        <div v-if="passwordMismatch" class="text-red-500">Passwords do not match.</div>
      </div>

      <!-- Profile Photo -->
      <div class="mb-4">
        <label class="block font-medium text-gray-800 dark:text-gray-200">Profile Photo</label>

        <div class="flex items-center gap-3">
          <!-- Preview -->
          <img
            v-if="previewPhoto"
            :src="previewPhoto"
            alt="Profile Preview"
            class="w-16 h-16 rounded-full object-cover border"
          />
          <img
            v-else-if="user.profile_photo_path"
            :src="`/storage/${user.profile_photo_path}`"
            alt="Current Photo"
            class="w-16 h-16 rounded-full object-cover border"
          />

          <input
            type="file"
            @change="handlePhotoUpload"
            accept="image/*"
            class="border p-1 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700"
          />
        </div>

        <div v-if="form.errors.profile_photo" class="text-red-500">{{ form.errors.profile_photo }}</div>
      </div>

      <!-- Actions -->
      <div class="flex gap-2 mt-4">
        <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-900">
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
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
import { ref, computed } from 'vue'

defineOptions({ layout: MainLayout })

const props = usePage().props.value
const user = props.user
const flash = props.flash || {}

const previewPhoto = ref(null)

// Create form
const form = useForm({
   _method: 'PUT',
  name: user.name || '',
  email: user.email || '',
  password: '',
  password_confirmation: '',
  profile_photo: null,
})

// Password match check
const passwordMismatch = computed(() => {
  return form.password && form.password_confirmation && form.password !== form.password_confirmation
})

// Handle photo upload
function handlePhotoUpload(event) {
  const file = event.target.files[0]
  if (file) {
    form.profile_photo = file
    previewPhoto.value = URL.createObjectURL(file)
  }
}

// Update profile
function updateProfile() {
  if (passwordMismatch.value) {
    return
  }

  form.post(route('user-profile-information.update'), {
    preserveScroll: true,
    forceFormData: true,
  })
}

// Cancel button
function goBack() {
  Inertia.get('/')
}
</script>
