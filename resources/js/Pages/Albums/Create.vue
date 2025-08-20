<template>
    <div class="max-w-2xl mx-auto py-8 shadow p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">
            Create New Album
        </h1>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200"
                    >Name</label
                >
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full border rounded px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
                />
                <div v-if="form.errors.name" class="text-red-500">
                    {{ form.errors.name }}
                </div>
            </div>

            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200"
                    >Description</label
                >
                <textarea
                    v-model="form.description"
                    class="w-full border rounded px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500">
                    {{ form.errors.description }}
                </div>
            </div>

            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200"
                    >Location</label
                >
                <input
                    v-model="form.location"
                    type="text"
                    class="w-full border rounded px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
                />
                <div v-if="form.errors.location" class="text-red-500">
                    {{ form.errors.location }}
                </div>
            </div>

            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200"
                    >Keyword</label
                >
                <input
                    v-model="form.keyword"
                    type="text"
                    class="w-full border rounded px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
                />
                <div v-if="form.errors.keyword" class="text-red-500">
                    {{ form.errors.keyword }}
                </div>
            </div>

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

            <div class="mb-4">
                <label
                    class="block font-medium text-gray-800 dark:text-gray-200 mb-1"
                    >Image</label
                >
                <div class="flex items-center gap-3">
                    <label
                        class="inline-flex items-center gap-2 cursor-pointer px-4 py-2 rounded text-sm font-semibold bg-gray-700 text-white hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-900"
                    >
                        <i class="fas fa-upload"></i>
                        <!-- Font Awesome icon -->
                        Upload Image
                        <input
                            type="file"
                            @change="handleFileUpload"
                            accept=".jpg,.jpeg,.png"
                            class="hidden"
                        />
                    </label>

                    <span
                        class="text-sm text-gray-700 dark:text-gray-300"
                        v-if="selectedImageName"
                    >
                        {{ selectedImageName }}
                    </span>
                </div>
                <div v-if="form.errors.image" class="text-red-500 mt-1">
                    {{ form.errors.image }}
                </div>
            </div>

            <div class="flex space-x-2">
                <button
                    type="submit"
                    class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-900 border border-transparent"
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
import { useForm } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { ref } from "vue";

const selectedImageName = ref("");
const newImage = ref(null);

const form = useForm({
    name: "",
    description: "",
    location: "",
    keyword: "",
    status: false,
    image: null,
});

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const allowedTypes = ["image/jpeg", "image/png"];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!allowedTypes.includes(file.type)) {
            form.errors.image = "Only JPG and PNG images are allowed.";
            return;
        }

        if (file.size > maxSize) {
            form.errors.image = "Image must be less than 2MB.";
            return;
        }

        form.errors.image = null;
        newImage.value = file;
        selectedImageName.value = file.name;
        form.image = file;
    } else {
        selectedImageName.value = "";
        form.image = null;
    }
}

function submit() {
    const formData = new FormData();
    formData.append("name", form.name);
    formData.append("description", form.description);
    formData.append("location", form.location);
    formData.append("keyword", form.keyword);
    formData.append("status", form.status ? 1 : 0);

    if (form.image) {
        formData.append("image", form.image);
    }

    form.post("/albums", {
        preserveScroll: true,
        forceFormData: true,
        data: formData,
    });
}

function goBack() {
    Inertia.get("/albums");
}
</script>

<script>
import MainLayout from "@/Layouts/MainLayout.vue";

export default {
    layout: MainLayout,
};
</script>
