<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, ref, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { BFormCheckbox, BFormInput, BFormTextarea } from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys } from '@/Services/FormService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Multiselect = defineAsyncComponent(() => import('@vueform/multiselect'));
import '@vueform/multiselect/themes/default.css';

const props = defineProps({
    playlist: {
        type: Object,
        default: {},
    },
});

const defaultArtworkLink = '/assets/images/playlist.png';

const artworkPreview = ref(props.playlist.artwork);
const artworkSelected = ref(false);

const page = usePage();
const user = page.props.auth.user;
const genres = ref(page.props.filter_presets.genres);

const form = useForm({
    _method: 'patch',
    artwork: null,
    genres: props.playlist.genres,
    title: props.playlist.title,
    description: props.playlist.description,
    is_visible: props.playlist.is_visible,
    allow_comments: props.playlist.allow_comments,
    is_explicit: props.playlist.is_explicit,
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
    form.transform(removeEmptyObjectsKeys).post(route('playlists.update', props.playlist), {
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
    <Head :title="playlist.title" />
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

<style scoped></style>
