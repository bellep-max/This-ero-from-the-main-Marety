<script setup>
import { $t } from '@/i18n.js';
import { defineAsyncComponent, ref } from 'vue';
import { BOffcanvas } from 'bootstrap-vue-next';
import { useAuthStore } from '@/stores/auth';
import { useModal } from 'vue-final-modal';
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const ProfileButton = defineAsyncComponent(() => import('@/Components/Buttons/ProfileButton.vue'));
const AvatarInput = defineAsyncComponent(() => import('@/Components/AvatarInput.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const LinktreeButton = defineAsyncComponent(() => import('@/Components/Buttons/LinktreeButton.vue'));
const SubscriptionButton = defineAsyncComponent(() => import('@/Components/Buttons/SubscriptionButton.vue'));
const UserFollowButton = defineAsyncComponent(() => import('@/Components/Buttons/UserFollowButton.vue'));
const ShareModal = defineAsyncComponent(() => import('@/Components/Modals/ShareModal.vue'));

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
});

const currentUser = useAuthStore().user;
const showMenu = ref(false);

const setMenu = () => {
    showMenu.value = !showMenu.value;
};

const { open, close } = useModal({
    component: ShareModal,
    attrs: {
        title: props.user.name,
        item: props.user,
        onClose() {
            close();
        },
        onConfirm() {
            close();
        },
    },
    clickToClose: true,
    escToClose: true,
});
</script>

<template>
    <DefaultButton class-list="btn-outline d-xl-none" @click="setMenu">
        {{ $t('buttons.show') }}
    </DefaultButton>
    <BOffcanvas v-model="showMenu" responsive="xl" placement="start">
        <div class="d-flex flex-column w-100 bg-default rounded-5 px-lg-3 py-lg-4 gap-3">
            <div class="d-flex flex-column justify-content-start align-items-center gap-4 text-center">
                <AvatarInput :user="user" />
                <div class="fs-4 font-default">
                    {{ user.name }}
                </div>
            </div>
            <div class="d-flex flex-column justify-content-start align-items-center gap-1">
                <UserFollowButton v-if="!user.own_profile" :current-user="currentUser" :user="user" />
                <SubscriptionButton v-if="!user.own_profile && currentUser.allow_upload" :user="user" />
                <DefaultButton class-list="btn-outline w-100" @click="open">
                    <Icon :icon="['fas', 'share-nodes']" />
                    {{ $t('buttons.share.title') }}
                </DefaultButton>
                <LinktreeButton :own-profile="user.own_profile" :linktree_link="user.linktree_link" />
            </div>
            <div class="w-100 d-flex flex-column gap-1">
                <ProfileButton v-for="menuItem in user.menu" :menuItem="menuItem" />
            </div>
        </div>
    </BOffcanvas>
</template>
