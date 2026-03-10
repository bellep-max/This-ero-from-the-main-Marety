<script setup>
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent } from 'vue';
import { isNotEmpty } from '@/Services/MiscService.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));

const props = defineProps({
    menuItem: {
        required: true,
        type: Object,
    },
});

const hasStats = computed(() => props.menuItem.stats !== null);
</script>

<template>
    <a
        class="btn-default btn-profile w-100"
        :class="{
            active: menuItem.active,
        }"
        :href="menuItem.route"
    >
        <Icon v-if="isNotEmpty(menuItem.icon)" :icon="['fas', menuItem.icon]" />
        {{ $t(menuItem.key) }}
        <span v-if="hasStats" class="ms-auto">
            {{ menuItem.stats }}
        </span>
    </a>
</template>
