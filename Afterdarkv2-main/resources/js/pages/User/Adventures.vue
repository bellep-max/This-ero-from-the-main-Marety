<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent, ref } from 'vue';
import { isNotEmpty } from '@/Services/MiscService.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const AdventureUploadModal = defineAsyncComponent(() => import('@/Components/Modals/AdventureUploadModal.vue'));
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Adventure = defineAsyncComponent(() => import('@/Components/Adventure.vue'));

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
    adventures: {
        required: true,
        type: Object,
        default: {},
    },
});

const page = usePage();

const showAdventureModal = ref(false);

const canUpload = computed(() => props.user.own_profile && page.props.auth.user.can_upload);
const pageTitle = computed(() =>
    props.user.own_profile
        ? $t('pages.user.my_adventures')
        : $t('pages.user.user_adventures', { name: props.user.name }),
);
</script>

<template>
    <Head :title="pageTitle" />
    <UserLayout :title="pageTitle" :user="user">
        <template v-if="canUpload" #controls>
            <DefaultButton class-list="btn-pink btn-narrow" @click="showAdventureModal = true">
                {{ $t('buttons.upload.adventure') }}
            </DefaultButton>
        </template>
        <div v-if="isNotEmpty(adventures)" class="d-flex flex-column w-100 vh-100 overflow-y-auto p-1">
            <Adventure
                v-for="adventure in adventures"
                :adventure="adventure"
                :key="adventure.uuid"
                :is_owned="user.own_profile"
                dark-font
                can-view
                @add-to-favorites="addToFavorites($event, page.props.auth.user.uuid)"
                @remove-from-favorites="removeFromFavorites($event, page.props.auth.user.uuid)"
            />
        </div>
        <div v-else class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
            {{ $t('pages.user.no_adventures', { name: user.name }) }}
        </div>
    </UserLayout>
    <AdventureUploadModal v-model="showAdventureModal" @close="showAdventureModal = false" />
</template>
