<script setup>
import { BFormTextarea } from 'bootstrap-vue-next';
import { defineAsyncComponent, nextTick, ref, watch } from 'vue';
const EmojiPicker = defineAsyncComponent(() => import('vue3-emoji-picker'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
import '../../../../node_modules/vue3-emoji-picker/dist/style.css';

const props = defineProps({
    response: {
        type: Object,
    },
});

const model = defineModel();

const emit = defineEmits(['input', 'cancel']);

const textInput = ref('');
const textarea = ref(null);
const showPicker = ref(false);
const hasResponse = ref(false);

const togglePicker = () => {
    showPicker.value = !showPicker.value;
};
const hidePicker = () => {
    showPicker.value = false;
};

const addEmoji = (emoji) => {
    const textareaElement = textarea.value.element;
    const cursorPosition = textareaElement.selectionEnd;

    const start = model.value.substring(0, textareaElement.selectionStart);
    const end = model.value.substring(textareaElement.selectionStart);
    const text = `${start}${emoji.i}${end}`;

    model.value = text;
    emit('input', text);
    textareaElement.focus();

    nextTick(() => {
        textareaElement.selectionEnd = cursorPosition + emoji.i.length;
    });
};

watch(
    () => props.response,
    (newValue) => {
        hasResponse.value = Object.keys(newValue).length > 0;
    },
);
</script>

<template>
    <div class="d-flex flex-column flex-grow-1">
        <div
            class="z-0 rounded-top-4 bg-secondary-subtle w-100 p-2 font-merge d-flex flex-row flex-grow-0 justify-content-between text-start"
            v-if="hasResponse"
        >
            <span>
                {{ response.content }}
            </span>
            <Icon :icon="['fas', 'xmark']" @click="$emit('cancel')" />
        </div>
        <div v-if="hasResponse"></div>
        <div class="textarea-emoji-picker w-100 z-1">
            <Icon
                :icon="['fas', 'face-smile']"
                class-list="color-pink emoji-trigger"
                @click="togglePicker"
                v-click-outside="hidePicker"
            />
            <EmojiPicker v-show="showPicker" class="emoji-picker-wrapper" @select="addEmoji" />
            <BFormTextarea
                rows="3"
                ref="textarea"
                no-resize
                v-model="model"
                @update:model-value="$emit('input', $event)"
            ></BFormTextarea>
        </div>
    </div>
</template>

<style scoped>
.textarea-emoji-picker {
    position: relative;
    margin: 0 auto;
}

.emoji-picker-wrapper {
    position: absolute;
    top: 33px;
    right: 10px;
}

.emoji-trigger {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    cursor: pointer;
    height: 20px;
}

.emoji-trigger:hover {
    color: var(--hover-color);
}
</style>
