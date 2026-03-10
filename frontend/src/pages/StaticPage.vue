<script setup>
import apiClient from "@/api/client";
import { ref, onMounted } from "vue";
const content = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/pages/${currentRoute.params.slug}`);
      const apiData = response.data;
      content.value = apiData.content ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    <div class="container">
        <span v-html="content.content" />
    </div>
</template>
  </template>
