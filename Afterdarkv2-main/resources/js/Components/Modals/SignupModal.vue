<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { BFormCheckbox, BFormInput, BFormRadio, BFormRadioGroup, BInput, BInputGroup } from 'bootstrap-vue-next';
import { defineAsyncComponent, onMounted, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Roles from '@/Enums/Roles.js';
import signupImage from '@/assets/images/signup.webp';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const emit = defineEmits(['close']);

const form = useForm({
    email: null,
    name: null,
    username: null,
    password: null,
    password_confirmation: null,
    over_18: null,
    role: Roles.Listener,
});

const isLoading = ref(true);
const isCustomUsername = ref(false);
const username = ref('');
const currentLocation = ref('');
const customLocation = ref('');

const signup = () => {
    if (!isCustomUsername.value) {
        form.username = slugify(username.value);
    }

    form.post(route('register.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            emit('close');
            form.reset('email', 'name', 'username', 'password', 'password_confirmation', 'over_18');

            username.value = '';
            isCustomUsername.value = false;
        },
    });
};

const setCurrentLocation = () => {
    currentLocation.value = `${window.location.origin}/user/`;
};

const slugify = (text) => {
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/--+/g, '-')
        .trim();
};

watch(
    () => form.name,
    (newValue) => {
        if (newValue !== null) {
            username.value = slugify(newValue);
        }
    },
);

watch(username, (newValue) => {
    customLocation.value = currentLocation.value + newValue;
});

onMounted(() => {
    setCurrentLocation();
    customLocation.value = currentLocation.value;
});
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex p-3 p-md-0"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="signup-modal"
    >
        <div class="col col-md-5 d-flex flex-column justify-content-center align-items-center gap-3 ps-md-5">
            <div class="text-center font-default fs-5">
                {{ $t('misc.sign_up') }}
            </div>
            <div class="col-12">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                    <label class="font-default fs-14 default-text-color">
                        {{ $t('misc.email') }}
                    </label>
                    <BInput type="email" v-model="form.email" />
                    <div v-if="form.errors?.email">{{ form.errors.email }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                    <label class="font-default fs-14 default-text-color">
                        {{ $t('misc.name') }}
                    </label>
                    <BInput type="text" v-model="form.name" />
                    <div v-if="form.errors?.name">{{ form.errors.name }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                    <div class="d-flex flex-row justify-content-between align-items-center w-100">
                        <label class="font-default fs-14 default-text-color">
                            {{ $t('misc.username') }}
                        </label>
                        <BFormCheckbox v-model="isCustomUsername">
                            {{ $t('modals.custom_username') }}
                        </BFormCheckbox>
                    </div>
                    <BInputGroup v-if="isCustomUsername" :prepend="currentLocation">
                        <BFormInput type="text" v-model="form.username" />
                    </BInputGroup>
                    <BInput v-else type="text" plaintext :placeholder="customLocation" />
                    <div v-if="form.errors?.username">{{ form.errors.username }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                    <label class="font-default fs-14 default-text-color">
                        {{ $t('misc.password') }}
                    </label>
                    <BInput type="password" v-model="form.password" />
                    <div v-if="form.errors?.password">{{ form.errors.password }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                    <label class="font-default fs-14 default-text-color">
                        {{ $t('misc.retype_password') }}
                    </label>
                    <BInput type="password" v-model="form.password_confirmation" />
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                    <label class="font-default fs-14 default-text-color">
                        {{ $t('misc.choose_role') }}
                    </label>
                    <BFormRadioGroup v-model="form.role">
                        <BFormRadio
                            v-for="role in Roles.options"
                            :value="role.value"
                            :v-b-tooltip.click.bottom="role.description"
                            >{{ role.text }}</BFormRadio
                        >
                    </BFormRadioGroup>
                </div>
            </div>
            <BFormCheckbox v-model="form.over_18">
                {{ $t('modals.signup_agreement') }}
            </BFormCheckbox>
            <div v-if="form.errors?.email">{{ form.errors.has_18 }}</div>
            <DefaultButton class-list="btn-pink" @click="signup">
                {{ $t('buttons.sign_up') }}
            </DefaultButton>
        </div>
        <div class="col d-none d-md-flex image-container">
            <div v-if="isLoading" class="loading-state">
                {{ $t('misc.loading') }}
            </div>
            <img
                v-show="!isLoading"
                :src="signupImage"
                class="object-fit-scale"
                alt="Signup image"
                @load="isLoading = false"
            />
        </div>
    </VueFinalModal>
</template>

<style scoped></style>
