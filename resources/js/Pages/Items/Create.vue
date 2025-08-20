<template>
    <div class="max-w-2xl mx-auto py-8 shadow p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">
            Create New Item
        </h1>

        <form @submit.prevent="submit">
            <!-- Album Selection -->
            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200"
                    >Album</label
                >
                <select
                    v-model="form.album_id"
                    class="w-full border rounded px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
                >
                    <option value="" disabled>Select an album</option>
                    <option
                        v-for="album in albums"
                        :key="album.id"
                        :value="album.id"
                    >
                        {{ album.name }}
                    </option>
                </select>
                <div v-if="form.errors.album_id" class="text-red-500">
                    {{ form.errors.album_id }}
                </div>
            </div>

            <!-- Name -->
            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200"
                    >Name</label
                >
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700"
                />
                <div v-if="form.errors.name" class="text-red-500">
                    {{ form.errors.name }}
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200"
                    >Description</label
                >
                <textarea
                    v-model="form.description"
                    class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500">
                    {{ form.errors.description }}
                </div>
            </div>

            <!-- Keyword -->
            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200"
                    >Keyword</label
                >
                <input
                    v-model="form.keyword"
                    type="text"
                    class="w-full border rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700"
                />
                <div v-if="form.errors.keyword" class="text-red-500">
                    {{ form.errors.keyword }}
                </div>
            </div>

            <!-- Status Checkbox -->
            <div class="mb-4 flex items-center">
                <input
                    v-model="form.status"
                    type="checkbox"
                    id="status"
                    class="mr-2"
                />
                <label
                    for="status"
                    class="font-medium text-gray-800 dark:text-gray-200"
                    >Active</label
                >
            </div>

            <!-- Media Upload -->
            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200 mb-1"
                    >Media File</label
                >
                <div class="flex items-center gap-3">
                    <label
                        class="inline-flex items-center gap-2 cursor-pointer px-4 py-2 rounded text-sm font-semibold bg-gray-700 text-white hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-900"
                    >
                        <i class="fas fa-upload"></i>
                        Upload Media
                        <input
                            type="file"
                            @change="handleMediaUpload"
                            accept="audio/*"
                            class="hidden"
                        />
                    </label>

                    <span
                        class="text-sm text-gray-700 dark:text-gray-300"
                        v-if="selectedMediaName"
                    >
                        {{ selectedMediaName }}
                    </span>
                </div>
                <div v-if="form.errors.media_url" class="text-red-500 mt-1">
                    {{ form.errors.media_url }}
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <button
                    type="submit"
                    class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-900"
                >
                    Save
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
import MainLayout from "@/Layouts/MainLayout.vue";
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { ref } from "vue";

defineOptions({ layout: MainLayout });

const selectedMediaName = ref("");
const newMedia = ref(null);

const page = usePage();

const props = usePage().props.value;
const albums = props.albums || [];

const form = useForm({
    album_id: "",
    name: "",
    description: "",
    keyword: "",
    status: false,
    media_url: null,
});

function handleMediaUpload(event) {
    const file = event.target.files[0];
    form.errors.media_url = null; // reset before check

    if (!file) {
        selectedMediaName.value = "";
        form.media_url = null;
        return;
    }

    const allowedTypes = ["audio/mpeg", "audio/wav", "audio/ogg"];
    if (!allowedTypes.includes(file.type)) {
        form.errors.media_url = "Only MP3, WAV, or OGG files are allowed.";
        selectedMediaName.value = "";
        form.media_url = null;
        return;
    }

    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
        form.errors.media_url = "File size must not exceed 10MB.";
        selectedMediaName.value = "";
        form.media_url = null;
        return;
    }

    // Valid
    newMedia.value = file;
    selectedMediaName.value = file.name;
    form.media_url = file;
}

function submit() {
    const formData = new FormData();
    formData.append("album_id", form.album_id);
    formData.append("name", form.name);
    formData.append("description", form.description);
    formData.append("keyword", form.keyword);
    formData.append("status", form.status ? 1 : 0);

    if (form.media_url) {
        formData.append("media_url", form.media_url);
    }

    form.post("/items", {
        preserveScroll: true,
        forceFormData: true,
        data: formData,
    });
}

function goBack() {
    Inertia.get("/items");
}
</script>
