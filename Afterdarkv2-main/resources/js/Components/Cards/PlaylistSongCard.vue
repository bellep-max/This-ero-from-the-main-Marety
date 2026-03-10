<script setup>
import { computed, defineAsyncComponent } from 'vue';
import { useAudioPlayerStore } from '@/stores/track.js';
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));

const audioPlayer = useAudioPlayerStore();
const emit = defineEmits(['deleted']);

const props = defineProps({
    song: {
        type: Object,
        required: true,
    },
});

const trimmedTitle = computed(() => `${props.song.title?.substring(0, 25)}...`);

const deleteFromQueue = () => {
    audioPlayer.deleteFromQueue(props.song.uuid);
    emit('deleted');
};
</script>

<template>
    <div class="d-flex flex-row align-items-center justify-content-start gap-3 p-2 module position-relative w-100">
        <IconButton
            class-list="position-absolute top-0 start-100 translate-middle"
            icon="xmark"
            @click="deleteFromQueue"
        />
        <img class="img-thumb rounded-2" :src="song.artwork" :alt="song.title" />
        <span class="text-truncate color-text font-default flex-nowrap text-decoration-none">
            {{ trimmedTitle }}
        </span>
    </div>
</template>
