<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t, $td } from '@/i18n.js';
import {
    BFormCheckbox,
    BFormInput,
    BFormSelect,
    BFormTextarea,
    BFormFile,
    BTab,
    BTabs,
    BOverlay,
    BSpinner,
} from 'bootstrap-vue-next';
import { defineAsyncComponent, nextTick, ref, watch, reactive } from 'vue';
import { useForm } from '@/helpers/useForm'
import apiClient from '@/api/client'
import { useAuthStore } from '@/stores/auth'
import { parseBlob } from 'music-metadata';
import { songAssetImage } from '@/Services/AssetService.js';
import { removeEmptyObjectsKeys, slugify } from '@/Services/FormService.js';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
import route from "@/helpers/route"

const emit = defineEmits(['close']);

const authStore = useAuthStore();
const user = authStore.user;

const allowedFileSize = 92970;
const maxAllowed = 60;
const today = new Date(Date.now()).toJSON().substring(0, 10);

const titleLength = ref(0);
const inputAllowed = ref(true);

const tabIndex = ref(0);

const hasFile = ref(false);
const validateFileSize = ref(false);
const genres = ref(authStore.filter_presets.genres);
const vocals = ref(authStore.filter_presets.vocals);
const tags = ref(authStore.filter_presets.tags);
const artworkPreview = ref(null);
const defaultArtworkPreview = ref(songAssetImage);

const form = useForm({
    title: null,
    tags: [],
    vocal_id: null,
    genres: null,
    released_at: today,
    published_at: today,
    is_visible: false,
    allow_comments: false,
    allow_download: false,
    is_explicit: true,
    notify: false,
    is_patron: false,
    description: null,
    script: null,
    file: null,
    artwork: null,
});

const setId3Data = (data) => {
    if (!data) {
        return;
    }

    parseBlob(data).then((tags) => {
        form.title = tags.common.title ? tags.common.title : data.name;

        if (tags.common.picture?.length) {
            const blob = new Blob([tags.common.picture[0].data], {
                type: tags.common.picture[0].format,
            });

            defaultArtworkPreview.value = URL.createObjectURL(blob);
            artworkPreview.value = defaultArtworkPreview.value;
        } else {
            defaultArtworkPreview.value = songAssetImage;
            artworkPreview.value = songAssetImage;
        }
    });
};

const setImagePreview = () => {
    artworkPreview.value = form.artwork ? URL.createObjectURL(form.artwork) : defaultArtworkPreview.value;
};

const upload = () => {
    form.transform(removeEmptyObjectsKeys).post(route('upload.tracks.store'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const addTag = (tag) => {
    const newTag = {
        id: -Math.floor(Math.random() * 10000000),
        tag: slugify(tag),
    };

    tags.value.push(newTag);
    form.tags.push(newTag);
};

const close = () => {
    form.reset();
    emit('close');

    nextTick(() => {
        tabIndex.value--;
    });
};

watch(
    () => form.title,
    (newValue) => {
        titleLength.value = newValue?.length ?? 0;
        inputAllowed.value = newValue?.length < maxAllowed;
    },
);

watch(
    () => form.file,
    (newValue) => {
        const isEmpty = !newValue || newValue.size <= 0;
        validateFileSize.value = isEmpty ? false : Math.round(newValue.size / 1024) <= allowedFileSize;
        hasFile.value = !isEmpty && validateFileSize.value;

        if (validateFileSize.value) {
            nextTick(() => {
                tabIndex.value++;
            });
        } else {
            nextTick(() => {
                tabIndex.value--;
            });
        }
    },
);
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-4 max-height-75"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        :click-to-close="!form.processing"
        :esc-to-close="!form.processing"
        modal-id="track-upload-modal"
    >
        <div class="container-fluid overflow-y-auto">
            <div class="w-100 text-center font-default fs-5 mb-2">
                {{ $t('modals.upload.track') }}
            </div>
            <BOverlay rounded="sm" :show="form.processing">
                <template #overlay>
                    <div class="d-flex flex-column w-100 text-center align-items-center justify-content-center gap-4">
                        <span class="font-default">{{ $td('misc.please_wait') }}</span>
                        <div class="d-flex flex-row gap-2 align-items-center">
                            <BSpinner variant="danger" />
                            <span class="font-default fw-bold fs-5"> {{ form.progress?.percentage }}% </span>
                        </div>
                    </div>
                </template>
                <BTabs
                    nav-wrapper-class="tabs-header w-100"
                    nav-class="tab-item px-4 d-flex"
                    active-nav-item-class="tab-item-active"
                    tab-class="py-3"
                    nav-item-class="tab-item default-text-color px-4"
                    content-class="overflow-x-hidden"
                    v-model="tabIndex"
                    fill
                >
                    <BTab :title="$t('modals.upload.select_file')">
                        <div class="d-flex flex-column w-100 justify-content-start align-items-start gap-1">
                            <BFormFile
                                v-model="form.file"
                                accept="audio/*"
                                :state="validateFileSize"
                                @update:model-value="setId3Data"
                            />
                        </div>
                    </BTab>
                    <BTab :title="$t('modals.upload.input_details')" :disabled="!hasFile">
                        <div class="row">
                            <div class="col-12 col-xl-4">
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
                                    <BFormFile
                                        v-model="form.artwork"
                                        accept="image/*"
                                        @update:model-value="setImagePreview"
                                    />
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
                                    <div class="d-flex flex-row justify-content-between align-items-start gap-3">
                                        <div
                                            class="d-flex flex-column w-50 justify-content-start align-items-start gap-1"
                                        >
                                            <label class="text-start font-default fs-14 default-text-color">
                                                {{ $t('misc.gender') }}
                                            </label>
                                            <BFormSelect v-model="form.vocal_id" :options="vocals" />
                                        </div>
                                        <div
                                            class="d-flex flex-column w-50 justify-content-start align-items-start gap-1"
                                        >
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
                                    <div class="d-flex flex-row justify-content-between align-items-end gap-3">
                                        <div
                                            class="d-flex flex-column w-50 justify-content-start align-items-start gap-1"
                                        >
                                            <label class="text-start font-default fs-14 default-text-color">
                                                {{ $t('misc.release_at') }}
                                            </label>
                                            <BFormInput v-model="form.released_at" type="date" :max="today" />
                                        </div>
                                        <div
                                            class="d-flex flex-column w-50 justify-content-start align-items-start gap-1"
                                        >
                                            <label class="text-start font-default fs-14 default-text-color">
                                                {{ $t('misc.schedule_publish') }}
                                            </label>
                                            <BFormInput v-model="form.published_at" type="date" :min="today" required />
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
                                                <BFormCheckbox v-model="form.allow_download" switch>
                                                    {{ $t('misc.allow_download') }}
                                                </BFormCheckbox>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex flex-column justify-content-start align-items-start">
                                                <BFormCheckbox v-model="form.is_explicit" switch>
                                                    {{ $t('misc.explicit') }}
                                                </BFormCheckbox>
                                                <BFormCheckbox v-model="form.notify" switch>
                                                    {{ $t('misc.notify_fans') }}
                                                </BFormCheckbox>
                                                <BFormCheckbox
                                                    v-if="user.has_active_subscription"
                                                    v-model="form.is_patron"
                                                    switch
                                                >
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
                        <div class="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                            <DefaultButton
                                class-list="btn-pink"
                                @click="upload"
                                :disabled="form.processing || !hasFile"
                            >
                                <Icon :icon="['fas', 'floppy-disk']" />
                                {{ $t('buttons.upload.default') }}
                            </DefaultButton>
                            <DefaultButton class-list="btn-outline" :disabled="form.processing" @click="close">
                                <Icon :icon="['fas', 'trash']" />
                                {{ $t('buttons.cancel') }}
                            </DefaultButton>
                        </div>
                    </BTab>
                </BTabs>
            </BOverlay>
        </div>
    </VueFinalModal>
</template>
