<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { BFormCheckbox, BFormInput, BFormTextarea } from 'bootstrap-vue-next';
import { defineAsyncComponent, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { removeEmptyObjectsKeys } from '@/Services/FormService.js';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const Multiselect = defineAsyncComponent(() => import('@vueform/multiselect'));
import '@vueform/multiselect/themes/default.css';

const emit = defineEmits(['close']);

const form = useForm({
    artwork: null,
    genres: null,
    title: null,
    description: null,
    is_visible: false,
    allow_comments: false,
    is_explicit: true,
});

const defaultArtworkLink = '/assets/images/playlist.png';

const artworkPreview = ref('/assets/images/playlist.png');
const artworkSelected = ref(false);

const page = usePage();
const genres = ref(page.props.filter_presets.genres);

const submit = () => {
    form.transform(removeEmptyObjectsKeys).post(route('playlists.store'), {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};

const setArtwork = (e) => {
    form.artwork = e.target.files[0];
};

const resetArtwork = () => {
    form.reset('artwork');
};

const setImagePreview = (image) => {
    artworkPreview.value = URL.createObjectURL(image);
    artworkSelected.value = true;
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
    <VueFinalModal
        class="container-fluid"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-4"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="create-playlist-modal"
    >
        <div class="text-center font-default fs-5 mb-3">
            {{ $t('modals.playlist.title') }}
        </div>
        <div class="row gy-3 gy-lg-4">
            <div class="col-12 col-md-5">
                <div class="d-flex flex-column justify-content-start align-items-center gap-4">
                    <img
                        class="img-fluid rounded-4 border-pink"
                        :src="artworkPreview"
                        :alt="$t('misc.playlist_artwork')"
                    />
                    <DefaultButton v-if="artworkSelected" class-list="btn-outline" @click="resetArtwork">
                        <Icon :icon="['fas', 'trash']" />
                        {{ $t('buttons.clear') }}
                    </DefaultButton>
                    <label class="btn-default btn-pink">
                        {{ $t('misc.artwork') }}
                        <input class="d-none" accept="image/*" type="file" @change="setArtwork" />
                    </label>
                </div>
            </div>
            <div class="col-12 col-md-7">
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
                        <!--            <BFormSelect v-model="form.genres" :options="genres" multiple></BFormSelect>-->
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

                    <DefaultButton class-list="btn-pink" @click="submit">
                        {{ $t('buttons.save') }}
                    </DefaultButton>
                </div>
            </div>
        </div>
    </VueFinalModal>
</template>
