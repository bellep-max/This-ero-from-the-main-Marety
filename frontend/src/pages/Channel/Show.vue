<script setup>
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent, ref, onMounted } from 'vue';
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { isLogged } from '@/Services/AuthService.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const Song = defineAsyncComponent(() => import('@/Components/Song.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const currentRoute = useRoute();
const channel = ref(null);
const objects = ref([]);
const pagination = ref(null);
const loading = ref(true);

const authStore = useAuthStore();
const user = computed(() => authStore?.user);
const lastPage = computed(() => !pagination.value || pagination.value.current_page >= pagination.value.last_page);

const fetchData = async (page = 1) => {
  try {
    const response = await apiClient.get(`/channels/${currentRoute.params.slug}`, { params: { page } });
    const apiData = response.data;
    channel.value = apiData.channel ?? null;
    if (page === 1) {
      objects.value = apiData.objects ?? [];
    } else {
      objects.value = [...objects.value, ...(apiData.objects ?? [])];
    }
    pagination.value = apiData.pagination ?? null;
  } catch (error) {
    console.error('Failed to load page data:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => fetchData());

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
                            {{ channel?.title }}
                        </div>
                        <div class="block-description color-light">
                            {{ channel?.description }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col flex-column">
                    <template v-if="channel?.type === ObjectTypes.Song">
                        <Song
                            v-for="song in objects"
                            :song="song"
                            :key="song.uuid"
                            :can-view="isLogged"
                            :is-owned="user?.uuid === song.user?.uuid"
                            @add-to-favorites="addToFavorites($event, authStore.user?.uuid)"
                            @remove-from-favorites="removeFromFavorites($event, authStore.user?.uuid)"
                        />
                    </template>
                    <DefaultButton class-list="btn-outline mt-2 mx-auto" @click="loadMore" :disabled="lastPage">
                        {{ $t('buttons.load_more') }}
                    </DefaultButton>
                </div>
            </div>
        </div>
    </div>
</template>
