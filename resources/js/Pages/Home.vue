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
    startLoadingAnimation();
};

// Interactive Loading State
const currentMessageIndex = ref(0);
const currentStep = ref(0);
const currentMessage = ref(0);

const loadingMessages = [
    "The ravens are flying to the Citadel...",
    "The Maesters are consulting ancient scrolls...",
    "Analyzing the Seven Kingdoms' archives...",
    "Deciphering your archetype from the Old Gods...",
    "Mapping the realms to your desires...",
    "The Grand Maester is reviewing your decree...",
    "Searching for your perfect stronghold...",
    "The Council is deliberating your fate...",
];

const loadingSteps = ["Reading Scroll", "Analyzing", "Mapping Realms"];

let messageInterval = null;
let stepInterval = null;

const startLoadingAnimation = () => {
    currentMessageIndex.value = 0;
    currentStep.value = 0;
    
    // Rotate messages every 3 seconds
    messageInterval = setInterval(() => {
        currentMessageIndex.value = (currentMessageIndex.value + 1) % loadingMessages.length;
        currentMessage.value++; // Trigger re-render
    }, 5000);
    
    // Progress through steps every 5 seconds
    stepInterval = setInterval(() => {
        if (currentStep.value < loadingSteps.length - 1) {
            currentStep.value++;
        }
    }, 10000);
};

// Cleanup intervals when component unmounts
onMounted(() => {
    const browserLang = navigator.language || navigator.userLanguage;
    if (browserLang.startsWith("es")) {
        selectedLanguage.value = "es-ES";
    } else if (browserLang.startsWith("ca")) {
        selectedLanguage.value = "ca-ES";
    } else {
        selectedLanguage.value = "en-US";
    }
    
    // Cleanup on unmount
    return () => {
        if (messageInterval) clearInterval(messageInterval);
        if (stepInterval) clearInterval(stepInterval);
    };
});
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

        <!-- Interactive Loading Overlay -->
        <div
            v-if="form.processing"
            class="fixed inset-0 bg-gray-900/95 z-50 flex flex-col items-center justify-center backdrop-blur-xl transition-opacity duration-500"
        >
            <!-- Animated Scroll -->
            <div class="relative w-40 h-40 mb-8">
                <div class="absolute inset-0 border-4 border-yellow-900/30 rounded-full"></div>
                <div class="absolute inset-0 border-t-4 border-yellow-500 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-16 w-16 text-yellow-500 animate-pulse"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>

            <h2 class="text-3xl md:text-4xl font-cinzel font-bold text-yellow-500 mb-6 tracking-widest">
                Consulting the Maesters
            </h2>

            <!-- Rotating Lore Messages -->
            <div class="max-w-2xl px-8 text-center mb-8">
                <p class="text-gray-300 font-lato text-lg italic animate-pulse" :key="currentMessage">
                    {{ loadingMessages[currentMessageIndex] }}
                </p>
            </div>

            <!-- Progress Steps -->
            <div class="flex gap-4 items-center">
                <div v-for="(step, index) in loadingSteps" :key="index" class="flex items-center gap-2">
                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg border transition-all duration-300"
                         :class="currentStep >= index ? 'border-yellow-500 bg-yellow-500/10' : 'border-gray-700 bg-gray-800/50'">
                        <svg v-if="currentStep > index" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <div v-else class="w-2 h-2 rounded-full" :class="currentStep === index ? 'bg-yellow-500 animate-pulse' : 'bg-gray-600'"></div>
                        <span class="text-sm font-cinzel" :class="currentStep >= index ? 'text-yellow-500' : 'text-gray-500'">{{ step }}</span>
                    </div>
                    <svg v-if="index < loadingSteps.length - 1" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </div>
    </GameLayout>
</template>
