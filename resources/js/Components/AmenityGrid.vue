<template>
    <div
        v-if="amenities && Object.keys(amenities).length > 0"
        class="bg-gray-900/30 p-6 rounded-xl border border-yellow-700/20"
        :class="containerClass"
    >
        <h3
            class="text-gray-400 font-cinzel text-sm uppercase tracking-widest border-b border-yellow-700/20 pb-3 mb-4"
        >
            {{ title }}
        </h3>
        <div :class="gridClass">
            <AmenityCard
                v-for="(count, type) in amenities"
                :key="type"
                :type="type"
                :count="count"
            />
        </div>
    </div>
</template>

<script setup>
import AmenityCard from "./AmenityCard.vue";

const props = defineProps({
    amenities: {
        type: Object,
        default: () => ({}),
    },
    title: {
        type: String,
        default: "Local Amenities (Real-time)",
    },
    gridClass: {
        type: String,
        default: "grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4",
    },
    containerClass: {
        type: String,
        default: "",
    },
});
</script>
