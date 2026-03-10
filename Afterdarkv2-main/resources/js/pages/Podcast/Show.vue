<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent } from 'vue';
const DefaultLink = defineAsyncComponent(() => import('@/Components/Links/DefaultLink.vue'));
const ImageCard = defineAsyncComponent(() => import('@/Components/Cards/ImageCard.vue'));

const props = defineProps({
    podcast: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col text-start">
                    <div class="d-block">
                        <div class="block-title color-light text-truncate">
                            {{ podcast.title }}
                        </div>
                        <DefaultLink
                            class-list="block-description color-light text-decoration-none"
                            :link="route('podcasts.index')"
                        >
                            {{ $t('pages.podcasts.see_all') }}
                        </DefaultLink>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <div class="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
                        <ImageCard
                            v-for="n in podcast.seasons"
                            :model="podcast"
                            :title="$t('pages.podcasts.season', { season: n })"
                            :key="n"
                            :route="
                                route('podcasts.seasons.show', {
                                    podcast: podcast.uuid,
                                    season: n,
                                })
                            "
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
