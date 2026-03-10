<script setup>
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { showModal } from '@/Services/ModalService.js';
import { isNotEmpty } from '@/Services/MiscService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const PlaylistCard = defineAsyncComponent(() => import('@/Components/Cards/PlaylistCard.vue'));
const CreatePlaylistModal = defineAsyncComponent(() => import('@/Components/Modals/CreatePlaylistModal.vue'));

const user = ref(null);
const playlists = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/users/${currentRoute.params.username}/playlists`);
      const apiData = response.data;
      user.value = apiData.user ?? null;
    playlists.value = apiData.playlists ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const pageTitle = computed(() =>
    user.value?.own_profile ? $t('pages.user.my_playlists') : $t('pages.user.user_playlists', { name: user.value?.name }),
);
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :title="pageTitle" :user="user">
        <template v-if="user.own_profile" #controls>
            <DefaultButton class-list="btn-outline btn-narrow" @click="showModal(CreatePlaylistModal)">
                {{ $t('buttons.create.playlist') }}
            </DefaultButton>
        </template>
        <div v-if="isNotEmpty(playlists)" class="row w-100 vh-100 overflow-y-auto">
            <PlaylistCard v-for="playlist in playlists" :playlist="playlist" :controllable="user.own_profile" />
        </div>
        <div v-else class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
            {{ $t('pages.user.no_playlists', { name: user.name }) }}
        </div>
    </UserLayout>
</template>
  </template>
