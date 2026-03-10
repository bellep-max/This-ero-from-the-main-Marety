<script setup>
import { $t } from '../i18n.js';
import { defineAsyncComponent, onMounted, ref, watch } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import {
    BAccordion,
    BAccordionItem,
    BFormCheckboxGroup,
    BFormRadioGroup,
    BOffcanvas,
    BTab,
    BTabs,
} from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys } from '@/Services/FormService.js';
import { isLogged } from '@/Services/AuthService.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
const Song = defineAsyncComponent(() => import('@/Components/Song.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const Adventure = defineAsyncComponent(() => import('@/Components/Adventure.vue'));
const Slider = defineAsyncComponent(() => import('@vueform/slider'));
const Multiselect = defineAsyncComponent(() => import('@vueform/multiselect'));
import '@vueform/slider/themes/default.css';
import '@vueform/multiselect/themes/default.css';
import { addToPlaylist } from '@/Services/PlaylistService.js';
import route from "@/helpers/route"

const loading = ref(true);
const song_max_duration = ref(3600);

const authStore = useAuthStore();
const user = authStore.user;

const genres = ref([]);
const tags = ref([]);
const vocals = ref([]);

const hasFilters = ref(false);
const showFilters = ref(false);
const isLastSongPage = ref(false);
const isLastAdventurePage = ref(false);
const filteredTags = ref([]);

const songs = ref({ data: [], meta: {}, links: {} });
const adventures = ref({ data: [], meta: {}, links: {} });

const form = useForm({
    genres: [],
    vocals: [],
    duration: [0, song_max_duration.value],
    released_at: null,
    tags: [],
    type: null,
});

const releaseDates = [
    {
        text: $t('pages.discover.filters.release_date.options.recent'),
        value: 1,
    },
    {
        text: $t('pages.discover.filters.release_date.options.older'),
        value: 0,
    },
];

onMounted(async () => {
    try {
        const response = await apiClient.get('/discover');
        const apiData = response.data;
        if (apiData.songs) songs.value = apiData.songs;
        if (apiData.adventures) adventures.value = apiData.adventures;
        if (apiData.filters) {
            genres.value = apiData.filters.genres ?? [];
            tags.value = apiData.filters.tags ?? [];
            vocals.value = apiData.filters.vocals ?? [];
            filteredTags.value = apiData.filters.tags ?? [];
        }
        if (apiData.song_max_duration) {
            song_max_duration.value = apiData.song_max_duration;
            form.duration = [0, apiData.song_max_duration];
        }
        isLastSongPage.value = checkLastPage(ObjectTypes.Song);
        isLastAdventurePage.value = checkLastPage(ObjectTypes.Adventure);
    } catch (error) {
        console.error('Failed to load page data:', error);
    } finally {
        loading.value = false;
    }
});

const setFiltersDrawer = () => {
    showFilters.value = !showFilters.value;
};

const resetFilters = () => {
    form.reset();
    applyFilters();
};

const setFilterValues = (filtersData) => {
    let count = 0;

    if (filtersData.isDirty) {
        hasFilters.value = true;

        for (const value of Object.values(filtersData.data())) {
            if (typeof value === 'number') {
                count += 1;
            } else if (value === null) {
            } else {
                count += value.length;
            }
        }
    } else {
        hasFilters.value = false;
    }
};

const applyFilters = async () => {
    form.type = null;
    try {
        const response = await apiClient.get('/discover', {
            params: removeEmptyObjectsKeys(form.data()),
        });
        const apiData = response.data;
        if (apiData.songs) songs.value = apiData.songs;
        if (apiData.adventures) adventures.value = apiData.adventures;
        setFilterValues(form);
        isLastSongPage.value = checkLastPage(ObjectTypes.Song);
        isLastAdventurePage.value = checkLastPage(ObjectTypes.Adventure);
    } catch (error) {
        console.error('Failed to apply filters:', error);
    }
};

const loadMore = async (type) => {
    const targetRef = type === ObjectTypes.Song ? songs : adventures;
    const nextUrl = targetRef.value.links?.next;
    if (!nextUrl) return;

    try {
        const response = await apiClient.get(nextUrl);
        const apiData = response.data;
        const newData = type === ObjectTypes.Song ? apiData.songs : apiData.adventures;
        if (newData) {
            targetRef.value.data = [...targetRef.value.data, ...(newData.data || [])];
            targetRef.value.links = newData.links;
            targetRef.value.meta = newData.meta;
        }
        if (type === ObjectTypes.Song) {
            isLastSongPage.value = checkLastPage(ObjectTypes.Song);
        } else {
            isLastAdventurePage.value = checkLastPage(ObjectTypes.Adventure);
        }
    } catch (error) {
        console.error('Failed to load more:', error);
    }
};

const checkLastPage = (type) => {
    return ObjectTypes.getObjectType(type) === ObjectTypes.Adventure
        ? adventures.value.meta?.current_page === adventures.value.meta?.last_page
        : songs.value.meta?.current_page === songs.value.meta?.last_page;
};
</script>

<template>
    <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
        <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
    </div>
    <div v-else class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col text-start">
                    <div class="d-block">
                        <div class="block-title color-light text-truncate">
                            {{ $t('pages.discover.title') }}
                        </div>
                        <div class="block-description color-light">
                            {{ $t('pages.discover.description') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    <DefaultButton class-list="btn-outline d-xl-none" @click="setFiltersDrawer">
                        {{ $t('buttons.filters') }}
                    </DefaultButton>
                    <BOffcanvas v-model="showFilters" responsive="xl" placement="start">
                        <div class="d-flex flex-column w-100 bg-default rounded-5 px-3 py-4 gap-3">
                            <div class="fs-4 font-default">
                                {{ $t('buttons.filters') }}
                            </div>
                            <BAccordion free>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'cubes-stacked']" class-list="color-pink" />
                                        <span class="font-default">{{ $t('pages.discover.filters.genre') }}</span>
                                    </template>
                                    <template #default>
                                        <BFormCheckboxGroup
                                            :options="genres"
                                            v-model="form.genres"
                                            @change="applyFilters"
                                            stacked
                                        />
                                    </template>
                                </BAccordionItem>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'microphone']" class-list="color-pink" />
                                        <span class="font-default">{{ $t('pages.discover.filters.voice') }}</span>
                                    </template>
                                    <template #default>
                                        <BFormCheckboxGroup
                                            :options="vocals"
                                            v-model="form.vocals"
                                            @change="applyFilters"
                                            stacked
                                        />
                                    </template>
                                </BAccordionItem>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'hashtag']" class-list="color-pink" />
                                        <span class="font-default">{{ $t('pages.discover.filters.tags.name') }}</span>
                                    </template>
                                    <template #default>
                                        <div class="d-flex flex-column gap-2">
                                            <Multiselect
                                                :options="filteredTags"
                                                v-model="form.tags"
                                                :searchable="true"
                                                :multiple="true"
                                                mode="tags"
                                                label="tag"
                                                track-by="tag"
                                                value-prop="tag"
                                                clear-on-search
                                                hide-selected
                                                @select="applyFilters"
                                                @deselect="applyFilters"
                                            />
                                        </div>
                                    </template>
                                </BAccordionItem>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'clock']" class-list="color-pink" />
                                        <span class="font-default">{{ $t('pages.discover.filters.duration') }}</span>
                                    </template>
                                    <template #default>
                                        <div class="py-4">
                                            <Slider
                                                :min="0"
                                                :max="song_max_duration"
                                                size="lg"
                                                v-model="form.duration"
                                                tooltip-position="bottom"
                                                @update="applyFilters"
                                            />
                                        </div>
                                    </template>
                                </BAccordionItem>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'calendar-days']" class-list="color-pink" />
                                        <span class="font-default">{{
                                            $t('pages.discover.filters.release_date.name')
                                        }}</span>
                                    </template>
                                    <template #default>
                                        <BFormRadioGroup
                                            v-model="form.released_at"
                                            :options="releaseDates"
                                            @change="applyFilters"
                                        />
                                    </template>
                                </BAccordionItem>
                            </BAccordion>
                            <div v-if="hasFilters" class="d-flex flex-row justify-content-center align-items-center">
                                <DefaultButton class-list="btn-outline" @click="resetFilters">
                                    {{ $t('buttons.cancel') }}
                                </DefaultButton>
                            </div>
                        </div>
                    </BOffcanvas>
                </div>
                <div class="col col-xl-9 flex-column">
                    <BTabs
                        nav-wrapper-class="tabs-header w-100"
                        nav-class="px-4"
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
                                        :is-owned="song.user?.uuid === user?.uuid"
                                        @add-to-favorites="addToFavorites($event, user?.uuid)"
                                        @remove-from-favorites="removeFromFavorites($event, user?.uuid)"
                                        @add-to-playlist="addToPlaylist($event)"
                                    />
                                    <DefaultButton
                                        class-list="btn-outline mt-2 mx-auto"
                                        @click.prevent="loadMore(ObjectTypes.Song)"
                                        :disabled="isLastSongPage || form.processing"
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
                                        :is-owned="adventure.user?.uuid === user?.uuid"
                                        @add-to-favorites="addToFavorites($event, user?.uuid)"
                                        @remove-from-favorites="removeFromFavorites($event, user?.uuid)"
                                    />
                                    <DefaultButton
                                        class-list="btn-outline mt-2 mx-auto"
                                        @click.prevent="loadMore(ObjectTypes.Adventure)"
                                        :disabled="isLastAdventurePage || form.processing"
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
