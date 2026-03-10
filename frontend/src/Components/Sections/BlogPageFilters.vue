<script setup>
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent, ref, watch, reactive } from 'vue';
import { useForm } from '@/helpers/useForm'
import { BAccordion, BAccordionItem, BFormCheckboxGroup, BFormInput, BOffcanvas } from 'bootstrap-vue-next';
import apiClient from '@/api/client'
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));

const props = defineProps({
    categories: {
        type: Array,
        default: [],
    },
    archives: {
        type: Array,
        default: [],
    },
    tags: {
        type: Array,
        default: [],
    },
    filters: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['updated']);

const hasFilters = ref(false);
const numberFilters = ref(0);
const showFilters = ref(false);

const filters = useForm({
    categories: props.filters.categories ?? [],
    dates: props.filters.dates ?? [],
    tags: props.filters.tags ?? [],
});

const setFiltersDrawer = () => {
    showFilters.value = !showFilters.value;
};

const categoriesOptions = computed(() =>
    props.categories.map((element) => {
        return {
            value: element.id,
            text: element.name,
        };
    }),
);

const archivesOptions = computed(() =>
    props.archives.map((element) => {
        return {
            value: element.created_at,
            text: `(${element.count}) ${element.date}`,
        };
    }),
);

const resetFilters = () => {
    filters.reset();
};

const setFilterValues = (filterValues) => {
    let count = 0;

    if (filterValues.isDirty) {
        for (const value of Object.values(filterValues.data())) {
            if (value.length) {
                hasFilters.value = true;
            }

            count += value.length;
        }
    } else {
        hasFilters.value = false;
    }

    numberFilters.value = count;
};

watch(filters, (newValue) => {
    setFilterValues(newValue);
});
</script>

<template>
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
                        <Icon :icon="['fas', 'layer-group']" class-list="color-pink" />
                        <span class="font-default">{{ $t('pages.blog.filters.categories') }}</span>
                    </template>
                    <template #default>
                        <BFormCheckboxGroup
                            :options="categoriesOptions"
                            v-model="filters.categories"
                            stacked
                            @update:model-value="$emit('updated', filters)"
                        />
                    </template>
                </BAccordionItem>
                <BAccordionItem button-class="gap-2" header-class="border-0">
                    <template #title>
                        <Icon :icon="['fas', 'folder-tree']" class-list="color-pink" />
                        <span class="font-default">{{ $t('pages.blog.filters.archives') }}</span>
                    </template>
                    <template #default>
                        <BFormCheckboxGroup
                            :options="archivesOptions"
                            v-model="filters.dates"
                            stacked
                            @update:model-value="$emit('updated', filters)"
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
                            <BFormInput type="search" :placeholder="$t('pages.discover.filters.tags.placeholder')" />
                            <BFormCheckboxGroup
                                :options="tags"
                                v-model="filters.tags"
                                @update:model-value="$emit('updated', filters)"
                            />
                        </div>
                    </template>
                </BAccordionItem>
            </BAccordion>
            <div v-if="hasFilters" class="d-flex flex-column justify-content-between align-items-center gap-3">
                <div class="store-filter-mobile-count">
                    <span class="total-filter">{{ numberFilters }}</span>
                    {{ $t('misc.filters_selected') }}
                </div>
                <div class="d-flex flex-row gap-3 justify-content-center align-items-center">
                    <DefaultButton class-list="btn-outline" @click="resetFilters">
                        {{ $t('buttons.cancel') }}
                    </DefaultButton>
                </div>
            </div>
        </div>
    </BOffcanvas>
</template>

<style scoped></style>
