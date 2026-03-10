<script setup>
import { ref, reactive, onMounted, defineAsyncComponent } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { isNotEmpty } from '@/Services/MiscService.js';
const FollowerCard = defineAsyncComponent(() => import('@/Components/Cards/FollowerCard.vue'));
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
import route from "@/helpers/route"

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
    const currentUser = useAuthStore().user;

    apiClient.delete(route('users.following.destroy', { user: currentUser.uuid }), {
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
    
    <UserLayout :title="$t('pages.user.following')" :user="user">
        <div v-if="isNotEmpty(following)" class="row w-100">
            <FollowerCard v-for="user in following" :user="user" @unfollow="unfollow(user)" controllable />
        </div>
        <div v-else class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
            {{ $t('pages.user.no_following') }}
        </div>
    </UserLayout>
</template>
