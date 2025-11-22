<script setup>
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-900 text-gray-100 font-lato relative overflow-hidden selection:bg-yellow-500 selection:text-gray-900">
        <!-- Background Texture Overlay -->
        <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/dark-leather.png');"></div>
        
        <!-- Header -->
        <header class="relative z-10 border-b border-yellow-700/30 bg-gray-900/90 backdrop-blur-md shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-24 flex items-center justify-between">
                <div class="flex items-center gap-4 group">
                    <!-- Logo Icon -->
                    <div class="w-10 h-10 bg-yellow-700/20 rounded-full flex items-center justify-center border border-yellow-700/50 group-hover:border-yellow-500 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <Link href="/" class="text-2xl md:text-3xl font-cinzel font-bold text-yellow-500 tracking-widest hover:text-yellow-400 transition drop-shadow-md">
                        GAME OF NEIGHBORHOODS
                    </Link>
                </div>
                <nav class="hidden md:flex space-x-8">
                    <!-- Navigation items can go here -->
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-yellow-700/30 mt-20 bg-gray-900/90 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex justify-center items-center gap-2 mb-4">
                    <span class="h-[1px] w-12 bg-yellow-700/50"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="h-[1px] w-12 bg-yellow-700/50"></span>
                </div>
                <p class="text-gray-500 text-sm font-cinzel tracking-wider">
                    &copy; With love by DuckHats
                </p>
            </div>
        </footer>
    </div>
</template>

<style>
.font-cinzel {
    font-family: 'Cinzel', serif;
}
.font-lato {
    font-family: 'Lato', sans-serif;
}
</style>
