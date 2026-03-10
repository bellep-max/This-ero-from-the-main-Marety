<script setup>
import { useAudioPlayerStore } from '@/stores/track.js';
import { storeToRefs } from 'pinia';
import { computed, defineAsyncComponent, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Navigation, Slide } from 'vue3-carousel';
import { usePage } from '@inertiajs/vue3';
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const VolumeController = defineAsyncComponent(() => import('@/Components/Player/VolumeController.vue'));
const PlaylistSongCard = defineAsyncComponent(() => import('@/Components/Cards/PlaylistSongCard.vue'));
const DefaultCarousel = defineAsyncComponent(() => import('@/Components/Carousels/DefaultCarousel.vue'));

const page = usePage();
const audioPlayer = useAudioPlayerStore();
const { isPlaying, queue, currentTrack, isShuffle, isRepeat } = storeToRefs(audioPlayer);

const isHover = ref(false);
const isExtended = ref(false);
const seeker = ref(null);
const seekerContainer = ref(null);

const carouselConfig = {
    breakpointMode: 'viewport',
    itemsToShow: 5,
    wrapAround: false,
    gap: 12,
    snapAlign: 'start',
    breakpoints: {
        576: {
            itemsToShow: 5,
            gap: 12,
            dir: 'ltr',
        },
        0: {
            itemsToShow: 'auto',
            gap: 4,
            dir: 'ttb',
        },
    },
};

const formatTime = (seconds) => {
    if (isNaN(seconds)) return '0:00';

    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);

    return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
};

const extensionIcon = computed(() => (isExtended.value ? 'angles-down' : 'angles-up'));

const hasSingleTrackQueue = computed(() => queue.value.length === 1);

const checkQueueLength = () => {
    if (audioPlayer.queue.length === 0) {
        isExtended.value = false;
    }
};

// Watch for page changes to maintain audio state
watch(
    () => page.url,
    () => {
        // Ensure audio is initialized after page changes
        if (currentTrack.value && (!audioPlayer.audio || !audioPlayer.audio.src)) {
            audioPlayer.initializeAudio();
        }
    },
    { immediate: true },
);

// Initialize audio on component mount
onMounted(() => {
    if (currentTrack.value && !audioPlayer.audio) {
        audioPlayer.initializeAudio();
    }
});

// Cleanup on unmount
onBeforeUnmount(() => {
    // Only clean up if we're not playing
    if (!isPlaying.value) {
        audioPlayer.unloadAudio();
    }
});
</script>

<template>
    <div class="d-flex flex-column fixed-bottom justify-content-end">
        <div
            v-if="currentTrack"
            id="audioPlayer"
            class="w-100 rounded-top-4 bg-default d-flex flex-row justify-content-between align-items-center player p-3"
        >
            <div class="d-flex flex-row align-items-center gap-3 w-25 h-100">
                <img
                    :alt="currentTrack.title || 'No track'"
                    :src="currentTrack.artwork"
                    class="track-image rounded-4"
                />
                <span class="title font-default default-text-color d-none d-md-inline-block">
                    {{ currentTrack.title }}
                </span>
            </div>
            <div class="d-flex flex-column w-50 gap-2 gap-lg-3">
                <div class="d-flex flex-row justify-content-center align-items-center gap-2 gap-lg-4">
                    <IconButton
                        icon="shuffle"
                        size="lg"
                        :class-list="{
                            'color-active': isShuffle,
                        }"
                        @click="isShuffle = !isShuffle"
                    />
                    <IconButton
                        icon="backward-step"
                        size="xl"
                        :disabled="hasSingleTrackQueue"
                        @click="audioPlayer.playPrevious()"
                    />
                    <button class="btn-play" @click="audioPlayer.togglePlayPause()">
                        <Icon v-if="!isPlaying" class-list="ms-1" :icon="['fas', 'play']" size="xl" />
                        <Icon v-else :icon="['fas', 'pause']" size="xl" />
                    </button>
                    <IconButton
                        icon="forward-step"
                        size="xl"
                        :disabled="hasSingleTrackQueue"
                        @click="audioPlayer.playNext()"
                    />
                    <IconButton
                        icon="repeat"
                        size="lg"
                        :class-list="{
                            'color-active': isRepeat,
                        }"
                        @click="isRepeat = !isRepeat"
                    />
                </div>
                <div class="d-flex flex-row justify-content-between gap-4 w-100">
                    <div v-if="audioPlayer.currentTime" class="font-merge color-grey">
                        {{ formatTime(audioPlayer.currentTime) }}
                    </div>
                    <div
                        ref="seekerContainer"
                        class="w-100 seeker-control"
                        @mouseover="isHover = true"
                        @mouseleave="isHover = false"
                    >
                        <div
                            class="seeker-bar rounded"
                            :style="`width: ${(audioPlayer.currentTime / audioPlayer.duration) * 100 || 0}%;`"
                        />
                        <input
                            :value="(audioPlayer.currentTime / audioPlayer.duration) * 100 || 0"
                            type="range"
                            ref="seeker"
                            class="seeker-slider w-100"
                            min="0"
                            max="100"
                            @input="audioPlayer.seek($event.target.value)"
                            :disabled="!currentTrack"
                        />
                    </div>
                    <div v-if="audioPlayer.duration" class="font-merge color-grey">
                        {{ formatTime(audioPlayer.duration) }}
                    </div>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-start align-items-center w-25 gap-4">
                <VolumeController class="d-none d-lg-flex" />
                <IconButton class-list="ms-auto" :icon="extensionIcon" @click="isExtended = !isExtended" />
            </div>
        </div>
        <Transition>
            <div
                v-if="isExtended"
                class="d-flex flex-column flex-lg-row justify-content-start align-items-center bg-default w-100 overflow-hidden height-100 border-top p-2"
            >
                <DefaultCarousel class="w-100 player-carousel" :config="carouselConfig">
                    <template #slides>
                        <Slide v-for="song in audioPlayer.queue" :key="song.uuid || song.title">
                            <template #default>
                                <PlaylistSongCard :song="song" @deleted="checkQueueLength" />
                            </template>
                        </Slide>
                    </template>
                    <template #navigation>
                        <Navigation>
                            <template #prev>
                                <IconButton icon="arrow-left" size="1x" />
                            </template>
                            <template #next>
                                <IconButton icon="arrow-right" size="1x" />
                            </template>
                        </Navigation>
                    </template>
                </DefaultCarousel>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
@media (max-width: 576px) {
    .height-100 {
        height: 100%;
    }

    .v-enter-active,
    .v-leave-active {
        transition: height 1s ease-in;
    }

    .v-enter-from,
    .v-leave-to {
        height: 0;
    }

    .v-enter-to,
    .v-leave-from {
        height: 100%;
    }
}

@media (min-width: 768px) {
    .height-100 {
        height: 100px;
    }

    .v-enter-active,
    .v-leave-active {
        transition: height 0.5s ease-in;
    }

    .v-enter-from,
    .v-leave-to {
        height: 0;
    }

    .v-enter-to,
    .v-leave-from {
        height: 100px;
    }
}

.seeker-control {
    display: grid;
    grid-template-areas: 'stack';
    align-items: center;
    position: relative;
    width: 200px; /* Adjust as needed */
    height: 20px; /* Adjust as needed */
}

.seeker-bar,
.seeker-slider {
    grid-area: stack;
}

.seeker-bar {
    background-color: var(--primary-color, #e836c5);
    height: 8px;
    z-index: 2; /* Ensure the bar is behind the thumb */
    pointer-events: none; /* Make the bar non-interactive */
}

.seeker-slider {
    -webkit-appearance: none;
    background: transparent;
    width: 100%;
    height: 100%;
    z-index: 1; /* Ensure the slider is interactive */
    cursor: pointer;
}

/* Customizing the thumb for consistent styling */
.seeker-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    background: var(--primary-color, #e836c5);
    border: 1px solid #ccc;
    border-radius: 50%;
    margin-top: -6px; /* Adjust to center the thumb vertically */
}

/* Customizing the track (the gray line) */
.seeker-slider::-webkit-slider-runnable-track {
    width: 100%;
    height: 8px;
    background: #ddd;
    border-radius: 4px;
}
</style>
