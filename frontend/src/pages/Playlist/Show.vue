<script setup>
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent, ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import {
    BDropdown,
    BDropdownDivider,
    BDropdownGroup,
    BDropdownItem,
    BDropdownItemButton,
    BTab,
    BTabs,
} from 'bootstrap-vue-next';
import { useModal, useVfm } from 'vue-final-modal';
import { useAudioPlayerStore } from '@/stores/track.js';
import { isNotEmpty } from '@/Services/MiscService.js';
import { isLogged } from '@/Services/AuthService.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
import { removeFromPlaylist } from '@/Services/PlaylistService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const CircledText = defineAsyncComponent(() => import('@/Components/CircledText.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const PlaylistCard = defineAsyncComponent(() => import('@/Components/Cards/PlaylistCard.vue'));
const PlaylistTrack = defineAsyncComponent(() => import('@/Components/PlaylistTrack.vue'));
const PlaylistFollowerCard = defineAsyncComponent(() => import('@/Components/Cards/PlaylistFollowerCard.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const ConfirmDeletionModal = defineAsyncComponent(() => import('@/Components/Modals/ConfirmDeletionModal.vue'));
const InviteCollaboratorsModal = defineAsyncComponent(() => import('@/Components/Modals/InviteCollaboratorsModal.vue'));
const LoginModal = defineAsyncComponent(() => import('@/Components/Modals/LoginModal.vue'));
const ShareModal = defineAsyncComponent(() => import('@/Components/Modals/ShareModal.vue'));
const ReportModal = defineAsyncComponent(() => import('@/Components/Modals/ReportModal.vue'));
const CommentsSection = defineAsyncComponent(() => import('@/Components/Sections/CommentsSection.vue'));
import route from "@/helpers/route"

const audioPlayer = useAudioPlayerStore();
const vfm = useVfm();

const playlist = ref(null);
const following = ref(null);
const related = ref(null);
const comments = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/playlists/${currentRoute.params.uuid}`);
      const apiData = response.data;
      playlist.value = apiData.playlist ?? null;
    following.value = apiData.following ?? null;
    related.value = apiData.related ?? null;
    comments.value = apiData.comments ?? null;
    if (playlist.value) {
      isSubscribed.value = playlist.value.favorite ?? false;
      playlistSongs.value = playlist.value.songs ? JSON.parse(JSON.stringify(playlist.value.songs)) : [];
    }
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const isSubscribed = ref(false);

const playlistSongs = ref([]);

const ownPlaylist = computed(() => playlist.value?.user.own_profile);
const isEditable = computed(() => playlist.value?.is_editable);
const user = computed(() => useAuthStore().user);

const setCollaboration = (bool) => {
    apiClient.post(route('playlists.collab.set', playlist.value?.uuid), {
        collaboration: bool,
    });
};

const openDeleteModal = () =>
    useModal({
        component: ConfirmDeletionModal,
        attrs: {
            title: playlist.value?.title,
            type: ObjectTypes.Playlist,
            onClose() {
                vfm.close('delete-modal');
            },
            onConfirm() {
                apiClient.delete(route('playlists.destroy', playlist.value?.uuid));
                vfm.close('delete-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const openInviteModal = () =>
    useModal({
        component: InviteCollaboratorsModal,
        attrs: {
            playlist_uuid: playlist.value?.uuid,
            following: following.value,
            onClose() {
                vfm.close('invite-collab-modal');
            },
            onConfirm() {
                vfm.close('invite-collab-modal');
            },
        },
    }).open();

const openShareModal = (title, item) =>
    useModal({
        component: ShareModal,
        attrs: {
            title: title,
            item: item,
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

const openReportModal = (item, type) =>
    useModal({
        component: ReportModal,
        attrs: {
            item: item,
            type: type,
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
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :user="playlist.user">
        <div class="d-flex flex-column gap-3">
            <div class="row gy-3">
                <div class="col-12 col-lg-4 d-flex justify-content-center justify-content-xl-start align-items-start">
                    <img :src="playlist.artwork" :alt="playlist.title" class="img-fluid rounded-4 border-pink" />
                </div>
                <div class="col">
                    <div class="d-flex flex-column gap-4">
                        <div
                            v-if="isLogged"
                            class="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-start flex-wrap"
                        >
                            <CircledText :title="playlist.songs_count" :description="$t('pages.playlist.songs')" />
                            <CircledText
                                :title="playlist.subscribers_count"
                                :description="$t('pages.playlist.subscribers')"
                            />
                            <CircledText :title="playlist.duration" :description="$t('pages.playlist.duration')" />
                            <BDropdown no-wrapper no-caret toggle-class="btn-default p-2 btn-icon ms-auto">
                                <template #button-content>
                                    <Icon :icon="['fas', 'ellipsis-vertical']" />
                                </template>
                                <BDropdownItemButton @click="openShareModal(playlist.title, playlist)">
                                    {{ $t('buttons.share.title') }}
                                </BDropdownItemButton>
                                <BDropdownItemButton @click="openReportModal(playlist, ObjectTypes.Playlist)">
                                    {{ $t('menus.track.report') }}
                                </BDropdownItemButton>
                                <template v-if="isLogged && !ownPlaylist">
                                    <BDropdownItemButton
                                        v-if="isSubscribed"
                                        @click="
                                            removeFromFavorites(
                                                { uuid: playlist.uuid, type: ObjectTypes.Playlist },
                                                user.uuid,
                                            )
                                        "
                                    >
                                        <Icon :icon="['fas', 'xmark']" />
                                        {{ $t('buttons.unsubscribe') }}
                                    </BDropdownItemButton>
                                    <BDropdownItemButton
                                        v-else
                                        @click="
                                            addToFavorites(
                                                { uuid: playlist.uuid, type: ObjectTypes.Playlist },
                                                user.uuid,
                                            )
                                        "
                                    >
                                        <Icon :icon="['fas', 'check']" />
                                        {{ $t('buttons.subscribe') }}
                                    </BDropdownItemButton>
                                </template>
                                <BDropdownGroup
                                    v-if="ownPlaylist"
                                    :header="$t('pages.playlist.menus.manage_collaborative')"
                                >
                                    <BDropdownItem
                                        v-if="!playlist.collaboration"
                                        @click="setCollaboration(true)"
                                        link-class="font-merge"
                                    >
                                        {{ $t('pages.playlist.menus.make_collaborative') }}
                                    </BDropdownItem>
                                    <template v-else>
                                        <BDropdownItem @click="openInviteModal" link-class="font-merge">
                                            {{ $t('pages.playlist.menus.invite_collaborators') }}
                                        </BDropdownItem>
                                        <BDropdownItem @click="setCollaboration(false)" link-class="font-merge">
                                            {{ $t('pages.playlist.menus.disable_collaborative') }}
                                        </BDropdownItem>
                                    </template>
                                    <BDropdownDivider />
                                    <BDropdownItem :href="route('playlists.edit', playlist)" link-class="font-merge">
                                        {{ $t('buttons.edit') }}
                                    </BDropdownItem>
                                    <BDropdownItem @click="openDeleteModal" link-class="font-merge">
                                        {{ $t('buttons.delete') }}
                                    </BDropdownItem>
                                </BDropdownGroup>
                            </BDropdown>
                        </div>
                        <div class="d-flex flex-row align-items-center py-3 border-top border-bottom gap-3">
                            <button class="btn-play lg" @click="audioPlayer.setTracks(playlistSongs)">
                                <Icon class-list="ms-1" :icon="['fas', 'play']" size="2xl" />
                            </button>
                            <div class="d-flex flex-column align-items-start justify-content-start gap-2">
                                <span class="font-default fs-4">{{ playlist.title }}</span>
                                <span class="font-merge">{{ playlist.user?.name }}</span>
                                <div class="d-flex flex-row justify-content-start align-items-center gap-2">
                                    <Icon :icon="['fas', 'calendar-check']" class-list="color-pink" />
                                    <span class="font-merge">
                                        {{ playlist.created_at }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-if="isNotEmpty(playlist.tags)" class="d-flex flex-row gap-2 border-bottom py-3">
                            <a
                                v-for="tag in playlist.tags"
                                :href="route('discover.index', { tags: tag.tag })"
                                class="btn-default btn-outline btn-narrow"
                            >
                                {{ tag.tag }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="isNotEmpty(playlist.description)" class="d-flex flex-row text-start my-3">
                <span class="font-default">{{ $t('misc.summary', { text: playlist.description }) }}</span>
            </div>
            <BTabs
                nav-wrapper-class="tabs-header w-100"
                nav-class="px-4 w-100"
                active-nav-item-class="tab-item-active fs-5 font-merge"
                nav-item-class="tab-item default-text-color px-4 fs-5 font-merge"
                tab-class="py-4"
                fill
            >
                <BTab :title="$t('pages.playlist.tabs.overview')">
                    <div class="row playlist-container">
                        <div class="col-12 col-lg-7 flex-column overflow-y-auto">
                            <PlaylistTrack
                                v-for="song in playlist.songs"
                                :track="song"
                                :likeable="isLogged"
                                :draggable="playlist.draggable"
                                :controllable="isEditable || ownPlaylist"
                                @like="addToFavorites($event, user.uuid)"
                                @deleted="
                                    removeFromPlaylist({
                                        song_uuid: $event,
                                        playlist_uuid: playlist.uuid,
                                    })
                                "
                                @reported="openReportModal($event, ObjectTypes.PodcastEpisode)"
                                @shared="openShareModal($event.title, $event)"
                            />
                        </div>
                        <div class="col-12 col-lg-5">
                            <h3 class="tab-item text-center">
                                {{ $t('pages.playlist.recent_activity') }}
                            </h3>
                            <div class="d-flex flex-column"></div>
                        </div>
                    </div>
                    <!--      <template v-if="isLogged" #comments>-->
                    <!--        <CommentsSection :model="song"/>-->
                    <!--      </template>-->
                </BTab>
                <BTab
                    :title="
                        $t('pages.playlist.tabs.subscribers', {
                            count: playlist.subscribers_count,
                        })
                    "
                >
                    <div class="row w-100 playlist-container overflow-y-auto">
                        <PlaylistFollowerCard v-for="user in playlist.subscribers" :user="user" />
                    </div>
                </BTab>
                <BTab
                    :disabled="!playlist.collaboration"
                    :title="
                        $t('pages.playlist.tabs.collaborators', {
                            count: playlist.collaborators_count,
                        })
                    "
                >
                    <div v-if="playlist.collaborators.length" class="row w-100 playlist-container overflow-y-auto">
                        <PlaylistFollowerCard v-for="user in playlist.collaborators" :user="user" />
                    </div>
                    <div v-else class="d-flex flex-row w-100 justify-content-center align-items-center">
                        <DefaultButton
                            :disabled="!playlist.collaboration"
                            class-list="btn-pink"
                            @click="openInviteModal"
                        >
                            {{ $t('pages.playlist.menus.invite_collaborators') }}
                        </DefaultButton>
                    </div>
                    <!--            <div v-if="checkIsNotEmpty(user.free_followers)"-->
                    <!--                 class="row w-100 vh-100 overflow-y-auto"-->
                    <!--            >-->
                    <!--              <FollowerCard v-for="user in free_followers" :user="user"/>-->
                    <!--            </div>-->
                </BTab>
                <BTab :title="$t('pages.playlist.tabs.related')">
                    <div v-if="isNotEmpty(related)" class="row w-100 playlist-container overflow-y-auto">
                        <PlaylistCard v-for="relatedPlaylist in related" :playlist="relatedPlaylist" />
                    </div>
                    <div v-else class="d-flex flex-column w-100 align-items-center justify-content-center text-center">
                        {{ $t('pages.user.no_adventures', { name: playlist.user.name }) }}
                    </div>
                </BTab>
            </BTabs>
        </div>
        <template v-if="isLogged && playlist.allow_comments" #comments>
            <CommentsSection
                :comments="comments"
                :type="playlist.type"
                :uuid="playlist.uuid"
                @commented="updateComments"
            />
        </template>
    </UserLayout>
</template>
  </template>
