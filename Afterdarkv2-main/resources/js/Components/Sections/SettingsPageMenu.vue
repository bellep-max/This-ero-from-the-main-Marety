<script setup>
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent, ref } from 'vue';
import { BOffcanvas } from 'bootstrap-vue-next';
import { usePage } from '@inertiajs/vue3';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const ProfileButton = defineAsyncComponent(() => import('@/Components/Buttons/ProfileButton.vue'));
const AvatarInput = defineAsyncComponent(() => import('@/Components/AvatarInput.vue'));

const showMenu = ref(false);

const page = usePage();

const user = computed(() => page.props.auth.user);
const menu = computed(() => page.props.auth.pageMenu);

const setMenu = () => {
    showMenu.value = !showMenu.value;
};
</script>

<template>
    <DefaultButton class-list="btn-outline d-xl-none" @click="setMenu">
        {{ $t('buttons.filters') }}
    </DefaultButton>
    <BOffcanvas v-model="showMenu" responsive="xl" placement="start">
        <div class="d-flex flex-column w-100 bg-default rounded-5 px-3 py-4 gap-3">
            <div class="d-flex flex-column justify-content-start align-items-center gap-4 text-center">
                <AvatarInput :user="user" />
                <div class="fs-4 font-default">
                    {{ user.name }}
                </div>
            </div>
            <div class="w-100 d-flex flex-column gap-1">
                <ProfileButton v-for="menuItem in menu" :menuItem="menuItem" />
            </div>
        </div>
    </BOffcanvas>
</template>

<style scoped></style>
