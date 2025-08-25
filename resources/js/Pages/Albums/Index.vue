<template>
    <div>
        <div class="space-y-4 p-4">
            <!-- Row 1: Title + Flash Messages -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2"
            >
                <h1 class="text-2xl font-bold dark:text-gray-200">Albums</h1>

                <div
                    class="flex flex-col sm:flex-row sm:justify-center items-center gap-2 w-full"
                >
                    <!-- Laravel Flash Messages -->
                    <div
                        v-if="globalSuccess"
                        class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-4 py-2 rounded text-center"
                    >
                        {{ globalSuccess }}
                    </div>

                    <div
                        v-if="globalError"
                        class="bg-red-100 text-red-800 px-4 py-2 rounded text-center"
                    >
                        {{ globalError }}
                    </div>

                    <!-- CSV Import Messages -->
                    <div
                        v-if="successMessage"
                        class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-4 py-2 rounded text-center"
                    >
                        {{ successMessage }}
                    </div>

                    <div
                        v-if="errorMessage"
                        class="bg-red-100 text-red-800 px-4 py-2 rounded text-center"
                    >
                        {{ errorMessage }}
                    </div>
                </div>
            </div>

            <!-- Row 2: Filters + Actions -->
            <div class="flex flex-wrap items-center justify-end gap-2 mb-12">
                <!-- Search -->
                <input
                    v-model="filters.keyword"
                    @input="search"
                    type="text"
                    placeholder="Search albums..."
                    class="border px-3 py-2 rounded w-64"
                />

                <!-- Status Dropdown -->
                <select
                    v-model="filters.status"
                    @change="search"
                    class="appearance-none border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-3 py-2 pr-8 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="0">In-Active</option>
                </select>

                <!-- Reset -->
                <button
                    @click="resetSearch"
                    class="text-white px-4 py-2 rounded bg-gray-700 hover:bg-gray-900"
                >
                    Reset
                </button>

                <!-- Add New -->
                <button
                    @click="goToCreateForm"
                    class="text-white px-4 py-2 rounded bg-gray-700 hover:bg-gray-900"
                >
                    Add New
                </button>

                <!-- Import -->
                <input
                    type="file"
                    ref="csvInput"
                    @change="importCsv"
                    accept=".csv"
                    class="hidden"
                />
                <button
                    @click="$refs.csvInput.click()"
                    class="text-white px-4 py-2 rounded bg-gray-700 hover:bg-gray-900"
                >
                    Import
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table
                class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-gray-200"
            >
                <thead
                    class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                >
                    <tr>
                        <th
                            @click="sortBy('id')"
                            class="cursor-pointer px-4 py-2 whitespace-nowrap text-left"
                        >
                            ID
                            <span class="ml-1">
                                <span v-if="sort.field === 'id'">
                                    {{ sort.direction === "asc" ? "▲" : "▼" }}
                                </span>
                                <span v-else class="text-gray-400">▲▼</span>
                            </span>
                        </th>
                        <th
                            @click="sortBy('name')"
                            class="cursor-pointer px-4 py-2 whitespace-nowrap text-left"
                        >
                            Name
                            <span class="ml-1">
                                <span v-if="sort.field === 'name'">
                                    {{ sort.direction === "asc" ? "▲" : "▼" }}
                                </span>
                                <span v-else class="text-gray-400">▲▼</span>
                            </span>
                        </th>
                        <th
                            @click="sortBy('description')"
                            class="cursor-pointer px-4 py-2 whitespace-nowrap text-left"
                        >
                            Description
                            <span class="ml-1">
                                <span v-if="sort.field === 'description'">
                                    {{ sort.direction === "asc" ? "▲" : "▼" }}
                                </span>
                                <span v-else class="text-gray-400">▲▼</span>
                            </span>
                        </th>
                        <th
                            @click="sortBy('location')"
                            class="cursor-pointer px-4 py-2 whitespace-nowrap text-left"
                        >
                            Location
                            <span class="ml-1">
                                <span v-if="sort.field === 'location'">
                                    {{ sort.direction === "asc" ? "▲" : "▼" }}
                                </span>
                                <span v-else class="text-gray-400">▲▼</span>
                            </span>
                        </th>
                        <th class="text-left py-2 px-4">Keywords</th>
                        <th class="text-left py-2 px-4">Status</th>
                        <th class="px-4 py-2">Actions</th>

                        <!-- Repeat for other headers -->
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="album in albums.data"
                        :key="album.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        <td
                            class="py-2 px-4 border border-gray-200 dark:border-gray-600"
                        >
                            {{ album.id }}
                        </td>
                        <td
                            class="py-2 px-4 border border-gray-200 dark:border-gray-600"
                        >
                            <span>{{ album.name }}</span>
                            <span
                                v-if="album.items_count > 0"
                                class="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-red-300 text-red-800 dark:bg-red-900 dark:text-blue-200"
                            >
                                {{ album.items_count }}
                            </span>
                        </td>

                        <td
                            class="py-2 px-4 border border-gray-200 dark:border-gray-600"
                        >
                            {{
                                album.description.length > 100
                                    ? album.description.slice(0, 100) + "..."
                                    : album.description
                            }}
                        </td>
                        <td
                            class="py-2 px-4 border border-gray-200 dark:border-gray-600"
                        >
                            {{ album.location }}
                        </td>
                        <td
                            class="py-2 px-4 border border-gray-200 dark:border-gray-600"
                        >
                            <div
                                v-if="
                                    album.keyword && album.keyword.trim() !== ''
                                "
                                class="flex flex-wrap gap-1"
                            >
                                <span
                                    v-for="(tag, index) in album.keyword
                                        .split(',')
                                        .map((k) => k.trim())
                                        .filter((k) => k)"
                                    :key="index"
                                    class="inline-block bg-yellow-200 dark:bg-yellow-600 text-gray-800 dark:text-white text-xs font-medium px-2 py-0.5 rounded"
                                >
                                    {{ tag }}
                                </span>
                            </div>
                        </td>

                        <td
                            class="py-2 px-4 border border-gray-200 dark:border-gray-600 text-center"
                        >
                            <input
                                type="checkbox"
                                :checked="album.status === 1"
                                @change="
                                    toggleStatus(
                                        album.id,
                                        $event.target.checked
                                    )
                                "
                                class="toggle-checkbox hidden"
                                :id="'toggle-' + album.id"
                            />
                            <label
                                :for="'toggle-' + album.id"
                                class="toggle-label block w-10 h-5 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer relative"
                            >
                                <span
                                    class="dot absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition"
                                    :class="{
                                        'translate-x-5': album.status === 1,
                                    }"
                                ></span>
                            </label>
                        </td>

                        <td
                            class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-center"
                        >
                            <button
                                @click="editAlbum(album.id)"
                                class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-400 mr-2"
                                title="Edit"
                            >
                                <i class="fas fa-edit"></i>
                            </button>

                            <button
                                v-if="can('album.delete')"
                                @click="deleteAlbum(album.id)"
                                class="text-red-500 hover:text-red-700 dark:hover:text-red-400"
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
                class="mr-2 px-4 py-2 rounded border text-sm font-medium bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Prev
            </button>

            <button
                :disabled="!albums.next_page_url"
                @click="goToPage(albums.current_page + 1)"
                class="px-4 py-2 rounded border text-sm font-medium bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Next
            </button>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { ref, watch, computed, reactive, watchEffect } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";

const successMessage = ref("");
const errorMessage = ref("");

const page = usePage();
const albums = computed(() => usePage().props.value.albums);
const props = usePage().props.value;
const permissions = computed(
    () => page.props.value?.auth?.user.all_permissions || []
);

// Fallback to {} if flash is undefined
const flash = page.props.value.flash || {};

// Local reactive variables for global Laravel flash messages
const globalSuccess = ref(flash.success || "");
const globalError = ref(flash.error || "");

const filters = ref({
    keyword: page.props.filters?.keyword || "",
    status: page.props.filters?.status || "", // Default to "All"
});

const sort = reactive({
    field: page.props.filters?.sort || "id",
    direction: page.props.filters?.direction || "desc",
});

function sortBy(field) {
    if (sort.field === field) {
        sort.direction = sort.direction === "asc" ? "desc" : "asc";
    } else {
        sort.field = field;
        sort.direction = "asc";
    }

    Inertia.get(
        "/albums",
        {
            keyword: filters.value.keyword,
            sort: sort.field,
            direction: sort.direction,
            page: albums.value.current_page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}

function goToPage(page) {
    Inertia.get(
        "/albums",
        {
            page,
            sort: sort.field,
            direction: sort.direction,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}

function search() {
    Inertia.get(
        "/albums",
        {
            keyword: filters.value.keyword,
            status: filters.value.status,
            sort: sort.field,
            direction: sort.direction,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}

function resetSearch() {
    filters.value.keyword = "";
    filters.value.status = "";
    search();
}

function editAlbum(id) {
    Inertia.get(`/albums/${id}/edit`);
}

function deleteAlbum(id) {
    if (confirm("Are you sure you want to delete this album?")) {
        Inertia.delete(`/albums/${id}`);
    }
}

function goToCreateForm() {
    Inertia.get("/albums/create");
}

function toggleStatus(albumId) {
    Inertia.put(
        `/albums/${albumId}/toggle-status`,
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
}

// --- NEW functions for CSV import ---

function importCsv(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("csv", file);

    // Reset messages
    successMessage.value = "";
    errorMessage.value = "";

    axios
        .post("/albums/import", formData)
        .then(({ data }) => {
            if (data.success) {
                showMessage("success", data.message);
                refreshList();
            } else {
                showMessage("error", data.message);
            }
        })
        .catch((err) => {
            showMessage(
                "error",
                err.response?.data?.message || "Import failed."
            );
        });
}

function showMessage(type, message) {
    if (type === "success") {
        successMessage.value = message;
        setTimeout(() => {
            successMessage.value = "";
        }, 15000);
    } else if (type === "error") {
        errorMessage.value = message;
        setTimeout(() => {
            errorMessage.value = "";
        }, 10000);
    }
}

function refreshList() {
    Inertia.get("/albums", {}, { preserveState: true, preserveScroll: true });
}

// Permission Checking
function can(permission) {
    return permissions.value.includes(permission);
}

watch(
    () => usePage().props.value.albums,
    (newVal) => {
        albums.value = newVal;
    }
);

watchEffect(() => {
    if (page.props.value.flash?.success) {
        globalSuccess.value = page.props.value.flash.success;

        setTimeout(() => {
            globalSuccess.value = "";
        }, 5000);
    }
});

watchEffect(() => {
    if (page.props.value.flash?.error) {
        globalError.value = page.props.value.flash.error;

        setTimeout(() => {
            globalError.value = "";
        }, 8000);
    }
});
</script>

<script>
import MainLayout from "@/Layouts/MainLayout.vue";

export default {
    layout: MainLayout,
};
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
