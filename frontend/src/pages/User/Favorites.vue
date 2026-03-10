<script setup>
import { ref, reactive, onMounted, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { BTab, BTabs } from 'bootstrap-vue-next';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const Song = defineAsyncComponent(() => import('@/Components/Song.vue'));
const Adventure = defineAsyncComponent(() => import('@/Components/Adventure.vue'));
const ImageCard = defineAsyncComponent(() => import('@/Components/Cards/ImageCard.vue'));
const PlaylistCard = defineAsyncComponent(() => import('@/Components/Cards/PlaylistCard.vue'));
const FollowerCard = defineAsyncComponent(() => import('@/Components/Cards/FollowerCard.vue'));
import route from "@/helpers/route"

const user = ref(null);
const adventures = ref(null);
const podcasts = ref(null);
const episodes = ref(null);
const songs = ref(null);
const playlists = ref(null);
const users = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/users/${currentRoute.params.username}/favorites`);
      const apiData = response.data;
      user.value = apiData.user ?? null;
    adventures.value = apiData.adventures ?? null;
    podcasts.value = apiData.podcasts ?? null;
    episodes.value = apiData.episodes ?? null;
    songs.value = apiData.songs ?? null;
    playlists.value = apiData.playlists ?? null;
    users.value = apiData.users ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const authStore = useAuthStore();
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :title="$t('pages.user.my_favorites.title')" :user="user" :overflow="false">
        <BTabs
            nav-wrapper-class="tabs-header w-100"
            nav-class="px-4 d-flex flex-row justify-content-between w-100"
            active-nav-item-class="tab-item-active fs-5 font-merge"
            nav-item-class="tab-item default-text-color px-4 fs-5 font-merge"
            tab-class="py-4"
            fill
        >
            <BTab :title="$t('pages.user.my_favorites.tabs.songs', { count: songs.length })">
                <Song
                    v-for="song in songs"
                    :song="song"
                    :key="song.uuid"
                    :is-owned="user.uuid === song.user.uuid"
                    @add-to-favorites="addToFavorites($event, authStore.user.uuid)"
                    @remove-from-favorites="removeFromFavorites($event, authStore.user.uuid)"
                    dark-font
                    can-view
                />
            </BTab>
            <BTab
                :title="
                    $t('pages.user.my_favorites.tabs.adventures', {
                        count: adventures.length,
                    })
                "
            >
                <Adventure
                    v-for="adventure in adventures"
                    :adventure="adventure"
                    :key="adventure.uuid"
                    :is-owned="user.uuid === adventure.user.uuid"
                    @add-to-favorites="addToFavorites($event, authStore.user.uuid)"
                    @remove-from-favorites="removeFromFavorites($event, authStore.user.uuid)"
                    dark-font
                    can-view
                />
            </BTab>
            <BTab
                :title="
                    $t('pages.user.my_favorites.tabs.podcasts', {
                        count: podcasts.length,
                    })
                "
            >
                <div class="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
                    <ImageCard
                        v-for="podcast in podcasts"
                        :model="podcast"
                        :route="route('podcasts.show', podcast.uuid)"
                        :key="podcast.uuid"
                    />
                </div>
            </BTab>
            <BTab
                :title="
                    $t('pages.user.my_favorites.tabs.episodes', {
                        count: episodes.length,
                    })
                "
            >
                <div class="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
                    <ImageCard
                        v-for="episode in episodes"
                        :model="episode"
                        :route="route('episodes.show', episode.uuid)"
                        :key="episode.uuid"
                    />
                </div>
            </BTab>
            <BTab
                :title="
                    $t('pages.user.my_favorites.tabs.playlists', {
                        count: playlists.length,
                    })
                "
            >
                <div class="row w-100 vh-100 overflow-y-auto">
                    <PlaylistCard v-for="playlist in playlists" :playlist="playlist" :key="playlist.uuid" />
                </div>
            </BTab>
            <BTab :title="$t('pages.user.my_favorites.tabs.users', { count: users.length })">
                <div class="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
                    <FollowerCard v-for="user in users" :user="user" :key="user.uuid" />
                </div>
            </BTab>
        </BTabs>
    </UserLayout>
</template>
  </template>
