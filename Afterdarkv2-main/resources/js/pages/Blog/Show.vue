<script setup>
import { $t } from '@/i18n.js';
import { router } from '@inertiajs/vue3';
import { computed, defineAsyncComponent } from 'vue';
const CommentsSection = defineAsyncComponent(() => import('@/Components/Sections/CommentsSection.vue'));

const props = defineProps({
    post: {
        type: Object,
        default: {},
    },
    comments: {
        type: Array,
    },
});

const updateComments = () => {
    router.reload({
        only: ['comments'],
    });
};

const content = computed(() => (props.post.full_content ? props.post.full_content : props.post.short_content));
</script>

<template>
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container d-flex flex-column gap-4">
            <div class="row">
                <div class="col text-start">
                    <div class="d-block">
                        <div class="block-title color-light text-truncate">
                            {{ post.title }}
                        </div>
                        <a :href="route('posts.index')" class="block-description color-light">
                            {{ $t('misc.back') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div
                        class="d-flex flex-column justify-content-center align-items-center gap-4 bg-default rounded-5 p-5 h-100"
                    >
                        <img v-if="post.artwork" class="img-fluid" :alt="post.title" :src="post.artwork" />
                        <div v-html="content" class="fs-5"></div>
                    </div>
                </div>
            </div>
            <div v-if="post.allow_comments" class="row">
                <CommentsSection :comments="comments" :type="post.type" :uuid="post.uuid" @commented="updateComments" />
            </div>
            <div v-else class="row text-center color-light font-default fs-5">
                <span>
                    {{ $t('misc.comments_disabled') }}
                </span>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
