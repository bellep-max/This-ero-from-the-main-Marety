<script setup>
import { useAudioPlayerStore } from '@/stores/track.js';
import { storeToRefs } from 'pinia';
import { computed, defineAsyncComponent, ref } from 'vue';
import { isLogged } from '@/Services/AuthService.js';
import { useModal, useVfm } from 'vue-final-modal';
import { setItemLink } from '@/Services/FormService.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const SubscriptionModal = defineAsyncComponent(() => import('@/Components/Modals/SubscriptionModal.vue'));

const audioPlayer = useAudioPlayerStore();
const { isPlaying, currentTrack } = storeToRefs(audioPlayer);

const props = defineProps({
    track: {
        type: Object,
        required: true,
    },
});

const vfm = useVfm();
const isHover = ref(false);

const openSubscriptionModal = () =>
    useModal({
        component: SubscriptionModal,
        attrs: {
            onClose() {
                vfm.close('subscription-modal');
            },
            onConfirm() {
                vfm.close('subscription-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const isCurrentTrackPlaying = computed(() => currentTrack && currentTrack.uuid === props.track.uuid);

const isCurrentTrack = computed(() => currentTrack.value?.uuid === props.track.uuid);

const handlePlayClick = () => {
    if (props.track.is_patron) {
        openSubscriptionModal();
        return;
    }

    try {
        if (isCurrentTrack.value) {
            audioPlayer.togglePlayPause();
        } else {
            audioPlayer.setTracks(props.track);
        }
    } catch (error) {
        console.error('Failed to play track:', error);
    }
};
</script>

<template>
    <div class="audio-carousel-item" @mouseover="isHover = true" @mouseleave="isHover = false">
        <div class="position-relative">
            <div
                class="position-absolute top-50 start-50 translate-middle"
                :class="{
                    'd-none': !isHover,
                    'd-block': isHover,
                }"
            >
                <button class="btn-play lg" @click="handlePlayClick" :disabled="!track.streamable && !isLogged">
                    <Icon
                        :class="{ 'ms-1': !isPlaying || !isCurrentTrack }"
                        :icon="['fas', isPlaying && isCurrentTrack ? 'pause' : 'play']"
                        size="2xl"
                    />
                </button>
            </div>
            <img :src="track.artwork" class="audio-carousel-item__img" :alt="track.title" />
        </div>
        <div class="audio-carousel-item__info">
            <a :href="setItemLink(track)">
                <div class="audio-carousel-item__info__title">
                    {{ track.title }}
                </div>
                <div class="audio-carousel-item__info__desc overflow-hidden text-truncate">
                    {{ track.description }}
                </div>
            </a>
        </div>
    </div>
</template>
