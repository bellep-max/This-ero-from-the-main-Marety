<script setup>
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { isLogged } from '@/Services/AuthService.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { addToFavorites, removeFromFavorites } from '@/Services/FavoriteService.js';
const Song = defineAsyncComponent(() => import('@/Components/Song.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const props = defineProps({
    channel: {
        type: Array,
        default: [],
    },
    objects: {
        type: Array,
        default: [],
    },
    pagination: {
        type: Object,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const lastPage = computed(() => props.pagination.current_page === props.pagination.last_page);
// const applyFilters = (formData) => {
//   formData
//       // .transform((data) => prepareFilterValues(data))
//       .get(route('discover.index'), {
//         preserveScroll: true,
//         preserveState: true,
//       });

// }
const loadMore = () => {
    router.reload({
        only: ['objects', 'pagination'],
        data: {
            page: props.pagination.current_page + 1,
        },
    });
};
</script>

<template>
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col text-start">
                    <div class="d-block">
                        <div class="block-title color-light text-truncate">
                            {{ channel.title }}
                        </div>
                        <div class="block-description color-light">
                            {{ channel.description }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col flex-column">
                    <template v-if="channel.type === ObjectTypes.Song">
                        <Song
                            v-for="song in objects"
                            :song="song"
                            :key="song.uuid"
                            :can-view="isLogged"
                            :is-owned="user?.uuid === song.user.uuid"
                            @add-to-favorites="addToFavorites($event, page.props.auth.user.uuid)"
                            @remove-from-favorites="removeFromFavorites($event, page.props.auth.user.uuid)"
                        />
                    </template>
                    <DefaultButton class-list="btn-outline mt-2 mx-auto" @click="loadMore" :disabled="lastPage">
                        {{ $t('buttons.load_more') }}
                    </DefaultButton>
                </div>
            </div>
        </div>
    </div>
</template>
