<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent } from 'vue';
const BlogPageFilters = defineAsyncComponent(() => import('@/Components/Sections/BlogPageFilters.vue'));
const PostCard = defineAsyncComponent(() => import('@/Components/Cards/PostCard.vue'));
import route from "@/helpers/route"

const props = defineProps({
    posts: {
        type: Object,
        default: {},
    },
    categories: {
        type: Array,
        default: [],
    },
    archives: {
        type: Array,
        default: [],
    },
    tags: {
        type: Array,
        default: [],
    },
    filters: {
        type: Object,
        required: true,
    },
});

const applyFilters = (formData) => {
    formData
        // .transform((data) => prepareFilterValues(data))
        .get(route('posts.index'), {
            preserveScroll: true,
            preserveState: true,
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
                            {{ $t('pages.blog.title') }}
                        </div>
                        <div class="block-description color-light">
                            {{ $t('pages.blog.description') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    <BlogPageFilters
                        :categories="categories"
                        :archives="archives"
                        :tags="tags"
                        :filters="filters"
                        @updated="applyFilters"
                    />
                </div>
                <div class="col col-xl-9 d-flex flex-column gap-4">
                    <PostCard v-for="post in posts" :post="post" />
                </div>
            </div>
        </div>
    </div>
</template>
