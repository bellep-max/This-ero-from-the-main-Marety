<script setup>
import { computed } from 'vue';
import ImageLinkTypes from '@/Enums/ImageLinkTypes.js';

const props = defineProps({
    linkItem: {
        required: true,
    },
    type: {
        required: true,
        type: String,
    },
});

const getStyleString = computed(() => `background: url('${props.linkItem.artwork}')`);

const itemLink = computed(() => {
    switch (props.type) {
        case ImageLinkTypes.Genre:
            return route('genres.show', { genre: props.linkItem.slug });
        case ImageLinkTypes.Post:
            return route('posts.show', props.linkItem.uuid);
    }
});
</script>

<template>
    <a class="image-carousel-item" :href="itemLink" :style="getStyleString">
        <div class="image-carousel-item__title">
            {{ linkItem.title }}
        </div>
    </a>
</template>
