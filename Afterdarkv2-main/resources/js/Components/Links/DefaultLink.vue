<script setup>
const props = defineProps({
    link: {
        type: String,
        required: true,
    },
    classList: {
        type: String,
        required: false,
    },
});
</script>

<template>
    <a :class="classList" :href="link">
        <slot />
    </a>
</template>
