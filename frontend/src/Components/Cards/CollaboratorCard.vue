<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, onMounted, ref } from 'vue';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
import route from "@/helpers/route"

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    playlist_uuid: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['invited']);

const isInvited = ref(false);

const sendInvitation = () => {
    emit('invited', props.user);
    isInvited.value = true;
};

onMounted(() => {
    isInvited.value = props.user.playlists?.some((playlist) => playlist.uuid === props.playlist_uuid);
});
</script>

<template>
    <div class="col-12 col-md-6 p-1">
        <div class="playlist-subscriber-card d-flex flex-row justify-content-between align-items-center gap-2 p-3">
            <a :href="route('users.show', user)">
                <img :src="user.artwork" class="playlist-subscriber-card-avatar" :alt="user.title" />
            </a>
            <div class="d-flex flex-column justify-content-between gap-3 text-truncate px-2">
                <a
                    :href="route('users.show', user)"
                    class="title font-default default-text-color text-center text-decoration-none"
                >
                    {{ user.username }}
                </a>
                <DefaultButton
                    :class="[
                        {
                            'btn-pink': isInvited,
                            'btn-outline': !isInvited,
                        },
                        'w-100',
                        'btn-default',
                    ]"
                    :disabled="isInvited"
                    @click="sendInvitation"
                >
                    <Icon :icon="['fas', 'user-plus']" />
                    {{ $t('buttons.invite') }}
                </DefaultButton>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
