<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t, $td } from '@/i18n.js';
import { BFormCheckbox, BFormInput, BFormTextarea, BFormFile, BOverlay, BSpinner } from 'bootstrap-vue-next';
import { defineAsyncComponent, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { songAssetImage } from '@/Services/AssetService.js';
import { removeEmptyObjectsKeys, slugify } from '@/Services/FormService.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const emit = defineEmits(['close']);

const page = usePage();

const maxAllowed = 60;

const titleLength = ref(0);
const inputAllowed = ref(true);

const tags = ref(page.props.filter_presets.tags);
const categories = ref(page.props.filter_presets.podcast_categories);
const artworkPreview = ref(songAssetImage);
const defaultArtworkPreview = ref(songAssetImage);

const form = useForm({
    title: null,
    tags: [],
    categories: [],
    is_visible: false,
    allow_comments: false,
    allow_download: false,
    explicit: false,
    description: null,
    artwork: null,
    language_id: null,
});

const setImagePreview = () => {
    artworkPreview.value = form.artwork ? URL.createObjectURL(form.artwork) : defaultArtworkPreview.value;
};

const create = () => {
    form.transform(removeEmptyObjectsKeys).post(route('podcasts.store'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => emit('close'),
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
};

watch(
    () => form.title,
    (newValue) => {
        titleLength.value = newValue?.length ?? 0;
        inputAllowed.value = newValue?.length < maxAllowed;
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
        modal-id="podcast-create-modal"
    >
        <div class="container-fluid overflow-y-auto">
            <div class="w-100 text-center font-default fs-5 mb-2">
                {{ $t('modals.create_podcast') }}
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
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label class="text-start font-default fs-14 default-text-color">
                                    {{ $t('misc.categories') }}
                                </label>
                                <vue-multiselect
                                    v-model="form.categories"
                                    :options="categories"
                                    :multiple="true"
                                    track-by="id"
                                    label="name"
                                />
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
                <div class="d-flex flex-row justify-content-center align-items-center w-100 gap-4 mt-4">
                    <DefaultButton class-list="btn-pink" @click="create" :disabled="form.processing">
                        <Icon :icon="['fas', 'floppy-disk']" />
                        {{ $t('buttons.create.title') }}
                    </DefaultButton>
                    <DefaultButton class-list="btn-outline" :disabled="form.processing" @click="close">
                        <Icon :icon="['fas', 'trash']" />
                        {{ $t('buttons.cancel') }}
                    </DefaultButton>
                </div>
            </BOverlay>
        </div>
    </VueFinalModal>
</template>
