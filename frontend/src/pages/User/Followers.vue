<script setup>
import { ref, reactive, onMounted, computed, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { BTab, BTabs } from 'bootstrap-vue-next';
import { isNotEmpty } from '@/Services/MiscService.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const FollowerCard = defineAsyncComponent(() => import('@/Components/Cards/FollowerCard.vue'));

const props = defineProps({
    user: {
        required: true,
        type: Object,
    },
    patrons: {
        required: true,
        type: Array,
        default: [],
    },
    free_followers: {
        required: true,
        type: Array,
        default: [],
    },
});

const pageTitle = computed(() =>
    props.user.own_profile
        ? $t('pages.user.followers.my')
        : $t('pages.user.followers.other', { name: props.user.name }),
);
</script>

<template>
    
    <UserLayout :title="pageTitle" :user="user" :overflow="false">
        <BTabs
            nav-wrapper-class="tabs-header w-100"
            nav-class="tab-item px-4 w-100"
            active-nav-item-class="tab-item-active"
            nav-item-class="tab-item default-text-color px-4"
            tab-class="py-4 container-fluid w-100 vh-100 overflow-y-auto"
            v-if="user.own_profile"
            fill
        >
            <BTab :title="$t('pages.user.followers.free_count', free_followers.length)">
                <div v-if="isNotEmpty(free_followers)" class="row w-100">
                    <FollowerCard v-for="user in free_followers" :user="user" />
                </div>
                <div v-else class="d-flex flex-row align-items-center text-center justify-content-center w-100">
                    {{ $t('pages.user.user_no_followers', { name: user.name }) }}
                </div>
            </BTab>
            <BTab :title="$t('pages.user.followers.patrons_count', patrons.length)">
                <div v-if="isNotEmpty(patrons)" class="row w-100">
                    <FollowerCard v-for="user in patrons" :user="user" />
                </div>
                <div v-else class="d-flex flex-row align-items-center text-center justify-content-center w-100">
                    {{ $t('pages.user.user_no_followers', { name: user.name }) }}
                </div>
            </BTab>
        </BTabs>
        <template v-else>
            <div v-if="isNotEmpty(free_followers)" class="row w-100">
                <FollowerCard v-for="user in free_followers" :user="user" />
            </div>
            <div v-else class="d-flex flex-row align-items-center text-center justify-content-center w-100">
                {{ $t('pages.user.user_no_followers', { name: user.name }) }}
            </div>
        </template>
    </UserLayout>
</template>
