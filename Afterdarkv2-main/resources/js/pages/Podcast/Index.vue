<script setup>
import { $t } from '@/i18n.js';
import { BAccordion, BAccordionItem, BFormCheckboxGroup, BOffcanvas } from 'bootstrap-vue-next';
import { defineAsyncComponent, onMounted, ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { removeEmptyObjectsKeys } from '@/Services/FormService.js';
const ImageCard = defineAsyncComponent(() => import('@/Components/Cards/ImageCard.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const Multiselect = defineAsyncComponent(() => import('@vueform/multiselect'));
import '@vueform/multiselect/themes/default.css';

const props = defineProps({
    regions: {
        type: Array,
        default: [],
    },
    filters: {
        type: Object,
        required: true,
    },
});

const page = usePage();

const categories = ref(page.props.filter_presets.podcast_categories);
const languages = ref(page.props.filter_presets.languages);
const countries = ref(page.props.filter_presets.countries);
const tags = ref(page.props.filter_presets.tags);

const hasFilters = ref(false);
const showFilters = ref(false);
const isLastPage = ref(false);
const filteredTags = ref(tags);

const podcasts = ref([]);

const form = useForm({
    categories: [],
    languages: [],
    countries: [],
    regions: [],
    tags: [],
});

const setFiltersDrawer = () => {
    showFilters.value = !showFilters.value;
};

const resetFilters = () => {
    form.reset();
    applyFilters();
};

const setFilterValues = (filtersData) => {
    let count = 0;

    if (filtersData.isDirty) {
        hasFilters.value = true;

        for (const value of Object.values(filtersData.data())) {
            if (typeof value === 'number') {
                count += 1;
            } else if (value === null) {
            } else {
                count += value.length;
            }
        }
    } else {
        hasFilters.value = false;
    }
};

const applyFilters = () => {
    router.get(
        route('podcasts.index'),
        {
            ...removeEmptyObjectsKeys(form.data()),
        },
        {
            preserveState: true,
            preserveUrl: true,
            only: ['podcasts', 'pagination', 'filters'],
            onSuccess: (page) => {
                podcasts.value = page.props.podcasts;
                setFilterValues(form);

                isLastPage.value = checkLastPage();
            },
        },
    );
};

const loadMore = () => {
    form.transform(() => removeEmptyObjectsKeys()).get(podcasts.value.links?.next, {
        preserveState: true,
        preserveScroll: true,
        only: ['podcasts', 'pagination'],
        onSuccess: (page) => {
            podcasts.value.data = [...podcasts.value.data, ...page.props.podcasts.data];

            // Update the pagination metadata with the new data from the server
            podcasts.value.links = page.props.podcasts.links;
            podcasts.value.meta = page.props.podcasts.meta;

            isLastPage.value = checkLastPage();
        },
    });
};

const checkLastPage = () => podcasts.value.meta?.current_page === podcasts.value.meta?.last_page;

onMounted(() => {
    podcasts.value = page.props.podcasts;

    isLastPage.value = checkLastPage();
});
</script>

<template>
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col text-start">
                    <div class="d-block">
                        <div class="block-title color-light text-truncate">
                            {{ $t('pages.podcasts.title') }}
                        </div>
                        <div class="block-description color-light">
                            {{ $t('pages.podcasts.description') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    <DefaultButton class-list="btn-outline d-xl-none" @click="setFiltersDrawer">
                        {{ $t('buttons.filters') }}
                    </DefaultButton>
                    <BOffcanvas v-model="showFilters" responsive="xl" placement="start">
                        <div class="d-flex flex-column w-100 bg-default rounded-5 px-3 py-4 gap-3">
                            <div class="fs-4 font-default">
                                {{ $t('buttons.filters') }}
                            </div>
                            <BAccordion free>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'cubes-stacked']" class-list="color-pink" />
                                        <span class="font-default">{{ $t('pages.podcasts.filters.categories') }}</span>
                                    </template>
                                    <template #default>
                                        <BFormCheckboxGroup
                                            :options="categories"
                                            v-model="form.categories"
                                            @change="applyFilters"
                                            stacked
                                        />
                                    </template>
                                </BAccordionItem>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'language']" class-list="color-pink" />
                                        <span class="font-default">{{ $t('pages.podcasts.filters.languages') }}</span>
                                    </template>
                                    <template #default>
                                        <BFormCheckboxGroup
                                            :options="languages"
                                            v-model="form.languages"
                                            @change="applyFilters"
                                            stacked
                                        />
                                    </template>
                                </BAccordionItem>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'flag']" class-list="color-pink" />
                                        <span class="font-default">{{ $t('pages.podcasts.filters.countries') }}</span>
                                    </template>
                                    <template #default>
                                        <BFormCheckboxGroup
                                            :options="countries"
                                            v-model="form.countries"
                                            @change="applyFilters"
                                            stacked
                                        />
                                    </template>
                                </BAccordionItem>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'hashtag']" class-list="color-pink" />
                                        <span class="font-default">{{ $t('pages.discover.filters.tags.name') }}</span>
                                    </template>
                                    <template #default>
                                        <div class="d-flex flex-column gap-2">
                                            <Multiselect
                                                :options="filteredTags"
                                                v-model="form.tags"
                                                :searchable="true"
                                                :multiple="true"
                                                mode="tags"
                                                label="tag"
                                                track-by="tag"
                                                clear-on-search
                                                hide-selected
                                                @change="applyFilters"
                                            >
                                            </Multiselect>
                                        </div>
                                    </template>
                                </BAccordionItem>
                                <BAccordionItem button-class="gap-2" header-class="border-0">
                                    <template #title>
                                        <Icon :icon="['fas', 'globe']" class-list="color-pink" />
                                        <span class="font-default">{{ $t('pages.podcasts.filters.regions') }}</span>
                                    </template>
                                    <template #default>
                                        <BFormCheckboxGroup
                                            :options="regions"
                                            v-model="form.regions"
                                            @change="applyFilters"
                                            stacked
                                        />
                                    </template>
                                </BAccordionItem>
                            </BAccordion>
                            <div v-if="hasFilters" class="d-flex flex-row justify-content-center align-items-center">
                                <DefaultButton class-list="btn-outline" @click="resetFilters">
                                    {{ $t('buttons.cancel') }}
                                </DefaultButton>
                            </div>
                        </div>
                    </BOffcanvas>
                </div>
                <div class="col col-xl-9 flex-column">
                    <div class="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
                        <ImageCard
                            v-for="podcast in podcasts.data"
                            :model="podcast"
                            :route="route('podcasts.show', podcast.uuid)"
                            :key="podcast.uuid"
                        />
                    </div>
                    <DefaultButton
                        class-list="btn-outline mt-2 mx-auto"
                        @click.prevent="loadMore()"
                        :disabled="isLastPage || form.processing"
                    >
                        {{ $t('buttons.load_more') }}
                    </DefaultButton>
                </div>
            </div>
        </div>
    </div>
</template>
