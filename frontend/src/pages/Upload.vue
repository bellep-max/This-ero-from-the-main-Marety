<script setup>
import { $t } from '../i18n.js';
import { computed, defineAsyncComponent, ref, reactive, onMounted } from 'vue';
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { uploadAssetImage } from '@/Services/AssetService.js';
import { useModal, useVfm } from 'vue-final-modal';
import { isNotEmpty } from '@/Services/MiscService.js';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const CircledText = defineAsyncComponent(() => import('@/Components/CircledText.vue'));
const TrackUploadModal = defineAsyncComponent(() => import('@/Components/Modals/TrackUploadModal.vue'));
const AdventureUploadModal = defineAsyncComponent(() => import('@/Components/Modals/AdventureUploadModal.vue'));
const PodcastUploadModal = defineAsyncComponent(() => import('@/Components/Modals/PodcastEpisodeUploadModal.vue'));
const CreatePodcastModal = defineAsyncComponent(() => import('@/Components/Modals/CreatePodcastModal.vue'));
import route from "@/helpers/route"

const vfm = useVfm();

const plan = ref(null);
const genres = ref(null);
const loading = ref(true);

  onMounted(async () => {
    try {
      const response = await apiClient.get('/uploads');
      const apiData = response.data;
      plan.value = apiData.plan ?? null;
    genres.value = apiData.genres ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const showTrackModal = ref(false);
const showAdventureModal = ref(false);
const hasUploadAccess = ref(useAuthStore().user.can_upload);

const openPodcastModal = () => {
    if (isNotEmpty(currentUser.value.podcasts)) {
        useModal({
            component: PodcastUploadModal,
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
    } else {
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
    }
};

const parsedPlanPrice = computed(() => `$${plan.value?.price ?? '0'}`);
const currentUser = computed(() => useAuthStore().user);
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col text-start">
                    <div class="d-block">
                        <div class="block-title color-light text-truncate">
                            {{ $t('pages.upload.title') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col d-flex flex-column justify-content-center align-items-center">
                    <div class="container bg-default rounded-5 p-3 p-lg-5 h-100">
                        <div
                            v-if="hasUploadAccess"
                            class="d-flex flex-column align-items-center justify-content-center gap-4"
                        >
                            <img :src="uploadAssetImage" :alt="$t('pages.upload.title')" />
                            <div class="text-start font-default">
                                <p>{{ $t('pages.upload.recommended_format') }}</p>
                                <p>{{ $t('pages.upload.max_size') }}</p>
                                <p>{{ $t('pages.upload.time_allowed') }}</p>
                                <p>{{ $t('pages.upload.formats_allowed') }}</p>
                            </div>
                            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3">
                                <DefaultButton class-list="btn-pink" @click="showTrackModal = true">
                                    {{ $t('buttons.upload.track') }}
                                </DefaultButton>
                                <DefaultButton class-list="btn-pink" @click="showAdventureModal = true">
                                    {{ $t('buttons.upload.adventure') }}
                                </DefaultButton>
                                <DefaultButton class-list="btn-pink" @click="openPodcastModal">
                                    {{ $t('buttons.upload.podcast_episode') }}
                                </DefaultButton>
                            </div>
                        </div>
                        <div v-else class="d-flex flex-column align-items-center justify-content-center gap-4">
                            <div class="text-center">
                                <p class="font-default default-text-color">
                                    {{ $t('pages.upload.limit_exceeded') }}
                                </p>
                                <p class="font-default default-text-color">
                                    {{ $t('pages.upload.upgrade_account') }}
                                </p>
                            </div>
                            <div class="text-center fs-5 font-default fw-bolder">
                                {{ plan.name }}
                            </div>
                            <CircledText :title="parsedPlanPrice" :description="`/${$t('misc.month')}`" />
                            <DefaultButton :href="route('settings.subscription.edit')" class-list="btn-pink">
                                {{ $t('buttons.buy_now') }}
                            </DefaultButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <TrackUploadModal v-model="showTrackModal" />
    <AdventureUploadModal v-model="showAdventureModal" @close="showAdventureModal = false" />
</template>
  </template>
