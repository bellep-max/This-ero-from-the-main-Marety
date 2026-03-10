<script setup>
import { ref, reactive, onMounted, defineAsyncComponent } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { isNotEmpty } from '@/Services/MiscService.js';
const FollowerCard = defineAsyncComponent(() => import('@/Components/Cards/FollowerCard.vue'));
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
import route from "@/helpers/route"

const user = ref(null);
const following = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/users/${currentRoute.params.username}/following`);
      const apiData = response.data;
      user.value = apiData.user ?? null;
    following.value = apiData.following ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const unfollow = (user) => {
    const currentUser = useAuthStore().user;

    apiClient.delete(route('users.following.destroy', { user: currentUser.uuid }), {
        data: {
            user_uuid: user.value?.uuid,
        },
        onSuccess: () => {},
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :title="$t('pages.user.following')" :user="user">
        <div v-if="isNotEmpty(following)" class="row w-100">
            <FollowerCard v-for="user in following" :user="user" @unfollow="unfollow(user)" controllable />
        </div>
        <div v-else class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
            {{ $t('pages.user.no_following') }}
        </div>
    </UserLayout>
</template>
  </template>
