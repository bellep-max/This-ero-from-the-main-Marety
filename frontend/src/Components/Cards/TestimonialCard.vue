<script setup>
import { defineAsyncComponent } from 'vue';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="testimonials_card_item">
        <div class="text-center fs-3 color-light">
            <Icon :icon="['fas', 'quote-left']" />
            {{ item.title }}
            <Icon :icon="['fas', 'quote-right']" />
        </div>
        <div class="testimonials_card_item_text">
            {{ item.description }}
        </div>
        <div class="d-flex flex-row justify-content-center align-items-center gap-1">
            <Icon v-for="n in item.rating" :icon="['fas', 'star']" :class-list="'rating_star'" />
        </div>
    </div>
</template>
