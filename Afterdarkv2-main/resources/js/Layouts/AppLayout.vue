<script setup>
import { defineAsyncComponent, onMounted, ref, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ModalsContainer, useVfm } from 'vue-final-modal';
import { toast } from 'vue3-toastify';
import { storeToRefs } from 'pinia';
import { useAudioPlayerStore } from '@/stores/track.js';
const Header = defineAsyncComponent(() => import('@/Layouts/Components/Header.vue'));
const Footer = defineAsyncComponent(() => import('@/Layouts/Components/Footer.vue'));
const AdultOnlyModal = defineAsyncComponent(() => import('@/Components/Modals/AdultOnlyModal.vue'));
const Player = defineAsyncComponent(() => import('@/Components/Player/Player.vue'));
const Persistent = defineAsyncComponent(() => import('@/Components/Layout/Persistent.vue'));
const AdventureBranchSelectModal = defineAsyncComponent(
    () => import('@/Components/Modals/AdventureBranchSelectModal.vue'),
);

// Get the audio player store instance
const audioPlayer = useAudioPlayerStore();
const { showAdventureModal, currentTrack } = storeToRefs(audioPlayer);

const props = defineProps({
    title: {
        type: String,
        required: false,
    },
});

const page = usePage();
const vfm = useVfm();
const showAdultOnlyModal = ref(false);

const showToast = (text, type) => {
    toast(text, {
        position: 'bottom-left',
        type: type,
        transition: toast.TRANSITIONS.SLIDE,
    });
};

const showErrors = (errors) => {
    for (const error of errors) {
        showToast(error, 'error');
    }
};

const showFlashMessage = (message) => {
    showToast(message.content, message.level);
};

const setTrack = (track) => {
    audioPlayer.setTracks(track);
    showAdventureModal.value = false;
};

watch(
    () => page.props.errors,
    (newVal) => {
        showErrors(Object.values(newVal));
    },
);

watch(
    () => page.props.flash_message,
    (newVal) => {
        if (newVal) {
            showFlashMessage(newVal);
        }
    },
);

// Reinitialize audio on page transitions if needed
watch(
    () => page.url,
    () => {
        if (audioPlayer.isPlaying && !audioPlayer.audio) {
            audioPlayer.initializeAudio();
        }
    },
);

// Ensure audio is properly initialized when component mounts
onMounted(() => {
    if (audioPlayer.currentTrack && !audioPlayer.audio) {
        audioPlayer.initializeAudio();
    }

    showAdultOnlyModal.value = !page.props.auth.is_adult;
});
</script>

<template>
    <Head :title="title" />

    <div class="d-flex flex-column min-vh-100">
        <Header />
        <div class="app-main flex-column">
            <!--begin::Content wrapper-->
            <div class="d-flex flex-column">
                <slot />
            </div>
        </div>
        <!-- Keep Player outside the main content area to persist across page changes -->
        <Persistent>
            <Player />
        </Persistent>
        <Footer />
    </div>
    <AdultOnlyModal v-model="showAdultOnlyModal" @accepted="vfm.close('adult-only-modal')" />
    <AdventureBranchSelectModal v-model="showAdventureModal" :adventure="currentTrack" @selected="setTrack" />
    <ModalsContainer />
</template>
