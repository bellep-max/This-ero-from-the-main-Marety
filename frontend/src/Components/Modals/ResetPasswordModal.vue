<script setup>
import apiClient from '@/api/client';
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { BInput } from 'bootstrap-vue-next';
import { defineAsyncComponent, reactive } from 'vue';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const emit = defineEmits(['cancel']);

const form = reactive({
    email: null,
});

const submit = () => {
    console.log(form);
    emit('cancel');
    // apiClient.post('', form);
};
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-3 p-md-5"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="reset-password-modal"
    >
        <div class="text-center font-default fs-5">
            {{ $t('modals.reset_password') }}
        </div>
        <p class="font-default fs-14">
            {{ $t('modals.reset_password_message') }}
        </p>
        <div class="row gy-3 gy-lg-4">
            <div class="col-12">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                    <label for="forget-email" class="font-default fs-14 default-text-color">
                        {{ $t('misc.email') }}
                    </label>
                    <BInput type="email" v-model="form.email" />
                </div>
            </div>
        </div>
        <div class="d-flex flex-row justify-content-between align-items-center mt-5 w-100">
            <DefaultButton class-list="btn-outline" @click="$emit('cancel')">
                {{ $t('buttons.cancel') }}
            </DefaultButton>
            <DefaultButton class-list="btn-pink" @click="submit">
                {{ $t('buttons.submit') }}
            </DefaultButton>
        </div>
    </VueFinalModal>
</template>

<style scoped></style>
