<script setup>
import { Slide, Navigation } from 'vue3-carousel';
import { defineAsyncComponent } from 'vue';
const DefaultCarousel = defineAsyncComponent(() => import('@/Components/Carousels/DefaultCarousel.vue'));
const RoundButton = defineAsyncComponent(() => import('@/Components/Buttons/RoundButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const TestimonialCard = defineAsyncComponent(() => import('@/Components/Cards/TestimonialCard.vue'));
import 'vue3-carousel/carousel.css';

const props = defineProps({
    items: {
        type: Object,
        required: true,
        default: {},
    },
});

const carouselConfig = {
    itemsToShow: 'auto',
    wrapAround: true,
    gap: 24,
};
</script>

<template>
    <DefaultCarousel :config="carouselConfig">
        <template #slides>
            <Slide v-for="(slide, i) in items" :key="i">
                <template #default>
                    <TestimonialCard :item="slide" />
                </template>
            </Slide>
        </template>
        <template #navigation>
            <Navigation>
                <template #prev>
                    <RoundButton class-list="btn-white">
                        <Icon :icon="['fas', 'arrow-left']" size="1x" />
                    </RoundButton>
                </template>
                <template #next>
                    <RoundButton class-list="btn-white">
                        <Icon :icon="['fas', 'arrow-right']" size="1x" />
                    </RoundButton>
                </template>
            </Navigation>
        </template>
    </DefaultCarousel>
</template>

<style scoped></style>
