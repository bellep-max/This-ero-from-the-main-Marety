<script setup>
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { isNotEmpty } from '@/Services/MiscService.js';
import { useModal, useVfm } from 'vue-final-modal';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const ImageCard = defineAsyncComponent(() => import('@/Components/Cards/ImageCard.vue'));
const PodcastEpisodeUploadModal = defineAsyncComponent(
    () => import('@/Components/Modals/PodcastEpisodeUploadModal.vue'),
);
const CreatePodcastModal = defineAsyncComponent(() => import('@/Components/Modals/CreatePodcastModal.vue'));
import route from "@/helpers/route"

const vfm = useVfm();

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
    podcasts: {
        required: true,
        type: Object,
        default: {},
    },
});

const currentUser = computed(() => useAuthStore().user);
const canUpload = computed(() => props.user.own_profile && currentUser.value.can_upload);
const pageTitle = computed(() =>
    props.user.own_profile ? $t('pages.user.my_podcasts') : $t('pages.user.user_podcasts', { name: props.user.name }),
);

const openPodcastCreateModal = () => {
    useModal({
        component: CreatePodcastModal,
        attrs: {
            onClose() {
                vfm.close('podcast-create-modal');
            },
            onConfirm() {
                vfm.close('podcast-create-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();
};

const openPodcastUploadModal = () => {
    useModal({
        component: PodcastEpisodeUploadModal,
        attrs: {
            onClose() {
                vfm.close('podcast-upload-modal');
            },
            onConfirm() {
                vfm.close('podcast-upload-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();
};
</script>

<template>
    
    <UserLayout :title="pageTitle" :user="user">
        <template v-if="canUpload" #controls>
            <div class="d-flex flex-row gap-3">
                <DefaultButton @click="openPodcastCreateModal" class-list="btn-outline btn-narrow">
                    {{ $t('buttons.create.podcast') }}
                </DefaultButton>
                <DefaultButton @click="openPodcastUploadModal" class-list="btn-outline btn-narrow">
                    {{ $t('buttons.upload.podcast_episode') }}
                </DefaultButton>
            </div>
        </template>
        <div
            v-if="isNotEmpty(podcasts)"
            class="d-flex flex-row justify-content-start flex-wrap align-items-start gap-2"
        >
            <ImageCard
                v-for="podcast in podcasts"
                :model="podcast"
                :route="route('podcasts.show', podcast)"
                :key="podcast.uuid"
            />
        </div>
        <div v-else class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
            {{ $t('pages.user.no_podcasts', { name: user.name }) }}
        </div>
    </UserLayout>
</template>
