<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, ref, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { BFormCheckbox, BFormInput, BFormTextarea, BFormSelect } from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys, slugify } from '@/Services/FormService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const props = defineProps({
    podcast: {
        type: Object,
        default: {},
    },
});

const defaultArtworkLink = '/assets/images/podcast.png';

const artworkPreview = ref(props.podcast.artwork);
const artworkSelected = ref(false);

const page = usePage();
const user = page.props.auth.user;
const tags = ref(page.props.filter_presets.tags);
const countries = ref(page.props.filter_presets.countries);
const languages = ref(page.props.filter_presets.languages);
const categories = ref(page.props.filter_presets.categories);

const form = useForm({
    _method: 'patch',
    artwork: null,
    tags: props.podcast.tags ?? [],
    title: props.podcast.title,
    description: props.podcast.description,
    is_visible: props.podcast.is_visible,
    allow_comments: props.podcast.allow_comments,
    allow_download: props.podcast.allow_download,
    explicit: props.podcast.explicit,
    categories: props.podcast.categories ?? [],
    language_id: props.podcast.language_id,
    country_id: props.podcast.country_id,
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

const addTag = (tag) => {
    const newTag = {
        id: -Math.floor(Math.random() * 10000000),
        tag: slugify(tag),
    };

    tags.value.push(newTag);
    form.tags.push(newTag);
};

const submit = () => {
    form.transform(removeEmptyObjectsKeys).post(route('podcasts.update', props.podcast), {
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
    <Head :title="podcast.title" />
    <UserLayout :user="podcast.user">
        <div class="d-flex flex-column gap-3">
            <div class="row gy-3">
                <div
                    class="col-12 col-lg-4 d-flex flex-column gap-4 justify-content-center justify-content-xl-start align-items-center"
                >
                    <img :src="artworkPreview" :alt="podcast.title" class="img-fluid rounded-4 border-pink" />
                    <DefaultButton v-if="artworkSelected" class-list="btn-outline" @click="resetArtwork">
                        <Icon :icon="['fas', 'trash']" />
                        {{ $t('buttons.clear') }}
                    </DefaultButton>
                    <label class="btn-default btn-pink">
                        {{ $t('misc.artwork') }}
                        <input class="d-none" accept="image/*" type="file" @change="setArtwork" />
                    </label>
                </div>
                <div class="col d-flex flex-column justify-content-start align-items-center gap-4">
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                        <label class="font-default fs-14 color-text">{{ $t('misc.title') }}</label>
                        <BFormInput maxlength="175" v-model="form.title" required />
                    </div>
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                        <label class="font-default fs-14 color-text">
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
                    <!--          <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">-->
                    <!--            <label class="font-default fs-14 color-text">-->
                    <!--              {{ $t('misc.categories') }}-->
                    <!--            </label>-->
                    <!--            <vue-multiselect-->
                    <!--                v-model="form.categories"-->
                    <!--                :options="categories"-->
                    <!--                :multiple="true"-->
                    <!--            />-->
                    <!--          </div>-->
                    <div class="d-flex flex-row justify-content-between align-items-start gap-4 w-100">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                            <label class="font-default fs-14 color-text">
                                {{ $t('misc.country') }}
                            </label>
                            <BFormSelect v-model="form.country_id" :options="countries"></BFormSelect>
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                            <label class="font-default fs-14 color-text">
                                {{ $t('misc.language') }}
                            </label>
                            <BFormSelect v-model="form.language_id" :options="languages"></BFormSelect>
                        </div>
                    </div>
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                        <label class="font-default fs-14 color-text">
                            {{ $t('misc.description') }}
                        </label>
                        <BFormTextarea maxlength="180" v-model="form.description" />
                    </div>
                </div>
                <div class="col-12 d-flex flex-column gap-3">
                    <div class="d-flex flex-row justify-content-between align-items-center w-100 flex-wrap">
                        <BFormCheckbox v-model="form.is_visible" switch size="lg">
                            {{ $t('misc.make_public') }}
                        </BFormCheckbox>
                        <BFormCheckbox v-model="form.allow_comments" switch size="lg">
                            {{ $t('misc.allow_comments') }}
                        </BFormCheckbox>
                        <BFormCheckbox v-model="form.allow_download" switch size="lg">
                            {{ $t('misc.allow_download') }}
                        </BFormCheckbox>
                        <BFormCheckbox v-model="form.explicit" switch size="lg">
                            {{ $t('misc.explicit') }}
                        </BFormCheckbox>
                    </div>
                    <div class="d-flex flex-row justify-content-end align-items-center gap-3 w-100">
                        <DefaultButton class-list="btn-outline" :href="route('podcasts.show', podcast)">
                            {{ $t('buttons.cancel') }}
                        </DefaultButton>
                        <DefaultButton class-list="btn-pink" @click="submit">
                            {{ $t('buttons.save') }}
                        </DefaultButton>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
