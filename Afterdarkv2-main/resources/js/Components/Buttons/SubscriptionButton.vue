<script setup>
import { $t } from '@/i18n';
import { computed, defineAsyncComponent } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import SubscriptionStatus from '@/Enums/SubscriptionStatus.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const props = defineProps({
    user: {
        type: Object,
    },
});

const currentUser = usePage().props.auth.user;
const hasSubscription = computed(() =>
    props.user?.subscription ? Object.keys(props.user?.subscription).length > 0 : false,
);

const getSubscriptionRoute = () => {
    switch (props.user.subscription?.pivot.status) {
        case SubscriptionStatus.Active:
            return route('users.subscriptions.suspend', props.user.uuid);
        case SubscriptionStatus.Suspended:
            return route('users.subscriptions.activate', props.user.uuid);
        default:
            return route('users.subscriptions.checkout', props.user.uuid);
    }
};

const submit = () => {
    if (!currentUser.subscription) {
        return router.visit(route('settings.subscription.edit'));
    }

    router.post(getSubscriptionRoute(), {
        preserveScroll: true,
    });
};

const cancel = () => {
    router.post(route('users.subscriptions.cancel', props.user.uuid), {
        preserveScroll: true,
    });
};
</script>

<template>
    <template v-if="hasSubscription">
        <DefaultButton
            v-if="user.subscription?.pivot.status === SubscriptionStatus.Suspended"
            class-list="btn-outline w-100"
            @click="submit"
        >
            <Icon :icon="['fas', 'person-circle-check']" />
            {{ $t('buttons.patron.activate') }}
        </DefaultButton>
        <DefaultButton v-else class-list="btn-outline w-100" @click="submit">
            <Icon :icon="['fas', 'person-circle-exclamation']" />
            {{ $t('buttons.patron.suspend') }}
        </DefaultButton>
        <DefaultButton class-list="btn-outline w-100" @click="cancel">
            <Icon :icon="['fas', 'person-circle-xmark']" />
            {{ $t('buttons.patron.cancel') }}
        </DefaultButton>
    </template>
    <template v-else>
        <DefaultButton class="btn-outline w-100" @click="submit">
            <Icon :icon="['fas', 'person-circle-plus']" />
            {{ $t('buttons.patron.set') }}
        </DefaultButton>
    </template>
</template>
