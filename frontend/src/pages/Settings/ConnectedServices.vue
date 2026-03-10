<script setup>
import { ref, reactive, onMounted, defineAsyncComponent } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { BTable } from 'bootstrap-vue-next';
import { isNotEmpty } from '@/Services/MiscService.js';
const SettingsLayout = defineAsyncComponent(() => import('@/Layouts/SettingsLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const props = defineProps({
    connections: {
        type: Array,
        default: [],
    },
});

const authStore = useAuthStore();
const user = authStore.user;

const form = useForm({
    name: user.name,
    email: user.email,
    bio: user.bio,
    birth: user.birth,
    gender: user.gender,
    country: user.country,
});

const submit = () => {
    form.patch(route('subscription.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    
    <SettingsLayout :title="$t('menus.user_settings.connect')">
        <div class="d-flex flex-column justify-content-center align-items-center gap-4">
            <template v-if="isNotEmpty(connections)">
                <BTable :items="connections" />
            </template>
            <template v-else>
                <img src="/assets/images/services.svg" :alt="$t('menus.user_settings.connect')" />
                <div class="profile_page__content__subscription__desc">
                    {{ $t('pages.settings.connect.description') }}
                </div>
                <DefaultButton
                    class-list="btn-pink btn-wide"
                    :href="route('settings.connections.redirect', 'spotify')"
                    external
                >
                    {{ $t('pages.settings.connect.connect_spotify') }}
                </DefaultButton>
            </template>
        </div>
    </SettingsLayout>
</template>
