<script setup>
import { Head } from '@inertiajs/vue3';
import { $t } from '@/i18n.js';
import { BTab, BTabs } from 'bootstrap-vue-next';
import { isNotEmpty } from '@/Services/MiscService.js';
import { defineAsyncComponent } from 'vue';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const NotificationFullCard = defineAsyncComponent(() => import('@/Components/Cards/NotificationFullCard.vue'));

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
    notifications: {
        required: true,
        type: Object,
        default: {},
    },
    recent_activities: {
        type: Object,
        default: {},
    },
});
</script>

<template>
    <Head :title="$t('pages.user.notifications.my_notifications')" />
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
