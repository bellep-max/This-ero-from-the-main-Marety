<script setup>
import apiClient from "@/api/client";
import { BTab, BTabs } from 'bootstrap-vue-next';
import { $t } from '@/i18n';
import { defineAsyncComponent, onBeforeMount, ref, onMounted } from 'vue';
import { isNotEmpty } from '@/Services/MiscService.js';
import ImageLinkTypes from '@/Enums/ImageLinkTypes.js';
const SongBlockCarousel = defineAsyncComponent(() => import('@/Components/Carousels/SongBlockCarousel.vue'));
const MainPageBlock = defineAsyncComponent(() => import('@/Components/Sections/MainPageBlock.vue'));
const ImageCarousel = defineAsyncComponent(() => import('@/Components/Carousels/ImageCarousel.vue'));
const TestimonialCarousel = defineAsyncComponent(() => import('@/Components/Carousels/TestimonialCarousel.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const GenreBlockCarousel = defineAsyncComponent(() => import('@/Components/Carousels/GenreBlockCarousel.vue'));
import indexImage from '@/assets/images/homepage.webp';
import route from "@/helpers/route"

const newAudios = ref(null);
const popularAudios = ref(null);
const genres = ref(null);
const adventures = ref(null);
const posts = ref(null);
const loading = ref(true);

  onMounted(async () => {
    try {
      const response = await apiClient.get('/homepage');
      const apiData = response.data;
      newAudios.value = apiData.newAudios ?? null;
    popularAudios.value = apiData.popularAudios ?? null;
    genres.value = apiData.genres ?? null;
    adventures.value = apiData.adventures ?? null;
    posts.value = apiData.posts ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const hasContent = (contentBlockName) => {
    if (contentBlockName === 'newAudios') {
        return true;
        // return newAudios.length > 0;
    } else if (contentBlockName === 'popularAudios') {
        return isNotEmpty(popularAudios.value);
    } else if (contentBlockName === 'posts') {
        return isNotEmpty(posts.value);
    } else if (contentBlockName === 'adventures') {
        return isNotEmpty(adventures.value);
    }

    return isNotEmpty(genres.value);
};

const testimonials = ref([]);

onBeforeMount(() => {
    let i = 0;

    do {
        testimonials.value.push({
            title: 'Lorem!',
            description: `Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
          incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
          exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.`,
            rating: Math.floor(Math.random() * 5),
        });

        i += 1;
    } while (i < 10);
});
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    <div class="container-fluid p-0">
        <div class="d-flex flex-row justify-content-between align-items-center">
            <div class="d-flex flex-column justify-content-center align-items-start p-2 ps-lg-8">
                <div class="d-flex flex-column gap-2 gap-lg-5 ms-lg-5">
                    <div class="block-title default-text-color text-lg-start text-center main_info__left-section">
                        Lorem ipsum dolor
                    </div>
                    <div class="font-merge default-text-color lh-lg">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                        labore et dolore magna aliqua.
                    </div>
                    <div
                        class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start gap-4"
                    >
                        <DefaultButton class-list="btn-pink btn-wide"> Lorem ipsum </DefaultButton>
                        <DefaultButton class-list="btn-outline btn-wide"> Lorem ipsum </DefaultButton>
                    </div>
                </div>
            </div>
            <img :src="indexImage" alt="Login Icon" class="d-none d-lg-inline-block" />
        </div>
    </div>
    <MainPageBlock
        v-if="hasContent('newAudios')"
        has-icon
        :title="$t('pages.home.new_audios.title')"
        :description="$t('pages.home.new_audios.description')"
        :link="route('channels.show', 'new-audios')"
    >
        <BTabs
            nav-wrapper-class="tabs-header"
            nav-class="gap-lg-4 px-lg-4"
            active-nav-item-class="tab-item-active"
            nav-item-class="tab-item default-text-color px-4"
            tab-class="py-4"
        >
            <BTab
                v-for="(data, name, index) in newAudios"
                :key="index"
                :title="$t(`pages.home.new_audios.categories.${name}`)"
            >
                <SongBlockCarousel :items="data" />
            </BTab>
        </BTabs>
    </MainPageBlock>
    <MainPageBlock
        v-if="hasContent('adventures')"
        has-icon
        :title="$t('pages.home.adventures.title')"
        :description="$t('pages.home.adventures.description')"
    >
        <SongBlockCarousel :items="adventures" />
    </MainPageBlock>
    <MainPageBlock
        v-if="hasContent('genres')"
        :title="$t('pages.home.genres.title')"
        :description="$t('pages.home.genres.description')"
        gradient-background
        :link="route('genres.index')"
        class-list="rounded-5"
    >
        <ImageCarousel :items="genres" :type="ImageLinkTypes.Genre" />
    </MainPageBlock>
    <MainPageBlock
        has-icon
        :title="$t('pages.home.popular_audios.title')"
        :description="$t('pages.home.popular_audios.description')"
        :link="route('channels.show', 'new-audios')"
    >
        <BTabs
            nav-wrapper-class="tabs-header"
            nav-class="tab-item px-lg-4 gap-lg-4"
            active-nav-item-class="tab-item-active"
            tab-class="py-4"
        >
            <BTab
                v-for="(genres, name) in popularAudios"
                :key="name"
                :title="$t(`pages.home.popular_audios.categories.${name}`)"
            >
                <GenreBlockCarousel :items="genres" />
            </BTab>
        </BTabs>
    </MainPageBlock>
    <MainPageBlock
        v-if="hasContent('posts')"
        title="New Posts"
        :link="route('posts.index')"
        gradient-background
        class-list="rounded-5"
    >
        <ImageCarousel :items="posts" :type="ImageLinkTypes.Post" />
    </MainPageBlock>
    <div class="bg-default container-fluid">
        <div class="container py-5 d-flex flex-column gap-5">
            <div class="d-flex flex-column align-items-center">
                <div class="h2 font-default fw-bold">
                    {{ $t('pages.home.testimonials.title') }}
                </div>
                <div class="block-description">
                    {{ $t('pages.home.testimonials.description') }}
                </div>
            </div>
            <TestimonialCarousel :items="testimonials" />
        </div>
    </div>
</template>
  </template>
