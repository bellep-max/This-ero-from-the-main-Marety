<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { defineAsyncComponent } from 'vue';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const emit = defineEmits(['close']);
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-3 p-md-5"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="subscription-modal"
    >
        <div class="text-center font-default fs-5">
            {{ $t('modals.subscription.title') }}
        </div>
        <p class="font-default fs-14">
            {{ $t('modals.subscription.description') }}
        </p>
        <p class="font-default fs-6">
            {{ $t('modals.subscription.message') }}
        </p>
        <div class="d-flex flex-row justify-content-end align-items-center mt-5 w-100">
            <DefaultButton class-list="btn-pink" :href="route('settings.subscription.edit')">
                {{ $t('buttons.subscribe') }}
            </DefaultButton>
        </div>
    </VueFinalModal>
</template>
