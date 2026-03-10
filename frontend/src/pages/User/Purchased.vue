<script setup>
import { ref, reactive, onMounted, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { isNotEmpty } from '@/Services/MiscService.js';
import { BTab, BTabs } from 'bootstrap-vue-next';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const UserSubscriptionCard = defineAsyncComponent(() => import('@/Components/Cards/UserSubscriptionCard.vue'));
const Song = defineAsyncComponent(() => import('@/Components/Song.vue'));

const user = ref(null);
const orders = ref(null);
const subscriptions = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/users/${currentRoute.params.username}/purchased`);
      const apiData = response.data;
      user.value = apiData.user ?? null;
    orders.value = apiData.orders ?? null;
    subscriptions.value = apiData.subscriptions ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const authStore = useAuthStore();
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :title="$t('pages.user.my_orders.title')" :user="user" :overflow="false">
        <BTabs
            nav-wrapper-class="tabs-header w-100"
            nav-class="px-4 w-100"
            active-nav-item-class="tab-item-active fs-5 font-merge"
            nav-item-class="tab-item default-text-color px-4 fs-5 font-merge"
            tab-class="py-4"
            fill
        >
            <BTab
                :title="
                    $t('pages.user.my_orders.tabs.subscriptions', {
                        count: subscriptions.length,
                    })
                "
            >
                <div v-if="isNotEmpty(subscriptions)" class="d-flex flex-column gap-2 w-100 vh-100 overflow-y-auto">
                    <UserSubscriptionCard v-for="user in subscriptions" :key="user.uuid" :user="user" />
                </div>
                <div
                    v-else
                    class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center"
                >
                    {{ $t('pages.user.my_orders.no_orders') }}
                </div>
            </BTab>
            <BTab :title="$t('pages.user.my_orders.tabs.orders', { count: orders.length })">
                <div v-if="isNotEmpty(orders)" class="d-flex flex-column gap-2 w-100 vh-100 overflow-y-auto">
                    <Song
                        v-for="song in orders"
                        :song="song"
                        :key="song.uuid"
                        @add-to-favorites="addToFavorites($event, authStore.user.uuid)"
                        @remove-from-favorites="removeFromFavorites($event, authStore.user.uuid)"
                        dark-font
                        can-view
                    />
                </div>
                <div
                    v-else
                    class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center"
                >
                    {{ $t('pages.user.my_orders.no_orders') }}
                </div>
            </BTab>
        </BTabs>
    </UserLayout>
</template>
  </template>
