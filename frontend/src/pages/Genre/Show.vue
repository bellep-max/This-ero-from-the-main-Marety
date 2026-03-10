<script setup>
import { $t } from '@/i18n.js';
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { isLogged } from '@/Services/AuthService.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { BTab, BTabs } from 'bootstrap-vue-next';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const Adventure = defineAsyncComponent(() => import('@/Components/Adventure.vue'));
const Song = defineAsyncComponent(() => import('@/Components/Song.vue'));
const DefaultLink = defineAsyncComponent(() => import('@/Components/Links/DefaultLink.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const props = defineProps({
    genre: {
        type: Object,
        default: {},
    },
    slides: {
        type: Array,
        default: [],
    },
    channels: {
        type: Array,
        default: [],
    },
    related: {
        type: Array,
        default: [],
    },
});

const authStore = useAuthStore();

const songs = ref([]);
const adventures = ref([]);
const isLastSongPage = ref(false);
const isLastAdventurePage = ref(false);

const user = computed(() => authStore.user);

const loadMore = (type) => {
    if (type === ObjectTypes.Song) {
        apiClient.get(
            songs.value.links?.next,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                only: ['songs', 'pagination'],
                onSuccess: (page) => {
                    // Assuming you have a `songs` ref in your component
                    songs.value.data = [...songs.value.data, ...authStore.songs.data];

                    // Update the pagination metadata with the new data from the server
                    songs.value.links = authStore.songs.links;
                    songs.value.meta = authStore.songs.meta;

                    isLastSongPage.value = checkLastPage(ObjectTypes.Song);
                },
            },
        );
    } else {
        apiClient.get(
            adventures.value.links?.next,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                only: ['adventures', 'pagination'],
                onSuccess: (page) => {
                    // Assuming you have a `songs` ref in your component
                    adventures.value.data = [...adventures.value.data, ...authStore.adventures.data];

                    // Update the pagination metadata with the new data from the server
                    adventures.value.links = authStore.adventures.links;
                    adventures.value.meta = authStore.adventures.meta;

                    isLastAdventurePage.value = checkLastPage(ObjectTypes.Adventure);
                },
            },
        );
    }
};

const checkLastPage = (type) => {
    return ObjectTypes.getObjectType(type) === ObjectTypes.Adventure
        ? adventures.value.meta?.current_page === adventures.value.meta?.last_page
        : songs.value.meta?.current_page === songs.value.meta?.last_page;
};

onMounted(() => {
    songs.value = authStore.songs;
    adventures.value = authStore.adventures;

    isLastSongPage.value = checkLastPage(ObjectTypes.Song);
    isLastAdventurePage.value = checkLastPage(ObjectTypes.Adventure);
});
</script>

<template>
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col text-start">
                    <div class="d-block">
                        <div class="block-title color-light text-truncate">
                            {{ genre.title }}
                        </div>
                        <DefaultLink
                            class-list="block-description color-light text-decoration-none"
                            :link="route('genres.index')"
                        >
                            {{ $t('pages.genres.see_all') }}
                        </DefaultLink>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <BTabs
                        nav-wrapper-class="tabs-header w-100"
                        nav-class="px-4 w-100"
                        nav-item-class="tab-item color-light px-4 fs-5 font-merge"
                        active-nav-item-class="tab-item-active fs-5 font-merge"
                        tab-class="py-4"
                        fill
                    >
                        <BTab
                            :title="
                                $t('pages.discover.tabs.songs', {
                                    count: songs.meta?.total ?? 0,
                                })
                            "
                        >
                            <div class="row">
                                <div class="col flex-column overflow-y-auto p-1">
                                    <Song
                                        v-for="song in songs.data"
                                        :song="song"
                                        :can-view="isLogged"
                                        :is-owned="song.user.uuid === user?.uuid"
                                        @add-to-favorites="addToFavorites($event, authStore.user.uuid)"
                                        @remove-from-favorites="removeFromFavorites($event, authStore.user.uuid)"
                                    />
                                    <DefaultButton
                                        class-list="btn-outline mt-2 mx-auto"
                                        @click.prevent="loadMore(ObjectTypes.Song)"
                                        :disabled="isLastSongPage"
                                    >
                                        {{ $t('buttons.load_more') }}
                                    </DefaultButton>
                                </div>
                            </div>
                        </BTab>
                        <BTab
                            :title="
                                $t('pages.discover.tabs.adventures', {
                                    count: adventures.meta?.total ?? 0,
                                })
                            "
                        >
                            <div class="row">
                                <div class="col flex-column overflow-y-auto p-1">
                                    <Adventure
                                        v-for="adventure in adventures.data"
                                        :adventure="adventure"
                                        :can-view="isLogged"
                                        :is-owned="adventure.user.uuid === user?.uuid"
                                        @add-to-favorites="addToFavorites($event, authStore.user.uuid)"
                                        @remove-from-favorites="removeFromFavorites($event, authStore.user.uuid)"
                                    />
                                    <DefaultButton
                                        class-list="btn-outline mt-2 mx-auto"
                                        @click.prevent="loadMore(ObjectTypes.Adventure)"
                                        :disabled="isLastAdventurePage"
                                    >
                                        {{ $t('buttons.load_more') }}
                                    </DefaultButton>
                                </div>
                            </div>
                        </BTab>
                    </BTabs>
                </div>
            </div>
        </div>
    </div>
</template>
