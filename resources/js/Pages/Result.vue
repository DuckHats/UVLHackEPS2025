<script setup>
import GameLayout from '@/Layouts/GameLayout.vue';
import Map from '@/Components/Map.vue';
import { Link } from '@inertiajs/inertia-vue3';

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
    color: index === 0 ? '#8fce00' : '#fbbf24', // Green for best, Yellow for others
}));
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
                    :enter="{ opacity: 1, y: 0, transition: { duration: 800, type: 'spring' } }"
                >
                    <div class="h-[450px] w-full rounded-xl overflow-hidden border-2 border-yellow-700/50 shadow-2xl relative group">
                        <Map :center="bestMatch.coords" :markers="markers" />
                        <!-- Overlay Title on Map -->
                        <div class="absolute top-6 left-6 z-[400] bg-gray-900/90 backdrop-blur-md px-6 py-3 rounded-lg border border-yellow-700/40 shadow-lg transform group-hover:scale-105 transition-transform duration-300">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <h2 class="text-yellow-500 font-cinzel font-bold text-xl tracking-wide">Target Acquired</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom: The "Why" Section -->
                <div 
                    class="bg-gray-800/80 p-10 rounded-xl border-2 border-yellow-700/40 backdrop-blur-md relative overflow-hidden shadow-2xl"
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{ opacity: 1, y: 0, transition: { duration: 800, delay: 200, type: 'spring' } }"
                >
                    <!-- Decorative background texture -->
                    <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/dark-leather.png');"></div>
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-yellow-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-yellow-500/5 rounded-full blur-3xl"></div>

                    <div class="relative z-10">
                        <!-- Header Section -->
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 border-b border-yellow-700/30 pb-8">
                            <div class="space-y-2">
                                <h2 class="text-yellow-500 font-cinzel text-sm uppercase tracking-[0.3em] flex items-center gap-2">
                                    <span class="w-8 h-[1px] bg-yellow-500/50"></span>
                                    The Verdict
                                </h2>
                                <h1 class="text-5xl md:text-6xl text-white font-bold font-cinzel tracking-tight drop-shadow-lg">{{ bestMatch.name }}</h1>
                            </div>
                            <div class="mt-6 md:mt-0 text-right bg-gray-900/50 p-4 rounded-lg border border-yellow-700/20">
                                <div class="text-xs text-gray-400 uppercase tracking-widest font-cinzel mb-1">Compatibility</div>
                                <div class="text-5xl font-bold text-yellow-500 font-cinzel flex items-center justify-end gap-1">
                                    {{ bestMatch.score }}<span class="text-2xl text-yellow-700">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                            <!-- Left: Justification -->
                            <div class="lg:col-span-7 space-y-8">
                                <div class="prose prose-invert prose-lg font-lato text-gray-300 leading-relaxed">
                                    <p class="first-letter:text-5xl first-letter:font-cinzel first-letter:text-yellow-500 first-letter:mr-2 first-letter:float-left">
                                        {{ justification }}
                                    </p>
                                </div>
                                
                                <div class="bg-gray-900/40 p-6 rounded-lg border border-yellow-700/20">
                                    <h3 class="text-yellow-500 font-cinzel text-sm uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Your Archetype: <span class="text-white ml-1">{{ profile.archetype }}</span>
                                    </h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="(val, key) in profile.kpis" :key="key" class="text-xs bg-gray-800 text-gray-300 px-3 py-1.5 rounded border border-gray-700 font-mono flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                            {{ key.replace(/_/g, ' ') }}: <span class="text-yellow-500">{{ val }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Data Points -->
                            <div class="lg:col-span-5 space-y-8">
                                <div class="bg-gray-900/30 p-6 rounded-xl border border-yellow-700/20">
                                    <h3 class="text-gray-400 font-cinzel text-sm uppercase tracking-widest border-b border-yellow-700/20 pb-3 mb-4">Realm Statistics</h3>
                                    <div class="space-y-4">
                                        <div v-for="(count, type) in bestMatch.data" :key="type" class="flex justify-between items-center group p-3 rounded hover:bg-yellow-900/10 transition-colors cursor-default">
                                            <div class="flex items-center gap-3">
                                                <!-- Dynamic Icons based on type (simplified mapping) -->
                                                <span class="text-yellow-600 group-hover:text-yellow-400 transition-colors">
                                                    <svg v-if="type.toLowerCase().includes('safety')" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                                    <svg v-else-if="type.toLowerCase().includes('affordability') || type.toLowerCase().includes('price')" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    <svg v-else-if="type.toLowerCase().includes('nightlife') || type.toLowerCase().includes('fun')" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                </span>
                                                <span class="text-gray-400 text-sm uppercase tracking-wider group-hover:text-yellow-500 transition-colors">{{ type }}</span>
                                            </div>
                                            <span class="text-white font-bold font-mono text-lg">{{ count }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Call to Action Buttons -->
                                <div class="pt-4 space-y-4">
                                    <Link href="/" class="group block w-full text-center px-6 py-4 bg-yellow-700 hover:bg-yellow-600 text-white font-cinzel font-bold rounded transition-all shadow-lg shadow-yellow-900/20 hover:shadow-yellow-500/20 hover:-translate-y-0.5">
                                        <span class="flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-180 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Consult Again
                                        </span>
                                    </Link>
                                    <Link href="/contact" class="group block w-full text-center px-6 py-4 bg-transparent border border-yellow-700 text-yellow-500 hover:bg-yellow-700/10 font-cinzel font-bold rounded transition-all hover:-translate-y-0.5">
                                        <span class="flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                            </svg>
                                            Save Results
                                        </span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </GameLayout>
</template>
