<script setup>
import { computed, defineAsyncComponent, ref } from 'vue';
import { $t } from '@/i18n.js';
import { router } from '@inertiajs/vue3';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));

const props = defineProps({
    playlist: {
        type: Object,
        required: true,
    },
    controllable: {
        type: Boolean,
        default: false,
    },
});

const isClicked = ref(false);

const deletePlaylist = () => {
    router.delete(route('playlists.destroy', props.playlist.uuid));
};

const trimmedTitle = computed(() => `${props.playlist.title.substring(0, 25)}...`);
</script>

<template>
    <div class="col-12 col-md-4 p-1">
        <div
            v-if="controllable"
            class="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3"
        >
            <a
                class="d-flex flex-column align-items-center gap-2 text-decoration-none"
                :href="route('playlists.show', playlist)"
            >
                <img :src="playlist.artwork" class="card-item-avatar" :alt="playlist.title" />
                <div class="title font-default default-text-color text-center">
                    {{ trimmedTitle }}
                </div>
            </a>
            <div class="d-flex flex-row justify-content-center align-items-center mt-auto gap-3">
                <DefaultButton v-if="!isClicked" class-list="btn-outline btn-rounded" @click="isClicked = !isClicked">
                    <Icon :icon="['fas', 'trash']" />
                </DefaultButton>
                <div v-if="isClicked" class="d-flex flex-column text-center gap-2">
                    <span class="w-100 font-merge">
                        <Icon :icon="['fas', 'question']" /> {{ $t('misc.you_sure') }}
                    </span>
                    <div class="d-flex flex-row justify-content-between align-items-center gap-4">
                        <DefaultButton v-if="isClicked" class-list="btn-pink btn-narrow" @click="deletePlaylist">
                            {{ $t('buttons.yes') }}
                        </DefaultButton>
                        <DefaultButton
                            v-if="isClicked"
                            class-list="btn-outline btn-narrow"
                            @click="isClicked = !isClicked"
                        >
                            {{ $t('buttons.no') }}
                        </DefaultButton>
                    </div>
                </div>
                <DefaultButton
                    :href="route('playlists.edit', playlist)"
                    v-if="!isClicked"
                    class-list="btn-outline btn-rounded"
                >
                    <Icon :icon="['fas', 'pen']" />
                </DefaultButton>
            </div>
        </div>
        <a
            v-else
            :href="route('playlists.show', playlist)"
            class="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3"
        >
            <img :src="playlist.artwork" class="card-item-avatar" :alt="playlist.title" />
            <div class="title font-default default-text-color text-center">
                {{ trimmedTitle }}
            </div>
        </a>
    </div>
</template>

<style scoped></style>
