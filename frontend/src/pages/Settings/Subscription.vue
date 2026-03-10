<script setup>
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { subscriptionAssetImage } from '@/Services/AssetService.js';
const SettingsLayout = defineAsyncComponent(() => import('@/Layouts/SettingsLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const subscription = ref(null);
const plans = ref(null);
const loading = ref(true);

  onMounted(async () => {
    try {
      const response = await apiClient.get('/settings/subscription');
      const apiData = response.data;
      subscription.value = apiData.subscription ?? null;
    plans.value = apiData.plans ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const submit = () => {
    form.patch(route('subscription.update'), {
        preserveScroll: true,
    });
};

const suspendSubscription = () => {
    apiClient.post(route('settings.subscription.suspend'), {
        preserveScroll: true,
    });
};

const activateSubscription = () => {
    apiClient.post(route('settings.subscription.activate'), {
        preserveScroll: true,
    });
};

const cancelSubscription = () => {
    apiClient.post(route('settings.subscription.cancel'), {
        preserveScroll: true,
    });
};

const subscriptionActionName = computed(() =>
    subscription.value?.status === 'active' ? $t('buttons.suspend_subscription') : $t('buttons.activate_subscription'),
);
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <SettingsLayout :title="$t('menus.user_settings.subscription')">
        <div class="d-flex flex-column justify-content-center align-items-center gap-4">
            <img :src="subscriptionAssetImage" alt="$t('menus.user_settings.subscription')" />
            <template v-if="subscription">
                <DefaultButton
                    class-list="btn-pink btn-wide"
                    @click="subscription.status === 'active' ? suspendSubscription() : activateSubscription()"
                >
                    {{ subscriptionActionName }}
                </DefaultButton>
                <DefaultButton class-list="btn-pink btn-wide" @click="cancelSubscription()">
                    {{ $t('buttons.cancel_subscription') }}
                </DefaultButton>
            </template>
            <template v-else>
                <div class="font-default fs-5 fw-bolder">
                    {{ $t('pages.settings.subscription_title') }}
                </div>
                <div class="text-start font-default fs-14">
                    <p>- {{ $t('pages.settings.subscription_feature_1') }}</p>
                    <p>- {{ $t('pages.settings.subscription_feature_2') }}</p>
                    <p>- {{ $t('pages.settings.subscription_feature_3') }}</p>
                    <p>- {{ $t('pages.settings.subscription_feature_4') }}</p>
                    <p>- {{ $t('pages.settings.subscription_feature_5') }}</p>
                </div>
                <DefaultButton :href="route('settings.subscription.checkout')" class-list="btn-pink">
                    {{ $t('buttons.subscribe_site') }}
                </DefaultButton>
            </template>
        </div>
    </SettingsLayout>
</template>
  </template>
