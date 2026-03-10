<script setup>
import { defineAsyncComponent } from 'vue';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const props = defineProps({
    comment: {
        type: Object,
        required: true,
    },
    replied: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['reply', 'cancel']);
</script>

<template>
    <div class="d-flex flex-row gap-4 justify-content-start align-items-center w-100">
        <Icon v-if="comment.is_reply" :icon="['fas', 'arrow-turn-up']" class-list="color-light" />
        <div class="bg-default rounded-4 p-3 d-flex flex-row gap-4 flex-grow-1">
            <div class="d-flex flex-row gap-3">
                <img class="comment-img" :src="comment.artwork" :alt="comment.user" />
                <div class="d-flex flex-column text-start">
                    <span class="d-inline-block font-default fw-bolder">
                        {{ comment.user }}
                    </span>
                    <span class="d-inline-block font-merge color-grey">
                        {{ comment.created_at }}
                    </span>
                </div>
            </div>
            <div class="d-block fs-5">
                {{ comment.content }}
            </div>
        </div>
        <div v-show="!comment.is_reply">
            <DefaultButton v-if="replied" class-list="btn-outline btn-rounded" @click="$emit('cancel')">
                <Icon :icon="['fas', 'xmark']" />
            </DefaultButton>
            <DefaultButton v-else class-list="btn-outline btn-rnd" @click="$emit('reply', comment)">
                <Icon :icon="['fas', 'reply']" />
            </DefaultButton>
        </div>
    </div>
</template>

<style scoped></style>
