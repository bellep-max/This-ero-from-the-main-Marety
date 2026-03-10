<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { $t } from '@/i18n.js';
import { BFormCheckbox } from 'bootstrap-vue-next';
import { defineAsyncComponent } from 'vue';
const SettingsLayout = defineAsyncComponent(() => import('@/Layouts/SettingsLayout.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const page = usePage();
const user = page.props.auth.user;

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
    <Head :title="$t('menus.user_settings.preferences')" />
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
