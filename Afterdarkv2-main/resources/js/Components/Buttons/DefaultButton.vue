<script setup>
import { computed } from 'vue';

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
    href: {
        type: String,
        required: false,
        default: '',
    },
    external: {
        type: Boolean,
        required: false,
        default: false,
    },
});

const hasLink = computed(() => props.href.length > 0);
const target = computed(() => (props.external ? '_blank' : '_self'));
</script>

<template>
    <a v-if="hasLink" :href="href" :target="target" class="btn-default" :class="classList">
        <slot />
    </a>
    <button v-else class="btn-default" :class="classList" :disabled="disabled">
        <slot />
    </button>
</template>
