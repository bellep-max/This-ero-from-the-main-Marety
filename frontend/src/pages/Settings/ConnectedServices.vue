<script setup>
import { ref, reactive, onMounted, defineAsyncComponent } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { BTable } from 'bootstrap-vue-next';
import { isNotEmpty } from '@/Services/MiscService.js';
const SettingsLayout = defineAsyncComponent(() => import('@/Layouts/SettingsLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const connections = ref(null);
const loading = ref(true);

  onMounted(async () => {
    try {
      const response = await apiClient.get('/settings/connected-services');
      const apiData = response.data;
      connections.value = apiData.connections ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const authStore = useAuthStore();
const user = authStore.user;

const form = useForm({
    name: user.name,
    email: user.email,
    bio: user.bio,
    birth: user.birth,
    gender: user.gender,
    country: user.country,
});

const submit = () => {
    form.patch(route('subscription.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <SettingsLayout :title="$t('menus.user_settings.connect')">
        <div class="d-flex flex-column justify-content-center align-items-center gap-4">
            <template v-if="isNotEmpty(connections)">
                <BTable :items="connections" />
            </template>
            <template v-else>
                <img src="/assets/images/services.svg" :alt="$t('menus.user_settings.connect')" />
                <div class="profile_page__content__subscription__desc">
                    {{ $t('pages.settings.connect.description') }}
                </div>
                <DefaultButton
                    class-list="btn-pink btn-wide"
                    :href="route('settings.connections.redirect', 'spotify')"
                    external
                >
                    {{ $t('pages.settings.connect.connect_spotify') }}
                </DefaultButton>
            </template>
        </div>
    </SettingsLayout>
</template>
  </template>
