<script setup>
import { Carousel } from 'vue3-carousel';
import 'vue3-carousel/carousel.css';

const props = defineProps({
    config: {
        type: {
            Object,
            default: {
                itemsToShow: 'auto',
                wrapAround: true,
                gap: 24,
            },
        },
    },
});
</script>

<template>
    <Carousel v-bind="config">
        <template #slides>
            <slot name="slides"></slot>
        </template>
        <template #addons>
            <slot name="navigation"></slot>
        </template>
    </Carousel>
</template>

<style>
.carousel__track {
    padding: 0.25rem 0 0.25rem 0;
}
</style>
