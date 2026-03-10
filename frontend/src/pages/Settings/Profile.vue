<script setup>
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { BForm, BInput, BFormSelect, BFormTextarea } from 'bootstrap-vue-next';
import { removeEmptyObjectsKeys } from '@/Services/FormService.js';
const SettingsLayout = defineAsyncComponent(() => import('@/Layouts/SettingsLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const props = defineProps({
    genders: {
        required: true,
        type: Object,
    },
    countries: {
        type: Object,
        default: {},
    },
});

const authStore = useAuthStore();
const user = authStore.user;
const today = new Date(Date.now()).toJSON().substring(0, 10);

const form = useForm({
    name: user.name,
    bio: user.bio,
    birth: user.birth,
    gender: user.gender,
    country_id: user.country,
});

const onSubmit = (e) => {
    e.preventDefault();

    form.transform(removeEmptyObjectsKeys).patch(route('settings.profile.update'), {
        preserveScroll: true,
        preserveState: true,
    });
};

const reset = () => {
    form.reset();
};

const countryOptions = computed(() =>
    props.countries.map((element) => {
        return {
            value: element.id,
            text: element.name,
        };
    }),
);
</script>

<template>
    
    <SettingsLayout :title="$t('menus.user_settings.profile')">
        <BForm class="row gy-4" @submit="onSubmit" @reset="reset">
            <div class="col-12 col-lg-6 d-flex flex-column justify-content-start align-items-start gap-1">
                <label class="font-default fs-14 default-text-color">
                    {{ $t('pages.settings.profile_display_name_title') }}:
                </label>
                <BInput type="text" max="175" v-model="form.name" />
                <span class="fs-12 font-merge color-grey">
                    {{ $t('pages.settings.profile_display_name_description') }}
                </span>
            </div>
            <div class="col-12 col-lg-6 d-flex flex-column justify-content-start align-items-start gap-1">
                <label class="font-default fs-14 default-text-color">
                    {{ $t('pages.settings.profile_country_title') }}:
                </label>
                <BFormSelect v-model="form.country_id" :options="countryOptions"></BFormSelect>
                <span class="fs-12 font-merge color-grey">
                    {{ $t('pages.settings.profile_country_description') }}
                </span>
            </div>
            <div class="col-12 col-lg-6 d-flex flex-column justify-content-start align-items-start gap-1">
                <label class="font-default fs-14 default-text-color">
                    {{ $t('pages.settings.profile_gender_title') }}:
                </label>
                <BFormSelect v-model="form.gender" :options="genders"></BFormSelect>
                <span class="fs-12 font-merge color-grey">
                    {{ $t('pages.settings.profile_gender_description') }}
                </span>
            </div>
            <div class="col-12 col-lg-6 d-flex flex-column justify-content-start align-items-start gap-1">
                <label class="font-default fs-14 default-text-color">
                    {{ $t('pages.settings.profile_birth_title') }}:
                </label>
                <BInput type="date" v-model="form.birth" :max="today" />
                <span class="fs-12 font-merge color-grey">
                    {{ $t('pages.settings.profile_birth_description') }}
                </span>
            </div>
            <div class="col-12 d-flex flex-column justify-content-start align-items-start gap-1">
                <label class="font-default fs-14 default-text-color">
                    {{ $t('pages.settings.profile_bio_title') }}:
                </label>
                <BFormTextarea max="180" v-model="form.bio" />
                <span class="fs-12 font-merge color-grey">
                    {{ $t('pages.settings.profile_bio_description') }}
                </span>
            </div>
            <div class="col-12 d-flex flex-row justify-content-center align-items-center gap-4 mt-5">
                <DefaultButton type="submit" class-list="btn-pink">
                    {{ $t('buttons.save_changes') }}
                </DefaultButton>
            </div>
        </BForm>
    </SettingsLayout>
</template>
