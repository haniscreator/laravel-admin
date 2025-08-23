<template>
    <div
        class="min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-white flex flex-col justify-between relative overflow-hidden"
    >
        <!-- Decorative Images -->
        <img
            src="/images/left.png"
            alt="Top Left Decoration"
            class="absolute top-0 left-0 w-36 lg:w-48 z-0"
        />
        <img
            src="/images/right.png"
            alt="Bottom Right Decoration"
            class="absolute bottom-0 right-0 w-36 lg:w-48 z-0"
        />

        <!-- Top Right Login Button -->
        <div class="absolute top-6 right-6">
            <Link
                href="/login"
                class="text-sm font-semibold px-4 py-2 rounded bg-[#202430] text-white hover:bg-[#333740] transition"
            >
                Login
            </Link>
        </div>

        <!-- Main Content -->
        <div
            class="flex flex-col lg:flex-row items-center justify-center px-8 pt-48 pb-12 gap-28"
        >
            <!-- Left Section -->
            <div
                class="w-full lg:w-auto max-w-md space-y-4 text-center lg:text-left"
            >
                <!-- Centered Logo + Text Group -->
                <div class="flex flex-col items-center">
                    <img
                        src="/images/logo5.png"
                        alt="Logo"
                        class="w-32 h-32 animate-bounce"
                    />
                    <p class="text-3xl font-medium mt-2">Laravel Admin Panel</p>
                    <p
                        class="text-lg font-medium text-gray-700 dark:text-gray-300 mt-1"
                    >
                        Build with Love 🧡
                    </p>
                </div>

                <!-- Tags -->
                <div
                    class="mt-6 flex flex-wrap justify-center lg:justify-start gap-2 text-xs text-gray-600 dark:text-gray-400"
                >
                    <span class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded"
                        >Dark Mode</span
                    >
                    <span class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded"
                        >Tailwind UI</span
                    >
                    <span class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded"
                        >Inertia Powered</span
                    >
                </div>
            </div>

            <!-- Right Section -->
            <div class="w-full lg:w-auto">
                <img
                    src="/images/modify.png"
                    alt="Preview"
                    class="w-full max-w-md rounded-xl shadow-lg mx-auto"
                />
            </div>
        </div>

        <!-- Footer -->
        <div
            class="text-center text-sm text-gray-500 dark:text-gray-400 px-8 pb-6"
        >
            Created by <strong>KKLSYD</strong>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
</script>
