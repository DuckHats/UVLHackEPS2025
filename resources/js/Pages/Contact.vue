<script setup>
import GameLayout from '@/Layouts/GameLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const form = useForm({
    name: '',
    email: '',
    message: '',
});

const submit = () => {
    // In a real app, this would submit to a backend route
    console.log('Sending raven...', form);
    form.reset();
    alert('Your raven has been sent to the Maesters!');
};
</script>

<template>
    <GameLayout>
        <div class="min-h-[calc(100vh-80px)] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8 relative overflow-hidden">
            <!-- Background decorative elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-yellow-700/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-gray-700/30 rounded-full blur-3xl"></div>
            </div>

            <div 
                class="max-w-xl w-full space-y-8 relative z-10"
                v-motion
                :initial="{ opacity: 0, y: 50 }"
                :enter="{ opacity: 1, y: 0, transition: { duration: 800, type: 'spring' } }"
            >
                <div class="text-center space-y-2">
                    <div class="flex items-center justify-center gap-2 mb-4">
                        <span class="h-[1px] w-8 bg-yellow-500/50"></span>
                        <h2 class="text-yellow-500 font-cinzel text-sm uppercase tracking-[0.3em]">Send a Raven</h2>
                        <span class="h-[1px] w-8 bg-yellow-500/50"></span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white font-cinzel drop-shadow-lg">Contact the Maesters</h1>
                    <p class="text-gray-400 font-lato text-lg max-w-md mx-auto leading-relaxed">
                        Have questions about your realm placement? Or perhaps you wish to challenge the verdict? 
                        Send us your missive below.
                    </p>
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-6 medieval-card p-8 md:p-10">
                    <!-- Decorative texture overlay for form -->
                    <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/dark-leather.png');"></div>
                    
                    <div class="relative z-10 space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-bold text-yellow-500 font-cinzel tracking-widest mb-2">Your Name</label>
                            <div class="mt-1">
                                <input id="name" v-model="form.name" name="name" type="text" required 
                                    class="appearance-none block w-full px-4 py-3 border border-gray-600 rounded-lg shadow-sm placeholder-gray-500 bg-gray-900/80 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent sm:text-sm transition-all duration-300 hover:border-yellow-700/50" 
                                    placeholder="Jon Snow"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-yellow-500 font-cinzel tracking-widest mb-2">Email Address</label>
                            <div class="mt-1">
                                <input id="email" v-model="form.email" name="email" type="email" required 
                                    class="appearance-none block w-full px-4 py-3 border border-gray-600 rounded-lg shadow-sm placeholder-gray-500 bg-gray-900/80 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent sm:text-sm transition-all duration-300 hover:border-yellow-700/50" 
                                    placeholder="jon@winterfell.com"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-bold text-yellow-500 font-cinzel tracking-widest mb-2">Message</label>
                            <div class="mt-1">
                                <textarea id="message" v-model="form.message" name="message" rows="5" required 
                                    class="appearance-none block w-full px-4 py-3 border border-gray-600 rounded-lg shadow-sm placeholder-gray-500 bg-gray-900/80 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent sm:text-sm transition-all duration-300 hover:border-yellow-700/50" 
                                    placeholder="Winter is coming..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 pt-4">
                        <button type="submit" 
                            class="medieval-btn group relative w-full flex justify-center"
                            :disabled="form.processing"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-yellow-300 group-hover:text-white transition-colors duration-300 transform group-hover:translate-x-1 group-hover:-translate-y-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                </svg>
                            </span>
                            Send Raven
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </GameLayout>
</template>
