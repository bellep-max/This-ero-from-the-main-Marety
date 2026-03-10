<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { $t } from '@/i18n.js';
import { BFormInput } from 'bootstrap-vue-next';
import { showModal } from '@/Services/ModalService.js';
import { removeEmptyObjectsKeys, slugify, validateEmail } from '@/Services/FormService.js';
import { defineAsyncComponent } from 'vue';
const SettingsLayout = defineAsyncComponent(() => import('@/Layouts/SettingsLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const DeleteProfileModal = defineAsyncComponent(() => import('@/Components/Modals/DeleteProfileModal.vue'));

const page = usePage();
const user = page.props.auth.user;

const form = useForm({
    username: user.username,
    email: null,
    password: null,
});

const onSubmit = (e) => {
    e.preventDefault();

    form.transform(removeEmptyObjectsKeys).patch(route('settings.account.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head :title="$t('menus.user_settings.account')" />
    <SettingsLayout :title="$t('menus.user_settings.account')">
        <div class="d-flex flex-column justify-content-start align-items-center gap-4">
            <div class="d-flex flex-row justify-content-between align-items-start gap-4 w-100">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                    <label class="text-start font-default fs-14 default-text-color">
                        {{ $t('pages.settings.account.address_username.title') }}
                    </label>
                    <BFormInput v-model="form.username" :formatter="slugify" />
                    <span class="fs-12 font-merge color-grey">
                        {{ $t('pages.settings.account.address_username.description') }}
                        {{ route('users.show', { user: form.username }) }}
                    </span>
                </div>
                <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                    <label class="text-start font-default fs-14 default-text-color">
                        {{ $t('pages.settings.account.email.title') }}
                    </label>
                    <BFormInput type="email" v-model="form.email" :state="validateEmail(form.email)" />
                    <span class="fs-12 font-merge color-grey">
                        {{ $t('pages.settings.account.email.description') }}
                    </span>
                </div>
            </div>
            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                <label class="text-start font-default fs-14 default-text-color">
                    {{ $t('pages.settings.account.current_password.title') }}
                </label>
                <BFormInput type="password" v-model="form.password" :state="form.password?.length > 0" />
                <span class="fs-12 font-merge color-grey">
                    {{ $t('pages.settings.account.current_password.description') }}
                </span>
            </div>
            <div class="col-12 d-flex flex-row justify-content-center align-items-center gap-4 mt-5">
                <DefaultButton @click="onSubmit" class-list="btn-pink">
                    {{ $t('buttons.save_changes') }}
                </DefaultButton>
                <DefaultButton @click="showModal(DeleteProfileModal)" class-list="btn-outline">
                    {{ $t('buttons.delete_account') }}
                </DefaultButton>
            </div>
        </div>
    </SettingsLayout>
</template>
