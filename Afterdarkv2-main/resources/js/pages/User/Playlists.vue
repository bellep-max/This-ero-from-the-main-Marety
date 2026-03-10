<script setup>
import { Head } from '@inertiajs/vue3';
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent } from 'vue';
import { showModal } from '@/Services/ModalService.js';
import { isNotEmpty } from '@/Services/MiscService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const PlaylistCard = defineAsyncComponent(() => import('@/Components/Cards/PlaylistCard.vue'));
const CreatePlaylistModal = defineAsyncComponent(() => import('@/Components/Modals/CreatePlaylistModal.vue'));

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
    playlists: {
        required: true,
        type: Object,
        default: {},
    },
});

const pageTitle = computed(() =>
    props.user.own_profile ? $t('pages.user.my_playlists') : $t('pages.user.user_playlists', { name: props.user.name }),
);
</script>

<template>
    <Head :title="pageTitle" />
    <UserLayout :title="pageTitle" :user="user">
        <template v-if="user.own_profile" #controls>
            <DefaultButton class-list="btn-outline btn-narrow" @click="showModal(CreatePlaylistModal)">
                {{ $t('buttons.create.playlist') }}
            </DefaultButton>
        </template>
        <div v-if="isNotEmpty(playlists)" class="row w-100 vh-100 overflow-y-auto">
            <PlaylistCard v-for="playlist in playlists" :playlist="playlist" :controllable="user.own_profile" />
        </div>
        <div v-else class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
            {{ $t('pages.user.no_playlists', { name: user.name }) }}
        </div>
    </UserLayout>
</template>
