<script setup>
import { $t } from '../i18n.js';
import { defineAsyncComponent, onMounted, ref, reactive } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute, useRouter } from 'vue-router'
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

const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    song_max_duration: {
        type: Number,
        default: 0,
    },
});

const authStore = useAuthStore();
const user = useAuthStore().user;

const genres = ref(authStore.filter_presets.genres);
const tags = ref(authStore.filter_presets.tags);
const vocals = ref(authStore.filter_presets.vocals);

const hasFilters = ref(false);
const showFilters = ref(false);
const isLastSongPage = ref(false);
const isLastAdventurePage = ref(false);
const filteredTags = ref(tags);

const songs = ref([]);
const adventures = ref([]);

const form = useForm({
    genres: [],
    vocals: [],
    duration: [0, props.song_max_duration],
    released_at: null,
    tags: props.filters.tags,
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

const applyFilters = () => {
    form.type = null;

    apiClient.get(
        route('discover.index'),
        {
            ...removeEmptyObjectsKeys(form.data()),
        },
        {
            preserveState: true,
            preserveUrl: true,
            only: ['songs', 'adventures', 'pagination', 'filters'],
            onSuccess: (page) => {
                // Assuming you have a `songs` ref in your component
                songs.value = authStore.songs;
                adventures.value = authStore.adventures;
                setFilterValues(form);

                isLastSongPage.value = checkLastPage(ObjectTypes.Song);
                isLastAdventurePage.value = checkLastPage(ObjectTypes.Adventure);
                // showFilters.value = false; // Close the filters drawer after applying
            },
        },
    );
};

const loadMore = (type) => {
    if (type === ObjectTypes.Song) {
        form.transform(() => removeEmptyObjectsKeys()).get(songs.value.links?.next, {
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
        });
    } else {
        form.transform(() => removeEmptyObjectsKeys()).get(adventures.value.links?.next, {
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
        });
    }
};
//
// const addToFavorites = (uuid, type) => {
//   apiClient.post(route('users.favorites.store', {
//     user: user.uuid,
//   }), {
//     uuid: uuid,
//     type: type,
//   }, {
//     preserveState: true,
//     preserveScroll: true,
//     only: ['songs', 'adventures', 'filters'],
//   });
// };
//
// const removeFromFavorites = (uuid, type) => {
//   apiClient.delete(route('users.favorites.destroy', {
//     user: user.uuid,
//   }), {
//     data: {
//       uuid: uuid,
//       type: type,
//     },
//     preserveState: true,
//     preserveScroll: true,
//     only: ['songs', 'adventures', 'filters'],
//   });
// };

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
                                        :is-owned="song.user.uuid === user?.uuid"
                                        @add-to-favorites="addToFavorites($event, user.uuid)"
                                        @remove-from-favorites="removeFromFavorites($event, user.uuid)"
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
                                        :is-owned="adventure.user.uuid === user?.uuid"
                                        @add-to-favorites="addToFavorites($event, user.uuid)"
                                        @remove-from-favorites="removeFromFavorites($event, user.uuid)"
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
