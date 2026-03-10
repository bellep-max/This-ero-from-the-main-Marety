<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t, $td } from '@/i18n.js';
import {
    BCollapse,
    BFormCheckbox,
    BFormFile,
    BFormInput,
    BFormSelect,
    BFormTextarea,
    BOverlay,
    BSpinner,
    BTab,
    BTabs,
} from 'bootstrap-vue-next';
import { computed, defineAsyncComponent, nextTick, reactive, ref, watch } from 'vue';
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client';
import { parseBlob } from 'music-metadata';
import { songAssetImage } from '@/Services/AssetService.js';
import { slugify } from '@/Services/FormService.js';
import AdventureSongTypes from '@/Enums/AdventureSongTypes.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const emit = defineEmits(['close']);

const authStore = useAuthStore();
const user = authStore.user;

const allowedFileSize = 92970;
const maxAllowed = 60;

const errors = reactive({});
const touched = reactive({});

const validationRules = {
    heading: {
        file: (value) => (value && value.size < allowedFileSize ? 'File is required' : null), // File validation would be more complex (type, size, etc.)
        artwork: () => null,
        title: (value) => (value ? null : 'Heading title is required.'),
        description: (value) => (value && value.length > 500 ? 'Description cannot exceed 500 characters.' : null),
        genres: (value) => (Array.isArray(value) && value.length > 0 ? null : 'At least one genre is required.'),
        tags: (value) =>
            Array.isArray(value) && value.length > 0 ? null : 'At least one tag is required for heading.',
    },
    roots: {
        file: (value) => (value && value.size < allowedFileSize ? 'File is required' : null),
        artwork: () => null,
        title: (value) => (value ? null : 'Root title is required.'),
        description: (value) =>
            value && value.length > 500 ? 'Description cannot exceed 500 characters for root.' : null,
        order: (value) => (value && value >= 1 ? null : 'Final order is required and must be at least 1.'),
    },
    finals: {
        file: (value) => (value && value.size < allowedFileSize ? 'File is required' : null),
        artwork: () => null,
        title: (value) => (value ? null : 'Final title is required.'),
        description: (value) =>
            value && value.length > 500 ? 'Description cannot exceed 500 characters for final.' : null,
        order: (value) => (value && value >= 1 ? null : 'Final order is required and must be at least 1.'),
        root_id: (value) =>
            value && roots.value.includes(value) ? null : 'Root order is required and must be a valid root',
    },
};

const inputAllowed = ref(true);
const isLoading = ref(false);
const parentUuid = ref(null);
const loadingMessage = ref(null);

const dataLayout = ref({
    inputAllowed: true,
    titleLength: 0,
    artworkPreview: songAssetImage,
    validateFileSize: false,
});

const headingData = ref({
    inputAllowed: true,
    titleLength: 0,
    artworkPreview: songAssetImage,
    validateFileSize: false,
});

const rootData = ref([
    {
        inputAllowed: true,
        titleLength: 0,
        artworkPreview: songAssetImage,
        validateFileSize: false,
        finals: [
            {
                inputAllowed: true,
                titleLength: 0,
                artworkPreview: songAssetImage,
                validateFileSize: false,
            },
        ],
    },
]);

const tabIndex = ref(0);

const genres = ref(authStore.filter_presets.genres);
const tags = ref(authStore.filter_presets.tags);

const roots = ref([1, 2, 3, 4, 5]);

const finalRoots = ref([1, 2, 3]);

const headingForm = ref({
    type: AdventureSongTypes.Heading,
    title: null,
    tags: [],
    genres: null,
    is_visible: false,
    allow_comments: false,
    description: null,
    file: null,
    artwork: null,
    roots: 1,
});

const rootsForm = ref([
    {
        is_uploaded: false,
        parent_uuid: parentUuid.value,
        title: null,
        description: null,
        file: null,
        artwork: null,
        order: 1,
        finals_number: 1,
        finals: [
            {
                parent_uuid: null,
                title: null,
                description: null,
                file: null,
                artwork: null,
                order: 1,
            },
        ],
    },
]);

const markTouched = (path) => {
    touched[path] = true;
};

const validateField = (path, value, dataContext) => {
    const parts = path.split('.');
    const topLevel = parts[0];
    let rule = null;
    let fieldName = null;
    let validationCategory = null;

    if (topLevel === AdventureSongTypes.Heading && parts.length === 2) {
        validationCategory = AdventureSongTypes.Heading;
        fieldName = parts[1];
    } else if (topLevel === AdventureSongTypes.Root) {
        if (parts.length === 3) {
            // e.g., 'roots.0.title' or 'roots.0.tags'
            validationCategory = AdventureSongTypes.Root;
            fieldName = parts[2];
        } else if (parts.length === 5 && parts[2] === AdventureSongTypes.Final) {
            // e.g., 'roots.0.finals.0.title'
            validationCategory = AdventureSongTypes.Final;
            fieldName = parts[4];
        }
    }

    if (validationCategory && validationRules[validationCategory] && validationRules[validationCategory][fieldName]) {
        rule = validationRules[validationCategory][fieldName];
    }

    if (rule) {
        errors[path] = rule(value, dataContext);
    } else {
        // console.warn(`No validation rule found for path: ${path}`);
    }
};

const validateHeadingForm = (headingData) => {
    let isValid = true;
    // Clear only heading-related errors
    for (const key in errors) {
        if (key.startsWith(`${AdventureSongTypes.Heading}.`)) {
            delete errors[key];
        }
    }

    for (const key in headingData) {
        const path = `${AdventureSongTypes.Heading}.${key}`;
        validateField(path, headingData[key], headingData);
        if (errors[path]) isValid = false;
    }
    return isValid;
};

const validateRootsForm = (rootsData) => {
    let isValid = true;
    // Clear only roots/finals-related errors
    for (const key in errors) {
        if (key.startsWith(`${AdventureSongTypes.Root}.`)) {
            delete errors[key];
        }
    }
    if (errors['roots']) {
        // Clear the general roots error if present
        delete errors['roots'];
    }

    if (rootsData.length === 0) {
        errors['roots'] = 'At least one root is required.';
        isValid = false;
    } else {
        rootsData.forEach((root, rootIndex) => {
            for (const key in root) {
                const value = root[key];
                const path = `${AdventureSongTypes.Root}.${rootIndex}.${key}`;

                if (key === 'finals' && Array.isArray(value)) {
                    // Validate finals array (e.g., if empty when required by root)
                    // You might add a rule here if 'finals' itself needs validation (e.g., minimum finals)
                    if (root.finals_number > 0 && value.length === 0) {
                        errors[path] = 'At least one final is required based on finals_number.';
                        isValid = false;
                    }

                    // Iterate through individual final items
                    value.forEach((finalItem, finalIndex) => {
                        for (const finalKey in finalItem) {
                            const finalPath = `${AdventureSongTypes.Root}.${rootIndex}.finals.${finalIndex}.${finalKey}`;
                            validateField(finalPath, finalItem[finalKey], finalItem, errors);
                            if (errors[finalPath]) isValid = false;
                        }
                    });
                } else {
                    // Validate other top-level root fields (e.g., 'title', 'finals_number')
                    validateField(path, value, root, errors);
                    if (errors[path]) isValid = false;
                }
            }
        });
    }
    return isValid;
};

const setImagePreview = (rootIndex = null, finalIndex = null) => {
    const targetData = getTargetData(rootIndex, finalIndex);
    const targetForm = getTargetForm(rootIndex, finalIndex);

    targetData.artworkPreview = targetForm.artwork ? URL.createObjectURL(targetForm.artwork) : songAssetImage;
};

const generateForms = (rootIndex = null) => {
    if (rootIndex === null) {
        rootsForm.value = [];
        rootData.value = [];

        for (let i = 0; i < headingForm.value.roots; i++) {
            rootsForm.value.push(setFormData(i));

            rootData.value.push({
                ...dataLayout.value,
                finals: [dataLayout.value],
            });
        }
    } else {
        rootsForm.value[rootIndex].finals = [];
        rootData.value[rootIndex].finals = [];

        for (let i = 0; i < rootsForm.value[rootIndex].finals_number; i++) {
            rootsForm.value[rootIndex].finals.push(setFormData(i, rootIndex));

            rootData.value[rootIndex].finals.push(dataLayout.value);
        }
    }
};

const setFormData = (index, nestedIndex = null) => {
    return nestedIndex === null
        ? {
              parent_uuid: null,
              is_uploaded: false,
              title: null,
              description: null,
              file: null,
              artwork: null,
              order: index + 1,
              finals_number: 1,
              finals: [
                  {
                      title: null,
                      description: null,
                      file: null,
                      artwork: null,
                      order: 1,
                  },
              ],
          }
        : {
              title: null,
              description: null,
              file: null,
              artwork: null,
              order: index + 1,
          };
};

const uploadHeading = () => {
    const isValid = validateHeadingForm(headingForm.value);
    if (!isValid) {
        // console.log('Heading form has errors:', errors);
        return;
    }

    isLoading.value = true;
    loadingMessage.value = $td('modals.upload.adventure.uploading_heading');

    axios
        .post(route('upload.adventures.heading.store'), headingForm.value, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
        .then((response) => {
            parentUuid.value = response.data.uuid;
            loadingMessage.value = null;
            isLoading.value = false;

            nextTick(() => {
                tabIndex.value++;
            });
        })
        .catch(() => {
            isLoading.value = false;
            loadingMessage.value = null;
        });
};

const uploadRoot = (index) => {
    const isValid = validateRootsForm(rootsForm.value);
    if (!isValid) {
        // console.log('Roots form has errors:', errors);
        return;
    }

    isLoading.value = true;
    loadingMessage.value = $td('modals.upload.adventure.uploading_root', {
        index: index + 1,
    });

    axios
        .post(route('upload.adventures.root.store'), rootsForm.value[index], {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
        .then(() => {
            isLoading.value = false;
            loadingMessage.value = null;
            rootsForm.value[index].is_uploaded = true;

            nextTick(() => {
                tabIndex.value++;

                if (validateRootsUploaded()) {
                    vueRouter.push(route('adventures.show', parentUuid.value));
                }
            });
        })
        .catch(() => {
            isLoading.value = false;
            loadingMessage.value = null;
        });
};

const validateRootsUploaded = () => {
    let check = true;
    for (const root of rootsForm.value) {
        if (!root.is_uploaded) {
            check = false;
            break;
        }
    }

    return check;
};

const addTag = (tag, rootIndex = null, finalIndex = null) => {
    const newTag = {
        id: -Math.floor(Math.random() * 10000000),
        tag: slugify(tag),
    };

    tags.value.push(newTag);

    const targetForm = getTargetForm(rootIndex, finalIndex);
    targetForm.tags.push(newTag);
};

const setId3Data = (file, rootIndex = null, finalIndex = null) => {
    if (!file) {
        return;
    }

    parseBlob(file).then((tags) => {
        const title = tags.common.title ? tags.common.title : file.name;

        const targetData = getTargetData(rootIndex, finalIndex);
        const targetForm = getTargetForm(rootIndex, finalIndex);

        targetForm.title = title;
        targetData.titleLength = title.length;

        if (tags.common.picture?.length) {
            const blob = new Blob([tags.common.picture[0].data], {
                type: tags.common.picture[0].format,
            });

            targetData.artworkPreview = URL.createObjectURL(blob);
        } else {
            targetData.artworkPreview = songAssetImage;
        }
    });
};

const processAudioFile = (file, rootIndex = null, finalIndex = null) => {
    if (!file || !file.size) {
        return;
    }

    const targetData = getTargetData(rootIndex, finalIndex);

    targetData.validateFileSize = Math.round(file.size / 1024) <= allowedFileSize;

    setId3Data(file, rootIndex, finalIndex);
};

const close = () => {
    headingForm.value;
    rootsForm.value;
    emit('close');

    nextTick(() => {
        tabIndex.value = 0;
    });
};

const cancel = () => {
    if (hasUploadedHeading.value) {
        axios
            .post(route('upload.adventure.destroy'), {
                uuid: parentUuid.value,
            })
            .then(() => {
                close();
            })
            .catch((error) => {
                console.error('Error cancelling upload:', error);
            });
    } else {
        close();
    }

    return null;
};

const checkLength = (rootIndex = null, finalIndex = null) => {
    const targetData = getTargetData(rootIndex, finalIndex);
    const targetForm = getTargetForm(rootIndex, finalIndex);

    targetData.titleLength = targetForm.title?.length ?? 0;
    targetData.inputAllowed = targetForm.title?.length < maxAllowed;
};

const getTargetData = (rootIndex = null, finalIndex = null) => {
    if (rootIndex !== null && finalIndex !== null) {
        return rootData.value[rootIndex].finals[finalIndex];
    } else if (rootIndex !== null) {
        return rootData.value[rootIndex];
    }

    return headingData.value;
};

const getTargetForm = (rootIndex = null, finalIndex = null) => {
    if (rootIndex !== null && finalIndex !== null) {
        return rootsForm.value[rootIndex].finals[finalIndex];
    } else if (rootIndex !== null && finalIndex === null) {
        return rootsForm.value[rootIndex];
    } else {
        return headingForm.value;
    }
};

const hasUploadedHeading = computed(() => !!parentUuid.value);

watch(
    () => parentUuid.value,
    (newValue) => {
        rootsForm.value.forEach((root) => {
            root.parent_uuid = newValue;
        });
    },
);
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-4 max-height-75"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        :click-to-close="!isLoading && !hasUploadedHeading"
        :esc-to-close="!isLoading && !hasUploadedHeading"
        modal-id="track-upload-modal"
    >
        <div class="container-fluid overflow-y-auto">
            <div class="w-100 text-center font-default fs-5 mb-2">
                {{ $t('modals.upload.adventure.title') }}
            </div>
            <BOverlay rounded="sm" :show="isLoading">
                <template #overlay>
                    <div class="d-flex flex-column w-100 text-center align-items-center justify-content-center gap-4">
                        <span class="font-default">{{ $td('misc.please_wait') }}</span>
                        <span>{{ loadingMessage }}</span>
                        <div class="d-flex flex-row gap-2 align-items-center">
                            <BSpinner variant="danger" />
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
                    <BTab :title="$t('modals.upload.adventure.heading')" :disabled="hasUploadedHeading">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="d-flex flex-column justify-content-center align-items-start gap-1">
                                    <label class="text-center font-default fs-14 default-text-color">
                                        {{ $t('modals.upload.select_file') }}
                                    </label>
                                    <BFormFile
                                        v-model="headingForm.file"
                                        accept="audio/*"
                                        :state="headingData.validateFileSize"
                                        @update:model-value="processAudioFile($event)"
                                    />
                                </div>
                            </div>
                            <div class="col-12 col-xl-4">
                                <div class="d-flex flex-column justify-content-start w-100 gap-4">
                                    <div class="d-flex flex-column justify-content-center align-items-start gap-1">
                                        <label class="text-center font-default fs-14 default-text-color">
                                            {{ $t('misc.artwork') }}
                                        </label>
                                        <img
                                            :src="headingData.artworkPreview"
                                            class="img-fluid rounded-4 border-pink"
                                            alt="artwork"
                                            ref="imagePreview"
                                        />
                                    </div>
                                    <BFormFile
                                        v-model="headingForm.artwork"
                                        accept="image/*"
                                        @update:model-value="setImagePreview()"
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
                                            v-model="headingForm.title"
                                            type="text"
                                            :state="headingData.inputAllowed"
                                            :max="maxAllowed"
                                            @blur="markTouched('heading.title')"
                                            @update:model-value="checkLength()"
                                        />
                                        <span
                                            class="fs-12 font-merge color-grey"
                                            :class="{
                                                'color-pink': !headingData.inputAllowed,
                                            }"
                                        >
                                            {{
                                                $t('modals.upload.characters_allowed', {
                                                    max: maxAllowed,
                                                    used: headingData.titleLength,
                                                })
                                            }}
                                        </span>
                                        <p v-if="errors[`heading.title`]" class="text-red-500 text-sm">
                                            {{ errors[`heading.title`] }}
                                        </p>
                                    </div>
                                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                        <label class="text-start font-default fs-14 default-text-color">
                                            {{ $t('misc.tags') }}
                                        </label>
                                        <vue-multiselect
                                            v-model="headingForm.tags"
                                            :options="tags"
                                            :multiple="true"
                                            :taggable="true"
                                            track-by="id"
                                            label="tag"
                                            @tag="addTag($event)"
                                            @blur="markTouched('heading.tags')"
                                        />
                                        <p v-if="errors[`heading.tags`]" class="text-red-500 text-sm">
                                            {{ errors[`heading.tags`] }}
                                        </p>
                                    </div>
                                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                        <label class="text-start font-default fs-14 default-text-color">
                                            {{ $t('misc.genres') }}
                                        </label>
                                        <vue-multiselect
                                            v-model="headingForm.genres"
                                            :options="genres"
                                            :multiple="true"
                                            track-by="value"
                                            label="text"
                                            @blur="markTouched('heading.genres')"
                                        />
                                        <p v-if="errors[`heading.genres`]" class="text-red-500 text-sm">
                                            {{ errors[`heading.genres`] }}
                                        </p>
                                    </div>
                                    <div class="d-flex flex-row justify-content-between align-items-end gap-3">
                                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                            <label class="text-start font-default fs-14 default-text-color">
                                                {{ $t('misc.roots') }}
                                            </label>
                                            <BFormSelect
                                                v-model="headingForm.roots"
                                                :options="roots"
                                                @update:model-value="generateForms()"
                                                @blur="markTouched('heading.roots')"
                                            />
                                            <p v-if="errors[`heading.roots`]" class="text-red-500 text-sm">
                                                {{ errors[`heading.roots`] }}
                                            </p>
                                        </div>
                                        <div class="d-flex flex-column justify-content-start align-items-center gap-3">
                                            <label class="text-start font-default fs-14 default-text-color">
                                                {{ $t('misc.misc') }}
                                            </label>
                                            <div
                                                class="d-flex flex-row justify-content-between align-items-center gap-3"
                                            >
                                                <BFormCheckbox v-model="headingForm.is_visible" switch>
                                                    {{ $t('misc.make_public') }}
                                                </BFormCheckbox>
                                                <BFormCheckbox v-model="headingForm.allow_comments" switch>
                                                    {{ $t('misc.allow_comments') }}
                                                </BFormCheckbox>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                        <label class="text-start font-default fs-14 default-text-color">
                                            {{ $t('misc.description') }}
                                        </label>
                                        <BFormTextarea
                                            v-model="headingForm.description"
                                            @blur="markTouched('heading.description')"
                                        />
                                        <p v-if="errors[`heading.description`]" class="text-red-500 text-sm">
                                            {{ errors[`heading.description`] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                            <DefaultButton
                                class-list="btn-pink"
                                :disabled="isLoading || !headingForm.file"
                                @click="uploadHeading()"
                            >
                                <Icon :icon="['fas', 'angles-right']" />
                                {{ $t('buttons.upload_heading_proceed') }}
                            </DefaultButton>
                            <DefaultButton class-list="btn-outline" :disabled="isLoading" @click="close">
                                <Icon :icon="['fas', 'trash']" />
                                {{ $t('buttons.cancel') }}
                            </DefaultButton>
                        </div>
                    </BTab>
                    <BTab
                        v-for="(root, rootIndex) in rootsForm"
                        :key="rootIndex"
                        :title="$t('modals.upload.adventure.root', { index: rootIndex + 1 })"
                        :disabled="!hasUploadedHeading || root.is_uploaded"
                    >
                        <div class="row py-2 px-3 gap-3">
                            <BFormFile
                                v-model="root.file"
                                accept="audio/*"
                                :state="rootData[rootIndex].validateFileSize"
                                @update:model-value="processAudioFile($event, rootIndex)"
                                @blur="markTouched(`roots[${rootIndex}].file`)"
                            />
                            <p v-if="errors[`roots[${rootIndex}].file`]" class="text-red-500 text-sm">
                                {{ errors[`roots[${rootIndex}].file`] }}
                            </p>
                            <div class="col-12 col-xl-4">
                                <div class="d-flex flex-column justify-content-start w-100 gap-4">
                                    <div class="d-flex flex-column justify-content-center align-items-start gap-1">
                                        <label class="text-center font-default fs-14 default-text-color">
                                            {{ $t('misc.artwork') }}
                                        </label>
                                        <img
                                            :src="rootData[rootIndex].artworkPreview"
                                            class="img-fluid rounded-4 border-pink"
                                            alt="artwork"
                                        />
                                    </div>
                                    <BFormFile
                                        v-model="root.artwork"
                                        accept="image/*"
                                        @update:model-value="setImagePreview(rootIndex)"
                                        @blur="markTouched(`roots[${rootIndex}].artwork`)"
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
                                            v-model="root.title"
                                            type="text"
                                            :state="rootData[rootIndex].inputAllowed"
                                            :max="maxAllowed"
                                            @update:model-value="checkLength(rootIndex)"
                                            @blur="markTouched(`roots[${rootIndex}].title`)"
                                        />
                                        <span
                                            class="fs-12 font-merge color-grey"
                                            :class="{
                                                'color-pink': !rootData[rootIndex].inputAllowed,
                                            }"
                                        >
                                            {{
                                                $t('modals.upload.characters_allowed', {
                                                    max: maxAllowed,
                                                    used: rootData[rootIndex].titleLength,
                                                })
                                            }}
                                        </span>
                                        <p v-if="errors[`roots[${rootIndex}].title`]" class="text-red-500 text-sm">
                                            {{ errors[`roots[${rootIndex}].title`] }}
                                        </p>
                                    </div>
                                    <div class="d-flex flex-row justify-content-between align-items-start gap-3">
                                        <div
                                            class="d-flex flex-column w-100 justify-content-start align-items-start gap-1"
                                        >
                                            <label class="text-start font-default fs-14 default-text-color">
                                                {{ $t('misc.finals') }}
                                            </label>
                                            <BFormSelect
                                                v-model="root.finals_number"
                                                :options="finalRoots"
                                                @update:model-value="generateForms(rootIndex)"
                                                @blur="markTouched(`roots[${rootIndex}].finals_number`)"
                                            />
                                            <p
                                                v-if="errors[`roots[${rootIndex}].finals_number`]"
                                                class="text-red-500 text-sm"
                                            >
                                                {{ errors[`roots[${rootIndex}].finals_number`] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                        <label class="text-start font-default fs-14 default-text-color">
                                            {{ $t('misc.description') }}
                                        </label>
                                        <BFormTextarea
                                            v-model="root.description"
                                            @blur="markTouched(`roots[${rootIndex}].description`)"
                                        />
                                        <p
                                            v-if="errors[`roots[${rootIndex}].description`]"
                                            class="text-red-500 text-sm"
                                        >
                                            {{ errors[`roots[${rootIndex}].description`] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row px-4">
                            <BCollapse v-for="(final, finalIndex) in rootsForm[rootIndex].finals" :key="finalIndex">
                                <template #header="{ visible, toggle, id }">
                                    <DefaultButton
                                        :class-list="{
                                            'my-2 w-100': true,
                                            'btn-outline': !visible,
                                            'btn-pink': visible,
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
                                <div class="row py-2 px-3 gap-3">
                                    <BFormFile
                                        v-model="final.file"
                                        accept="audio/*"
                                        :state="rootData[rootIndex].finals[finalIndex].validateFileSize"
                                        @update:model-value="processAudioFile($event, rootIndex, finalIndex)"
                                        @blur="markTouched(`roots[${rootIndex}].finals[${finalIndex}].file`)"
                                    />
                                    <p
                                        v-if="errors[`roots[${rootIndex}].finals[${finalIndex}].file`]"
                                        class="text-red-500 text-sm"
                                    >
                                        {{ errors[`roots[${rootIndex}].finals[${finalIndex}].file`] }}
                                    </p>
                                    <div class="col-12 col-xl-4">
                                        <div class="d-flex flex-column justify-content-start w-100 gap-4">
                                            <div
                                                class="d-flex flex-column justify-content-center align-items-start gap-1"
                                            >
                                                <label class="text-center font-default fs-14 default-text-color">
                                                    {{ $t('misc.artwork') }}
                                                </label>
                                                <img
                                                    :src="rootData[rootIndex].finals[finalIndex].artworkPreview"
                                                    class="img-fluid rounded-4 border-pink"
                                                    alt="artwork"
                                                />
                                            </div>
                                            <BFormFile
                                                v-model="final.artwork"
                                                accept="image/*"
                                                @update:model-value="setImagePreview(rootIndex, finalIndex)"
                                                @blur="markTouched(`roots[${rootIndex}].finals[${finalIndex}].artwork`)"
                                            />
                                            <div
                                                class="d-flex flex-column fw-light text-secondary text-center font-merge"
                                            >
                                                <span>{{ $t('modals.upload.image_description.line_1') }}</span>
                                                <span>{{ $t('modals.upload.image_description.line_2') }}</span>
                                                <span>{{ $t('modals.upload.image_description.line_3') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column justify-content-start w-100 gap-4">
                                            <div
                                                class="d-flex flex-column justify-content-start align-items-start gap-1"
                                            >
                                                <label class="text-start font-default fs-14 default-text-color">
                                                    {{ $t('misc.title') }}
                                                </label>
                                                <BFormInput
                                                    v-model="final.title"
                                                    type="text"
                                                    :state="rootData[rootIndex].finals[finalIndex].inputAllowed"
                                                    :max="maxAllowed"
                                                    @update:model-value="checkLength(rootIndex, finalIndex)"
                                                    @blur="
                                                        markTouched(`roots[${rootIndex}].finals[${finalIndex}].title`)
                                                    "
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
                                                            used: rootData[rootIndex].finals[finalIndex].titleLength,
                                                        })
                                                    }}
                                                </span>
                                                <p
                                                    v-if="errors[`roots[${rootIndex}].finals[${finalIndex}].title`]"
                                                    class="text-red-500 text-sm"
                                                >
                                                    {{ errors[`roots[${rootIndex}].finals[${finalIndex}].title`] }}
                                                </p>
                                            </div>
                                            <div
                                                class="d-flex flex-column justify-content-start align-items-start gap-1"
                                            >
                                                <label class="text-start font-default fs-14 default-text-color">
                                                    {{ $t('misc.description') }}
                                                </label>
                                                <BFormTextarea
                                                    v-model="final.description"
                                                    @blur="
                                                        markTouched(
                                                            `roots[${rootIndex}].finals[${finalIndex}].description`,
                                                        )
                                                    "
                                                />
                                                <p
                                                    v-if="
                                                        errors[`roots[${rootIndex}].finals[${finalIndex}].description`]
                                                    "
                                                    class="text-red-500 text-sm"
                                                >
                                                    {{
                                                        errors[`roots[${rootIndex}].finals[${finalIndex}].description`]
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </BCollapse>
                        </div>
                        <div class="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                            <DefaultButton
                                class-list="btn-pink w-100"
                                :disabled="isLoading || !rootsForm[rootIndex].file || root.is_uploaded"
                                @click="uploadRoot(rootIndex)"
                            >
                                <Icon :icon="['fas', 'angles-right']" />
                                {{ $t('buttons.upload_root_finals', { index: rootIndex + 1 }) }}
                            </DefaultButton>
                        </div>
                        <!--            <div class="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">-->
                        <!--              <DefaultButton-->
                        <!--                  class-list="btn-pink"-->
                        <!--                  @click="upload"-->
                        <!--                  :disabled="isLoading"-->
                        <!--              >-->
                        <!--                <Icon :icon="['fas', 'floppy-disk']"/>-->
                        <!--                {{ $t('buttons.upload.default') }}-->
                        <!--              </DefaultButton>-->
                        <!--              <DefaultButton-->
                        <!--                  class-list="btn-outline"-->
                        <!--                  :disabled="isLoading"-->
                        <!--                  @click="cancel"-->
                        <!--              >-->
                        <!--                <Icon :icon="['fas', 'trash']"/>-->
                        <!--                {{ $t('buttons.cancel') }}-->
                        <!--              </DefaultButton>-->
                        <!--            </div>-->
                    </BTab>
                </BTabs>
            </BOverlay>
        </div>
    </VueFinalModal>
</template>

<style scoped>
.text-red-500 {
    --tw-text-opacity: 1;
    color: rgb(239 68 68 / var(--tw-text-opacity, 1)) /* #ef4444 */;
}
</style>
