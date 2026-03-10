<script setup>
import { $t } from '@/i18n.js';
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { isLogged, isOwner } from '@/Services/AuthService.js';
import { isNotEmpty } from '@/Services/MiscService.js';
import { BDropdown, BDropdownItem, BDropdownItemButton, BTab, BTabs } from 'bootstrap-vue-next';
import { useModal, useVfm } from 'vue-final-modal';
import { useAudioPlayerStore } from '@/stores/track.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const PlaylistTrack = defineAsyncComponent(() => import('@/Components/PlaylistTrack.vue'));
const PlaylistFollowerCard = defineAsyncComponent(() => import('@/Components/Cards/PlaylistFollowerCard.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const CircledText = defineAsyncComponent(() => import('@/Components/CircledText.vue'));
const ShareModal = defineAsyncComponent(() => import('@/Components/Modals/ShareModal.vue'));
const CommentsSection = defineAsyncComponent(() => import('@/Components/Sections/CommentsSection.vue'));
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const ReportModal = defineAsyncComponent(() => import('@/Components/Modals/ReportModal.vue'));
import route from "@/helpers/route"

const vfm = useVfm();
const audioPlayer = useAudioPlayerStore();

const props = defineProps({
    podcast: {
        type: Object,
        required: true,
    },
    episodes: {
        type: Array,
        required: true,
    },
    season: {
        type: Number,
        required: true,
    },
});

const seasonSongs = ref(JSON.parse(JSON.stringify(props.episodes)));

const isOwned = ref(isOwner(props.podcast.user.uuid));

const isSubscribed = computed(() => props.podcast.favorite);
const user = computed(() => useAuthStore().user);

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
</script>

<template>
    
    <UserLayout :user="podcast.user">
        <div class="d-flex flex-column gap-3">
            <div class="row gy-3">
                <div
                    class="col-12 col-lg-4 d-flex flex-column justify-content-center justify-content-xl-start align-items-start"
                >
                    <img :src="podcast.artwork" :alt="podcast.title" class="img-fluid rounded-4 border-pink" />
                </div>
                <div class="col">
                    <div class="d-flex flex-column gap-3">
                        <div
                            v-if="isLogged"
                            class="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-start flex-wrap"
                        >
                            <CircledText :title="season" :description="$t('misc.season')" />
                            <CircledText :title="episodes.length" :description="$t('pages.podcasts.episodes')" />
                            <BDropdown no-wrapper no-caret toggle-class="btn-default p-2 btn-icon ms-auto">
                                <template #button-content>
                                    <Icon :icon="['fas', 'ellipsis-vertical']" />
                                </template>
                                <template v-if="isLogged && !isOwned">
                                    <BDropdownItemButton
                                        v-if="isSubscribed"
                                        @click="
                                            addToFavorites({ uuid: podcast.uuid, type: ObjectTypes.Podcast }, user.uuid)
                                        "
                                    >
                                        {{ $t('buttons.unsubscribe') }}
                                    </BDropdownItemButton>
                                    <BDropdownItemButton
                                        v-else
                                        @click="
                                            removeFromFavorites(
                                                { uuid: podcast.uuid, type: ObjectTypes.Podcast },
                                                user.uuid,
                                            )
                                        "
                                    >
                                        {{ $t('buttons.subscribe') }}
                                    </BDropdownItemButton>
                                </template>
                                <BDropdownItemButton @click="openShareModal(podcast.title, podcast)">
                                    {{ $t('menus.track.share') }}
                                </BDropdownItemButton>
                                <BDropdownItemButton @click="openReportModal(podcast, ObjectTypes.Podcast)">
                                    {{ $t('menus.track.report') }}
                                </BDropdownItemButton>
                                <BDropdownItem v-if="isOwned" :href="route('podcasts.edit', podcast)">
                                    {{ $t('buttons.edit') }}
                                </BDropdownItem>
                            </BDropdown>
                        </div>
                        <div class="d-flex flex-row align-items-center py-3 border-top border-bottom gap-3">
                            <button class="btn-play lg" @click="audioPlayer.setTracks(seasonSongs)">
                                <Icon class-list="ms-1" :icon="['fas', 'play']" size="2xl" />
                            </button>
                            <div class="d-flex flex-column align-items-start justify-content-start gap-2">
                                <span class="font-default fs-4">{{ podcast.title }}</span>
                                <span class="font-merge">{{ podcast.user?.name }}</span>
                                <div class="d-flex flex-row justify-content-start align-items-center gap-2">
                                    <Icon :icon="['fas', 'calendar-check']" class-list="color-pink" />
                                    <span class="font-merge">
                                        {{ podcast.created_at }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-if="isNotEmpty(podcast.tags)" class="d-flex flex-row gap-2 border-bottom pb-3 flex-wrap">
                            <a
                                v-for="tag in podcast.tags"
                                :href="route('discover.index', { tags: tag.tag })"
                                class="btn-default btn-outline btn-narrow"
                            >
                                {{ tag.tag }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="isNotEmpty(podcast.description)" class="d-flex flex-row text-start my-3">
                <span class="font-default">{{ $t('misc.summary', { text: podcast.description }) }}</span>
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
                    <div class="d-flex flex-column overflow-y-auto playlist-container w-100 justify-content-start">
                        <PlaylistTrack
                            v-for="episode in episodes"
                            :key="episode.uuid"
                            :track="episode"
                            :podcast_uuid="podcast.uuid"
                            :likeable="isLogged"
                            :controllable="isLogged"
                            @like="addToFavorites($event, user.uuid)"
                            @reported="openReportModal($event, ObjectTypes.PodcastEpisode)"
                            @shared="openShareModal($event.title, $event)"
                        />
                    </div>
                </BTab>
                <BTab
                    :title="
                        $t('pages.podcasts.tabs.subscribers', {
                            count: podcast.subscribers_count,
                        })
                    "
                >
                    <div class="row w-100 playlist-container overflow-y-auto">
                        <PlaylistFollowerCard v-for="user in podcast.subscribers" :user="user" />
                    </div>
                </BTab>
                <BTab :title="$t('pages.podcasts.tabs.related')">
                    <!--              <div v-if="isNotEmpty(related)"-->
                    <!--                   class="row w-100 playlist-container overflow-y-auto"-->
                    <!--              >-->
                    <!--                <PlaylistCard v-for="relatedPlaylist in related" :playlist="relatedPlaylist"/>-->
                    <!--              </div>-->
                    <div class="d-flex flex-column w-100 align-items-center justify-content-center text-center">
                        {{ $t('pages.podcasts.no_related') }}
                    </div>
                </BTab>
            </BTabs>
        </div>
        <template v-if="isLogged && podcast.allow_comments" #comments>
            <CommentsSection :model="podcast" :type="podcast.type" :comments="podcast.comments" :uuid="podcast.uuid" />
        </template>
    </UserLayout>
</template>
