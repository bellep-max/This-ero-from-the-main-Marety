<script setup>
import { useAudioPlayerStore } from '@/stores/track.js';
import { storeToRefs } from 'pinia';
import { onMounted, ref, watch, onBeforeUnmount, defineAsyncComponent } from 'vue';
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));

const audioPlayer = useAudioPlayerStore();
const { volume: storeVolume } = storeToRefs(audioPlayer);

const isHover = ref(false);
const isMuted = ref(false);
const volumeLevel = ref(Math.round(storeVolume.value * 100));
const volumeInput = ref(null);

// Convert store volume (0-1) to percentage (0-100) for display
const volumePercentage = ref(Math.round(storeVolume.value * 100));

const toggleVolume = () => {
    isMuted.value = !isMuted.value;
};

const handleVolumeInput = (e) => {
    const value = parseInt(e.currentTarget.value);
    volumePercentage.value = value;
    audioPlayer.setVolume(value / 100);

    if (value === 0) {
        isMuted.value = true;
    } else {
        isMuted.value = false;
        volumeLevel.value = value;
    }
};

watch(
    () => isMuted.value,
    (newValue) => {
        const newVolume = newValue ? 0 : volumeLevel.value / 100;
        audioPlayer.setVolume(newVolume);
        volumePercentage.value = newValue ? 0 : volumeLevel.value;
    },
);

// Watch store volume changes and update local state
watch(
    () => storeVolume.value,
    (newValue) => {
        volumePercentage.value = Math.round(newValue * 100);
        if (newValue === 0) {
            isMuted.value = true;
        } else {
            isMuted.value = false;
            volumeLevel.value = volumePercentage.value;
        }
    },
);

onMounted(() => {
    if (volumeInput.value) {
        volumeInput.value.addEventListener('input', handleVolumeInput);
    }
});

onBeforeUnmount(() => {
    if (volumeInput.value) {
        volumeInput.value.removeEventListener('input', handleVolumeInput);
    }
});
</script>

<template>
    <div class="d-flex flex-row justify-content-end align-items-center gap-3 w-75">
        <IconButton
            :icon="
                isMuted || volumePercentage === 0
                    ? 'volume-xmark'
                    : volumePercentage < 50
                      ? 'volume-low'
                      : 'volume-high'
            "
            @click="toggleVolume"
            class-list="w-25"
        />
        <div class="volume-control w-75" @mouseover="isHover = true" @mouseleave="isHover = false">
            <div class="volume-bar rounded" :style="`width: ${volumePercentage}%;`" />
            <input
                ref="volumeInput"
                :value="volumePercentage"
                type="range"
                class="volume-slider w-100"
                min="0"
                max="100"
            />
        </div>
    </div>
</template>

<style scoped>
.volume-control {
    display: grid;
    grid-template-areas: 'stack';
    align-items: center;
    position: relative;
    width: 200px; /* Adjust as needed */
    height: 20px; /* Adjust as needed */
}

.volume-bar,
.volume-slider {
    grid-area: stack;
}

.volume-bar {
    background-color: var(--primary-color, #e836c5);
    height: 8px;
    z-index: 2; /* Ensure the bar is behind the thumb */
    pointer-events: none; /* Make the bar non-interactive */
}

.volume-slider {
    -webkit-appearance: none;
    background: transparent;
    width: 100%;
    height: 100%;
    z-index: 1; /* Ensure the slider is interactive */
    cursor: pointer;
}

/* Customizing the thumb for consistent styling */
.volume-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    background: var(--primary-color, #e836c5);
    border: 1px solid #ccc;
    border-radius: 50%;
    margin-top: -6px; /* Adjust to center the thumb vertically */
}

/* Customizing the track (the gray line) */
.volume-slider::-webkit-slider-runnable-track {
    width: 100%;
    height: 8px;
    background: #ddd;
    border-radius: 4px;
}
</style>
