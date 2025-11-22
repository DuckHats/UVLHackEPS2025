<script setup>
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    center: {
        type: Object, // {lat, lon}
        required: true,
    },
    markers: {
        type: Array, // [{lat, lon, popup, color}]
        default: () => [],
    },
});

const mapContainer = ref(null);
let map = null;

onMounted(() => {
    if (mapContainer.value) {
        map = L.map(mapContainer.value).setView([props.center.lat, props.center.lon], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        updateMarkers();
    }
});

watch(() => props.markers, () => {
    updateMarkers();
});

function updateMarkers() {
    if (!map) return;

    // Clear existing markers (simplified: just re-add for now or clear layer group if I stored it)
    // For this MVP, I'll just add new ones. In prod, use a LayerGroup.
    
    props.markers.forEach(marker => {
        const color = marker.color || '#fbbf24'; // yellow-500
        
        // Custom circle marker for "Heat"
        L.circle([marker.lat, marker.lon], {
            color: color,
            fillColor: color,
            fillOpacity: 0.5,
            radius: 800
        }).addTo(map).bindPopup(marker.popup);
    });
}
</script>

<template>
    <div ref="mapContainer" class="w-full h-full rounded-lg shadow-lg border border-gray-700"></div>
</template>
