<script setup>
import { defineAsyncComponent } from 'vue';
const SettingsPageMenu = defineAsyncComponent(() => import('@/Components/Sections/SettingsPageMenu.vue'));

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    <SettingsPageMenu />
                </div>
                <div class="col col-xl-9">
                    <div class="d-flex flex-column w-100 gap-4 bg-default rounded-5 p-3 p-lg-5">
                        <div class="font-default fs-4">
                            {{ title }}
                        </div>
                        <div class="container-fluid overflow-x-hidden">
                            <slot></slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
