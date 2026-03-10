<script setup>
import apiClient from "@/api/client";
import { $t } from '@/i18n.js';
import { defineAsyncComponent, ref, onMounted } from 'vue';
const BlogPageFilters = defineAsyncComponent(() => import('@/Components/Sections/BlogPageFilters.vue'));
const PostCard = defineAsyncComponent(() => import('@/Components/Cards/PostCard.vue'));
import route from "@/helpers/route"

const posts = ref([]);
const categories = ref([]);
const archives = ref([]);
const tags = ref([]);
const filters = ref({});
const loading = ref(true);

  onMounted(async () => {
    try {
      const response = await apiClient.get('/blog');
      const apiData = response.data;
      posts.value = apiData.posts ?? null;
    categories.value = apiData.categories ?? null;
    archives.value = apiData.archives ?? null;
    tags.value = apiData.tags ?? null;
    filters.value = apiData.filters ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
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
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
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
  </template>
