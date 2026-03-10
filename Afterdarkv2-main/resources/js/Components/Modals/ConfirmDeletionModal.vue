<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { defineAsyncComponent } from 'vue';
import ObjectTypes from '@/Enums/ObjectTypes.js';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        required: true,
        default: ObjectTypes.Playlist,
    },
});

const emit = defineEmits(['confirm', 'close']);
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-3 p-md-5"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        overlay-style="backdrop-filter: blur(5px);
    background-color: #e836c542;"
        modal-id="delete-modal"
    >
        <div class="text-center font-default fs-5">
            {{ $t('modals.delete.title', { type: type }) }}
        </div>
        <p class="font-default fs-14">
            {{ $t(`modals.delete.description`, { title: title }) }}
        </p>
        <div class="d-flex flex-row justify-content-between align-items-center mt-5 w-100">
            <DefaultButton class-list="btn-outline" @click="$emit('close')">
                {{ $t('buttons.cancel') }}
            </DefaultButton>
            <DefaultButton class-list="btn-pink" @click="$emit('confirm')">
                {{ $t('buttons.yes') }}
            </DefaultButton>
        </div>
    </VueFinalModal>
</template>
