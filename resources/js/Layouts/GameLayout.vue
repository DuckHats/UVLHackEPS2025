<script setup>
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-900 text-gray-100 font-lato relative overflow-hidden">
        <!-- Background Texture Overlay -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/dark-matter.png');"></div>

        <!-- Header -->
        <header class="relative z-10 border-b border-yellow-700/30 bg-gray-900/80 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <div class="flex items-center">
                    <Link href="/" class="text-2xl font-cinzel font-bold text-yellow-500 tracking-widest hover:text-yellow-400 transition">
                        GAME OF NEIGHBORHOODS
                    </Link>
                </div>
                <nav class="hidden md:flex space-x-8">
                    <!--<a href="#" class="text-gray-400 hover:text-yellow-500 transition font-cinzel text-sm tracking-wider">The Realm</a>
                    <a href="#" class="text-gray-400 hover:text-yellow-500 transition font-cinzel text-sm tracking-wider">Houses</a>-->
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-yellow-700/30 mt-20 bg-gray-900/80">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-gray-500 text-sm font-cinzel">
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
