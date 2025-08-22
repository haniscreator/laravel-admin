<script setup>
import AuthenticatedLayout from "@/Layouts/MainLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Album, Box } from "lucide-vue-next";

defineProps({ albums: Number, items: Number });
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #default>
            <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">
                Admin Dashboard
            </h1>

            <!-- Albums & Items Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Albums Card -->
                <div
                    class="flex items-center justify-between p-6 rounded-2xl shadow-md bg-white dark:bg-gray-800 transition-colors"
                >
                    <div>
                        <h2
                            class="text-xl font-medium text-gray-600 dark:text-gray-300"
                        >
                            Total Albums
                        </h2>
                        <p
                            class="text-3xl font-bold mt-2 text-gray-900 dark:text-white"
                        >
                            {{ albums }}
                        </p>
                    </div>
                    <div
                        class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300"
                    >
                        <Album class="w-8 h-8" />
                    </div>
                </div>

                <!-- Items Card -->
                <div
                    class="flex items-center justify-between p-6 rounded-2xl shadow-md bg-white dark:bg-gray-800 transition-colors"
                >
                    <div>
                        <h2
                            class="text-xl font-medium text-gray-600 dark:text-gray-300"
                        >
                            Total Items
                        </h2>
                        <p
                            class="text-3xl font-bold mt-2 text-gray-900 dark:text-white"
                        >
                            {{ items }}
                        </p>
                    </div>
                    <div
                        class="p-3 rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300"
                    >
                        <Box class="w-8 h-8" />
                    </div>
                </div>
            </div>

            <!-- Full Width Congrats Card -->
            <div
                class="mt-8 flex flex-col md:flex-row items-center justify-between p-8 rounded-2xl shadow-md bg-white dark:bg-gray-800 transition-colors w-full"
            >
                <!-- Left Side Text -->
                <div class="max-w-xl">
                    <h2
                        class="text-xl font-bold text-purple-600 dark:text-purple-400 mb-2"
                    >
                        🎉 Congratulations Team!
                    </h2>
                    <p class="text-gray-700 dark:text-gray-300 text-base mb-4">
                        We’re not just
                        <span class="font-semibold">building a product</span>,
                        we're building progress.
                        <br />
                        Keep pushing forward—your effort today
                    </p>
                    <button
                        class="px-5 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition-colors"
                    >
                        Cheers!
                    </button>
                </div>

                <!-- Right Side Image -->
                <div class="mt-6 md:mt-0 md:ml-6">
                    <img
                        src="/images/admin.png"
                        alt="Congratulations"
                        class="w-64 h-auto"
                    />
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
