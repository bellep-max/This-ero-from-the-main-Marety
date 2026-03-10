<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, ref, watch, reactive, onMounted } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { BFormCheckbox, BFormInput, BFormTextarea } from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys } from '@/Services/FormService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Multiselect = defineAsyncComponent(() => import('@vueform/multiselect'));
import '@vueform/multiselect/themes/default.css';
import route from "@/helpers/route"

const playlist = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/playlists/${currentRoute.params.uuid}/edit`);
      const apiData = response.data;
      playlist.value = apiData.playlist ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const defaultArtworkLink = '/assets/images/playlist.png';

const artworkPreview = ref(playlist.value?.artwork);
const artworkSelected = ref(false);

const authStore = useAuthStore();
const user = authStore.user;
const genres = ref(authStore.filter_presets.genres);

const form = useForm({
    _method: 'patch',
    artwork: null,
    genres: playlist.value?.genres,
    title: playlist.value?.title,
    description: playlist.value?.description,
    is_visible: playlist.value?.is_visible,
    allow_comments: playlist.value?.allow_comments,
    is_explicit: playlist.value?.is_explicit,
});

const resetArtwork = () => {
    form.reset('artwork');
};

const setArtwork = (e) => {
    form.artwork = e.target.files[0];
};

const setImagePreview = (image) => {
    artworkPreview.value = URL.createObjectURL(image);
    artworkSelected.value = true;
};

const submit = () => {
    form.transform(removeEmptyObjectsKeys).post(route('playlists.update', playlist.value), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

watch(
    () => form.artwork,
    (newValue) => {
        if (newValue) {
            setImagePreview(newValue);
        } else {
            artworkPreview.value = defaultArtworkLink;
            artworkSelected.value = false;
        }
    },
);
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :user="playlist.user">
        <div class="d-flex flex-column gap-3">
            <div class="row gy-3">
                <div
                    class="col-12 col-lg-4 d-flex flex-column gap-4 justify-content-center justify-content-xl-start align-items-center"
                >
                    <img :src="artworkPreview" :alt="playlist.title" class="img-fluid rounded-4 border-pink" />
                    <DefaultButton v-if="artworkSelected" class-list="btn-outline" @click="resetArtwork">
                        <Icon :icon="['fas', 'trash']" />
                        {{ $t('buttons.clear') }}
                    </DefaultButton>
                    <label class="btn-default btn-pink">
                        {{ $t('misc.artwork') }}
                        <input class="d-none" accept="image/*" type="file" @change="setArtwork" />
                    </label>
                </div>
                <div class="col">
                    <div class="d-flex flex-column justify-content-start align-items-center gap-4">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                            <label class="font-default fs-14 color-text">{{ $t('misc.title') }}</label>
                            <BFormInput maxlength="175" v-model="form.title" required />
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                            <label class="font-default fs-14 color-text">
                                {{ $t('misc.genres') }}
                            </label>
                            <Multiselect
                                :options="genres"
                                v-model="form.genres"
                                :searchable="true"
                                :multiple="true"
                                mode="tags"
                                label="text"
                                track-by="value"
                                clear-on-search
                                hide-selected
                            >
                            </Multiselect>
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                            <label class="font-default fs-14 color-text">
                                {{ $t('misc.description') }}
                            </label>
                            <BFormTextarea maxlength="180" v-model="form.description" />
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center flex-wrap"
                    >
                        <div class="d-flex flex-row justify-content-start gap-3">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <BFormCheckbox checked="checked" v-model="form.allow_comments" />
                                <label class="font-default fs-14 color-text">{{ $t('misc.allow_comments') }}</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <BFormCheckbox checked="checked" v-model="form.is_visible" />
                                <label class="font-default fs-14 color-text">{{ $t('misc.make_public') }}</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <BFormCheckbox checked="checked" v-model="form.is_explicit" />
                                <label class="font-default fs-14 color-text">{{ $t('misc.explicit') }}</label>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-center align-items-center gap-3">
                            <DefaultButton class-list="btn-outline" :href="route('playlists.show', playlist)">
                                {{ $t('buttons.cancel') }}
                            </DefaultButton>
                            <DefaultButton class-list="btn-pink" @click="submit">
                                {{ $t('buttons.save') }}
                            </DefaultButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
  </template>

<style scoped></style>
