<script setup>
import { useVfm, VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { BInput, BFormSelect, BFormTextarea } from 'bootstrap-vue-next';
import { defineAsyncComponent, onMounted, ref, reactive } from 'vue';
import { useForm } from '@/helpers/useForm'
import apiClient from '@/api/client'
import { useAuthStore } from '@/stores/auth'
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const vfm = useVfm();
const emit = defineEmits(['cancel']);

const form = useForm({
    email: '',
    emotion: null,
    about: null,
    message: '',
});

const emotions = ref([
    'Angry',
    'Confused',
    'Happy',
    'Amused',
    'Impressed',
    'Surprised',
    'Disappointed',
    'Frustrated',
    'Curious',
    'Indifferent',
]);

const about = ref([
    { value: 'Website', text: 'Website' },
    { value: 'General', text: 'General Questions' },
    {
        label: 'Mobile Devices',
        options: [
            { value: 'iPhone', text: 'iPhone' },
            { value: 'Android', text: 'Android' },
        ],
    },
]);

const submit = () => {
    emit('cancel');
    vfm.close('feedback-modal');
    // apiClient.post('', form);
};

const cancel = () => {
    form.reset();
    emit('cancel');
    vfm.close('feedback-modal');
};

onMounted(() => {
    form.email = useAuthStore().user?.email;
});
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center gap-3 w-100"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-3 p-md-5"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="feedback-modal"
    >
        <div class="text-center font-default fs-5">
            {{ $t('modals.feedback') }}
        </div>
        <p class="font-default fs-14">
            {{ $t('modals.feedback_message') }}
        </p>
        <div class="container-fluid">
            <div class="row gy-3 gy-lg-4">
                <div class="col-12">
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label
                            for="email"
                            class="font-default fs-14 default-text-color"
                            data-translate-text="LB_FEEDBACK_EMAIL"
                        >
                            {{ $t('misc.email') }}
                        </label>
                        <BInput type="email" v-model="form.email" required />
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label
                            for="feedback_feeling"
                            class="font-default fs-14 default-text-color"
                            data-translate-text="LB_FEEDBACK_FEELING"
                        >
                            {{ $t('modals.i_feel') }}:
                        </label>
                        <BFormSelect :options="emotions" v-model="form.emotion" />
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label
                            for="feedback_about"
                            class="font-default fs-14 default-text-color"
                            data-translate-text="LB_FEEDBACK_ABOUT"
                        >
                            {{ $t('modals.feedback_about') }}:
                        </label>
                        <BFormSelect :options="about" v-model="form.about" />
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                        <label
                            for="comment"
                            class="font-default fs-14 default-text-color"
                            data-translate-text="LB_FEEDBACK_REPORT"
                        >
                            {{ $t('modals.share_thoughts') }}
                        </label>
                        <BFormTextarea v-model="form.message" :placeholder="$t('modals.share_thoughts')" rows="3" />
                    </div>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-between align-items-center mt-5 w-100">
                <DefaultButton class-list="btn-outline" @click="cancel">
                    {{ $t('buttons.cancel') }}
                </DefaultButton>
                <DefaultButton class-list="btn-pink" @click="submit">
                    {{ $t('buttons.submit') }}
                </DefaultButton>
            </div>
        </div>
    </VueFinalModal>
</template>

<style scoped></style>
