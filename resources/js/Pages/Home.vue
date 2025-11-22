<script setup>
import GameLayout from '@/Layouts/GameLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const form = useForm({
    prompt: '',
});

const submit = () => {
    form.post('/analyze');
};
</script>

<template>
    <GameLayout>
        <div class="max-w-4xl mx-auto px-4 py-16 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-cinzel font-bold text-yellow-500 mb-6 tracking-wider drop-shadow-lg">
                Who Are You in the Realm?
            </h1>
            <p class="text-xl text-gray-300 mb-12 font-lato max-w-2xl mx-auto leading-relaxed">
                Tell us your story. Are you a conqueror seeking power, a scholar seeking silence, or a wolf seeking the pack? 
                The Old Gods and the New shall decide your dwelling.
            </p>

            <div class="bg-gray-800/50 p-8 rounded-lg border border-yellow-700/50 shadow-2xl backdrop-blur-sm relative overflow-hidden group">
                <!-- Decorative corners -->
                <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-yellow-500"></div>
                <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-yellow-500"></div>
                <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-yellow-500"></div>
                <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-yellow-500"></div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="relative">
                        <textarea
                            v-model="form.prompt"
                            rows="6"
                            class="w-full bg-gray-900/80 border border-gray-700 text-gray-100 rounded-md p-4 focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition font-lato text-lg placeholder-gray-600"
                            placeholder="I am Daenerys Stormborn, looking for a place with community and dragons..."
                        ></textarea>
                    </div>

                    <div class="flex justify-center">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 bg-gradient-to-r from-yellow-700 to-yellow-600 text-white font-cinzel font-bold text-lg rounded shadow-lg hover:from-yellow-600 hover:to-yellow-500 transform hover:scale-105 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed border border-yellow-400/30"
                        >
                            Find My Domain
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div v-if="form.processing" class="fixed inset-0 bg-black/90 z-50 flex flex-col items-center justify-center backdrop-blur-md">
            <div class="relative w-24 h-24 mb-8">
                <div class="absolute inset-0 border-4 border-yellow-900/30 rounded-full"></div>
                <div class="absolute inset-0 border-t-4 border-yellow-500 rounded-full animate-spin"></div>
            </div>
            <h2 class="text-3xl font-cinzel text-yellow-500 animate-pulse mb-4">Consulting the Maesters...</h2>
            <p class="text-gray-400 font-lato italic">The ravens are flying to the Citadel.</p>
        </div>
    </GameLayout>
</template>
