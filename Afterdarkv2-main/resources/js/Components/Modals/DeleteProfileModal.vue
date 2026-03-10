<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { defineAsyncComponent } from 'vue';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const emit = defineEmits(['close', 'accept']);
</script>

<template>
    <VueFinalModal
        class="d-flex flex-column justify-content-center align-items-center gap-4"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-4"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="delete-profile-modal"
    >
        <span class="w-100 text-center font-default fs-5">
            {{ $t('modals.delete_profile.title') }}
        </span>
        <span class="w-100 text-center font-merge fs-6">
            {{ $t('modals.delete_profile.description') }}
        </span>
        <div class="col-12 d-flex flex-row justify-content-center align-items-center gap-4 mt-5">
            <DefaultButton class-list="btn-pink">
                {{ $t('buttons.confirm_delete_account') }}
            </DefaultButton>
            <DefaultButton @click="" class-list="btn-outline">
                {{ $t('buttons.cancel') }}
            </DefaultButton>
        </div>
    </VueFinalModal>
</template>
