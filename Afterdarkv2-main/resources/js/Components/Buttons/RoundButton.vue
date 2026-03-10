<script setup>
const props = defineProps({
    classList: {
        required: false,
        default: '',
    },
});
</script>

<template>
    <button class="btn-default btn-rounded" :class="classList">
        <slot />
    </button>
</template>
