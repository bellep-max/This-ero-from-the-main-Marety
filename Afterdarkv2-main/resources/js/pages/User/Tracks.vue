<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent, ref, watch } from 'vue';
import { BButton, BFormInput, BInputGroup } from 'bootstrap-vue-next';
import debounce from 'lodash.debounce';
import { removeEmptyObjectsKeys } from '@/Services/FormService.js';
import { isNotEmpty } from '@/Services/MiscService.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const Song = defineAsyncComponent(() => import('@/Components/Song.vue'));

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
    tracks: {
        required: true,
        type: Object,
        default: {},
    },
    filters: {
        type: Object,
        required: true,
    },
});

const page = usePage();

const clearable = ref(false);

const pageTitle = computed(() =>
    props.user.own_profile ? $t('pages.user.my_tracks') : $t('pages.user.user_tracks', { name: props.user.name }),
);

const filters = useForm({
    search: props.filters?.search,
});

const applyFilters = () => {
    filters.transform(removeEmptyObjectsKeys).get(route('users.tracks', props.user), {
        preserveScroll: true,
        preserveState: true,
        only: ['tracks'],
    });
};

const resetFilters = () => {
    filters.search = null;
    applyFilters();
};

const filtersChanged = debounce(applyFilters, 300, { maxWait: 1000 });

watch(
    () => filters.search,
    (newValue) => {
        clearable.value = newValue?.length > 0;
    },
);
</script>

<template>
    <Head :title="pageTitle" />
    <UserLayout :title="pageTitle" :user="user" :overflow="false">
        <div class="d-flex flex-column gap-4">
            <div class="d-flex flex-row justify-content-between gap-5">
                <BInputGroup size="sm">
                    <BFormInput
                        type="text"
                        :placeholder="$t('misc.search')"
                        v-model="filters.search"
                        @update:model-value="filtersChanged"
                    />
                    <BButton variant="outline-danger" @click="resetFilters" :disabled="!clearable">
                        {{ $t('buttons.clear') }}
                    </BButton>
                </BInputGroup>
            </div>
            <div v-if="isNotEmpty(tracks)" class="d-flex flex-column w-100 vh-100 overflow-y-auto p-1">
                <Song
                    v-for="song in tracks"
                    :song="song"
                    :key="song.uuid"
                    :is-owned="user.own_profile"
                    @add-to-favorites="addToFavorites($event, page.props.auth.user.uuid)"
                    @remove-from-favorites="removeFromFavorites($event, page.props.auth.user.uuid)"
                    dark-font
                    can-view
                />
            </div>
            <div v-else class="d-flex flex-column w-100 vh-100 align-items-center justify-content-center text-center">
                {{ $t('pages.user.no_tracks', { name: user.name }) }}
            </div>
        </div>
    </UserLayout>
</template>
