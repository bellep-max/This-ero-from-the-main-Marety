<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent } from 'vue';
const ImageCard = defineAsyncComponent(() => import('@/Components/Cards/ImageCard.vue'));

const props = defineProps({
    genres: {
        type: Array,
        default: [],
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
                            {{ $t('pages.genres.title') }}
                        </div>
                        <div class="block-description color-light">
                            {{ $t('pages.genres.description') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <div class="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
                        <ImageCard
                            v-for="genre in genres"
                            :model="genre"
                            :route="route('genres.show', genre.slug)"
                            :key="genre.uuid"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
