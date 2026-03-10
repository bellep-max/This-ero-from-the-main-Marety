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
    icon: {
        type: String,
        required: true,
    },
    size: {
        type: String,
        default: 'xl',
    },
});
</script>

<template>
    <button class="btn-default p-2 btn-icon" :class="classList" :disabled="disabled">
        <Icon :icon="['fas', icon]" :size="size" />
        <slot />
    </button>
</template>
