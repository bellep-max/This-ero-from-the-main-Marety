<script setup>
import { defineAsyncComponent, onMounted, ref, watch } from 'vue';
import { ModalsContainer, useVfm } from 'vue-final-modal';
import { toast } from 'vue3-toastify';
import { storeToRefs } from 'pinia';
import { useAudioPlayerStore } from '@/stores/track.js';
import { useAuthStore } from '@/stores/auth';
import { useRoute } from 'vue-router';
const Header = defineAsyncComponent(() => import('@/Layouts/Components/Header.vue'));
const Footer = defineAsyncComponent(() => import('@/Layouts/Components/Footer.vue'));
const AdultOnlyModal = defineAsyncComponent(() => import('@/Components/Modals/AdultOnlyModal.vue'));
const Player = defineAsyncComponent(() => import('@/Components/Player/Player.vue'));
const Persistent = defineAsyncComponent(() => import('@/Components/Layout/Persistent.vue'));
const AdventureBranchSelectModal = defineAsyncComponent(
    () => import('@/Components/Modals/AdventureBranchSelectModal.vue'),
);

const audioPlayer = useAudioPlayerStore();
const { showAdventureModal, currentTrack } = storeToRefs(audioPlayer);

const props = defineProps({
    title: {
        type: String,
        required: false,
    },
});

const authStore = useAuthStore();
const vfm = useVfm();
const currentRoute = useRoute();
const showAdultOnlyModal = ref(false);

const showToast = (text, type) => {
    toast(text, {
        position: 'bottom-left',
        type: type,
        transition: toast.TRANSITIONS.SLIDE,
    });
};

const setTrack = (track) => {
    audioPlayer.setTracks(track);
    showAdventureModal.value = false;
};

watch(
    () => currentRoute.fullPath,
    () => {
        if (audioPlayer.isPlaying && !audioPlayer.audio) {
            audioPlayer.initializeAudio();
        }
    },
);

watch(
    () => props.title,
    (newVal) => {
        if (newVal) {
            document.title = newVal;
        }
    },
    { immediate: true },
);

onMounted(() => {
    if (audioPlayer.currentTrack && !audioPlayer.audio) {
        audioPlayer.initializeAudio();
    }

    showAdultOnlyModal.value = !authStore.isAdult;
});
</script>

<template>
    <div class="d-flex flex-column min-vh-100">
        <Header />
        <div class="app-main flex-column">
            <div class="d-flex flex-column">
                <slot />
            </div>
        </div>
        <Persistent>
            <Player />
        </Persistent>
        <Footer />
    </div>
    <AdultOnlyModal v-model="showAdultOnlyModal" @accepted="vfm.close('adult-only-modal')" />
    <AdventureBranchSelectModal v-model="showAdventureModal" :adventure="currentTrack" @selected="setTrack" />
    <ModalsContainer />
</template>
