<script setup>
import { $t } from '@/i18n';
import { computed, defineAsyncComponent } from 'vue';
import { router } from '@inertiajs/vue3';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const props = defineProps({
    is_favorite: {
        type: Boolean,
        default: false,
    },
    user: {
        type: Object,
        required: true,
    },
    currentUser: {
        type: Object,
        required: true,
    },
});

const isFavorite = computed(() => props.user.favorite);

const changeFollowingStatus = () => {
    if (props.user.favorite) {
        router.delete(route('users.following.destroy', { user: props.currentUser.uuid }), {
            data: {
                user_uuid: props.user.uuid,
            },
            onSuccess: () => {},
            preserveScroll: true,
            preserveState: true,
        });
    } else {
        router.post(route('users.following.store', { user: props.currentUser.uuid }), {
            user_uuid: props.user.uuid,
            onSuccess: () => {},
            preserveScroll: true,
            preserveState: true,
        });
    }
};
</script>

<template>
    <DefaultButton
        :class="[
            {
                'btn-pink': isFavorite,
                'btn-outline': !isFavorite,
            },
            'w-100',
            'btn-default',
        ]"
        @click="changeFollowingStatus"
    >
        <Icon :icon="['fas', isFavorite ? 'user-minus' : 'user-plus']" />
        {{ isFavorite ? $t('buttons.unfollow') : $t('buttons.follow') }}
    </DefaultButton>
</template>

<style scoped></style>
