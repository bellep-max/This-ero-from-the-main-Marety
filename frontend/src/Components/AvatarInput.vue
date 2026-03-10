<script setup>
import { $t } from '@/i18n.js';
import { ref, reactive, computed, defineAsyncComponent, onMounted } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRouter } from 'vue-router'
import apiClient from '@/api/client'
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
import route from "@/helpers/route"

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
});

const avatarInput = ref(null);
const avatarPreview = ref(null);

const form = useForm({
    _method: 'patch',
    artwork: null,
});

const setImagePreview = ($event) => {
    const image = $event.target.files[0];
    form.artwork = image;
    avatarPreview.value = URL.createObjectURL(image);
};

const resetImage = () => {
    avatarPreview.value = props.user.artwork;
    form.reset();
};

const imageSelected = computed(() => form.artwork);

const isSubscribed = computed(() => props.user.subscription);

const saveImage = () => {
    apiClient.post(route('settings.profile.avatar.update'), form, {
        onSuccess: (response) => {
            form.reset();
        },
        onError: (error) => {},
    });
};

onMounted(() => {
    resetImage();
});
</script>

<template>
    <div class="d-flex flex-column justify-content-start align-items-center gap-2 text-center">
        <span v-if="user.own_profile" class="font-default fs-14">
            {{ $t('pages.settings.profile_image_title') }}
        </span>
        <div class="position-relative">
            <Icon
                v-if="user.own_profile && isSubscribed"
                :icon="['fas', 'star']"
                :class-list="{
                    'position-absolute top-0 start-0 translate-middle': true,
                    'color-yellow': user.subscription.status === 'active',
                    'color-grey': user.subscription.status !== 'active',
                }"
            />
            <img :src="avatarPreview" :alt="user.name" class="profile-img-lg rounded-circle" />
            <span class="p-1 rounded-pill bg-pink color-light position-absolute top-0 start-100 translate-middle">
                {{ user.role }}
            </span>
        </div>
        <div v-if="user.own_profile" class="mt-3 d-flex flex-column gap-2">
            <label v-if="!imageSelected" for="avatar-input" class="cursor-pointer btn-default btn-pink btn-narrow">
                {{ $t('buttons.select_photo') }}
            </label>
            <div v-else class="d-flex flex-row justify-content-center align-items-center gap-2 w-100">
                <DefaultButton class-list="btn-outline btn-narrow" @click="resetImage" :disabled="form.processing">
                    {{ $t('buttons.cancel') }}
                </DefaultButton>
                <DefaultButton class-list="btn-pink btn-narrow" @click="saveImage" :disabled="form.processing">
                    {{ $t('buttons.save_changes') }}
                </DefaultButton>
            </div>
            <input
                type="file"
                id="avatar-input"
                ref="avatarInput"
                class="d-none"
                accept="image/*"
                @change="setImagePreview"
            />

            <div class="font-merge color-grey fs-14">
                {{ $t('pages.settings.profile_image_description') }}
            </div>
        </div>
    </div>
</template>

<style scoped></style>
