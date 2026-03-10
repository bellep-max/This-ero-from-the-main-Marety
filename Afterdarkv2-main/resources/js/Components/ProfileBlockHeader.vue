<script setup>
const props = defineProps({
    title: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="d-flex flex-row justify-content start align-items-center gap-3 pb-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="39" height="40" viewBox="0 0 39 40" fill="none">
            <circle cx="19.5" cy="20.4336" r="18.5" fill="white" stroke="#E836C5" stroke-width="2" />
            <path d="M29.8232 20.4329L14.3379 29.3733L14.3379 11.4925L29.8232 20.4329Z" fill="#E836C5" />
        </svg>
        <span class="fs-4 font-default">{{ title }}</span>
    </div>
</template>
