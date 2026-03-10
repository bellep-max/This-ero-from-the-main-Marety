<script setup>
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { isNotEmpty } from '@/Services/MiscService.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const AdventureUploadModal = defineAsyncComponent(() => import('@/Components/Modals/AdventureUploadModal.vue'));
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Adventure = defineAsyncComponent(() => import('@/Components/Adventure.vue'));

const user = ref(null);
const adventures = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/users/${currentRoute.params.username}/adventures`);
      const apiData = response.data;
      user.value = apiData.user ?? null;
    adventures.value = apiData.adventures ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const authStore = useAuthStore();

const showAdventureModal = ref(false);

const canUpload = computed(() => user.value?.own_profile && authStore.user.can_upload);
const pageTitle = computed(() =>
    user.value?.own_profile
        ? $t('pages.user.my_adventures')
        : $t('pages.user.user_adventures', { name: user.value?.name }),
);
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :title="pageTitle" :user="user">
        <template v-if="canUpload" #controls>
            <DefaultButton class-list="btn-pink btn-narrow" @click="showAdventureModal = true">
                {{ $t('buttons.upload.adventure') }}
            </DefaultButton>
        </template>
        <div v-if="isNotEmpty(adventures)" class="d-flex flex-column w-100 vh-100 overflow-y-auto p-1">
            <Adventure
                v-for="adventure in adventures"
                :adventure="adventure"
                :key="adventure.uuid"
                :is_owned="user.own_profile"
                dark-font
                can-view
                @add-to-favorites="addToFavorites($event, authStore.user.uuid)"
                @remove-from-favorites="removeFromFavorites($event, authStore.user.uuid)"
            />
        </div>
        <div v-else class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
            {{ $t('pages.user.no_adventures', { name: user.name }) }}
        </div>
    </UserLayout>
    <AdventureUploadModal v-model="showAdventureModal" @close="showAdventureModal = false" />
</template>
  </template>
