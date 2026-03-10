<script setup>
import { computed, defineAsyncComponent, ref } from 'vue';
import { $t } from '@/i18n.js';
import { router, usePage } from '@inertiajs/vue3';
import Activities from '@/Enums/Activities.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

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

const user = usePage().props.auth.user;

const notificationLink = ref(Activities.getLink(props.notification));
const isLink = ref(notificationLink.value.length > 0);
const isCollaborationInvite = computed(() => props.notification.action === Activities.InviteCollaboration);
const isRead = computed(() => props.notification.is_read);

const emit = defineEmits(['read']);

const markAsRead = (follow = false) => {
    router.post(
        route('notifications.read', { notification: props.notification.id }),
        {},
        {
            preserveScroll: true,
            // onSuccess: () => {
            //   if (follow) {
            //     router.visit(notificationLink.value);
            //   }
            // }
        },
    );
};

const submit = (response) => {
    router.post(
        route('playlists.collaborators.response', {
            playlist: props.notification.subject.uuid,
            user: props.notification.user.uuid,
        }),
        {
            collaborator_uuid: user.uuid,
            response: response,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                markAsRead();
            },
        },
    );
};
</script>

<template>
    <div class="track no-wrap w-100 justify-content-between">
        <div
            class="d-flex flex-row justify-content-start align-items-center gap-3 pe-auto cursor-pointer"
            @click="markAsRead(isLink)"
        >
            <img
                class="track-image rounded-4 d-md-block d-none"
                :alt="notification.user?.name"
                :src="notification.user?.artwork"
            />
            <div
                class="description font-default"
                :class="{
                    'color-light': !darkFont,
                    'default-text-color': darkFont,
                }"
            >
                {{ Activities.getText(notification) }}
            </div>
        </div>
        <div v-if="isCollaborationInvite" class="d-flex flex-column justify-content-between gap-1">
            <DefaultButton class-list="btn-pink btn-narrow" @click="submit(true)">
                {{ $t('buttons.accept') }}
            </DefaultButton>
            <DefaultButton class-list="btn-outline btn-narrow" @click="submit(false)">
                {{ $t('buttons.decline') }}
            </DefaultButton>
        </div>
        <div class="d-flex flex-row justify-content-end align-items-center gap-3">
            <span
                class="font-merge"
                :class="{
                    'color-light': !darkFont,
                    'color-grey': darkFont,
                }"
            >
                <Icon :icon="['fas', 'clock']" /> {{ notification.created_at }}
            </span>
            <IconButton icon="xmark" @click="markAsRead" :disabled="isRead" />
        </div>
    </div>
</template>
