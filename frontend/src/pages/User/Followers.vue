<script setup>
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { BTab, BTabs } from 'bootstrap-vue-next';
import { isNotEmpty } from '@/Services/MiscService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const FollowerCard = defineAsyncComponent(() => import('@/Components/Cards/FollowerCard.vue'));

const user = ref(null);
const patrons = ref(null);
const free_followers = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/users/${currentRoute.params.username}/followers`);
      const apiData = response.data;
      user.value = apiData.user ?? null;
    patrons.value = apiData.patrons ?? null;
    free_followers.value = apiData.free_followers ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const pageTitle = computed(() =>
    user.value?.own_profile
        ? $t('pages.user.followers.my')
        : $t('pages.user.followers.other', { name: user.value?.name }),
);
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :title="pageTitle" :user="user" :overflow="false">
        <BTabs
            nav-wrapper-class="tabs-header w-100"
            nav-class="tab-item px-4 w-100"
            active-nav-item-class="tab-item-active"
            nav-item-class="tab-item default-text-color px-4"
            tab-class="py-4 container-fluid w-100 vh-100 overflow-y-auto"
            v-if="user.own_profile"
            fill
        >
            <BTab :title="$t('pages.user.followers.free_count', free_followers.length)">
                <div v-if="isNotEmpty(free_followers)" class="row w-100">
                    <FollowerCard v-for="user in free_followers" :user="user" />
                </div>
                <div v-else class="d-flex flex-row align-items-center text-center justify-content-center w-100">
                    {{ $t('pages.user.user_no_followers', { name: user.name }) }}
                </div>
            </BTab>
            <BTab :title="$t('pages.user.followers.patrons_count', patrons.length)">
                <div v-if="isNotEmpty(patrons)" class="row w-100">
                    <FollowerCard v-for="user in patrons" :user="user" />
                </div>
                <div v-else class="d-flex flex-row align-items-center text-center justify-content-center w-100">
                    {{ $t('pages.user.user_no_followers', { name: user.name }) }}
                </div>
            </BTab>
        </BTabs>
        <template v-else>
            <div v-if="isNotEmpty(free_followers)" class="row w-100">
                <FollowerCard v-for="user in free_followers" :user="user" />
            </div>
            <div v-else class="d-flex flex-row align-items-center text-center justify-content-center w-100">
                {{ $t('pages.user.user_no_followers', { name: user.name }) }}
            </div>
        </template>
    </UserLayout>
</template>
  </template>
