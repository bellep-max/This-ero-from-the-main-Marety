<script setup>
import { ref, reactive, onMounted, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { BTab, BTabs } from 'bootstrap-vue-next';
import { isNotEmpty } from '@/Services/MiscService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const NotificationFullCard = defineAsyncComponent(() => import('@/Components/Cards/NotificationFullCard.vue'));

const user = ref(null);
const notifications = ref(null);
const recent_activities = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/users/${currentRoute.params.username}/notifications`);
      const apiData = response.data;
      user.value = apiData.user ?? null;
    notifications.value = apiData.notifications ?? null;
    recent_activities.value = apiData.recent_activities ?? null;
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
    
    <UserLayout :title="$t('pages.user.notifications.my_notifications')" :user="user" :overflow="false">
        <BTabs
            nav-wrapper-class="tabs-header w-100"
            nav-class="tab-item px-4 w-100"
            active-nav-item-class="tab-item-active"
            nav-item-class="tab-item default-text-color px-4"
            tab-class="py-4"
            fill
        >
            <BTab :title="$t('pages.user.notifications.title')">
                <div v-if="isNotEmpty(notifications)" class="d-flex flex-column w-100 vh-100 overflow-y-auto p-1">
                    <NotificationFullCard
                        v-for="notification in notifications"
                        :notification="notification"
                        dark-font
                    />
                </div>
                <div v-else class="d-block text-center">
                    {{ $t('pages.user.notifications.no_notifications') }}
                </div>
            </BTab>
            <BTab :title="$t('pages.user.notifications.recent_actions')">
                <div v-if="isNotEmpty(recent_activities)" class="d-flex flex-column w-100 vh-100 overflow-y-auto p-1">
                    <NotificationFullCard
                        v-for="notification in recent_activities"
                        :notification="notification"
                        dark-font
                    />
                </div>
                <div v-else class="d-block text-center">
                    {{ $t('pages.user.notifications.no_recent_actions') }}
                </div>
            </BTab>
        </BTabs>
    </UserLayout>
</template>
  </template>
