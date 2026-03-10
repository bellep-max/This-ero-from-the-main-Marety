<script setup lang="ts">
import { computed, defineAsyncComponent } from 'vue';
const PlayIconBig = defineAsyncComponent(() => import('@/Components/Icons/PlayIconBig.vue'));

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
    },
    gradientBackground: {
        type: Boolean,
        default: false,
    },
    classList: {
        type: String,
        required: false,
        default: '',
    },
    hasIcon: {
        type: Boolean,
        default: false,
    },
    link: {
        type: String,
        default: '',
    },
});

const hasLink = computed(() => props.link.length > 0);
</script>

<template>
    <div class="bg-default">
        <div
            :class="[
                {
                    'bg-gradient-default': gradientBackground,
                },
                classList,
            ]"
        >
            <div class="container py-5">
                <div class="d-flex flex-column justify-content-start gap-4 px-2 px-lg-0">
                    <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                        <PlayIconBig v-if="hasIcon" />
                        <div
                            :class="{
                                'ms-3': !gradientBackground,
                            }"
                        >
                            <div
                                class="block-title"
                                :class="{
                                    'color-light': gradientBackground,
                                    'default-text-color': !gradientBackground,
                                }"
                            >
                                {{ title }}
                            </div>
                            <div
                                v-if="description"
                                class="block-description"
                                :class="{
                                    'color-light': gradientBackground,
                                }"
                            >
                                {{ description }}
                            </div>
                        </div>
                    </div>
                    <slot></slot>
                    <div v-if="hasLink" class="d-flex flex-row justify-content-center align-items-center">
                        <a
                            :href="link"
                            class="btn-default btn-wide"
                            :class="{
                                'btn-outline': !gradientBackground,
                                'btn-pink': gradientBackground,
                            }"
                        >
                            See All
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
