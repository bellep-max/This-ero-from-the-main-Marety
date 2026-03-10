<script setup>
import { $t } from '@/i18n.js';
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useRoute, useRouter } from 'vue-router'
import apiClient from '@/api/client'
const CommentsSection = defineAsyncComponent(() => import('@/Components/Sections/CommentsSection.vue'));
import route from "@/helpers/route"

const post = ref(null);
const comments = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/blog/${currentRoute.params.slug}`);
      const apiData = response.data;
      post.value = apiData.post ?? null;
    comments.value = apiData.comments ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const updateComments = async () => {
  try {
    const response = await apiClient.get(currentRoute.path.replace(/^\//, ''));
    const apiData = response.data;
    if (apiData.comments) comments.value = apiData.comments;
  } catch (error) {
    console.error('Failed to refresh comments:', error);
  }
};

const content = computed(() => (post.full_content ? post.value?.full_content : post.value?.short_content));
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
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
  </template>

<style scoped></style>
