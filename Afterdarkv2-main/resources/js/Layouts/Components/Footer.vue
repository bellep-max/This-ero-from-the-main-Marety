<script setup>
import { computed, defineAsyncComponent } from 'vue';
import { $t } from '@/i18n.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));

const year = computed(() => {
    const d = new Date();
    return d.getFullYear();
});
</script>

<template>
    <footer class="py-4 p-md-5 p-lg-6 bg-color-dark mt-auto">
        <div class="container d-flex flex-column justify-content-center align-items-center gap-5">
            <div class="d-flex flex-row justify-content-center align-items-center gap-3 gap-md-5 flex-wrap">
                <a class="footer-link" :href="route('pages.show', 'cookies-and-personal-data')">{{
                    $t('footer.cookie')
                }}</a>
                <a class="footer-link" :href="route('pages.show', 'privacy-policy')">{{ $t('footer.privacy') }}</a>
                <a class="footer-link" :href="route('pages.show', 'legal-information')">{{
                    $t('footer.copyrights')
                }}</a>
                <a class="footer-link" :href="route('pages.show', 'term-and-condition')">{{ $t('footer.terms') }}</a>
            </div>
            <div class="d-flex flex-row justify-content-center align-items-center gap-3 gap-md-5 flex-wrap">
                <a href="https://www.patreon.com/Erocasthost" class="footer-link d-flex flex-row gap-2" target="_blank">
                    <Icon :icon="['fab', 'patreon']" />
                    <span class="font-default">Patreon</span>
                </a>
                <a href="https://www.facebook.com/" class="footer-link d-flex flex-row gap-2" target="_blank">
                    <Icon :icon="['fab', 'facebook']" :class-list="'fill-light'" />
                    <span class="font-default">Facebook</span>
                </a>
                <a href="https://twitter.com/" class="footer-link d-flex flex-row gap-2" target="_blank">
                    <Icon :icon="['fab', 'x-twitter']" :class-list="'fill-light'" />
                    <span class="font-default">X (ex-Twitter)</span>
                </a>
                <a :href="route('feeds.main')" class="footer-link d-flex flex-row gap-2" target="_blank">
                    <Icon :icon="['fas', 'rss']" :class-list="'fill-light'" />
                    <span class="font-default">RSS</span>
                </a>
            </div>
            <div class="border-bottom w-100 border-color-gd"></div>
            <div class="fs-12 text-center font-default color-light">© {{ year }} EroCast. All Rights Reserved</div>
        </div>
    </footer>
</template>

<style scoped>
.bg-color-dark {
    background-color: var(--text-color, #0a0410) !important;
}
.border-color-gd {
    border-color: var(--gray-dark) !important;
}
.footer-link {
    color: var(--light-color, #fff9fb);
    text-decoration: none;
    font-family: Montserrat, sans-serif;
    font-size: 16px;
}
.footer-link:hover {
    text-decoration: underline !important;
}
</style>
