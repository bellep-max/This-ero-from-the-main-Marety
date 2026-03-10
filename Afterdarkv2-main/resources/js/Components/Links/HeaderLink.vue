<script setup>
const props = defineProps({
    linkItem: {
        required: true,
    },
});
</script>

<template>
    <a class="navigation-link text-center" :class="{ active: linkItem.active }" :href="linkItem.route">
        <span class="menu-title">{{ $t(linkItem.key) }}</span>
    </a>
</template>
