<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent } from 'vue';
import { isNotEmpty } from '@/Services/MiscService.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const CircledText = defineAsyncComponent(() => import('@/Components/CircledText.vue'));
const Song = defineAsyncComponent(() => import('@/Components/Song.vue'));
const ProfileBlockHeader = defineAsyncComponent(() => import('@/Components/ProfileBlockHeader.vue'));

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
    recent: {
        required: false,
        type: Object,
        default: {},
    },
});

const page = usePage();

const pageTitle = computed(() =>
    props.user.own_profile
        ? $t('pages.user.my_profile_overview')
        : $t('pages.user.user_profile_overview', { name: props.user.name }),
);
</script>

<template>
    <Head :title="pageTitle" />
    <UserLayout :title="pageTitle" :user="user" :overflow="false">
        <div class="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-center flex-wrap">
            <CircledText :title="user.favorites" :description="$t('pages.user.favorites')" />
            <CircledText :title="user.total_plays" :description="$t('pages.user.total_plays')" />
            <CircledText :title="user.free_followers_count" :description="$t('pages.user.followers.free')" />
            <CircledText :title="user.patrons_count" :description="$t('pages.user.followers.patrons')" />
        </div>
        <div v-if="isNotEmpty(recent)" class="d-flex flex-column w-100 mt-4 vh-100">
            <ProfileBlockHeader :title="$t('pages.user.recent_tracks')" />
            <div class="d-flex flex-column overflow-y-auto p-2">
                <Song
                    v-for="song in recent"
                    :song="song"
                    :key="song.uuid"
                    :is-owned="user.uuid === song.user.uuid"
                    @add-to-favorites="addToFavorites($event, page.props.auth.user.uuid)"
                    @remove-from-favorites="removeFromFavorites($event, page.props.auth.user.uuid)"
                    dark-font
                    can-view
                />
            </div>
        </div>
    </UserLayout>
</template>
