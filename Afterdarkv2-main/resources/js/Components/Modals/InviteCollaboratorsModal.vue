<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { router } from '@inertiajs/vue3';
import { defineAsyncComponent } from 'vue';
const CollaboratorCard = defineAsyncComponent(() => import('@/Components/Cards/CollaboratorCard.vue'));

const props = defineProps({
    playlist_uuid: {
        type: String,
        required: true,
    },
    following: {
        type: Array,
        default: [],
    },
});

const emit = defineEmits(['close']);

const submit = (user) => {
    router.post(
        route('playlists.collaborators.store', {
            playlist: props.playlist_uuid,
        }),
        {
            collaborator_uuid: user.uuid,
        },
    );
};
</script>

<template>
    <VueFinalModal
        class="container-fluid"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-4"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="invite-collab-modal"
    >
        <div class="text-center font-default fs-5 mb-3">
            {{ $t('modals.invite_collaborators.title') }}
        </div>
        <div class="row w-100 playlist-container overflow-y-auto">
            <CollaboratorCard v-for="user in following" :user="user" :playlist_uuid="playlist_uuid" @invited="submit" />
        </div>
    </VueFinalModal>
</template>

<style scoped></style>
