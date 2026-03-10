<script setup>
import { computed, defineAsyncComponent } from 'vue';
import { useRouter } from 'vue-router';
import {
    BCollapse,
    BDropdown,
    BDropdownDivider,
    BDropdownItem,
    BDropdownItemButton,
    BNavbar,
    BNavbarBrand,
    BNavbarToggle,
} from 'bootstrap-vue-next';
import { $t } from '@/i18n';
import { useModal, useVfm } from 'vue-final-modal';
import { useAuthStore } from '@/stores/auth';
import { useAppStore } from '@/stores/app';
import apiClient from '@/api/client';
import { AUTH } from '@/api/endpoints';
const HeaderLink = defineAsyncComponent(() => import('@/Components/Links/HeaderLink.vue'));
const SignupModal = defineAsyncComponent(() => import('@/Components/Modals/SignupModal.vue'));
const LoginModal = defineAsyncComponent(() => import('@/Components/Modals/LoginModal.vue'));
const DarkModeButton = defineAsyncComponent(() => import('@/Components/Buttons/DarkModeButton.vue'));
const FeedbackModal = defineAsyncComponent(() => import('@/Components/Modals/FeedbackModal.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const ShareModal = defineAsyncComponent(() => import('@/Components/Modals/ShareModal.vue'));
const Search = defineAsyncComponent(() => import('@/Components/Search.vue'));
const NotificationShortCard = defineAsyncComponent(() => import('@/Components/Cards/NotificationShortCard.vue'));

const authStore = useAuthStore();
const appStore = useAppStore();
const vfm = useVfm();
const router = useRouter();

const logout = async () => {
    await authStore.logout();
    router.push('/');
};

const user = computed(() => authStore.user);

const menuItems = computed(() => appStore.menu);
const userMenu = computed(() => authStore.userMenu);
const unreadNotifications = computed(() => user.value?.unread_notifications);
const hasNotifications = computed(() => user.value?.unread_notifications?.length > 0);
const isSubscribed = computed(() => user.value?.subscription);

const userIcon = computed(() => {
    switch (user.value?.role) {
        case 'admin':
            return 'crown';
        case 'listener':
            return 'headphones';
        case 'creator':
            return 'music';
    }
});

const openFeedbackModal = () =>
    useModal({
        component: FeedbackModal,
        attrs: {
            closeModal() {
                vfm.close('feedback-modal');
            },
            onConfirm() {
                vfm.close('feedback-modal');
            },
        },
    }).open();

const openShareModal = () =>
    useModal({
        component: ShareModal,
        attrs: {
            item: user,
            closeModal() {
                vfm.close('share-modal');
            },
            onConfirm() {
                vfm.close('share-modal');
            },
        },
    }).open();

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
    }).open();

const openSignupModal = () =>
    useModal({
        component: SignupModal,
        attrs: {
            onClose() {
                vfm.close('signup-modal');
            },
            onConfirm() {
                vfm.close('signup-modal');
            },
        },
    }).open();
</script>

<template>
    <BNavbar toggleable="lg" container sticky="top" class="px-2 py-0 py-lg-4 bg-default z-2">
        <BNavbarBrand class="header__content__logo">
            <router-link to="/">
                <svg width="142" height="53" viewBox="0 0 142 53" xmlns="http://www.w3.org/2000/svg">
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M17.6462 33.8095C17.0019 34.3624 15.9899 34.8834 14.6126 35.3727C13.0328 35.9327 11.4161 36.2129 9.7549 36.2129C6.97307 36.2129 4.65281 35.3018 2.79168 33.4797C0.93055 31.6578 0 29.4526 0 26.8645C0 24.5083 0.871319 22.4867 2.61644 20.7998C4.36156 19.113 6.52136 18.2695 9.09831 18.2695C11.7493 18.2695 13.9313 19.1354 15.6444 20.8675C17.3574 22.5992 18.2016 24.843 18.1744 27.5986H1.64145C1.92531 29.6975 2.80403 31.3873 4.27763 32.6686C5.75123 33.9497 7.57782 34.5905 9.7549 34.5905C10.9644 34.5905 12.2331 34.3908 13.5586 33.9916C14.8841 33.5925 15.9677 33.0645 16.8045 32.4079C16.8119 32.4018 16.8193 32.3956 16.8267 32.3897L17.6462 33.8095ZM16.533 25.9567C16.2121 24.1669 15.3654 22.712 13.993 21.5917C12.6231 20.4715 10.9891 19.9112 9.09831 19.9112C7.20509 19.9112 5.57597 20.4651 4.21098 21.5724C2.84598 22.6797 1.99688 24.1412 1.66119 25.9567H16.533Z"
                        fill="black"
                    />
                    <path
                        d="M21.8662 17.8427H23.4786V21.0497C23.8131 20.5719 24.2012 20.1121 24.6432 19.6701C26.0645 18.2488 27.5216 17.5023 29.0146 17.4307V19.1864C27.9994 19.2342 26.9483 19.8015 25.8614 20.8884C24.3326 22.4053 23.5384 24.2267 23.4786 26.3528V35.4361H21.8662V17.8427Z"
                        fill="black"
                    />
                    <path
                        d="M89.0966 30.8074L90.7986 31.4165C90.4881 31.8465 90.1357 32.2586 89.7416 32.6527C87.8186 34.5757 85.4955 35.5372 82.7723 35.5372C80.0611 35.5372 77.7439 34.5757 75.821 32.6527C73.898 30.7298 72.9365 28.4067 72.9365 25.6835C72.9365 22.9722 73.898 20.6551 75.821 18.7321C77.7439 16.8091 80.0611 15.8477 82.7723 15.8477C85.4955 15.8477 87.8186 16.8091 89.7416 18.7321C90.1357 19.1263 90.4881 19.5324 90.7986 19.9504L89.0966 20.5774C88.9175 20.3625 88.7264 20.1475 88.5233 19.9325C86.9348 18.3559 85.0178 17.5676 82.7723 17.5676C80.5388 17.5676 78.6278 18.3618 77.0393 19.9504C75.4507 21.527 74.6564 23.438 74.6564 25.6835C74.6564 27.9289 75.4507 29.8459 77.0393 31.4345C78.6278 33.023 80.5388 33.8173 82.7723 33.8173C85.0178 33.8173 86.9348 33.023 88.5233 31.4345C88.7264 31.2314 88.9175 31.0224 89.0966 30.8074Z"
                        fill="black"
                    />
                    <path
                        d="M114.108 35.0355H112.495V31.4524C112.185 31.8704 111.844 32.2705 111.474 32.6527C109.551 34.5757 107.228 35.5372 104.505 35.5372C101.793 35.5372 99.4764 34.5757 97.5534 32.6527C95.6304 30.7298 94.6689 28.4126 94.6689 25.7014C94.6689 22.9782 95.6304 20.6551 97.5534 18.7321C99.4764 16.8091 101.793 15.8477 104.505 15.8477C107.228 15.8477 109.551 16.8091 111.474 18.7321C111.844 19.1143 112.185 19.5144 112.495 19.9325V17.4422H114.108V35.0355ZM112.495 27.2959V24.1069C112.197 22.5303 111.45 21.1448 110.256 19.9504C108.667 18.3618 106.75 17.5676 104.505 17.5676C102.259 17.5676 100.342 18.3618 98.7538 19.9504C97.1652 21.5389 96.3709 23.4559 96.3709 25.7014C96.3709 27.9468 97.1652 29.8638 98.7538 31.4524C100.342 33.029 102.259 33.8173 104.505 33.8173C106.75 33.8173 108.667 33.023 110.256 31.4345C111.45 30.2401 112.197 28.8605 112.495 27.2959Z"
                        fill="black"
                    />
                    <path
                        d="M124.742 35.5094C121.892 35.5094 119.624 34.7644 118.069 33.7278C117.745 33.4686 117.713 33.1447 117.94 32.8207L118.069 32.6263C118.296 32.27 118.588 32.2052 118.976 32.432C120.434 33.3714 122.572 33.9545 124.678 33.9545C127.69 33.9545 129.569 32.8855 129.569 30.5855C129.569 27.9616 127.075 27.2813 124.386 26.6334C121.114 25.8883 117.519 25.2405 117.519 21.3855C117.519 17.5306 121.244 16.332 123.965 16.332C126.135 16.332 128.111 16.9799 129.375 17.6926C129.731 17.9194 129.764 18.2433 129.569 18.5996L129.44 18.8264C129.213 19.1827 128.889 19.2475 128.533 19.0531C127.172 18.2757 125.488 17.887 123.771 17.887C122.151 17.887 119.138 18.632 119.138 21.3207C119.138 23.9123 121.924 24.4306 124.807 25.1109C127.917 25.8235 131.189 26.6982 131.189 30.5855C131.189 33.9545 128.371 35.5094 124.742 35.5094Z"
                        fill="black"
                    />
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M132.273 14.8881H134.345C134.399 14.8881 134.443 14.845 134.443 14.7919V11.3309L135.524 9.86167C135.668 9.6727 135.826 9.59916 135.955 9.62668C136.072 9.65152 136.15 9.76257 136.15 9.91842V14.7919C136.15 14.845 136.194 14.8881 136.245 14.8881H141.047C141.267 14.8881 141.435 14.9421 141.547 15.0549C141.659 15.1672 141.715 15.3354 141.715 15.5519V15.8181C141.715 16.0346 141.659 16.2029 141.547 16.3151C141.435 16.4279 141.267 16.482 141.047 16.482H136.245C136.194 16.482 136.15 16.5251 136.15 16.5782V32.017C136.15 33.1808 136.513 33.9903 137.107 34.4896C137.994 35.234 139.404 35.2915 140.926 34.7767C140.928 34.7764 140.928 34.776 140.928 34.776C141.215 34.6737 141.427 34.6612 141.581 34.7406C141.678 34.7903 141.746 34.876 141.797 34.991L141.912 35.2559C141.997 35.4573 142.026 35.6332 141.973 35.7844C141.917 35.9351 141.778 36.0557 141.544 36.1563C141.544 36.1563 141.542 36.1567 141.542 36.1572C141.055 36.3818 140.117 36.7186 138.99 36.7186C137.589 36.7186 136.452 36.2931 135.665 35.5102C134.876 34.7272 134.443 33.5885 134.443 32.1695V16.5782C134.443 16.5251 134.399 16.482 134.345 16.482H131.732C131.964 15.8103 132.273 14.8881 132.273 14.8881Z"
                        fill="black"
                    />
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M33.0383 26.1128C33.034 26.229 32.9363 26.3196 32.8202 26.3154C32.7042 26.311 32.6135 26.2134 32.6179 26.0973C32.784 21.623 34.5734 17.1981 37.986 13.7855C41.3984 10.3731 45.8233 8.5837 50.2976 8.4176C50.4137 8.41316 50.5113 8.50385 50.5157 8.61993C50.5199 8.73601 50.4294 8.83373 50.3133 8.83792C45.9415 9.00033 41.6179 10.7488 38.2835 14.0831C34.9491 17.4176 33.2008 21.7411 33.0383 26.1128ZM60.5434 11.5725C60.4451 11.5107 60.4156 11.3807 60.4774 11.2825C60.5392 11.1841 60.6692 11.1544 60.7676 11.2163C61.9097 11.9351 62.9898 12.7913 63.984 13.7855C69.8249 19.6264 70.9106 28.4332 67.2411 35.3768C67.1869 35.4793 67.0594 35.5187 66.9568 35.4644C66.854 35.4102 66.8148 35.2828 66.8691 35.18C70.4545 28.3956 69.3937 19.7902 63.6865 14.083C62.7151 13.1116 61.6596 12.2747 60.5434 11.5725ZM59.3804 42.6686C59.4831 42.6145 59.6105 42.6537 59.6647 42.7565C59.719 42.8591 59.6796 42.9866 59.5771 43.0408C52.6336 46.7102 43.8267 45.6246 37.9858 39.7837C36.9917 38.7895 36.1354 37.7094 35.4167 36.5671C35.3548 36.4688 35.3844 36.3389 35.4828 36.2771C35.5811 36.2152 35.711 36.2448 35.7728 36.3431C36.475 37.4593 37.312 38.5147 38.2834 39.4861C43.9906 45.1933 52.5959 46.2542 59.3804 42.6686Z"
                        fill="#E836C5"
                        stroke="#E836C5"
                        stroke-width="1.5355"
                        stroke-miterlimit="2"
                        stroke-linejoin="round"
                    />
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M38.4089 18.8831C38.3471 18.9813 38.2171 19.0111 38.1188 18.9493C38.0204 18.8876 37.9907 18.7576 38.0524 18.6593C40.0311 15.5085 43.1573 13.0676 47.0309 12.0297C50.9044 10.9918 54.8322 11.5425 58.1212 13.2819C58.2238 13.3362 58.263 13.4636 58.2087 13.5662C58.1546 13.6688 58.027 13.7081 57.9244 13.6539C54.7262 11.9625 50.9065 11.4267 47.1398 12.436C43.3729 13.4454 40.333 15.8191 38.4089 18.8831ZM64.1206 19.8425C64.0665 19.7399 64.1057 19.6124 64.2085 19.5583C64.3112 19.5041 64.4386 19.5434 64.4929 19.6461C65.0161 20.6378 65.4376 21.703 65.74 22.8314C67.5165 29.4614 64.6388 36.2502 59.1132 39.7224C59.0149 39.7842 58.8849 39.7544 58.8231 39.6561C58.7613 39.5578 58.7909 39.4278 58.8893 39.366C64.2626 35.9895 67.0612 29.3877 65.3336 22.9402C65.0396 21.8429 64.6297 20.807 64.1206 19.8425ZM50.4293 41.6329C50.5454 41.6372 50.636 41.735 50.6317 41.851C50.6273 41.967 50.5296 42.0578 50.4135 42.0535C43.8923 41.8092 38.0056 37.3689 36.2291 30.7388C35.9267 29.6104 35.7592 28.4772 35.7167 27.3567C35.7122 27.2406 35.8028 27.1429 35.9189 27.1384C36.0349 27.134 36.1327 27.2247 36.1371 27.3406C36.1785 28.4305 36.3415 29.5325 36.6356 30.6299C38.3632 37.0773 44.0876 41.3954 50.4293 41.6329Z"
                        fill="#E836C5"
                        stroke="#E836C5"
                        stroke-width="1.53559"
                        stroke-miterlimit="2"
                        stroke-linejoin="round"
                    />
                </svg>
            </router-link>
        </BNavbarBrand>

        <BNavbarToggle target="nav-collapse" />

        <BCollapse id="nav-collapse" is-nav>
            <div class="d-flex flex-row align-items-center gap-2 gap-lg-3 ms-auto">
                <template v-if="menuItems">
                    <HeaderLink v-for="item in menuItems" :key="item.key" :link-item="item" />
                </template>

                <Search />
                <DarkModeButton />

                <template v-if="authStore.isLogged">
                    <BDropdown v-if="hasNotifications" no-wrapper no-caret toggle-class="btn-default p-2 btn-icon">
                        <template #button-content>
                            <Icon :icon="['fas', 'bell']" />
                            <span class="badge bg-danger rounded-pill">{{ unreadNotifications?.length }}</span>
                        </template>
                        <NotificationShortCard
                            v-for="notification in unreadNotifications"
                            :key="notification.id"
                            :notification="notification"
                        />
                    </BDropdown>

                    <BDropdown no-wrapper no-caret toggle-class="btn-default p-2 btn-icon">
                        <template #button-content>
                            <img
                                v-if="user?.artwork"
                                :src="user.artwork"
                                :alt="user.name"
                                class="rounded-circle"
                                width="32"
                                height="32"
                            />
                            <Icon v-else :icon="['fas', 'user']" />
                        </template>
                        <BDropdownItem v-if="user" :href="`/user/${user.username}`">
                            <Icon :icon="['fas', userIcon]" class="me-2" />
                            {{ user.name }}
                        </BDropdownItem>
                        <BDropdownDivider />
                        <template v-if="userMenu">
                            <BDropdownItem v-for="item in userMenu" :key="item.key" :href="item.route">
                                {{ $t(item.key) }}
                            </BDropdownItem>
                        </template>
                        <BDropdownDivider />
                        <BDropdownItemButton @click="openFeedbackModal">
                            {{ $t('menus.feedback') }}
                        </BDropdownItemButton>
                        <BDropdownItemButton @click="openShareModal">
                            {{ $t('menus.share') }}
                        </BDropdownItemButton>
                        <BDropdownDivider />
                        <BDropdownItemButton @click="logout">
                            {{ $t('menus.logout') }}
                        </BDropdownItemButton>
                    </BDropdown>
                </template>

                <template v-else>
                    <DefaultButton class-list="btn-outline" @click="openLoginModal">
                        {{ $t('menus.login') }}
                    </DefaultButton>
                    <DefaultButton class-list="btn-pink" @click="openSignupModal">
                        {{ $t('menus.signup') }}
                    </DefaultButton>
                </template>
            </div>
        </BCollapse>
    </BNavbar>
</template>
