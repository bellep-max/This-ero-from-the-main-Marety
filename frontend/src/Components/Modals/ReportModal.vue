<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { BFormRadio, BFormRadioGroup } from 'bootstrap-vue-next';
import { ref, reactive, defineAsyncComponent } from 'vue';
import { useForm } from '@/helpers/useForm'
import apiClient from '@/api/client'
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    type: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    report_type: null,
    type: props.type,
    uuid: props.item.uuid,
});

const submit = () => {
    form.post(route('report.store'), {
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-3 p-md-5"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="report-modal"
    >
        <div class="text-center font-default fs-5">
            {{ $t('modals.report.title') }}
        </div>
        <p class="font-default fs-14">
            {{ $t('modals.report.description') }}
        </p>
        <BFormRadioGroup v-model="form.report_type" size="lg" stacked>
            <BFormRadio value="1">
                {{ $t('modals.report.wrong_author') }}
            </BFormRadio>
            <BFormRadio value="2">
                {{ $t('modals.report.audio_problem') }}
            </BFormRadio>
            <BFormRadio value="3">
                {{ $t('modals.report.undesirable_content') }}
            </BFormRadio>
        </BFormRadioGroup>
        <div class="d-flex flex-row justify-content-between align-items-center mt-5 w-100">
            <DefaultButton class-list="btn-outline" @click="$emit('close')">
                {{ $t('buttons.cancel') }}
            </DefaultButton>
            <DefaultButton class-list="btn-pink" :disabled="!form.report_type" @click="submit">
                {{ $t('buttons.submit') }}
            </DefaultButton>
        </div>
    </VueFinalModal>
</template>

<style scoped>
#embed_iframe {
    width: 100%;
    pointer-events: none;
    border: 1px solid #ddd;
    box-sizing: border-box;
    border-radius: 4px;

    &.lg {
        height: 300px;
    }

    &.md {
        height: 180px;
    }

    &.sm {
        height: 60px;
    }
}
</style>
