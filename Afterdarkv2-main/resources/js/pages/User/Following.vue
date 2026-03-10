<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { $t } from '@/i18n.js';
import { isNotEmpty } from '@/Services/MiscService.js';
import { defineAsyncComponent } from 'vue';
const FollowerCard = defineAsyncComponent(() => import('@/Components/Cards/FollowerCard.vue'));
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
    following: {
        required: true,
        type: Array,
        default: [],
    },
});

const unfollow = (user) => {
    const currentUser = usePage().props.auth.user;

    router.delete(route('users.following.destroy', { user: currentUser.uuid }), {
        data: {
            user_uuid: user.uuid,
        },
        onSuccess: () => {},
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <Head :title="$t('pages.user.following')" />
    <UserLayout :title="$t('pages.user.following')" :user="user">
        <div v-if="isNotEmpty(following)" class="row w-100">
            <FollowerCard v-for="user in following" :user="user" @unfollow="unfollow(user)" controllable />
        </div>
        <div v-else class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
            {{ $t('pages.user.no_following') }}
        </div>
    </UserLayout>
</template>
