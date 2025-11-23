<script setup>
import GameLayout from "@/Layouts/GameLayout.vue";
import Map from "@/Components/Map.vue";
import { Link, useForm } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import axios from "axios";
import { countries } from "@/Data/countries";

const props = defineProps({
    profile: Object,
    bestMatch: Object,
    allMatches: Array,
    justification: String,
});

const markers = props.allMatches.map((match, index) => ({
    lat: match.coords.lat,
    lon: match.coords.lon,
    popup: `<b>${match.name}</b><br>Score: ${match.score}`,
    color: index === 0 ? "#8fce00" : "#fbbf24", // Green for best, Yellow for others
    name: match.name, // Include name for comparison
}));

const comparisonMatch = ref(null);

const handleMarkerClick = (marker) => {
    // Find the full match data based on the name
    const match = props.allMatches.find((m) => m.name === marker.name);
    if (match && match.name !== props.bestMatch.name) {
        comparisonMatch.value = match;
        // Scroll to comparison section
        setTimeout(() => {
            const element = document.getElementById("comparison-section");
            if (element) element.scrollIntoView({ behavior: "smooth" });
        }, 100);
    } else {
        comparisonMatch.value = null; // Reset if clicking best match
    }
};

// Modal State
const showSaveModal = ref(false);
const saveOption = ref("whatsapp"); // whatsapp, email, pdf
const whatsappNumber = ref("");
const countryPrefix = ref("+1");
const countryCodes = countries;
const isDropdownOpen = ref(false);
const searchQuery = ref("");

import { computed } from "vue";
const filteredCountries = computed(() => {
    const query = searchQuery.value.toLowerCase();
    return countryCodes.filter(
        (country) =>
            country.name.toLowerCase().includes(query) ||
            country.prefix.includes(query) ||
            country.code.toLowerCase().includes(query)
    );
});

const selectCountry = (country) => {
    countryPrefix.value = country.prefix;
    isDropdownOpen.value = false;
    searchQuery.value = "";
};

const emailForm = useForm({
    email: "",
    resultData: {
        archetype: props.profile.archetype,
        neighborhood: props.bestMatch.name,
        score: props.bestMatch.score,
        justification: props.justification,
    },
});

const shareWhatsapp = async () => {
    if (!whatsappNumber.value) {
        alert("Please enter a phone number.");
        return;
    }

    const fullNumber =
        countryPrefix.value.replace("+", "") + whatsappNumber.value;

    try {
        const response = await axios.post("/send-whatsapp", {
            phone: fullNumber,
            resultData: {
                archetype: props.profile.archetype,
                neighborhood: props.bestMatch.name,
                score: props.bestMatch.score,
                justification: props.justification,
            },
        });

        if (response.data.success) {
            alert(response.data.message);
            showSaveModal.value = false;
        } else {
            // Fallback to WhatsApp Web if API fails
            if (response.data.fallback_url) {
                window.open(response.data.fallback_url, "_blank");
                showSaveModal.value = false;
            } else {
                alert(response.data.message);
            }
        }
    } catch (error) {
        console.error("WhatsApp error:", error);
        // Fallback to WhatsApp Web on error
        const text = `I am a ${props.profile.archetype} and my ideal realm is ${props.bestMatch.name} (${props.bestMatch.score}% match)! Find your domain at: ${window.location.origin}`;
        const url = `https://wa.me/${fullNumber}?text=${encodeURIComponent(
            text
        )}`;
        window.open(url, "_blank");
        showSaveModal.value = false;
    }
};

const shareEmail = async () => {
    if (!emailForm.email) {
        alert("Please enter an email address.");
        return;
    }

    try {
        const response = await axios.post("/share-result", {
            email: emailForm.email,
            resultData: {
                archetype: props.profile.archetype,
                neighborhood: props.bestMatch.name,
                score: props.bestMatch.score,
                justification: props.justification,
                kpis: props.bestMatch.data,
            },
        });

        if (response.data.success) {
            alert(response.data.message);
            showSaveModal.value = false;
            emailForm.email = "";
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        console.error("Email error:", error);
        alert("The raven was lost in flight. Please try again.");
    }
};

const downloadPdf = async () => {
    try {
        const response = await axios.post(
            "/download-pdf",
            {
                resultData: {
                    archetype: props.profile.archetype,
                    neighborhood: props.bestMatch.name,
                    score: props.bestMatch.score,
                    justification: props.justification,
                    kpis: props.bestMatch.data,
                },
            },
            {
                responseType: "blob",
            }
        );

        // Create blob link to download
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute(
            "download",
            `realm-decree-${props.bestMatch.name
                .toLowerCase()
                .replace(/\s+/g, "-")}-${
                new Date().toISOString().split("T")[0]
            }.pdf`
        );
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);

        showSaveModal.value = false;
    } catch (error) {
        console.error("PDF error:", error);
        alert("Failed to generate the parchment. Please try again.");
    }
};
</script>

<template>
    <GameLayout>
        <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-10">
                <!-- Top: Map Section -->
                <div
                    class="w-full"
                    v-motion
                    :initial="{ opacity: 0, y: -50 }"
                    :enter="{
                        opacity: 1,
                        y: 0,
                        transition: { duration: 800, type: 'spring' },
                    }"
                >
                    <div
                        class="h-[450px] w-full rounded-xl overflow-hidden border-2 border-yellow-700/50 shadow-2xl relative group"
                    >
                        <Map
                            :center="bestMatch.coords"
                            :markers="markers"
                            @marker-click="handleMarkerClick"
                        />
                        <!-- Overlay Title on Map -->
                        <div
                            class="absolute top-6 left-6 z-[400] bg-gray-900/90 backdrop-blur-md px-6 py-3 rounded-lg border border-yellow-700/40 shadow-lg transform group-hover:scale-105 transition-transform duration-300"
                        >
                            <div class="flex items-center gap-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-yellow-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                                <h2
                                    class="text-yellow-500 font-cinzel font-bold text-xl tracking-wide"
                                >
                                    Target Acquired
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom: The "Why" Section -->
                <div
                    class="bg-gray-800/80 p-10 rounded-xl border-2 border-yellow-700/40 backdrop-blur-md relative overflow-hidden shadow-2xl"
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{
                        opacity: 1,
                        y: 0,
                        transition: {
                            duration: 800,
                            delay: 200,
                            type: 'spring',
                        },
                    }"
                >
                    <!-- Decorative background texture -->
                    <div
                        class="absolute inset-0 opacity-5 pointer-events-none"
                        style="
                            background-image: url('https://www.transparenttextures.com/patterns/dark-leather.png');
                        "
                    ></div>
                    <div
                        class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-yellow-500/10 rounded-full blur-3xl"
                    ></div>
                    <div
                        class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-yellow-500/5 rounded-full blur-3xl"
                    ></div>

                    <div class="relative z-10">
                        <!-- Header Section -->
                        <div
                            class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 border-b border-yellow-700/30 pb-8"
                        >
                            <div class="space-y-2">
                                <h2
                                    class="text-yellow-500 font-cinzel text-sm uppercase tracking-[0.3em] flex items-center gap-2"
                                >
                                    <span
                                        class="w-8 h-[1px] bg-yellow-500/50"
                                    ></span>
                                    The Verdict
                                </h2>
                                <h1
                                    class="text-5xl md:text-6xl text-white font-bold font-cinzel tracking-tight drop-shadow-lg"
                                >
                                    {{ bestMatch.name }}
                                </h1>
                            </div>
                            <div
                                class="mt-6 md:mt-0 text-right bg-gray-900/50 p-4 rounded-lg border border-yellow-700/20"
                            >
                                <div
                                    class="text-xs text-gray-400 uppercase tracking-widest font-cinzel mb-1"
                                >
                                    Score
                                </div>
                                <div
                                    class="text-5xl font-bold text-yellow-500 font-cinzel flex items-center justify-end gap-1"
                                >
                                    {{ bestMatch.score
                                    }}<span
                                        class="text-2xl text-yellow-700"
                                    ></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                            <!-- Left: Justification -->
                            <div class="lg:col-span-7 space-y-8">
                                <div
                                    class="prose prose-invert prose-lg font-lato text-gray-300 leading-relaxed"
                                >
                                    <p
                                        class="first-letter:text-5xl first-letter:font-cinzel first-letter:text-yellow-500 first-letter:mr-2 first-letter:float-left"
                                    >
                                        {{ justification }}
                                    </p>
                                </div>

                                <div
                                    class="bg-gray-900/40 p-6 rounded-lg border border-yellow-700/20"
                                >
                                    <h3
                                        class="text-yellow-500 font-cinzel text-sm uppercase tracking-widest mb-4 flex items-center gap-2"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        Your Archetype:
                                        <span class="text-white ml-1">{{
                                            profile.archetype
                                        }}</span>
                                    </h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="(val, key) in profile.kpis"
                                            :key="key"
                                            class="text-xs bg-gray-800 text-gray-300 px-3 py-1.5 rounded border border-gray-700 font-mono flex items-center gap-2"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-yellow-500"
                                            ></span>
                                            {{ key.replace(/_/g, " ") }}:
                                            <span class="text-yellow-500">{{
                                                val
                                            }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Data Points -->
                            <div class="lg:col-span-5 space-y-8">
                                <div
                                    class="bg-gray-900/30 p-6 rounded-xl border border-yellow-700/20"
                                >
                                    <h3
                                        class="text-gray-400 font-cinzel text-sm uppercase tracking-widest border-b border-yellow-700/20 pb-3 mb-4"
                                    >
                                        Realm Statistics
                                    </h3>
                                    <div class="space-y-4">
                                        <div
                                            v-for="(
                                                count, type
                                            ) in bestMatch.data"
                                            :key="type"
                                            class="flex justify-between items-center group p-3 rounded hover:bg-yellow-900/10 transition-colors cursor-default"
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <!-- Dynamic Icons based on type (simplified mapping) -->
                                                <span
                                                    class="text-yellow-600 group-hover:text-yellow-400 transition-colors"
                                                >
                                                    <svg
                                                        v-if="
                                                            type
                                                                .toLowerCase()
                                                                .includes(
                                                                    'safety'
                                                                )
                                                        "
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                                        />
                                                    </svg>
                                                    <svg
                                                        v-else-if="
                                                            type
                                                                .toLowerCase()
                                                                .includes(
                                                                    'affordability'
                                                                ) ||
                                                            type
                                                                .toLowerCase()
                                                                .includes(
                                                                    'price'
                                                                )
                                                        "
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        />
                                                    </svg>
                                                    <svg
                                                        v-else-if="
                                                            type
                                                                .toLowerCase()
                                                                .includes(
                                                                    'nightlife'
                                                                ) ||
                                                            type
                                                                .toLowerCase()
                                                                .includes('fun')
                                                        "
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                                        />
                                                    </svg>
                                                    <svg
                                                        v-else
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        />
                                                    </svg>
                                                </span>
                                                <span
                                                    class="text-gray-400 text-sm uppercase tracking-wider group-hover:text-yellow-500 transition-colors"
                                                    >{{ type }}</span
                                                >
                                            </div>
                                            <span
                                                class="text-white font-bold font-mono text-lg"
                                                >{{ count }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Call to Action Buttons -->
                                <div class="pt-4 space-y-4">
                                    <Link
                                        href="/"
                                        class="group block w-full text-center px-6 py-4 bg-yellow-700 hover:bg-yellow-600 text-white font-cinzel font-bold rounded transition-all shadow-lg shadow-yellow-900/20 hover:shadow-yellow-500/20 hover:-translate-y-0.5"
                                    >
                                        <span
                                            class="flex items-center justify-center gap-2"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 group-hover:rotate-180 transition-transform duration-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                                />
                                            </svg>
                                            Consult Again
                                        </span>
                                    </Link>
                                    <button
                                        @click="showSaveModal = true"
                                        class="group block w-full text-center px-6 py-4 bg-transparent border border-yellow-700 text-yellow-500 hover:bg-yellow-700/10 font-cinzel font-bold rounded transition-all hover:-translate-y-0.5"
                                    >
                                        <span
                                            class="flex items-center justify-center gap-2"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
                                                />
                                            </svg>
                                            Save Results
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comparison Section -->
                <div
                    v-if="comparisonMatch"
                    id="comparison-section"
                    class="bg-gray-800/80 p-10 rounded-xl border-2 border-yellow-700/40 backdrop-blur-md relative overflow-hidden shadow-2xl mt-10"
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{
                        opacity: 1,
                        y: 0,
                        transition: { duration: 800, type: 'spring' },
                    }"
                >
                    <!-- Decorative background texture -->
                    <div
                        class="absolute inset-0 opacity-5 pointer-events-none"
                        style="
                            background-image: url('https://www.transparenttextures.com/patterns/dark-leather.png');
                        "
                    ></div>

                    <div class="relative z-10">
                        <h2
                            class="text-yellow-500 font-cinzel text-sm uppercase tracking-[0.3em] flex items-center gap-2 mb-8"
                        >
                            <span class="w-8 h-[1px] bg-yellow-500/50"></span>
                            Realm Comparison
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Best Match -->
                            <div
                                class="bg-gray-900/40 p-6 rounded-lg border border-yellow-700/20 relative overflow-hidden"
                            >
                                <div
                                    class="absolute top-0 right-0 bg-yellow-600 text-white text-xs font-bold px-2 py-1 rounded-bl"
                                >
                                    RECOMMENDED
                                </div>
                                <h3
                                    class="text-2xl font-cinzel font-bold text-white mb-2"
                                >
                                    {{ bestMatch.name }}
                                </h3>
                                <div
                                    class="text-4xl font-bold text-yellow-500 font-cinzel mb-6"
                                >
                                    {{ bestMatch.score }}
                                </div>

                                <div class="space-y-3">
                                    <div
                                        v-for="(count, type) in bestMatch.data"
                                        :key="type"
                                        class="flex justify-between items-center text-sm border-b border-gray-700 pb-2 last:border-0"
                                    >
                                        <span
                                            class="text-gray-400 uppercase tracking-wider"
                                            >{{ type }}</span
                                        >
                                        <span
                                            class="text-white font-mono font-bold"
                                            >{{ count }}</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Comparison Match -->
                            <div
                                class="bg-gray-900/40 p-6 rounded-lg border border-gray-700 relative"
                            >
                                <h3
                                    class="text-2xl font-cinzel font-bold text-gray-300 mb-2"
                                >
                                    {{ comparisonMatch.name }}
                                </h3>
                                <div
                                    class="text-4xl font-bold text-gray-400 font-cinzel mb-6"
                                >
                                    {{ comparisonMatch.score }}
                                </div>

                                <div class="space-y-3">
                                    <div
                                        v-for="(
                                            count, type
                                        ) in comparisonMatch.data"
                                        :key="type"
                                        class="flex justify-between items-center text-sm border-b border-gray-700 pb-2 last:border-0"
                                    >
                                        <span
                                            class="text-gray-400 uppercase tracking-wider"
                                            >{{ type }}</span
                                        >
                                        <span
                                            class="text-white font-mono font-bold"
                                            >{{ count }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Save Result Modal -->
        <div
            v-if="showSaveModal"
            class="fixed inset-0 z-50 flex items-center justify-center px-4"
        >
            <div
                class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm"
                @click="showSaveModal = false"
            ></div>

            <div
                class="relative bg-gray-800 border-2 border-yellow-700/50 rounded-xl shadow-2xl max-w-lg w-full"
                v-motion
                :initial="{ opacity: 0, scale: 0.9 }"
                :enter="{ opacity: 1, scale: 1 }"
            >
                <!-- Texture -->
                <div
                    class="absolute inset-0 opacity-10 pointer-events-none rounded-xl"
                    style="
                        background-image: url('https://www.transparenttextures.com/patterns/dark-leather.png');
                    "
                ></div>

                <!-- Header -->
                <div
                    class="relative p-6 border-b border-yellow-700/30 flex justify-between items-center"
                >
                    <h3 class="text-2xl font-cinzel font-bold text-yellow-500">
                        Save Your Decree
                    </h3>
                    <button
                        @click="showSaveModal = false"
                        class="text-gray-400 hover:text-white transition-colors"
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="relative p-6 space-y-6">
                    <!-- Option Tabs -->
                    <div class="flex gap-2 bg-gray-900/50 p-1 rounded-lg">
                        <button
                            @click="saveOption = 'whatsapp'"
                            class="flex-1 py-2 text-sm font-bold font-cinzel rounded-md transition-all"
                            :class="
                                saveOption === 'whatsapp'
                                    ? 'bg-yellow-700 text-white shadow'
                                    : 'text-gray-400 hover:text-yellow-500'
                            "
                        >
                            WhatsApp
                        </button>
                        <button
                            @click="saveOption = 'email'"
                            class="flex-1 py-2 text-sm font-bold font-cinzel rounded-md transition-all"
                            :class="
                                saveOption === 'email'
                                    ? 'bg-yellow-700 text-white shadow'
                                    : 'text-gray-400 hover:text-yellow-500'
                            "
                        >
                            Email
                        </button>
                        <button
                            @click="saveOption = 'pdf'"
                            class="flex-1 py-2 text-sm font-bold font-cinzel rounded-md transition-all"
                            :class="
                                saveOption === 'pdf'
                                    ? 'bg-yellow-700 text-white shadow'
                                    : 'text-gray-400 hover:text-yellow-500'
                            "
                        >
                            PDF
                        </button>
                    </div>

                    <!-- WhatsApp Form -->
                    <div v-if="saveOption === 'whatsapp'" class="space-y-4">
                        <p class="text-gray-300 font-lato">
                            Send your result directly to WhatsApp.
                        </p>
                        <div>
                            <label
                                class="block text-xs font-bold text-yellow-500 uppercase mb-1"
                                >Phone Number</label
                            >
                            <div class="flex gap-2 relative">
                                <!-- Custom Dropdown Trigger -->
                                <div
                                    @click="isDropdownOpen = !isDropdownOpen"
                                    class="bg-gray-900/50 border border-gray-600 rounded p-3 text-white cursor-pointer min-w-[100px] flex items-center justify-between hover:border-yellow-500 transition-colors"
                                >
                                    <span>{{ countryPrefix }}</span>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </div>

                                <!-- Dropdown Menu -->
                                <div
                                    v-if="isDropdownOpen"
                                    class="absolute top-full left-0 mt-1 w-64 max-h-60 bg-gray-800 border border-yellow-700/50 rounded-lg shadow-xl z-50 flex flex-col overflow-hidden"
                                >
                                    <!-- Search Input -->
                                    <div
                                        class="p-2 border-b border-gray-700 top-0 bg-gray-800 z-10"
                                    >
                                        <input
                                            v-model="searchQuery"
                                            type="text"
                                            placeholder="Search country or code..."
                                            class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:border-yellow-500 focus:outline-none"
                                            @click.stop
                                            autofocus
                                        />
                                    </div>
                                    <!-- Options List -->
                                    <div class="overflow-y-auto flex-1">
                                        <div
                                            v-for="country in filteredCountries"
                                            :key="country.code"
                                            @click="selectCountry(country)"
                                            class="px-4 py-2 hover:bg-yellow-900/20 cursor-pointer flex items-center gap-3 text-sm text-gray-300 hover:text-white transition-colors"
                                        >
                                            <span class="text-lg">{{
                                                country.flag
                                            }}</span>
                                            <span class="flex-1">{{
                                                country.name
                                            }}</span>
                                            <span
                                                class="text-yellow-500 font-mono"
                                                >{{ country.prefix }}</span
                                            >
                                        </div>
                                        <div
                                            v-if="
                                                filteredCountries.length === 0
                                            "
                                            class="p-4 text-center text-gray-500 text-sm"
                                        >
                                            No realms found.
                                        </div>
                                    </div>
                                </div>

                                <!-- Overlay to close dropdown -->
                                <div
                                    v-if="isDropdownOpen"
                                    @click="isDropdownOpen = false"
                                    class="fixed inset-0 z-40 bg-transparent"
                                ></div>

                                <input
                                    v-model="whatsappNumber"
                                    type="text"
                                    placeholder="612345678"
                                    class="flex-1 bg-gray-900/50 border border-gray-600 rounded p-3 text-white focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition-colors"
                                />
                            </div>
                        </div>
                        <button
                            @click="shareWhatsapp"
                            class="w-full medieval-btn py-3 flex justify-center items-center gap-2"
                        >
                            <span>Send via WhatsApp</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Email Form -->
                    <div v-if="saveOption === 'email'" class="space-y-4">
                        <p class="text-gray-300 font-lato">
                            Receive a raven with your decree.
                        </p>
                        <div>
                            <label
                                class="block text-xs font-bold text-yellow-500 uppercase mb-1"
                                >Email Address</label
                            >
                            <input
                                v-model="emailForm.email"
                                type="email"
                                placeholder="lord@winterfell.com"
                                class="w-full bg-gray-900/50 border border-gray-600 rounded p-3 text-white focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition-colors"
                            />
                        </div>
                        <button
                            @click="shareEmail"
                            :disabled="emailForm.processing"
                            class="w-full medieval-btn py-3 flex justify-center items-center gap-2 disabled:opacity-50"
                        >
                            <span>Send Raven</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- PDF Form -->
                    <div
                        v-if="saveOption === 'pdf'"
                        class="space-y-4 text-center py-4"
                    >
                        <div
                            class="mx-auto w-16 h-16 bg-yellow-900/20 rounded-full flex items-center justify-center mb-4"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-yellow-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                        </div>
                        <p class="text-gray-300 font-lato mb-6">
                            Download your decree as a parchment to keep forever.
                        </p>
                        <button
                            @click="downloadPdf"
                            class="w-full medieval-btn py-3 flex justify-center items-center gap-2"
                        >
                            <span>Download Parchment</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </GameLayout>
</template>
