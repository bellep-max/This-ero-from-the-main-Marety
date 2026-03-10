<script setup>
import { defineAsyncComponent } from 'vue';
import { isLogged } from '@/Services/AuthService';
import { isNotEmpty } from '@/Services/MiscService';
import SlideCarousel from '@/Components/Carousels/SlideCarousel.vue';
const UserPageMenu = defineAsyncComponent(() => import('@/Components/Sections/UserPageMenu.vue'));

const props = defineProps({
    title: {
        type: String,
        required: false,
        default: '',
    },
    user: {
        type: Object,
        default: {},
    },
    overflow: {
        type: Boolean,
        default: true,
    },
    slides: {
        type: Array,
        required: false,
    },
});
</script>

<template>
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row mb-3" v-if="isNotEmpty(slides)">
                <div class="col-12">
                    <SlideCarousel :items="slides" />
                </div>
            </div>
            <div class="row">
                <div v-if="isLogged" class="col-12 col-xl-3 pb-3 pb-xl-0">
                    <UserPageMenu :user="user" />
                </div>
                <div
                    class="col"
                    :class="{
                        'col-xl-9': isLogged,
                        'col-xl-12': !isLogged,
                    }"
                >
                    <div class="d-flex flex-column w-100 gap-4 bg-default rounded-5 p-3 p-lg-5">
                        <div v-if="isNotEmpty(title)" class="d-flex flex-row justify-content-between w-100">
                            <div class="font-default fs-4">
                                {{ title }}
                            </div>
                            <slot name="controls"></slot>
                        </div>
                        <div
                            class="container-fluid w-100 max-height-75"
                            :class="{
                                'overflow-y-auto': overflow,
                            }"
                        >
                            <slot></slot>
                        </div>
                    </div>
                    <div class="container-fluid p-0 mt-3">
                        <slot name="comments"></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
