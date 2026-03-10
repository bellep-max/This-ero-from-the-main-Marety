<script setup>
import { Head, router } from '@inertiajs/vue3';
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent } from 'vue';
import { subscriptionAssetImage } from '@/Services/AssetService.js';
const SettingsLayout = defineAsyncComponent(() => import('@/Layouts/SettingsLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const props = defineProps({
    subscription: {
        required: true,
    },
    plans: {
        type: Object,
        default: {},
    },
});

const submit = () => {
    form.patch(route('subscription.update'), {
        preserveScroll: true,
    });
};

const suspendSubscription = () => {
    router.post(route('settings.subscription.suspend'), {
        preserveScroll: true,
    });
};

const activateSubscription = () => {
    router.post(route('settings.subscription.activate'), {
        preserveScroll: true,
    });
};

const cancelSubscription = () => {
    router.post(route('settings.subscription.cancel'), {
        preserveScroll: true,
    });
};

const subscriptionActionName = computed(() =>
    props.subscription.status === 'active' ? $t('buttons.suspend_subscription') : $t('buttons.activate_subscription'),
);
</script>

<template>
    <Head :title="$t('menus.user_settings.subscription')" />
    <SettingsLayout :title="$t('menus.user_settings.subscription')">
        <div class="d-flex flex-column justify-content-center align-items-center gap-4">
            <img :src="subscriptionAssetImage" alt="$t('menus.user_settings.subscription')" />
            <template v-if="subscription">
                <DefaultButton
                    class-list="btn-pink btn-wide"
                    @click="props.subscription.status === 'active' ? suspendSubscription() : activateSubscription()"
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
