<script setup>
import { useDark, useToggle } from '@vueuse/core';
import { defineAsyncComponent, onBeforeMount, ref, watch } from 'vue';
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));

const icon = ref('');
const isDark = useDark({
    attribute: 'data-bs-theme',
    valueDark: 'dark',
    valueLight: 'light',
});

const toggleDark = useToggle(isDark);

const setModeIcon = (currentMode) => {
    icon.value = currentMode ? 'sun' : 'moon';
};

watch(isDark, (newValue) => {
    setModeIcon(newValue);
});

onBeforeMount(() => {
    setModeIcon(isDark.value);
});
</script>

<template>
    <IconButton :icon="icon" class-list="dark-mode-button" @click="toggleDark()"> </IconButton>
</template>

<style scoped>
.dark-mode-button {
    width: 28px;
    height: 56px;
}
</style>
