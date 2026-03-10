<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, onMounted, ref, watch, reactive } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { BFormCheckbox, BFormFile, BFormInput, BFormSelect, BFormTextarea } from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys, slugify } from '@/Services/FormService.js';
import { useModal } from 'vue-final-modal';
import ObjectTypes from '@/Enums/ObjectTypes.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const ConfirmDeletionModal = defineAsyncComponent(() => import('@/Components/Modals/ConfirmDeletionModal.vue'));
import route from "@/helpers/route"

const song = ref(null);
const user = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/songs/${currentRoute.params.uuid}/edit`);
      const apiData = response.data;
      song.value = apiData.song ?? null;
    user.value = apiData.user ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const authStore = useAuthStore();

const genres = ref(authStore.filter_presets.genres);
const vocals = ref(authStore.filter_presets.vocals);
const tags = ref(authStore.filter_presets.tags);

const maxAllowed = 60;
const titleLength = ref(0);
const inputAllowed = ref(true);

const artworkPreview = ref(song.value?.artwork);

const form = useForm({
    _method: 'PATCH',
    title: song.value?.title,
    tags: song.value?.tags,
    vocal_id: song.value?.vocal_id,
    genres: song.value?.genres,
    is_visible: song.value?.is_visible,
    allow_comments: song.value?.allow_comments,
    allow_download: song.value?.allow_download,
    is_explicit: song.value?.is_explicit,
    is_patron: song.value?.is_patron,
    description: song.value?.description,
    script: song.value?.script,
    artwork: null,
});

const setImagePreview = () => {
    artworkPreview.value = form.artwork ? URL.createObjectURL(form.artwork) : song.value?.artwork;
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
    form.transform(removeEmptyObjectsKeys).post(route('songs.update', song.value), {
        preserveScroll: true,
    });
};

const { open, close } = useModal({
    component: ConfirmDeletionModal,
    attrs: {
        title: song.value?.title,
        type: ObjectTypes.Song,
        onClose() {
            close();
        },
        onConfirm() {
            close();
            apiClient.delete(route('songs.destroy', song.value));
        },
    },
    clickToClose: true,
    escToClose: true,
});

watch(
    () => form.title,
    (newValue) => {
        titleLength.value = newValue.length;
        inputAllowed.value = newValue.length < maxAllowed;
    },
);

onMounted(() => {
    titleLength.value = song.title.length;
});
</script>

<template>
      <div v-if="loading" class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <template v-else>
    
    <UserLayout :user="user">
        <div class="row gy-3">
            <div class="col-12 col-lg-4 d-flex justify-content-center justify-content-xl-start align-items-start">
                <div class="d-flex flex-column justify-content-start w-100 gap-4">
                    <div class="d-flex flex-column justify-content-center align-items-start gap-1">
                        <label class="text-center font-default fs-14 default-text-color">
                            {{ $t('misc.artwork') }}
                        </label>
                        <img
                            :src="artworkPreview"
                            class="img-fluid rounded-4 border-pink"
                            alt="artwork"
                            ref="imagePreview"
                        />
                    </div>
                    <BFormFile v-model="form.artwork" accept="image/*" @update:model-value="setImagePreview" />
                    <div class="d-flex flex-column fw-light text-secondary text-center font-merge">
                        <span>{{ $t('modals.upload.image_description.line_1') }}</span>
                        <span>{{ $t('modals.upload.image_description.line_2') }}</span>
                        <span>{{ $t('modals.upload.image_description.line_3') }}</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-column justify-content-start w-100 gap-4">
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label class="text-start font-default fs-14 default-text-color">
                            {{ $t('misc.title') }}
                        </label>
                        <BFormInput v-model="form.title" type="text" :state="inputAllowed" :max="maxAllowed" />
                        <span
                            class="fs-12 font-merge color-grey"
                            :class="{
                                'color-pink': !inputAllowed,
                            }"
                        >
                            {{
                                $t('modals.upload.characters_allowed', {
                                    max: maxAllowed,
                                    used: titleLength,
                                })
                            }}
                        </span>
                    </div>
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label class="text-start font-default fs-14 default-text-color">
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
                    <div class="d-flex flex-row justify-content-between align-items-start gap-3">
                        <div class="d-flex flex-column w-50 justify-content-start align-items-start gap-1">
                            <label class="text-start font-default fs-14 default-text-color">
                                {{ $t('misc.gender') }}
                            </label>
                            <BFormSelect v-model="form.vocal_id" :options="vocals" />
                        </div>
                        <div class="d-flex flex-column w-50 justify-content-start align-items-start gap-1">
                            <label class="text-start font-default fs-14 default-text-color">
                                {{ $t('misc.genres') }}
                            </label>
                            <vue-multiselect
                                v-model="form.genres"
                                :options="genres"
                                :multiple="true"
                                track-by="value"
                                label="text"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex flex-column justify-content-start align-items-start">
                                <BFormCheckbox v-model="form.is_visible" switch>
                                    {{ $t('misc.make_public') }}
                                </BFormCheckbox>
                                <BFormCheckbox v-model="form.allow_comments" switch>
                                    {{ $t('misc.allow_comments') }}
                                </BFormCheckbox>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column justify-content-start align-items-start">
                                <BFormCheckbox v-model="form.allow_download" switch>
                                    {{ $t('misc.allow_download') }}
                                </BFormCheckbox>
                                <BFormCheckbox v-model="form.is_explicit" switch>
                                    {{ $t('misc.explicit') }}
                                </BFormCheckbox>
                                <BFormCheckbox v-if="user.has_active_subscription" v-model="form.is_patron" switch>
                                    {{ $t('misc.patrons_only') }}
                                </BFormCheckbox>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label class="text-start font-default fs-14 default-text-color">
                            {{ $t('misc.description') }}
                        </label>
                        <BFormTextarea v-model="form.description" />
                    </div>
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label class="text-start font-default fs-14 default-text-color">
                            {{ $t('misc.script') }}
                        </label>
                        <BFormInput v-model="form.script" type="text" />
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex flex-row justify-content-center align-items-center gap-4 w-100 mt-5">
            <DefaultButton :href="route('songs.show', song)" class-list="btn-outline">
                {{ $t('buttons.cancel') }}
            </DefaultButton>
            <DefaultButton @click="submit" class-list="btn-pink">
                {{ $t('buttons.save') }}
            </DefaultButton>
            <DefaultButton class-list="btn-outline" @click="() => open()">
                <Icon :icon="['fas', 'trash']" size="lg" /> {{ $t('buttons.delete') }}
            </DefaultButton>
        </div>
    </UserLayout>
</template>
  </template>
