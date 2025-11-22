<script setup>
import { onMounted, ref, watch } from 'vue';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';

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

// TODO: Replace with your actual Mapbox Token in .env file (VITE_MAPBOX_TOKEN)
mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN || 'pk.eyJ1IjoiZHVja2hhdCIsImEiOiJjbWZlNXk1eDkwMnlzMm1zZ3RwbGFubW45In0.DcezTo0Brqh4pxgyPdhYkA'; 

onMounted(() => {
    if (mapContainer.value) {
        map = new mapboxgl.Map({
            container: mapContainer.value,
            style: 'mapbox://styles/mapbox/light-v11', // Dark theme
            center: [props.center.lon, props.center.lat],
            zoom: 11, // Start slightly zoomed out for the fly-in effect
            pitch: 45, // Tilt for 3D effect
            bearing: -17.6,
            antialias: true
        });

        map.on('load', () => {
            // Add a source for the heat zones
            map.addSource('heat-zones', {
                type: 'geojson',
                data: getGeoJsonFromMarkers(props.markers)
            });

            // Add a layer for the heat circles
            map.addLayer({
                id: 'heat-circles',
                type: 'circle',
                source: 'heat-zones',
                paint: {
                    'circle-radius': [
                        'interpolate', ['linear'], ['zoom'],
                        10, 20,
                        15, 100
                    ],
                    'circle-color': ['get', 'color'],
                    'circle-opacity': 0.6,
                    'circle-blur': 0.4
                }
            });

            // Add a layer for the center points (solid dot)
            map.addLayer({
                id: 'heat-centers',
                type: 'circle',
                source: 'heat-zones',
                paint: {
                    'circle-radius': 5,
                    'circle-color': '#ffffff',
                    'circle-stroke-width': 2,
                    'circle-stroke-color': ['get', 'color']
                }
            });

            // Fly to the destination
            map.flyTo({
                center: [props.center.lon, props.center.lat],
                zoom: 14,
                speed: 1.2,
                curve: 1.42,
                easing: (t) => t,
                essential: true
            });
            
            // Add popups on click
            map.on('click', 'heat-circles', (e) => {
                const coordinates = e.features[0].geometry.coordinates.slice();
                const description = e.features[0].properties.popup;

                while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                    coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                }

                new mapboxgl.Popup()
                    .setLngLat(coordinates)
                    .setHTML(description)
                    .addTo(map);
            });

            // Change cursor on hover
            map.on('mouseenter', 'heat-circles', () => {
                map.getCanvas().style.cursor = 'pointer';
            });
            map.on('mouseleave', 'heat-circles', () => {
                map.getCanvas().style.cursor = '';
            });
        });
    }
});

watch(() => props.markers, () => {
    if (map && map.getSource('heat-zones')) {
        map.getSource('heat-zones').setData(getGeoJsonFromMarkers(props.markers));
    }
});

function getGeoJsonFromMarkers(markers) {
    return {
        type: 'FeatureCollection',
        features: markers.map(marker => ({
            type: 'Feature',
            geometry: {
                type: 'Point',
                coordinates: [marker.lon, marker.lat]
            },
            properties: {
                popup: marker.popup,
                color: marker.color || '#fbbf24'
            }
        }))
    };
}
</script>

<template>
    <div ref="mapContainer" class="w-full h-full rounded-lg shadow-lg border border-gray-700 bg-gray-900"></div>
</template>

<style>
/* Ensure Mapbox canvas fills the container */
.mapboxgl-map {
    width: 100%;
    height: 100%;
}

/* Fix popup text color for light theme */
.mapboxgl-popup-content {
    color: #111827; /* gray-900 */
}
.mapboxgl-popup-content b {
    color: #000000;
}
</style>
