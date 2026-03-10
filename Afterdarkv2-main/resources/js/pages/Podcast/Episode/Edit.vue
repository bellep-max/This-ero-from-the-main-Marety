<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, onMounted, ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { BFormCheckbox, BFormFile, BFormInput, BFormTextarea } from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys } from '@/Services/FormService.js';
import { useModal } from 'vue-final-modal';
import ObjectTypes from '@/Enums/ObjectTypes.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const ConfirmDeletionModal = defineAsyncComponent(() => import('@/Components/Modals/ConfirmDeletionModal.vue'));

const props = defineProps({
    episode: {
        type: Object,
        default: {},
    },
    user: {
        type: Object,
        default: {},
    },
});

const allowedFileSize = 92970;
const maxAllowed = 60;
const titleLength = ref(0);
const inputAllowed = ref(true);

const artworkPreview = ref(props.episode.artwork);

const validateFileSize = ref(false);

const form = useForm({
    _method: 'PATCH',
    title: props.episode.title,
    is_visible: props.episode.is_visible,
    allow_comments: props.episode.allow_comments,
    allow_download: props.episode.allow_download,
    explicit: props.episode.explicit,
    description: props.episode.description,
    artwork: null,
    file: null,
});

const setImagePreview = () => {
    artworkPreview.value = form.artwork ? URL.createObjectURL(form.artwork) : props.episode.artwork;
};

const submit = () => {
    form.transform(removeEmptyObjectsKeys).post(route('episodes.update', props.episode), {
        preserveScroll: true,
    });
};

const { open, close } = useModal({
    component: ConfirmDeletionModal,
    attrs: {
        title: props.episode.title,
        type: ObjectTypes.PodcastEpisode,
        onClose() {
            close();
        },
        onConfirm() {
            close();
            router.delete(route('episodes.destroy', props.episode));
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

watch(
    () => form.file,
    (newValue) => {
        const isEmpty = !newValue || newValue.size <= 0;
        validateFileSize.value = isEmpty ? false : Math.round(newValue.size / 1024) <= allowedFileSize;
    },
);

onMounted(() => {
    titleLength.value = props.episode.title.length;
});
</script>

<template>
    <Head :title="episode.title" />
    <UserLayout :user="user">
        <div class="row gy-3">
            <div class="d-flex flex-column w-100 justify-content-start align-items-start gap-1">
                <BFormFile v-model="form.file" accept="audio/*" :state="validateFileSize" />
            </div>
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
                                <BFormCheckbox v-model="form.explicit" switch>
                                    {{ $t('misc.explicit') }}
                                </BFormCheckbox>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label class="text-start font-default fs-14 default-text-color">
                            {{ $t('misc.description') }}
                        </label>
                        <BFormTextarea v-model="form.description" rows="5" />
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex flex-row justify-content-center align-items-center gap-4 w-100 mt-5">
            <DefaultButton :href="route('episodes.show', episode)" class-list="btn-outline">
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
