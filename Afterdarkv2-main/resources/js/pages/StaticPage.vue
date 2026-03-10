<script setup>
const props = defineProps({
    content: {
        type: Object,
        required: true,
        default: {},
    },
});
</script>

<template>
    <div class="container">
        <span v-html="content.content" />
    </div>
</template>
