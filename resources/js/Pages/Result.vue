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
        <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column: Info -->
                <div class="space-y-8">
                    <div class="bg-gray-800/50 p-6 rounded-lg border border-yellow-700/50 backdrop-blur-sm">
                        <h2 class="text-yellow-500 font-cinzel text-xl mb-2 uppercase tracking-widest">Your Archetype</h2>
                        <h1 class="text-4xl text-white font-bold font-cinzel mb-4">{{ profile.archetype }}</h1>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span v-for="(val, key) in profile.kpis" :key="key" class="text-xs bg-gray-900 text-gray-400 px-2 py-1 rounded border border-gray-700">
                                {{ key.replace(/_/g, ' ') }}: {{ val }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-gray-800/50 p-6 rounded-lg border border-yellow-700/50 backdrop-blur-sm">
                        <h2 class="text-yellow-500 font-cinzel text-xl mb-2 uppercase tracking-widest">The Verdict</h2>
                        <h3 class="text-2xl text-white font-bold font-cinzel mb-4">{{ bestMatch.name }}</h3>
                        <div class="prose prose-invert font-lato text-lg leading-relaxed">
                            <p>{{ justification }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-800/50 p-6 rounded-lg border border-yellow-700/50 backdrop-blur-sm">
                        <h2 class="text-yellow-500 font-cinzel text-xl mb-4 uppercase tracking-widest">Realm Statistics</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div v-for="(count, type) in bestMatch.data" :key="type" class="bg-gray-900 p-3 rounded border border-gray-700 text-center">
                                <div class="text-2xl font-bold text-white">{{ count }}</div>
                                <div class="text-xs text-gray-400 uppercase tracking-wider">{{ type }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <Link href="/" class="inline-block px-8 py-3 bg-yellow-700 text-white font-cinzel font-bold rounded hover:bg-yellow-600 transition">
                            Consult the Maesters Again
                        </Link>
                    </div>
                </div>

                <!-- Right Column: Map -->
                <div class="h-[600px] lg:h-auto sticky top-8">
                    <Map :center="bestMatch.coords" :markers="markers" />
                </div>
            </div>
        </div>
    </GameLayout>
</template>
