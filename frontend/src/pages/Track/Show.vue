<script setup>
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent, ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { useAudioPlayerStore } from '@/stores/track.js';
import { storeToRefs } from 'pinia';
import { useModal, useVfm } from 'vue-final-modal';
import { BDropdown, BDropdownItem, BDropdownItemButton } from 'bootstrap-vue-next';
import { isLogged } from '@/Services/AuthService.js';
import { isNotEmpty } from '@/Services/MiscService.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const CircledText = defineAsyncComponent(() => import('@/Components/CircledText.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const CommentsSection = defineAsyncComponent(() => import('@/Components/Sections/CommentsSection.vue'));
const ConfirmDeletionModal = defineAsyncComponent(() => import('@/Components/Modals/ConfirmDeletionModal.vue'));
const ShareModal = defineAsyncComponent(() => import('@/Components/Modals/ShareModal.vue'));
const DownloadModal = defineAsyncComponent(() => import('@/Components/Modals/DownloadModal.vue'));
const ReportModal = defineAsyncComponent(() => import('@/Components/Modals/ReportModal.vue'));
const SubscriptionModal = defineAsyncComponent(() => import('@/Components/Modals/SubscriptionModal.vue'));
import route from "@/helpers/route"

const audioPlayer = useAudioPlayerStore();
const { isPlaying, currentTrack } = storeToRefs(audioPlayer);

const track = ref(null);
const user = ref(null);
const comments = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/songs/${currentRoute.params.uuid}`);
      const apiData = response.data;
      track.value = apiData.track ?? null;
    user.value = apiData.user ?? null;
    comments.value = apiData.comments ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const loggedUser = useAuthStore().user;
const vfm = useVfm();

const isOwned = computed(() => loggedUser && loggedUser.uuid === user.value?.uuid);
const canDownload = computed(
    () => user.value?.own_profile || loggedUser.group_settings?.option_download_hd || track.value?.allow_download,
);
const isCurrentTrack = computed(() => currentTrack.value?.uuid === track.value?.uuid);
const type = computed(() => ObjectTypes.getObjectType(track.value?.type));

const openDeleteModal = () =>
    useModal({
        component: ConfirmDeletionModal,
        attrs: {
            title: track.value?.title,
            type: type.value,
            onClose() {
                vfm.close('delete-track-modal');
            },
            onConfirm() {
                apiClient.delete(route('tracks.destroy', track.value));
                vfm.close('delete-track-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const openShareModal = () =>
    useModal({
        component: ShareModal,
        attrs: {
            title: track.value?.title,
            item: track.value,
            onClose() {
                vfm.close('share-modal');
            },
            onConfirm() {
                vfm.close('share-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const openDownloadModal = () =>
    useModal({
        component: DownloadModal,
        attrs: {
            title: track.value?.title,
            item: track.value,
            type: type.value,
            onClose() {
                vfm.close('download-modal');
            },
            onConfirm() {
                vfm.close('download-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const openReportModal = () =>
    useModal({
        component: ReportModal,
        attrs: {
            item: track.value,
            type: type.value,
            onClose() {
                vfm.close('report-modal');
            },
            onConfirm() {
                vfm.close('report-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const updateComments = async () => {
  try {
    const response = await apiClient.get(currentRoute.path.replace(/^\//, ''));
    const apiData = response.data;
    if (apiData.comments) comments.value = apiData.comments;
  } catch (error) {
    console.error('Failed to refresh comments:', error);
  }
};

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

const handlePlayClick = () => {
    if (track.value?.is_patron) {
        openSubscriptionModal();
        return;
    }

    try {
        if (isCurrentTrack.value) {
            audioPlayer.togglePlayPause();
        } else {
            audioPlayer.setTracks(track.value);
        }
    } catch (error) {
        console.error('Failed to play track:', error);
    }
};
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :user="user" :slides="track.slides">
        <div class="row gy-3">
            <div
                class="col-12 col-lg-4 d-flex flex-column justify-content-center justify-content-xl-start align-items-start"
            >
                <img :src="track.artwork" :alt="track.title" class="img-fluid rounded-4 border-pink" />
            </div>
            <div class="col">
                <div class="d-flex flex-column gap-3">
                    <div
                        v-if="isLogged"
                        class="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-start flex-wrap"
                    >
                        <CircledText :title="track.total_plays" :description="$t('pages.user.total_plays')" />
                        <CircledText :title="track.likes" :description="$t('misc.likes')" />
                        <BDropdown no-wrapper no-caret toggle-class="btn-default p-2 btn-icon ms-auto">
                            <template #button-content>
                                <Icon :icon="['fas', 'ellipsis-vertical']" />
                            </template>
                            <BDropdownItemButton
                                v-if="track.is_liked"
                                @click="removeFromFavorites({ uuid: track.uuid, type: type }, loggedUser.uuid)"
                            >
                                {{ $t('menus.track.remove_from_favorites') }}
                            </BDropdownItemButton>
                            <BDropdownItemButton
                                v-else
                                @click="addToFavorites({ uuid: track.uuid, type: type }, loggedUser.uuid)"
                            >
                                {{ $t('menus.track.add_to_favorites') }}
                            </BDropdownItemButton>
                            <BDropdownItemButton @click="openShareModal">
                                {{ $t('menus.track.share') }}
                            </BDropdownItemButton>
                            <BDropdownItemButton @click="openReportModal">
                                {{ $t('menus.track.report') }}
                            </BDropdownItemButton>
                            <BDropdownItem v-if="isOwned" :href="route(`${type}s.edit`, track.uuid)">
                                {{ $t('buttons.edit') }}
                            </BDropdownItem>
                            <BDropdownItemButton v-if="isOwned" @click="openDeleteModal">
                                {{ $t('buttons.delete') }}
                            </BDropdownItemButton>
                            <BDropdownItem v-if="canDownload" @click="openDownloadModal">
                                {{ $t('buttons.download') }}
                            </BDropdownItem>
                        </BDropdown>
                    </div>
                    <div class="d-flex flex-row align-items-center py-3 border-top border-bottom gap-3">
                        <div class="d-block">
                            <button
                                class="btn-play lg"
                                @click="handlePlayClick"
                                :disabled="!track.streamable && !isLogged"
                            >
                                <Icon
                                    :class="{ 'ms-1': !isPlaying || !isCurrentTrack }"
                                    :icon="['fas', isPlaying && isCurrentTrack ? 'pause' : 'play']"
                                    size="2xl"
                                />
                            </button>
                        </div>
                        <div class="d-flex flex-column align-items-start justify-content-start gap-2">
                            <span class="font-default fs-4">{{ track.title }}</span>
                            <span class="font-merge">{{ track.user.name }}</span>
                            <div class="d-flex flex-row justify-content-start align-items-center gap-2">
                                <Icon :icon="['fas', 'calendar-check']" class-list="color-pink" />
                                <span class="font-merge">
                                    {{ track.created_at }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-if="isNotEmpty(track.tags)" class="d-flex flex-row gap-2 border-bottom pb-3 flex-wrap">
                        <a
                            v-for="tag in track.tags"
                            :href="route('discover.index', { tags: [tag.tag] })"
                            class="btn-default btn-outline btn-narrow"
                        >
                            {{ tag.tag }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="isNotEmpty(track.description)" class="d-flex flex-row text-start my-3">
            <span class="font-default">{{ $t('misc.summary', { text: track.description }) }}</span>
        </div>
        <template v-if="isLogged && track.allow_comments" #comments>
            <CommentsSection :comments="comments" :type="track.type" :uuid="track.uuid" @commented="updateComments" />
        </template>
    </UserLayout>
</template>
  </template>
