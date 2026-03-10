<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { BCollapse, BFormCheckbox, BFormFile, BFormInput, BFormTextarea, BTab, BTabs } from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys, slugify } from '@/Services/FormService.js';
import { useModal } from 'vue-final-modal';
import ObjectTypes from '@/Enums/ObjectTypes.js';
const ConfirmDeletionModal = defineAsyncComponent(() => import('@/Components/Modals/ConfirmDeletionModal.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));

const props = defineProps({
    adventure: {
        type: Object,
        default: {},
    },
    user: {
        type: Object,
        default: {},
    },
});

const page = usePage();

const genres = ref(page.props.filter_presets.genres);
const tags = ref(page.props.filter_presets.tags);

const maxAllowed = 60;
const titleLength = ref(props.adventure.title?.length ?? 0);
const inputAllowed = ref(true);

const artworkPreview = ref(props.adventure.artwork);

const form = useForm({
    _method: 'PATCH',
    title: props.adventure.title,
    tags: props.adventure.tags,
    genres: props.adventure.genres,
    is_visible: props.adventure.is_visible,
    allow_comments: props.adventure.allow_comments,
    allow_download: props.adventure.allow_download,
    description: props.adventure.description,
    artwork: null,
});

const setImagePreview = () => {
    artworkPreview.value = form.artwork ? URL.createObjectURL(form.artwork) : props.song.artwork;
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
        title: props.adventure.title,
        type: ObjectTypes.Adventure,
        onClose() {
            close();
        },
        onConfirm() {
            close();
            router.delete(route('adventures.destroy', props.adventure));
        },
    },
    clickToClose: true,
    escToClose: true,
});

const submit = () => {
    form.transform(removeEmptyObjectsKeys).post(route('adventures.update', props.adventure), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="adventure.title" />
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
