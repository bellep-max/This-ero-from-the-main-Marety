<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
    icon: {
        required: true,
        type: Array,
    },
    classList: {
        required: false,
        type: String,
    },
    size: {
        type: String,
        default: 'xl',
    },
});
</script>

<template>
    <font-awesome-icon :icon="icon" :class="classList" :size="size" />
</template>
