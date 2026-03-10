<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { BInput } from 'bootstrap-vue-next';
import { useForm } from '@inertiajs/vue3';
import { showModal } from '@/Services/ModalService.js';
import { defineAsyncComponent } from 'vue';
import loginImage from '@/assets/images/login.webp';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const ResetPasswordModal = defineAsyncComponent(() => import('@/Components/Modals/ResetPasswordModal.vue'));
const SignupModal = defineAsyncComponent(() => import('@/Components/Modals/SignupModal.vue'));

const emit = defineEmits(['signup', 'close', 'reset-password']);

const form = useForm({
    username: null,
    password: null,
});

const loginAttempt = () => {
    form.post(route('login.store'), {
        onSuccess: () => {
            emit('close');
            form.reset('password', 'username');
        },
    });
};
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex p-3 p-md-0"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="login-modal"
    >
        <div class="col col-md-5 d-flex flex-column justify-content-center align-items-center gap-3 ps-md-5">
            <div class="text-center font-default fs-5">
                {{ $t('misc.sign_in') }}
            </div>
            <div class="col-12">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                    <label for="username" class="font-default fs-14 default-text-color">
                        {{ $t('misc.username') }}
                    </label>
                    <BInput type="text" v-model="form.username" @keyup.enter="loginAttempt" />
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                    <label for="password" class="font-default fs-14 default-text-color">
                        {{ $t('misc.password') }}
                    </label>
                    <BInput type="password" v-model="form.password" @keyup.enter="loginAttempt" />
                    <a class="navigation-link ms-auto" @click="showModal(ResetPasswordModal)">
                        {{ $t('buttons.reset_password') }}
                    </a>
                </div>
            </div>
            <DefaultButton class-list="btn-pink" @click="loginAttempt">
                {{ $t('buttons.login') }}
            </DefaultButton>
            <div class="d-flex flex-row justify-content-center align-items-center">
                <a class="navigation-link" @click="showModal(SignupModal)">
                    {{ $t('buttons.no_account') }}
                </a>
            </div>
        </div>
        <div class="col d-none d-md-flex">
            <img :src="loginImage" class="object-fit-scale" alt="Login Icon" />
        </div>
    </VueFinalModal>
</template>

<style scoped></style>
