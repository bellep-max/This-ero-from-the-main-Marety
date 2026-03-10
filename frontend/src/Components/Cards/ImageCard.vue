<script setup>
import { computed } from 'vue';

const props = defineProps({
    model: {
        type: Object,
        required: false,
    },
    route: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: false,
    },
});

const styleImage = computed(() => `background: url(${props.model.artwork})`);
const cardTitle = computed(() => props.title ?? props.model.title ?? '');
</script>

<template>
    <a :href="route" class="hoverable-image" :style="styleImage">
        <div class="hoverable-image-title">
            {{ cardTitle }}
        </div>
    </a>
</template>
