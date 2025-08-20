<template>
    <div>
        <div class="mb-4 space-y-4">
            <!-- Row 1: Title (left) + Flash (center) -->
            <div class="grid grid-cols-3 items-center">
                <!-- Left -->
                <div>
                    <h1 class="text-2xl font-bold dark:text-gray-200">Items</h1>
                </div>

                <!-- Center -->
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
                <!-- Right (empty for now) -->
                <div></div>
            </div>

            <!-- Row 2: Filters + Actions (Right aligned) -->
            <div class="flex flex-wrap justify-end items-center space-x-2">
                <input
                    v-model="filters.keyword"
                    @input="search"
                    type="text"
                    placeholder="Search items..."
                    class="border px-3 py-2 rounded w-64"
                />

                <!-- Album dropdown -->
                <select
                    v-model="filters.album_id"
                    @change="search"
                    class="border px-3 py-2 rounded max-w-xs"
                >
                    <option value="">All Albums</option>
                    <option
                        v-for="album in albums"
                        :key="album.id"
                        :value="album.id"
                    >
                        {{ album.name }}
                    </option>
                </select>

                <!-- Status Dropdown -->
                <select
                    v-model="filters.status"
                    @change="search"
                    class="border px-3 py-2 pr-8 rounded max-w-xs"
                >
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="0">In-Active</option>
                </select>

                <button
                    @click="resetSearch"
                    class="text-white px-4 py-2 rounded bg-gray-700 hover:bg-gray-900"
                >
                    Reset
                </button>

                <button
                    @click="goToCreateForm"
                    class="text-white px-4 py-2 rounded bg-gray-700 hover:bg-gray-900"
                >
                    Add New
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table
                class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 border-gray-200 dark:border-gray-600"
            >
                <thead>
                    <tr
                        class="bg-gray-100 dark:bg-gray-700 text-left text-gray-800 dark:text-white"
                    >
                        <th
                            @click="sortBy('id')"
                            class="cursor-pointer px-4 py-2 whitespace-nowrap"
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
                            class="cursor-pointer px-4 py-2 whitespace-nowrap"
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
                            class="cursor-pointer px-4 py-2 whitespace-nowrap"
                        >
                            Description
                            <span class="ml-1">
                                <span v-if="sort.field === 'description'">
                                    {{ sort.direction === "asc" ? "▲" : "▼" }}
                                </span>
                                <span v-else class="text-gray-400">▲▼</span>
                            </span>
                        </th>
                        <th class="px-4 py-2">Album</th>
                        <th class="px-4 py-2">Keyword</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="item in items.data"
                        :key="item.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-white"
                    >
                        <td class="table-cell">{{ item.id }}</td>
                        <td class="table-cell">{{ item.name }}</td>
                        <td class="table-cell">
                            {{ truncate(item.description) }}
                        </td>
                        <td class="table-cell">{{ item.album_name }}</td>
                        <td class="table-cell">
                            <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="(tag, index) in (
                                        item.keyword || ''
                                    ).split(',')"
                                    :key="index"
                                    class="inline-block bg-yellow-200 dark:bg-yellow-600 text-gray-800 dark:text-white text-xs font-medium px-2 py-0.5 rounded"
                                >
                                    {{ tag.trim() }}
                                </span>
                            </div>
                        </td>
                        <td
                            class="py-2 px-4 border text-center border-gray-200 dark:border-gray-600"
                        >
                            <input
                                type="checkbox"
                                :checked="item.status === 1"
                                @change="
                                    toggleStatus(item.id, $event.target.checked)
                                "
                                class="toggle-checkbox hidden"
                                :id="'toggle-' + item.id"
                            />
                            <label
                                :for="'toggle-' + item.id"
                                class="toggle-label block w-10 h-5 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer relative"
                            >
                                <span
                                    class="dot absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition"
                                    :class="{
                                        'translate-x-5': item.status === 1,
                                    }"
                                ></span>
                            </label>
                        </td>

                        <td
                            class="px-3 py-2 border text-center border-gray-200 dark:border-gray-600"
                        >
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
import { ref, reactive, computed, watch, watchEffect } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";

const page = usePage();
const items = computed(() => page.props.value.items);

const successMessage = ref("");
const errorMessage = ref("");
// Fallback to {} if flash is undefined
const flash = page.props.value.flash || {};
// Local reactive variables for global Laravel flash messages
const globalSuccess = ref(flash.success || "");
const globalError = ref(flash.error || "");

const props = defineProps({
    items: Object,
    albums: Array,
    filters: Object,
});

const filters = ref({
    keyword: page.props.value.filters?.keyword || "",
    album_id: props.filters.album_id || "",
    status: props.filters?.status ?? "",
});

const sort = reactive({
    field: page.props.value.filters?.sort || "id",
    direction: page.props.value.filters?.direction || "desc",
});

function truncate(text, length = 100) {
    return text.length > length ? text.slice(0, length) + "..." : text;
}

function search() {
    Inertia.get(
        "/items",
        {
            keyword: filters.value.keyword,
            album_id: filters.value.album_id,
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
    filters.value.album_id = "";
    filters.value.status = "";
    search();
}

function goToPage(page) {
    Inertia.get(
        "/items",
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

function sortBy(field) {
    if (sort.field === field) {
        // Toggle direction if clicking the same field
        sort.direction = sort.direction === "asc" ? "desc" : "asc";
    } else {
        // Change field, reset direction to ascending
        sort.field = field;
        sort.direction = "asc";
    }

    // Reload page with new sorting params
    Inertia.get(
        "/items",
        {
            sort: sort.field,
            direction: sort.direction,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}

function goToCreateForm() {
    Inertia.get("/items/create");
}

function editItem(id) {
    Inertia.get(`/items/${id}/edit`);
}

function deleteItem(id) {
    if (confirm("Are you sure you want to delete this item?")) {
        Inertia.delete(`/items/${id}`, {
            preserveScroll: true,
        });
    }
}

function toggleStatus(itemId) {
    Inertia.put(
        `/items/${itemId}/toggle-status`,
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
}

// Watch for changes in flash.success
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

.table-cell {
    padding: 0.5rem 1rem; /* px-4 py-2 */
    border-width: 1px; /* border */
    border-color: rgb(229 231 235); /* border-gray-200 */
}

.dark .table-cell {
    border-color: rgb(75 85 99); /* border-gray-600 */
}
</style>
