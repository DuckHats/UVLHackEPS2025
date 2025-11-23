<template>
    <div
        class="bg-gray-800/50 p-3 rounded border border-gray-700 flex flex-col items-center text-center hover:bg-gray-800/70 transition-colors h-full"
    >
        <span class="text-2xl font-bold text-white font-mono mb-1">
            {{ count }}
        </span>
        <span class="text-xs text-yellow-500 uppercase tracking-wider break-words">
            {{ formattedType }}
        </span>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    type: {
        type: String,
        required: true,
    },
    count: {
        type: Number,
        required: true,
    },
});

const formattedType = computed(() => {
    return props.type.replace(/_/g, " ");
});
</script>
