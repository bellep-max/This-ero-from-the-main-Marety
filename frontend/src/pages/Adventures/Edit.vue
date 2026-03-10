<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, ref, reactive, onMounted } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { BCollapse, BFormCheckbox, BFormFile, BFormInput, BFormTextarea, BTab, BTabs } from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys, slugify } from '@/Services/FormService.js';
import { useModal } from 'vue-final-modal';
import ObjectTypes from '@/Enums/ObjectTypes.js';
const ConfirmDeletionModal = defineAsyncComponent(() => import('@/Components/Modals/ConfirmDeletionModal.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
import route from "@/helpers/route"

const adventure = ref(null);
const user = ref(null);
const loading = ref(true);
const currentRoute = useRoute();

  onMounted(async () => {
    try {
      const response = await apiClient.get(`/adventures/${currentRoute.params.uuid}/edit`);
      const apiData = response.data;
      adventure.value = apiData.adventure ?? null;
    user.value = apiData.user ?? null;
    } catch (error) {
      console.error('Failed to load page data:', error);
    } finally {
      loading.value = false;
    }
  });

const authStore = useAuthStore();

const genres = ref(authStore.filter_presets.genres);
const tags = ref(authStore.filter_presets.tags);

const maxAllowed = 60;
const titleLength = ref(adventure.value?.title?.length ?? 0);
const inputAllowed = ref(true);

const artworkPreview = ref(adventure.value?.artwork);

const form = useForm({
    _method: 'PATCH',
    title: adventure.value?.title,
    tags: adventure.value?.tags,
    genres: adventure.value?.genres,
    is_visible: adventure.value?.is_visible,
    allow_comments: adventure.value?.allow_comments,
    allow_download: adventure.value?.allow_download,
    description: adventure.value?.description,
    artwork: null,
});

const setImagePreview = () => {
    artworkPreview.value = form.artwork ? URL.createObjectURL(form.artwork) : song.artwork;
};

const addTag = (tag) => {
    const newTag = {
        id: -Math.floor(Math.random() * 10000000),
        tag: slugify(tag),
    };

    tags.value.push(newTag);
    form.tags.push(newTag);
};

const checkLength = () => {
    titleLength.value = form.title?.length ?? 0;
    inputAllowed.value = form.title?.length < maxAllowed;
};

const { open, close } = useModal({
    component: ConfirmDeletionModal,
    attrs: {
        title: adventure.value?.title,
        type: ObjectTypes.Adventure,
        onClose() {
            close();
        },
        onConfirm() {
            close();
            apiClient.delete(route('adventures.destroy', adventure.value));
        },
    },
    clickToClose: true,
    escToClose: true,
});

const submit = () => {
    form.transform(removeEmptyObjectsKeys).post(route('adventures.update', adventure.value), {
        preserveScroll: true,
    });
};
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
                        <BFormInput
                            v-model="form.title"
                            type="text"
                            :state="inputAllowed"
                            :max="maxAllowed"
                            @update:model-value="checkLength()"
                        />
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
                    <div class="d-flex flex-column w-100 justify-content-start align-items-start gap-1">
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
                    <div class="row">
                        <div class="col d-flex flex-row justify-content-between align-items-center">
                            <BFormCheckbox v-model="form.is_visible" switch>
                                {{ $t('misc.make_public') }}
                            </BFormCheckbox>
                            <BFormCheckbox v-model="form.allow_comments" switch>
                                {{ $t('misc.allow_comments') }}
                            </BFormCheckbox>
                        </div>
                    </div>
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label class="text-start font-default fs-14 default-text-color">
                            {{ $t('misc.description') }}
                        </label>
                        <BFormTextarea v-model="form.description" />
                    </div>
                </div>
            </div>
            <BTabs
                nav-wrapper-class="tabs-header w-100"
                nav-class="px-4 w-100"
                active-nav-item-class="tab-item-active fs-5 font-merge"
                nav-item-class="tab-item default-text-color px-4 fs-5 font-merge"
                tab-class="py-4"
                fill
            >
                <BTab
                    v-for="(root, rootIndex) in adventure.children"
                    :key="rootIndex"
                    :title="$t('modals.upload.adventure.root', { index: rootIndex + 1 })"
                >
                    <div class="row">
                        <div class="col-4">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label class="text-center font-default fs-14 default-text-color">
                                    {{ $t('misc.artwork') }}
                                </label>
                                <img
                                    :src="root.artwork"
                                    class="img-fluid rounded-4 border-pink"
                                    :alt="`${rootIndex}_artwork`"
                                />
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="d-flex flex-column justify-content-start w-100 gap-4">
                                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                    <label class="text-start font-default fs-14 default-text-color">
                                        {{ $t('misc.title') }}
                                    </label>
                                    <BFormInput :model-value="root.title" type="text" disabled readonly />
                                </div>
                                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                    <label class="text-start font-default fs-14 default-text-color">
                                        {{ $t('misc.description') }}
                                    </label>
                                    <BFormTextarea :model-value="root.description" disabled readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    <BCollapse v-for="(final, finalIndex) in root.children" :key="`${finalIndex}_${rootIndex}`">
                        <template #header="{ visible, toggle, id }">
                            <DefaultButton
                                :class-list="{
                                    'w-100': true,
                                    'btn-outline': !visible,
                                    'btn-pink': visible,
                                    'mt-4': finalIndex === 0,
                                    'mt-2': finalIndex !== 0,
                                }"
                                :aria-expanded="visible"
                                :aria-controls="id"
                                @click="toggle"
                            >
                                <span
                                    >{{ visible ? 'Close' : 'Open' }}
                                    {{
                                        $t('modals.upload.adventure.final', {
                                            index: finalIndex + 1,
                                        })
                                    }}</span
                                >
                            </DefaultButton>
                        </template>
                        <!-- Content here -->
                        <div class="row mt-4">
                            <div class="col-4">
                                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                    <label class="text-center font-default fs-14 default-text-color">
                                        {{ $t('misc.artwork') }}
                                    </label>
                                    <img
                                        :src="final.artwork"
                                        class="img-fluid rounded-4 border-pink"
                                        :alt="`${finalIndex}_${rootIndex}_artwork`"
                                    />
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="d-flex flex-column justify-content-start w-100 gap-4">
                                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                        <label class="text-start font-default fs-14 default-text-color">
                                            {{ $t('misc.title') }}
                                        </label>
                                        <BFormInput :model-value="final.title" type="text" disabled readonly />
                                    </div>
                                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                        <label class="text-start font-default fs-14 default-text-color">
                                            {{ $t('misc.description') }}
                                        </label>
                                        <BFormTextarea :model-value="final.description" disabled readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCollapse>
                </BTab>
            </BTabs>
        </div>
        <div class="d-flex flex-row justify-content-center align-items-center gap-4 w-100 mt-5">
            <DefaultButton :href="route('adventures.show', adventure)" class-list="btn-outline">
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
