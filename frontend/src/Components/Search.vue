<script setup>
import { defineAsyncComponent, nextTick, ref, watch, reactive } from 'vue';
import { useForm } from '@/helpers/useForm'
import { BFormInput } from 'bootstrap-vue-next';
import { $t } from '@/i18n.js';
import apiClient from '@/api/client'
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));
import route from "@/helpers/route"

const showInput = ref(false);
const state = ref(false);
const inputRef = ref(null);

const form = useForm({
    search: null,
});

const toggleInput = () => {
    showInput.value = !showInput.value;

    if (showInput.value) {
        nextTick(() => {
            inputRef.value.focus();
        });
    }
};

const submit = () => {
    if (state.value) {
        form.get(route('search.show'));
    }
};

watch(
    () => form.search,
    (newValue) => {
        state.value = newValue.length > 3;
    },
);
</script>

<template>
    <IconButton icon="magnifying-glass" @click="toggleInput" />
    <Transition name="fold">
        <BFormInput
            ref="inputRef"
            type="text"
            v-if="showInput"
            v-model="form.search"
            :placeholder="$t('misc.search')"
            :state="state"
            @keyup.enter="submit"
        />
    </Transition>
</template>

<style scoped>
.fold-enter-active {
    transition:
        max-width 0.5s ease-out,
        padding 0.5s ease-out,
        opacity 0.5s ease-out;
    overflow: hidden;
}

.fold-leave-active {
    transition:
        max-width 0.5s cubic-bezier(0, 1, 0, 1),
        padding 0.5s cubic-bezier(0, 1, 0, 1),
        opacity 0.5s cubic-bezier(0, 1, 0, 1);
    overflow: hidden;
}

.fold-enter-from,
.fold-leave-to {
    max-width: 0;
    padding-left: 0;
    padding-right: 0;
    opacity: 0;
}

.fold-enter-to,
.fold-leave-from {
    max-width: 600px;
    padding-left: 20px;
    padding-right: 20px;
    opacity: 1;
}
</style>
