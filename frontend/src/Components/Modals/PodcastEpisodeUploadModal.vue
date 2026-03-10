<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t, $td } from '@/i18n.js';
import {
    BFormCheckbox,
    BFormInput,
    BFormSelect,
    BFormTextarea,
    BFormFile,
    BOverlay,
    BSpinner,
    BTabs,
    BTab,
} from 'bootstrap-vue-next';
import { computed, defineAsyncComponent, ref, watch, reactive } from 'vue';
import { useForm } from '@/helpers/useForm'
import apiClient from '@/api/client'
import { useAuthStore } from '@/stores/auth'
import { parseBlob } from 'music-metadata';
import { songAssetImage } from '@/Services/AssetService.js';
import { removeEmptyObjectsKeys } from '@/Services/FormService.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));
import route from "@/helpers/route"

const emit = defineEmits(['close']);

const authStore = useAuthStore();
const user = authStore.user;

const allowedFileSize = 92970;
const maxAllowed = 60;

const titleLength = ref(0);
const inputAllowed = ref(true);
const seasonAdded = ref(false);

const tabIndex = ref(0);

const hasFile = ref(false);
const podcastDataSelected = ref(false);
const validateFileSize = ref(false);
const artworkPreview = ref(songAssetImage);
const defaultArtworkPreview = ref(songAssetImage);
const seasonOptions = ref([]);
const episodeNumberOptions = ref([]);

const form = useForm({
    title: null,
    season: null,
    number: null,
    is_visible: false,
    allow_comments: false,
    allow_download: false,
    explicit: false,
    description: null,
    file: null,
    artwork: null,
    podcast_uuid: null,
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
    form.transform(removeEmptyObjectsKeys).post(route('upload.episodes.store'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => close(),
    });
};

const close = () => {
    form.reset();
    emit('close');
};

const proceed = () => {
    tabIndex.value = 1;
};

const addSeason = () => {
    if (form.podcast_uuid) {
        const podcast = user.podcasts.find((podcast) => podcast.uuid === form.podcast_uuid);

        if (!podcast) {
            return;
        }

        seasonOptions.value = Array.from({ length: podcast.seasons + 1 }, (_, i) => ({
            value: i + 1,
            text: `Season ${i + 1}`,
        }));

        episodeNumberOptions.value = Array.from({ length: 1 }, (_, i) => ({
            value: i + 1,
            text: `Episode ${i + 1}`,
        }));

        form.season = podcast.seasons + 1;
        form.number = 0;

        seasonAdded.value = true;
    }
};

const validatePodcastSelection = () =>
    (podcastDataSelected.value = !!form.podcast_uuid && !!form.season && !!form.number);

const podcasts = computed(() => {
    if (user.podcasts.length) {
        return user.podcasts.map((podcast) => {
            return {
                value: podcast.uuid,
                text: podcast.title,
            };
        });
    }

    return [];
});

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
    },
);

watch(
    () => form.podcast_uuid,
    (newValue) => {
        if (!newValue) {
            return;
        }

        const podcast = user.podcasts.find((podcast) => podcast.uuid === newValue);

        if (!podcast) {
            return;
        }

        seasonOptions.value = Array.from({ length: podcast.seasons }, (_, i) => ({
            value: i + 1,
            text: `Season ${i + 1}`,
        }));

        seasonAdded.value = false;
        form.number = null;
        form.season = null;

        validatePodcastSelection();
    },
);

watch(
    () => form.season,
    (newValue) => {
        const podcast = user.podcasts.find((podcast) => podcast.uuid === form.podcast_uuid);

        if (!podcast) {
            return;
        }

        const season = podcast.details.find((season) => season.season === newValue);

        if (!season) {
            episodeNumberOptions.value = Array.from({ length: 1 }, (_, i) => ({
                value: i + 1,
                text: $t('modals.upload.podcast.season', { index: i + 1 }),
            }));
        } else {
            episodeNumberOptions.value = Array.from({ length: season.episodes + 1 }, (_, i) => ({
                value: i + 1,
                text: $t('modals.upload.podcast.episode', { index: i + 1 }),
                disabled: i < season.episodes,
            }));
        }

        form.number = 0;

        validatePodcastSelection();
    },
);

watch(
    () => form.number,
    (newValue) => {
        if (!newValue) {
            return;
        }

        validatePodcastSelection();
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
        modal-id="podcast-upload-modal"
    >
        <div class="container-fluid overflow-y-auto">
            <div class="w-100 text-center font-default fs-5 mb-2">
                {{ $t('modals.upload.podcast.title') }}
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
                    <BTab :title="$t('modals.upload.podcast.select')" :disabled="tabIndex === 1">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label class="text-start font-default fs-14 default-text-color">
                                {{ $t('misc.podcast') }}
                            </label>
                            <BFormSelect v-model="form.podcast_uuid" :options="podcasts" />
                        </div>
                        <div class="row mt-4">
                            <div class="col-6 d-flex flex-column justify-content-end align-items-start gap-1">
                                <label
                                    class="text-start font-default fs-14 default-text-color d-flex flex-row align-items-center"
                                >
                                    <span>{{ $t('misc.season') }}</span>
                                    <IconButton
                                        icon="plus"
                                        :disabled="!form.podcast_uuid || seasonAdded"
                                        @click="addSeason"
                                        class-list="btn-pink btn-sm"
                                    />
                                </label>
                                <BFormSelect
                                    v-model="form.season"
                                    :options="seasonOptions"
                                    :disabled="!form.podcast_uuid"
                                    required
                                />
                            </div>
                            <div class="col-6 d-flex flex-column justify-content-end align-items-start gap-1">
                                <label class="text-start font-default fs-14 default-text-color">
                                    {{ $t('misc.episode_number') }}
                                </label>
                                <BFormSelect
                                    v-model="form.number"
                                    :options="episodeNumberOptions"
                                    :disabled="!form.podcast_uuid"
                                    required
                                />
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                            <DefaultButton
                                class-list="btn-pink"
                                @click="proceed"
                                :disabled="form.processing || !podcastDataSelected"
                            >
                                <Icon :icon="['fas', 'angles-right']" />
                                {{ $t('buttons.proceed') }}
                            </DefaultButton>
                            <DefaultButton class-list="btn-outline" :disabled="form.processing" @click="close">
                                <Icon :icon="['fas', 'trash']" />
                                {{ $t('buttons.cancel') }}
                            </DefaultButton>
                        </div>
                    </BTab>
                    <BTab :title="$t('modals.upload.input_details')" :disabled="!podcastDataSelected">
                        <div class="d-flex flex-column w-100 gap-4">
                            <div class="d-flex flex-column justify-content-center align-items-start gap-1 w-100">
                                <label class="text-center font-default fs-14 default-text-color">
                                    {{ $t('misc.select_file') }}
                                </label>
                                <BFormFile
                                    v-model="form.file"
                                    accept="audio/*"
                                    :state="validateFileSize"
                                    @update:model-value="setId3Data"
                                />
                            </div>
                            <div class="row">
                                <div class="col-12 col-xl-4 d-flex flex-column justify-content-start gap-4">
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
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="d-flex flex-column justify-content-start align-items-start">
                                                    <BFormCheckbox v-model="form.is_visible" switch>
                                                        {{ $t('misc.make_public') }}
                                                    </BFormCheckbox>
                                                    <BFormCheckbox v-model="form.explicit" switch>
                                                        {{ $t('misc.explicit') }}
                                                    </BFormCheckbox>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex flex-column justify-content-start align-items-start">
                                                    <BFormCheckbox v-model="form.allow_comments" switch>
                                                        {{ $t('misc.allow_comments') }}
                                                    </BFormCheckbox>
                                                    <BFormCheckbox v-model="form.allow_download" switch>
                                                        {{ $t('misc.allow_download') }}
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
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-row justify-content-center align-items-center w-100 gap-4">
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
                        </div>
                    </BTab>
                </BTabs>
            </BOverlay>
        </div>
    </VueFinalModal>
</template>
