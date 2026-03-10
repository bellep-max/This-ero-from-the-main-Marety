<script setup>
import { defineAsyncComponent, ref } from 'vue';
import { useRouter } from 'vue-router'
import apiClient from '@/api/client';
import Activities from '@/Enums/Activities.js';
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
import route from "@/helpers/route"

const props = defineProps({
    notification: {
        type: Object,
        required: true,
    },
    darkFont: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['read']);

const notificationLink = ref(Activities.getLink(props.notification));
const componentType = ref(notificationLink.value ? 'a' : 'div');

const markAsRead = () => {
    apiClient.post(
        route('notifications.read', { notification: props.notification.id }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                emit('read', props.notification);
            },
        },
    );
};
</script>

<template>
    <div class="track no-wrap w-100 justify-content-between p-2">
        <component
            :is="componentType"
            class="d-flex flex-row justify-content-start align-items-center gap-3 text-decoration-none"
            :href="notificationLink"
        >
            <img
                class="notification-image rounded-4 d-md-block d-none"
                :alt="notification.user?.name"
                :src="notification.user?.artwork"
            />
            <div
                class="description flex-wrap font-default"
                :class="{
                    'color-light': !darkFont,
                    'default-text-color': darkFont,
                }"
            >
                {{ Activities.getText(notification) }}
            </div>
        </component>
        <div class="d-flex flex-row justify-content-end align-items-center gap-3">
            <span
                class="font-merge"
                :class="{
                    'color-light': !darkFont,
                    'color-grey': darkFont,
                }"
            >
                <Icon :icon="['fas', 'clock']" size="md" />
                {{ notification.created_at }}
            </span>
            <IconButton icon="xmark" @click="markAsRead" />
        </div>
    </div>
</template>

<style scoped>
.notification-image {
    width: 35px;
    height: 35px;
}
</style>
