<script setup>
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { isLogged } from '@/Services/AuthService.js';
import { useModal, useVfm } from 'vue-final-modal';
const TextareaEmojiPicker = defineAsyncComponent(() => import('@/Components/FormItems/TextareaEmojiPicker.vue'));
const Comment = defineAsyncComponent(() => import('@/Components/Comment.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const LoginModal = defineAsyncComponent(() => import('@/Components/Modals/LoginModal.vue'));

const props = defineProps({
    comments: {
        type: Array,
        default: [],
    },
    type: {
        type: String,
        required: true,
    },
    uuid: {
        type: String,
        required: true,
    },
});

const vfm = useVfm();
const emit = defineEmits(['commented']);

const form = useForm({
    parent_id: null,
    content: '',
    commentable_type: props.type,
    uuid: props.uuid,
});

const emptyInput = ref(true);
const isResponse = ref(false);
const responseComment = ref({});

const hasComments = computed(() => props.comments.length > 0);

const setInput = (event) => {
    form.content = event;
};

const setReply = (comment) => {
    form.parent_id = comment.id;
    responseComment.value = comment;
};

const resetReply = () => {
    form.parent_id = null;
    responseComment.value = {};
};

const checkIsReplied = (comment) => {
    return !!form.parent_id && responseComment.value == comment;
};

const submit = () => {
    form.post(route('comments.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            emit('commented');
            form.reset('content', 'parent_id');
        },
    });
};

const openLoginModal = () =>
    useModal({
        component: LoginModal,
        attrs: {
            onClose() {
                vfm.close('login-modal');
            },
            onConfirm() {
                vfm.close('login-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

watch(
    () => form.content,
    (newValue) => {
        emptyInput.value = newValue.length === 0;
    },
);

watch(
    () => form.parent_id,
    (newValue) => {
        isResponse.value = !!newValue;
    },
);
</script>

<template>
    <div v-if="isLogged" class="col d-flex flex-column align-items-start gap-4">
        <div class="d-flex flex-row align-items-center gap-4 w-100">
            <TextareaEmojiPicker
                v-model="form.content"
                :response="responseComment"
                @input="setInput"
                @cancel="resetReply"
            />
            <DefaultButton class-list="btn-rounded btn-pink" :disabled="emptyInput" @click="submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="39" height="40" viewBox="13 21 38 22">
                    <path
                        d="M48.3105 17.915C48.1514 17.6903 47.9317 17.5154 47.6771 17.4108C47.4224 17.3061 47.1432 17.276 46.8721 17.3239L14.3178 23.1488L13.614 25.7752L24.4582 33.888L29.0373 47.9616L31.6643 48.6655L48.3838 19.4686C48.52 19.2293 48.5855 18.9562 48.5725 18.6812C48.5595 18.4061 48.4687 18.1404 48.3105 17.915ZM30.7136 45.7259L26.7776 33.6288L40.1067 25.1034L38.8751 23.1778L25.4418 31.7696L16.5008 25.0805L45.5078 19.89L30.7136 45.7259Z"
                    />
                </svg>
            </DefaultButton>
        </div>
        <template v-if="hasComments">
            <template v-for="comment in comments">
                <Comment :comment="comment" :replied="checkIsReplied(comment)" @reply="setReply" @cancel="resetReply" />
                <Comment
                    v-for="reply in comment.replies"
                    :comment="reply"
                    :replied="checkIsReplied(reply)"
                    @reply="setReply"
                    @cancel="resetReply"
                />
            </template>
        </template>
        <span v-else class="w-100 text-center color-light font-default fs-5">
            {{ $t('misc.no_comments') }}
        </span>
    </div>
    <a v-else href="javascript:void(0)" class="w-100 text-center color-light font-default fs-5" @click="openLoginModal">
        {{ $t('misc.login_to_comment') }}
    </a>
</template>
