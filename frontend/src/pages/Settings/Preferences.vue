<script setup>
import { ref, reactive, onMounted, defineAsyncComponent } from 'vue';
import { useForm } from '@/helpers/useForm'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/client'
import { $t } from '@/i18n.js';
import { BFormCheckbox } from 'bootstrap-vue-next';
const SettingsLayout = defineAsyncComponent(() => import('@/Layouts/SettingsLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const authStore = useAuthStore();
const user = authStore.user;

const form = useForm({
    restore_queue: user.restore_queue,
    play_pause_fade: user.play_pause_fade,
    allow_comments: user.allow_comments,
});

const onSubmit = (e) => {
    e.preventDefault();

    form.patch(route('settings.preferences.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    
    <SettingsLayout :title="$t('menus.user_settings.preferences')">
        <div class="d-flex flex-column align-items-start gap-4 ps-5">
            <BFormCheckbox v-model="form.restore_queue" switch>
                {{ $t('pages.settings.preferences.restore_songs') }}
            </BFormCheckbox>
            <BFormCheckbox v-model="form.play_pause_fade" switch>
                {{ $t('pages.settings.preferences.fade_on_play_pause') }}
            </BFormCheckbox>
            <BFormCheckbox v-model="form.allow_comments" switch>
                {{ $t('pages.settings.preferences.allow_comments') }}
            </BFormCheckbox>

            <DefaultButton @click="onSubmit" class-list="btn-pink mx-auto">
                {{ $t('buttons.save_changes') }}
            </DefaultButton>
        </div>
    </SettingsLayout>
</template>
