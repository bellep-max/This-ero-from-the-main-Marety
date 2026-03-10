<script setup>
import apiClient from "@/api/client";
import { BTab, BTabs } from 'bootstrap-vue-next';
import { $t } from '@/i18n';
import SongBlockCarousel from '@/Components/Carousels/SongBlockCarousel.vue';
import MainPageBlock from '@/Components/Sections/MainPageBlock.vue';
import ImageCarousel from '@/Components/Carousels/ImageCarousel.vue';
import TestimonialCarousel from '@/Components/Carousels/TestimonialCarousel.vue';
import { computed, ref, onMounted } from 'vue';
import route from "@/helpers/route"

const popularAudios = ref(null);
const topGenre = ref(null);
const topFemale = ref(null);
const topMale = ref(null);
const loading = ref(true);

  onMounted(async () => {
    try {
      const response = await apiClient.get('/trending');
      const apiData = response.data;
      popularAudios.value = apiData.popularAudios ?? null;
    topGenre.value = apiData.topGenre ?? null;
    topFemale.value = apiData.topFemale ?? null;
    topMale.value = apiData.topMale ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const hasContent = (contentBlockName) => {
    if (contentBlockName === 'top20') {
        return (topFemale.value?.length ?? 0) > 0 || (topMale.value?.length ?? 0) > 0;
    } else if (contentBlockName === 'popularAudios') {
        return (popularAudios.value?.length ?? 0) > 0;
    } else if (contentBlockName === 'topGenre') {
        return (topGenre.value?.length ?? 0) > 0;
    } else if (contentBlockName === 'topFemale') {
        return (topFemale.value?.length ?? 0) > 0;
    }

    return (topMale.value?.length ?? 0) > 0;
};

const top20 = computed(() => [...(topFemale.value ?? []), ...(topMale.value ?? [])]);

// const testimonials = ref([]);
//
// onBeforeMount(() => {
//   let i = 0;
//
//   do {
//     testimonials.value.push({
//       title: 'Lorem!',
//       description: `Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
//           incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
//           exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.`,
//       rating: Math.floor(Math.random() * 5),
//     });
//
//     i += 1;
//   } while (i < 10);
// })
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    <div class="min-vh-100 d-flex flex-column gap-5">
        <div class="py-3 p-md-5 p-lg-6">
            <div class="container">
                <div class="row">
                    <div class="col text-start">
                        <div class="d-block">
                            <div class="block-title text-truncate">
                                {{ $t('pages.trending.title') }}
                            </div>
                            <!--              <div class="block-description">-->
                            <!--                {{ $t('pages.trending.description') }}-->
                            <!--              </div>-->
                        </div>
                    </div>
                </div>
                <MainPageBlock
                    v-if="hasContent('top20')"
                    has-icon
                    :title="$t('pages.trending.top_20.title')"
                    :description="$t('pages.trending.top_20.description')"
                >
                    <SongBlockCarousel :items="top20" />
                </MainPageBlock>
            </div>
        </div>
        <MainPageBlock
            v-if="hasContent('topGenre')"
            gradient-background
            class-list="bg-rounded"
            :title="$t('pages.trending.top_genre.title')"
        >
            <SongBlockCarousel :items="topGenre" />
        </MainPageBlock>
        <MainPageBlock
            v-if="hasContent('popularAudios')"
            has-icon
            :title="$t('pages.trending.new_audios.title')"
            :description="$t('pages.trending.new_audios.description')"
            :link="route('channel', 'new-audios')"
        >
            <BTabs
                nav-wrapper-class="tabs-header"
                nav-class="tab-item px-4 gap-4"
                active-nav-item-class="tab-item-active"
                tab-class="py-4"
            >
                <BTab
                    v-for="(audioType, i) in popularAudios"
                    :key="i"
                    :title="$t(`pages.trending.popular_audios.categories.${audioType.tab_title}`)"
                >
                    <SongBlockCarousel :items="audioType.data.data" />
                </BTab>
            </BTabs>
        </MainPageBlock>

        <!--      <div class="py-3 p-md-5 p-lg-6">-->
        <!--        <div class="container">-->
        <!--          <div class="row">-->
        <!--            <div class="col text-start block-title">-->
        <!--              Popular Audios-->
        <!--            </div>-->
        <!--          </div>-->
        <!--        </div>-->
        <!--        <div class="container">-->
        <!--          <div class="d-flex flex-column justify-content-start gap-4 px-2 px-lg-0">-->
        <!--            <div class="d-flex flex-row justify-content-start align-items-center gap-3">-->
        <!--&lt;!&ndash;              @include('frontend.components.play-icon-big')&ndash;&gt;-->
        <!--              <div class="ms-3">-->
        <!--                <div class="block-title">-->
        <!--                  Top 20 categories-->
        <!--                </div>-->
        <!--                <div class="block-description">-->
        <!--                  Top 20 in different categories-->
        <!--                </div>-->
        <!--              </div>-->
        <!--            </div>-->
        <!--            <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">-->
        <!--&lt;!&ndash;              @include('frontend.components.buttons.btn-rnd-left', ['class' => 'trending__navigation__item&#45;&#45;prev btn-pink'])&ndash;&gt;-->
        <!--&lt;!&ndash;              @include('frontend.components.buttons.btn-rnd-right', ['class' => 'trending__navigation__item&#45;&#45;next btn-pink'])&ndash;&gt;-->
        <!--            </div>-->
        <!--            <div class="trending-carousel d-flex flex-row justify-content-start align-items-center">-->
        <!--&lt;!&ndash;              @foreach($top as $category)&ndash;&gt;-->
        <!--&lt;!&ndash;              @foreach($category as $song)&ndash;&gt;-->
        <!--&lt;!&ndash;              @include('frontend.components.cards.song-card-lg', ['song' => $song, 'class' => 'top_male_voice__carousel__item'])&ndash;&gt;-->
        <!--&lt;!&ndash;              @endforeach&ndash;&gt;-->
        <!--&lt;!&ndash;              @endforeach&ndash;&gt;-->
        <!--            </div>-->
        <!--          </div>-->
        <!--        </div>-->
        <!--      </div>-->
    </div>
</template>
  </template>

<style scoped></style>
