<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { defineAsyncComponent } from 'vue';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const emit = defineEmits(['selected']);

const props = defineProps({
    adventure: {
        type: Object,
        required: false,
    },
});
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-4 max-height-75"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        :click-to-close="false"
        :esc-to-close="false"
        modal-id="adventure-branch-modal"
    >
        <div class="container-fluid overflow-y-auto d-flex flex-column">
            <div class="w-100 text-center font-default fs-5 mb-2">
                {{ $t('modals.adventure_branch.title') }}
            </div>
            <div class="d-flex flex-row justify-content-center align-items-center gap-3">
                <DefaultButton
                    v-for="track in adventure.children"
                    :key="track.uuid"
                    class-list="btn-pink"
                    @click="$emit('selected', track)"
                >
                    {{ track.title }}
                </DefaultButton>
            </div>
        </div>
    </VueFinalModal>
</template>
