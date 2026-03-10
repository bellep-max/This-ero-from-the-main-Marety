<script setup>
import apiClient from "@/api/client";
import { $t } from '@/i18n.js';
import { defineAsyncComponent, ref, onMounted } from 'vue';
const ImageCard = defineAsyncComponent(() => import('@/Components/Cards/ImageCard.vue'));
import route from "@/helpers/route"

const genres = ref(null);
const loading = ref(true);

  onMounted(async () => {
    try {
      const response = await apiClient.get('/genres');
      const apiData = response.data;
      genres.value = apiData.genres ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
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
  </template>
