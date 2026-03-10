<script setup>
import { defineAsyncComponent } from 'vue';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));

const props = defineProps({
    classList: {
        required: false,
        default: '',
    },
    disabled: {
        type: Boolean,
        required: false,
        default: false,
    },
});
</script>

<template>
    <button
        class="btn-default btn-white rounded-circle"
        :class="classList"
        :disabled="disabled"
        style="filter: drop-shadow(0 0px 5px #dedede)"
    >
        <Icon :icon="['fas', 'play']" size="3x" />
        <slot />
    </button>
</template>
