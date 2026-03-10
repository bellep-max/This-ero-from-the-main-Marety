<script setup>
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute } from 'vue-router'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { BFormInput } from 'bootstrap-vue-next';
const SettingsLayout = defineAsyncComponent(() => import('@/Layouts/SettingsLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const minimalLength = 8;

const allFieldsSet = ref(false);

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const updatePassword = () => {
    form.put(route('settings.password.update'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => form.reset(),
        onError: (errors) => {
            if (errors.password) {
                form.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }

            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};

const validateForm = () => {
    if (!form.isDirty) {
        allFieldsSet.value = false;
    } else if (form.password && form.current_password && form.password_confirmation) {
        allFieldsSet.value = true;
    }
};

const validatePwdInput = computed(() => form.password.length > minimalLength);
const validatePwdConfirmInput = computed(() => form.password_confirmation.length > minimalLength);
</script>

<template>
    
    <SettingsLayout :title="$t('menus.user_settings.password')">
        <div class="d-flex flex-column justify-content-start align-items-center gap-4">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                <label class="text-start font-default fs-14 default-text-color">
                    {{ $t('pages.settings.current_password') }}
                </label>
                <BFormInput type="password" v-model="form.current_password" @update:model-value="validateForm" />
            </div>
            <div class="d-flex flex-row justify-content-between align-items-center gap-4 w-100">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                    <label class="text-start font-default fs-14 default-text-color">
                        {{ $t('pages.settings.new_password') }}
                    </label>
                    <BFormInput
                        type="password"
                        v-model="form.password"
                        @update:model-value="validateForm"
                        :state="validatePwdInput"
                    />
                </div>
                <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                    <label class="text-start font-default fs-14 default-text-color">
                        {{ $t('pages.settings.retype_new_password') }}
                    </label>
                    <BFormInput
                        type="password"
                        v-model="form.password_confirmation"
                        @update:model-value="validateForm"
                        :state="validatePwdConfirmInput"
                    />
                </div>
            </div>
            <span class="fs-12 font-merge color-grey text-center">
                {{ $t('pages.settings.password_description', { count: minimalLength }) }}
            </span>
            <DefaultButton @click="updatePassword" class-list="btn-pink" :disabled="!allFieldsSet">
                {{ $t('buttons.change_password') }}
            </DefaultButton>
        </div>
    </SettingsLayout>
</template>
