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
    color: index === 0 ? '#ef4444' : '#fbbf24', // Red for best, Yellow for others
}));
</script>

<template>
    <GameLayout>
        <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-8">
                
                <!-- Top: Map Section -->
                <div 
                    class="w-full"
                    v-motion
                    :initial="{ opacity: 0, y: -50 }"
                    :enter="{ opacity: 1, y: 0, transition: { duration: 800, type: 'spring' } }"
                >
                    <div class="h-[400px] w-full rounded-lg overflow-hidden border border-yellow-700/50 shadow-2xl relative">
                        <Map :center="bestMatch.coords" :markers="markers" />
                        <!-- Overlay Title on Map (Optional, purely aesthetic) -->
                        <div class="absolute top-4 left-4 z-[400] bg-gray-900/80 backdrop-blur px-4 py-2 rounded border border-yellow-700/30">
                            <h2 class="text-yellow-500 font-cinzel font-bold text-lg">Target Acquired</h2>
                        </div>
                    </div>
                </div>

                <!-- Bottom: The "Why" Section -->
                <div 
                    class="bg-gray-800/50 p-8 rounded-lg border border-yellow-700/50 backdrop-blur-sm relative overflow-hidden"
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{ opacity: 1, y: 0, transition: { duration: 800, delay: 200, type: 'spring' } }"
                >
                    <!-- Decorative background element -->
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-yellow-500/10 rounded-full blur-2xl"></div>

                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b border-gray-700 pb-6">
                            <div>
                                <h2 class="text-yellow-500 font-cinzel text-sm uppercase tracking-[0.2em] mb-1">The Verdict</h2>
                                <h1 class="text-4xl text-white font-bold font-cinzel">{{ bestMatch.name }}</h1>
                            </div>
                            <div class="mt-4 md:mt-0 text-right">
                                <div class="text-sm text-gray-400 uppercase tracking-wider font-cinzel">Compatibility Index</div>
                                <div class="text-5xl font-bold text-yellow-500 font-cinzel">{{ bestMatch.score }}%</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Left: Justification -->
                            <div class="md:col-span-2 prose prose-invert font-lato text-lg leading-relaxed text-gray-300">
                                <p>{{ justification }}</p>
                                
                                <div class="mt-6">
                                    <h3 class="text-yellow-500 font-cinzel text-sm uppercase tracking-widest mb-3">Your Archetype: {{ profile.archetype }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="(val, key) in profile.kpis" :key="key" class="text-xs bg-gray-900/80 text-gray-300 px-3 py-1.5 rounded border border-gray-700 font-mono">
                                            {{ key.replace(/_/g, ' ') }}: {{ val }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Data Points -->
                            <div class="space-y-4">
                                <h3 class="text-gray-400 font-cinzel text-sm uppercase tracking-widest border-b border-gray-700 pb-2">Realm Statistics</h3>
                                <div v-for="(count, type) in bestMatch.data" :key="type" class="flex justify-between items-center group">
                                    <span class="text-gray-400 text-sm uppercase tracking-wider group-hover:text-yellow-500 transition-colors">{{ type }}</span>
                                    <span class="text-white font-bold font-mono">{{ count }}</span>
                                </div>
                                
                                <!-- Call to Action Buttons -->
                                <div class="pt-6 space-y-3">
                                    <Link href="/" class="block w-full text-center px-6 py-3 bg-yellow-700 hover:bg-yellow-600 text-white font-cinzel font-bold rounded transition shadow-lg shadow-yellow-900/20">
                                        Consult Again
                                    </Link>
                                    <Link href="/contact" class="block w-full text-center px-6 py-3 bg-transparent border border-yellow-700 text-yellow-500 hover:bg-yellow-700/10 font-cinzel font-bold rounded transition">
                                        Save Results
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
