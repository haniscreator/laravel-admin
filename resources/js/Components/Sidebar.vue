<template>
    <!-- Sidebar -->
    <aside
        class="w-64 bg-[#202430] text-white flex flex-col justify-between h-screen p-4"
    >
        <div class="flex-1">
            <!-- Logo -->
            <div class="mb-6">
                <img src="/images/logo.png" alt="Logo" class="h-10 mx-auto" />
            </div>

            <!-- Menu Items -->
            <nav class="space-y-1 mt-6">
                <h2
                    class="text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider"
                >
                    Manage
                </h2>

                <Link
                    href="/dashboard"
                    class="flex items-center gap-3 px-3 py-2 rounded transition-all"
                    :class="
                        route().current('dashboard')
                            ? 'bg-white text-black'
                            : 'text-gray-400 hover:text-white hover:bg-gray-700'
                    "
                >
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </Link>

                <Link
                    :href="route('albums.index')"
                    class="flex items-center gap-3 px-3 py-2 rounded transition-all"
                    :class="
                        $page.url.startsWith('/albums')
                            ? 'bg-white text-black'
                            : 'text-gray-400 hover:text-white hover:bg-gray-700'
                    "
                >
                    <i class="fas fa-images"></i>
                    <span>Albums</span>
                </Link>

                <Link
                    href="/items"
                    class="flex items-center gap-3 px-3 py-2 rounded transition-all"
                    :class="
                        $page.url.startsWith('/items')
                            ? 'bg-white text-black'
                            : 'text-gray-400 hover:text-white hover:bg-gray-700'
                    "
                >
                    <i class="fas fa-box"></i>
                    <span>Items</span>
                </Link>
            </nav>
        </div>

        <!-- Dark/Light Mode Toggle -->
        <!-- Centered Toggle -->
        <div class="pt-6 flex justify-center">
            <DarkModeToggle />
        </div>
    </aside>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import DarkModeToggle from "@/Components/DarkModeToggle.vue";
</script>
