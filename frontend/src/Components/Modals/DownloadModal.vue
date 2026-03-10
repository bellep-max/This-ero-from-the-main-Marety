<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { defineAsyncComponent } from 'vue';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const props = defineProps({
    item: {
        type: Object,
        required: false,
    },
    type: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['close']);

const downloadStandard = async () => {
    const response = await axios.get(route('downloads.download', { uuid: props.item.uuid, type: props.type }));

    window.location.href = response.data.download_url;
    emit('close');
};

const downloadHQ = async () => {
    const response = await axios.get(route('downloads.download.hd', { uuid: props.item.uuid, type: props.type }));

    window.location.href = response.data.download_url;
    emit('close');
};
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-4 gap-4"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="download-modal"
    >
        <DefaultButton class-list="btn-pink" @click="downloadStandard">
            {{ $t('buttons.standard') }}
        </DefaultButton>
        <DefaultButton class-list="btn-pink" :disabled="!item.hd" @click="downloadHQ">
            {{ $t('buttons.high_quality') }}
        </DefaultButton>
    </VueFinalModal>
</template>
