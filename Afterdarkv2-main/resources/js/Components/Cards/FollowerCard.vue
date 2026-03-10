<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent } from 'vue';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    controllable: {
        type: Boolean,
        default: false,
    },
    showStats: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['unfollow']);
</script>

<template>
    <div class="col-12 col-md-4 p-1">
        <a
            :href="route('users.show', user)"
            class="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3 text-decoration-none"
        >
            <img :src="user.artwork" class="card-item-avatar" :alt="user.title" />
            <div class="title font-default default-text-color text-center">
                {{ user.username }}
            </div>
            <div
                v-if="showStats"
                class="mt-auto d-flex flex-row justify-content-between align-items-end text-center font-merge gap-2"
            >
                <div class="d-flex flex-column gap-1">
                    <span class="color-pink fs-5">
                        {{ user.tracks }}
                    </span>
                    <span class="color-grey">
                        {{ $t('pages.user.stats.tracks') }}
                    </span>
                </div>
                <div class="d-flex flex-column gap-1">
                    <span class="color-pink fs-5">
                        {{ user.playlists }}
                    </span>
                    <span class="color-grey">
                        {{ $t('pages.user.stats.playlists') }}
                    </span>
                </div>
                <div class="d-flex flex-column gap-1">
                    <span class="color-pink fs-5">
                        {{ user.adventures }}
                    </span>
                    <span class="color-grey">
                        {{ $t('pages.user.stats.adventures') }}
                    </span>
                </div>
            </div>
        </a>
        <DefaultButton v-if="controllable" class-list="mt-2 btn-outline w-100" @click="$emit('unfollow', user)">
            {{ $t('buttons.unfollow') }}
        </DefaultButton>
    </div>
</template>
