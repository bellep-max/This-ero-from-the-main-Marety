<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: false,
        default: '',
    },
});

const status = ref(false);
</script>

<template>
    <VueFinalModal v-model="status" :title="title">
        <slot />
    </VueFinalModal>
</template>

<style scoped></style>
