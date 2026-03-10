<script setup>
import { $t } from '../i18n.js';
import { computed, defineAsyncComponent, ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { Navigation, Slide } from 'vue3-carousel';
import { isLogged } from '@/Services/AuthService.js';
import { isNotEmpty } from '@/Services/MiscService.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const Song = defineAsyncComponent(() => import('@/Components/Song.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const RoundButton = defineAsyncComponent(() => import('@/Components/Buttons/RoundButton.vue'));
const DefaultCarousel = defineAsyncComponent(() => import('@/Components/Carousels/DefaultCarousel.vue'));
const ImageCard = defineAsyncComponent(() => import('@/Components/Cards/ImageCard.vue'));
import '@vueform/slider/themes/default.css';
import '@vueform/multiselect/themes/default.css';
import route from "@/helpers/route"

const currentRoute = useRoute();
const searchString = ref('');
const songs = ref([]);
const users = ref([]);
const pagination = ref(null);
const loading = ref(true);

const authStore = useAuthStore();
const user = computed(() => authStore.user);
const lastPage = computed(() => !pagination.value || pagination.value.current_page >= pagination.value.last_page);

const carouselConfig = {
    itemsToShow: 'auto',
    wrapAround: true,
    gap: 24,
};

const fetchData = async (page = 1) => {
  try {
    const q = currentRoute.query.q || '';
    const response = await apiClient.get('/search', { params: { q, page } });
    const apiData = response.data;
    searchString.value = apiData.searchString ?? q;
    if (page === 1) {
      songs.value = apiData.songs ?? [];
    } else {
      songs.value = [...songs.value, ...(apiData.songs ?? [])];
    }
    users.value = apiData.users ?? [];
    pagination.value = apiData.pagination ?? null;
  } catch (error) {
    console.error('Failed to load page data:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => fetchData());

watch(() => currentRoute.query.q, () => {
  loading.value = true;
  fetchData();
});

const loadMore = () => {
  if (pagination.value) {
    fetchData(pagination.value.current_page + 1);
  }
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
                            {{ $t('pages.search.title', { query: searchString }) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-start">
                    <div class="d-block">
                        <div class="block-description color-light">
                            {{ $t('pages.search.users') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <DefaultCarousel v-if="isNotEmpty(users)" :config="carouselConfig">
                        <template #slides>
                            <Slide v-for="(slide, i) in users" :key="i">
                                <template #default>
                                    <ImageCard
                                        :model="slide"
                                        :route="route('users.show', slide.uuid)"
                                        :key="slide.uuid"
                                    />
                                </template>
                            </Slide>
                        </template>
                        <template #navigation>
                            <Navigation>
                                <template #prev>
                                    <RoundButton class-list="btn-pink">
                                        <Icon :icon="['fas', 'arrow-left']" size="1x" />
                                    </RoundButton>
                                </template>
                                <template #next>
                                    <RoundButton class-list="btn-pink">
                                        <Icon :icon="['fas', 'arrow-right']" size="1x" />
                                    </RoundButton>
                                </template>
                            </Navigation>
                        </template>
                    </DefaultCarousel>
                    <span v-else class="block-description color-light text-center">
                        {{ $t('pages.search.not_found') }}
                    </span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-start">
                    <div class="d-block">
                        <div class="block-description color-light">
                            {{ $t('pages.search.songs') }}
                        </div>
                    </div>
                </div>
                <div v-if="isNotEmpty(songs)" class="col-12 flex-column">
                    <Song
                        v-for="song in songs"
                        :song="song"
                        :key="song.uuid"
                        :can-view="isLogged"
                        :is-owned="song.user?.uuid === user?.uuid"
                        @add-to-favorites="addToFavorites($event, authStore.user?.uuid)"
                        @remove-from-favorites="removeFromFavorites($event, authStore.user?.uuid)"
                    />
                    <DefaultButton class-list="btn-outline mt-2 mx-auto" @click="loadMore" :disabled="lastPage">
                        {{ $t('buttons.load_more') }}
                    </DefaultButton>
                </div>
                <span v-else class="block-description color-light text-center">
                    {{ $t('pages.search.not_found') }}
                </span>
            </div>
        </div>
    </div>
</template>
