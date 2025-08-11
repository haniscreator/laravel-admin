<template>
  <div class="relative" ref="dropdownRef">
      <div
        @click="toggleDropdown"
        class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-full shadow-sm hover:shadow-md bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition"

      >
      <img
        v-if="userPhoto"
        :src="`/storage/${userPhoto}`"
        alt="User"
        class="h-7 w-7 rounded-full"
      />
      <img
        v-else
        src="/images/admin.png"
        alt="User"
        class="h-7 w-7 rounded-full"
      />
      <span class="text-sm text-gray-800 dark:text-gray-200 font-medium">
        {{ userName }}
      </span>
      <ChevronDownIcon
        :class="[
          'h-4 w-4 transition-transform duration-200',
          showDropdown ? 'rotate-180' : 'rotate-0',
          'text-gray-500 dark:text-gray-300'
        ]"
      />
    </div>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="showDropdown"
        class="absolute right-0 mt-2 w-44 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50"
      >
        <Link
          href="/profile"
          class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          <UserIcon class="h-4 w-4" />
          Profile
        </Link>

        <button
          @click="logout"
          class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          <ArrowLeftOnRectangleIcon class="h-4 w-4" />
          Logout
        </button>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia' // ✅ ADD THIS LINE
import { onClickOutside } from '@vueuse/core'
import { ChevronDownIcon, UserIcon, ArrowLeftOnRectangleIcon } from '@heroicons/vue/24/solid'

const showDropdown = ref(false)
const dropdownRef = ref(null)

onClickOutside(dropdownRef, () => {
  showDropdown.value = false
})

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

const page = usePage()
const user = computed(() => page.props.value?.auth?.user)
const userName = computed(() => user.value?.name ?? 'User')
const userPhoto = computed(() => user.value?.profile_photo_path)
 console.log('userPhoto:', userPhoto);

// ✅ CSRF is not needed for Inertia POST
const logout = () => {
  Inertia.post(route('logout'))
}
</script>

