<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, ref, watch, reactive, onMounted } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { BFormCheckbox, BFormInput, BFormTextarea, BFormSelect } from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys, slugify } from '@/Services/FormService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const podcast = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/podcasts/${currentRoute.params.uuid}/edit`);
      const apiData = response.data;
      podcast.value = apiData.podcast ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const defaultArtworkLink = '/assets/images/podcast.png';

const artworkPreview = ref(podcast.value?.artwork);
const artworkSelected = ref(false);

const authStore = useAuthStore();
const user = authStore.user;
const tags = ref(authStore.filter_presets.tags);
const countries = ref(authStore.filter_presets.countries);
const languages = ref(authStore.filter_presets.languages);
const categories = ref(authStore.filter_presets.categories);

const form = useForm({
    _method: 'patch',
    artwork: null,
    tags: podcast.value?.tags ?? [],
    title: podcast.value?.title,
    description: podcast.value?.description,
    is_visible: podcast.value?.is_visible,
    allow_comments: podcast.value?.allow_comments,
    allow_download: podcast.value?.allow_download,
    explicit: podcast.value?.explicit,
    categories: podcast.value?.categories ?? [],
    language_id: podcast.value?.language_id,
    country_id: podcast.value?.country_id,
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

const addTag = (tag) => {
    const newTag = {
        id: -Math.floor(Math.random() * 10000000),
        tag: slugify(tag),
    };

    tags.value.push(newTag);
    form.tags.push(newTag);
};

const submit = () => {
    form.transform(removeEmptyObjectsKeys).post(route('podcasts.update', podcast.value), {
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
    
    <UserLayout :user="podcast.user">
        <div class="d-flex flex-column gap-3">
            <div class="row gy-3">
                <div
                    class="col-12 col-lg-4 d-flex flex-column gap-4 justify-content-center justify-content-xl-start align-items-center"
                >
                    <img :src="artworkPreview" :alt="podcast.title" class="img-fluid rounded-4 border-pink" />
                    <DefaultButton v-if="artworkSelected" class-list="btn-outline" @click="resetArtwork">
                        <Icon :icon="['fas', 'trash']" />
                        {{ $t('buttons.clear') }}
                    </DefaultButton>
                    <label class="btn-default btn-pink">
                        {{ $t('misc.artwork') }}
                        <input class="d-none" accept="image/*" type="file" @change="setArtwork" />
                    </label>
                </div>
                <div class="col d-flex flex-column justify-content-start align-items-center gap-4">
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                        <label class="font-default fs-14 color-text">{{ $t('misc.title') }}</label>
                        <BFormInput maxlength="175" v-model="form.title" required />
                    </div>
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                        <label class="font-default fs-14 color-text">
                            {{ $t('misc.tags') }}
                        </label>
                        <vue-multiselect
                            v-model="form.tags"
                            :options="tags"
                            :multiple="true"
                            :taggable="true"
                            track-by="id"
                            label="tag"
                            @tag="addTag"
                        />
                    </div>
                    <!--          <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">-->
                    <!--            <label class="font-default fs-14 color-text">-->
                    <!--              {{ $t('misc.categories') }}-->
                    <!--            </label>-->
                    <!--            <vue-multiselect-->
                    <!--                v-model="form.categories"-->
                    <!--                :options="categories"-->
                    <!--                :multiple="true"-->
                    <!--            />-->
                    <!--          </div>-->
                    <div class="d-flex flex-row justify-content-between align-items-start gap-4 w-100">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                            <label class="font-default fs-14 color-text">
                                {{ $t('misc.country') }}
                            </label>
                            <BFormSelect v-model="form.country_id" :options="countries"></BFormSelect>
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                            <label class="font-default fs-14 color-text">
                                {{ $t('misc.language') }}
                            </label>
                            <BFormSelect v-model="form.language_id" :options="languages"></BFormSelect>
                        </div>
                    </div>
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                        <label class="font-default fs-14 color-text">
                            {{ $t('misc.description') }}
                        </label>
                        <BFormTextarea maxlength="180" v-model="form.description" />
                    </div>
                </div>
                <div class="col-12 d-flex flex-column gap-3">
                    <div class="d-flex flex-row justify-content-between align-items-center w-100 flex-wrap">
                        <BFormCheckbox v-model="form.is_visible" switch size="lg">
                            {{ $t('misc.make_public') }}
                        </BFormCheckbox>
                        <BFormCheckbox v-model="form.allow_comments" switch size="lg">
                            {{ $t('misc.allow_comments') }}
                        </BFormCheckbox>
                        <BFormCheckbox v-model="form.allow_download" switch size="lg">
                            {{ $t('misc.allow_download') }}
                        </BFormCheckbox>
                        <BFormCheckbox v-model="form.explicit" switch size="lg">
                            {{ $t('misc.explicit') }}
                        </BFormCheckbox>
                    </div>
                    <div class="d-flex flex-row justify-content-end align-items-center gap-3 w-100">
                        <DefaultButton class-list="btn-outline" :href="route('podcasts.show', podcast)">
                            {{ $t('buttons.cancel') }}
                        </DefaultButton>
                        <DefaultButton class-list="btn-pink" @click="submit">
                            {{ $t('buttons.save') }}
                        </DefaultButton>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
  </template>
