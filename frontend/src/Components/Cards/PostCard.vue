<script setup>
import { computed } from 'vue';
import route from "@/helpers/route"

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
});

const styleImage = computed(() => `background: url(${props.post.artwork})`);
</script>

<template>
    <div class="blog-card bg-default rounded-4 p-3 gap-4 d-flex flex-column flex-md-row">
        <div class="blog_container__data__content__item__img" :style="styleImage"></div>
        <div class="d-flex flex-column gap-4 align-items-start justify-content-start">
            <p class="h3 font-default">
                {{ post.title }}
            </p>
            <div class="d-flex flex-row gap-5 justify-content-start align-items-center">
                <div class="blog-card-author text-desc">
                    {{ post.author }}
                </div>
                <div class="blog-card-author text-desc">
                    {{ post.created_at }}
                </div>
            </div>
            <div class="text-description text-secondary">
                {{ post.preview }}
            </div>
            <a class="text-description color-pink" href=""> erocast.com </a>
            <a :href="route('posts.show', post.uuid)" class="btn-default btn-pink mt-auto"> Read More </a>
        </div>
    </div>
</template>
