<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { useRouter } from 'vue-router'
import apiClient from '@/api/client';
import { defineAsyncComponent, ref } from 'vue';
import { BOverlay } from 'bootstrap-vue-next';
const DefaultLink = defineAsyncComponent(() => import('@/Components/Links/DefaultLink.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const processing = ref(false);
const emit = defineEmits(['accepted']);

const accept = () => {
    processing.value = true;

    apiClient.post(
        route('terms.accept'),
        {
            accept: true,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                processing.value = false;
                emit('accepted');
            },
            onError: () => {
                processing.value = false;
            },
        },
    );
};
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="lightbox-warning-18 position-absolute top-50 start-50 translate-middle rounded-4 d-flex justify-content-center p-3 p-md-0"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        overlay-style="backdrop-filter: blur(5px); background-color: #e836c542;"
        :click-to-close="false"
        :esc-to-close="false"
        modal-id="adult-only-modal"
    >
        <div class="d-flex flex-column justify-content-center align-items-center gap-4">
            <h2 class="fs-3 font-default fw-bold text-center color-light">
                {{ $t('modals.are_you_adult') }}
            </h2>
            <p class="fs-5 font-merge color-light">
                {{ $t('modals.adult_only_warning') }}
            </p>
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 gap-md-5 w-100">
                <DefaultLink link="https://google.com/" class-list="w-100 btn-default btn-pink">
                    {{ $t('buttons.not_18') }}
                </DefaultLink>
                <BOverlay :show="processing" rounded="pill" class="w-100">
                    <DefaultButton class-list="w-100 btn-outline" :disabled="processing" @click="accept">
                        {{ $t('buttons.has_18') }}
                    </DefaultButton>
                </BOverlay>
            </div>
        </div>
    </VueFinalModal>
</template>
