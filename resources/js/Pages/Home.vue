<script setup>
import GameLayout from "@/Layouts/GameLayout.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import { ref, onMounted } from "vue";

const form = useForm({
    prompt: "",
});

const isRecording = ref(false);
let recognition = null;
const selectedLanguage = ref("en-US");

onMounted(() => {
    const browserLang = navigator.language || navigator.userLanguage;
    if (browserLang.startsWith("es")) {
        selectedLanguage.value = "es-ES";
    } else if (browserLang.startsWith("ca")) {
        selectedLanguage.value = "ca-ES";
    } else {
        selectedLanguage.value = "en-US";
    }
});

const toggleRecording = () => {
    if (isRecording.value) {
        recognition.stop();
        isRecording.value = false;
    } else {
        const SpeechRecognition =
            window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            alert(
                "Your browser does not support speech recognition. Please try Chrome, Edge or Safari."
            );
            return;
        }

        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = selectedLanguage.value;

        recognition.onstart = () => {
            isRecording.value = true;
        };

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            form.prompt += (form.prompt ? " " : "") + transcript;
        };

        recognition.onerror = (event) => {
            console.error("Speech recognition error", event.error);
            isRecording.value = false;
        };

        recognition.onend = () => {
            isRecording.value = false;
        };

        recognition.start();
    }
};

const submit = () => {
    form.post("/analyze");
};
</script>

<template>
    <GameLayout>
        <div
            class="max-w-5xl mx-auto px-4 py-16 sm:px-6 lg:px-8 text-center relative z-10"
        >
            <!-- Hero Section -->
            <div
                v-motion
                :initial="{ opacity: 0, y: 30 }"
                :enter="{
                    opacity: 1,
                    y: 0,
                    transition: { duration: 800, type: 'spring' },
                }"
                class="mb-16"
            >
                <div class="flex justify-center mb-6">
                    <div
                        class="w-24 h-1 bg-gradient-to-r from-transparent via-yellow-500 to-transparent opacity-50"
                    ></div>
                </div>
                <h1
                    class="text-5xl md:text-7xl font-cinzel font-bold text-white mb-6 tracking-tight drop-shadow-2xl"
                >
                    Who Are You in the
                    <span class="text-yellow-500">Realm</span>?
                </h1>
                <p
                    class="text-xl md:text-2xl text-gray-300 mb-8 font-lato max-w-3xl mx-auto leading-relaxed font-light"
                >
                    Tell us your story. Are you a conqueror seeking power, a
                    scholar seeking silence, or a wolf seeking the pack? The Old
                    Gods and the New shall decide your dwelling.
                </p>
                <div class="flex justify-center">
                    <div
                        class="w-24 h-1 bg-gradient-to-r from-transparent via-yellow-500 to-transparent opacity-50"
                    ></div>
                </div>
            </div>

            <!-- Input Section -->
            <div
                class="medieval-card p-8 md:p-12 group max-w-4xl mx-auto"
                v-motion
                :initial="{ opacity: 0, y: 50 }"
                :enter="{
                    opacity: 1,
                    y: 0,
                    transition: { duration: 800, delay: 200, type: 'spring' },
                }"
            >
                <!-- Decorative background texture -->
                <div
                    class="absolute inset-0 opacity-5 pointer-events-none"
                    style="
                        background-image: url('https://www.transparenttextures.com/patterns/dark-leather.png');
                    "
                ></div>

                <!-- Decorative corners -->
                <div
                    class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-yellow-500/50"
                ></div>
                <div
                    class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-yellow-500/50"
                ></div>
                <div
                    class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-yellow-500/50"
                ></div>
                <div
                    class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-yellow-500/50"
                ></div>

                <form @submit.prevent="submit" class="space-y-8 relative z-10">
                    <div class="relative group">
                        <!-- Language Selector -->
                        <div class="absolute top-4 right-4 z-20">
                            <select
                                v-model="selectedLanguage"
                                class="bg-gray-900/80 text-yellow-500 border border-yellow-700/50 rounded-md text-xs font-cinzel uppercase tracking-wider py-1 px-2 focus:outline-none focus:ring-1 focus:ring-yellow-500 cursor-pointer hover:bg-gray-800 transition-colors"
                            >
                                <option value="en-US">English</option>
                                <option value="es-ES">Español</option>
                                <option value="ca-ES">Català</option>
                            </select>
                        </div>

                        <textarea
                            v-model="form.prompt"
                            rows="6"
                            class="w-full bg-gray-900/60 border border-gray-600 text-gray-100 rounded-lg p-6 focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-300 font-lato text-lg placeholder-gray-500 shadow-inner hover:bg-gray-900/80"
                            placeholder="I am Daenerys Stormborn, looking for a place with community and dragons..."
                        ></textarea>

                        <!-- Microphone Button -->
                        <button
                            type="button"
                            @click="toggleRecording"
                            class="absolute bottom-4 right-4 p-2 rounded-full transition-all duration-300 hover:bg-gray-700/50 focus:outline-none"
                            :class="{
                                'animate-pulse text-red-500': isRecording,
                                'text-gray-400 hover:text-yellow-500':
                                    !isRecording,
                            }"
                            title="Speak your truth"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
                                />
                            </svg>
                        </button>
                    </div>

                    <div class="flex justify-center">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="medieval-btn group relative overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="relative z-10 flex items-center gap-3">
                                Find My Domain
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 group-hover:translate-x-1 transition-transform"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"
                                    />
                                </svg>
                            </span>
                            <div
                                class="absolute inset-0 bg-yellow-400/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"
                            ></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div
            v-if="form.processing"
            class="fixed inset-0 bg-gray-900/95 z-50 flex flex-col items-center justify-center backdrop-blur-xl transition-opacity duration-500"
        >
            <div class="relative w-32 h-32 mb-8">
                <div
                    class="absolute inset-0 border-4 border-yellow-900/30 rounded-full"
                ></div>
                <div
                    class="absolute inset-0 border-t-4 border-yellow-500 rounded-full animate-spin"
                ></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-12 w-12 text-yellow-700 animate-pulse"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
            </div>
            <h2
                class="text-3xl md:text-4xl font-cinzel font-bold text-yellow-500 animate-pulse mb-4 tracking-widest"
            >
                Consulting the Maesters
            </h2>
            <p class="text-gray-400 font-lato text-lg italic">
                The ravens are flying to the Citadel...
            </p>
        </div>
    </GameLayout>
</template>
