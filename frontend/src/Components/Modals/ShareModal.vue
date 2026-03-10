<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { BButton, BFormCheckbox, BFormInput, BFormTextarea, BInputGroup, BTab, BTabs } from 'bootstrap-vue-next';
import { computed, defineAsyncComponent, onMounted, ref, watch } from 'vue';
import { setItemLink, setItemText } from '@/Services/FormService.js';
import { toast } from 'vue3-toastify';
import { setEmbedCode } from '@/Services/EmbedService.js';
import Sizes from '@/Enums/Sizes.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import Themes from '@/Enums/Themes.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
import route from "@/helpers/route"

const props = defineProps({
    item: {
        type: Object,
        required: false,
    },
});

const emit = defineEmits(['close']);

const twitterMaxAllowed = 280;
const twitterInputLength = ref(0);
const twitterPost = ref('');

const itemLink = ref('');
const embedTheme = ref(false);
const embedThemeName = ref(Themes.Light);
const embedSize = ref(Sizes.Large);
const embedCode = ref('');
const embedRoute = ref('');

const copyToClipboard = (item) => {
    if (!item) {
        return toast($t('modals.share.nothing_to_copy'), {
            position: 'bottom-left',
            type: 'error',
            transition: toast.TRANSITIONS.SLIDE,
        });
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(item);

        toast($t('modals.share.copied'), {
            position: 'bottom-left',
            type: 'success',
            transition: toast.TRANSITIONS.SLIDE,
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = item;

        // Make the textarea invisible
        textArea.style.position = 'fixed';
        textArea.style.top = '-9999px';
        textArea.style.left = '-9999px';

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            document.execCommand('copy');

            toast($t('modals.share.copied'), {
                position: 'bottom-left',
                type: 'success',
                transition: toast.TRANSITIONS.SLIDE,
            });
        } catch (err) {
            console.error('Fallback copy failed:', err);
        }

        document.body.removeChild(textArea);
    }
};

const setEmbed = (size) => {
    embedSize.value = size;
    embedCode.value = setEmbedCode(Sizes.config[size], embedRoute.value);
};

const setEmbedRoute = () => {
    embedRoute.value = route('share.embed', {
        theme: embedThemeName.value,
        type: ObjectTypes.getObjectType(props.item.type),
        uuid: props.item.uuid,
    });
};

const facebookLink = computed(
    () => `https://www.facebook.com/share.php?u=${encodeURIComponent(itemLink.value)}&ref=songShare`,
);
const twitterLink = computed(
    () =>
        `https://twitter.com/intent/tweet?url=${encodeURIComponent(itemLink.value)}&text=${encodeURIComponent(twitterPost.value)}`,
);
const redditLink = computed(
    () =>
        `https://www.reddit.com/submit?title=${encodeURIComponent(props.item?.title ?? props.item?.name)}&url=${encodeURIComponent(itemLink.value)}`,
);
const pinterestLink = computed(
    () =>
        `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(itemLink.value)}&media=${encodeURIComponent(props.item?.artwork)}&description=${encodeURIComponent(props.item?.title ?? props.item?.name)}`,
);
const linkedinLink = computed(
    () =>
        `https://www.linkedin.com/feed/?shareActive=true&text=${encodeURIComponent(props.item?.title ?? props.item?.name)}&shareUrl=${encodeURIComponent(itemLink.value)}`,
);
const emailLink = computed(
    () =>
        `mailto:?subject=${encodeURIComponent(props.item?.title ?? props.item?.name)}&body=${encodeURIComponent(itemLink.value)}`,
);

watch(
    () => twitterPost.value,
    (newValue) => {
        twitterInputLength.value = newValue.length;
    },
);

watch(
    () => embedTheme.value,
    (newValue) => {
        embedThemeName.value = newValue ? Themes.Dark : Themes.Light;
    },
);

onMounted(() => {
    if (props.item) {
        itemLink.value = setItemLink(props.item);
        twitterPost.value = setItemText(props.item);
        twitterInputLength.value = twitterPost.value?.length;
        setEmbedRoute();
        embedCode.value = setEmbedCode(Sizes.config[embedSize.value], embedRoute.value);
    }
});
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-4 gap-4"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="share-modal"
    >
        <BTabs
            nav-wrapper-class="tabs-header w-100"
            nav-class="tab-item px-4 d-flex"
            active-nav-item-class="tab-item-active"
            tab-class="pt-3"
            nav-item-class="tab-item default-text-color px-4"
            content-class="overflow-y-auto overflow-x-hidden"
            fill
        >
            <BTab v-if="item" :title="$t('modals.share.embed.title')">
                <div class="d-flex flex-column w-100 justify-content-center align-items-center gap-3">
                    <div class="d-flex flex-row justify-content-center align-items-center gap-4">
                        <DefaultButton
                            :class-list="{
                                'btn-pink': embedSize === Sizes.Large,
                                'btn-outline': embedSize !== Sizes.Large,
                                'btn-narrow': true,
                            }"
                            @click="setEmbed(Sizes.Large)"
                        >
                            {{ $t('buttons.share.size.picture') }}
                        </DefaultButton>
                        <DefaultButton
                            :class-list="{
                                'btn-pink': embedSize === Sizes.Medium,
                                'btn-outline': embedSize !== Sizes.Medium,
                                'btn-narrow': true,
                            }"
                            @click="setEmbed(Sizes.Medium)"
                        >
                            {{ $t('buttons.share.size.classic') }}
                        </DefaultButton>
                        <DefaultButton
                            :class-list="{
                                'btn-pink': embedSize === Sizes.Small,
                                'btn-outline': embedSize !== Sizes.Small,
                                'btn-narrow': true,
                            }"
                            @click="setEmbed(Sizes.Small)"
                        >
                            {{ $t('buttons.share.size.mini') }}
                        </DefaultButton>
                    </div>
                    <iframe
                        id="embed_iframe"
                        :src="embedRoute"
                        :class="{
                            sm: embedSize === Sizes.Small,
                            md: embedSize === Sizes.Medium,
                            lg: embedSize === Sizes.Large,
                        }"
                    ></iframe>
                    <BFormCheckbox switch size="lg" v-model="embedTheme" @update:model-value="setEmbedRoute">
                        {{ embedThemeName }}
                    </BFormCheckbox>
                    <BFormInput v-model="embedCode" @click="copyToClipboard(embedCode)" readonly />
                </div>
            </BTab>
            <BTab :title="$t('modals.share.facebook.title')">
                <div class="d-flex flex-column w-100 justify-content-start align-items-center gap-1">
                    <label class="font-default fs-14 default-text-color mx-auto">
                        {{ $t('modals.share.facebook.description') }}
                    </label>
                    <DefaultButton external :href="facebookLink" class-list="btn-pink">
                        {{ $t('buttons.share.title') }}
                    </DefaultButton>
                </div>
            </BTab>
            <BTab :title="$t('modals.share.twitter.title')">
                <div class="d-flex flex-column w-100 justify-content-start align-items-start gap-3">
                    <label class="font-default fs-14 default-text-color mx-auto">
                        {{ $t('modals.share.twitter.description') }}
                    </label>
                    <BFormTextarea v-model="twitterPost" rows="3" :state="twitterPost.length <= twitterMaxAllowed" />
                    <div class="d-flex flex-row w-100 justify-content-between align-items-center">
                        <span>{{ twitterInputLength }}</span>
                        <DefaultButton external :href="twitterLink" class-list="btn-pink">
                            {{ $t('buttons.share.title') }}
                        </DefaultButton>
                    </div>
                </div>
            </BTab>
            <BTab :title="$t('modals.share.more.title')">
                <div class="d-flex flex-column w-100 justify-content-center align-items-center gap-3">
                    <label class="font-default fs-14 default-text-color">
                        {{ $t('modals.share.more.description') }}
                    </label>
                    <!--          <DefaultButton-->
                    <!--              external-->
                    <!--              :href="redditLink"-->
                    <!--              class-list="btn-pink"-->
                    <!--          >-->
                    <!--            <Icon :icon="['fab', 'reddit']"></Icon>-->
                    <!--            {{ $t('modals.share.more.buttons.reddit') }}-->
                    <!--          </DefaultButton>-->
                    <DefaultButton external :href="pinterestLink" class-list="btn-pink">
                        <Icon :icon="['fab', 'pinterest']"></Icon>
                        {{ $t('modals.share.more.buttons.pinterest') }}
                    </DefaultButton>
                    <DefaultButton external :href="linkedinLink" class-list="btn-pink">
                        <Icon :icon="['fab', 'linkedin']"></Icon>
                        {{ $t('modals.share.more.buttons.linked_in') }}
                    </DefaultButton>
                    <DefaultButton external :href="emailLink" class-list="btn-pink">
                        <Icon :icon="['fas', 'envelope-open-text']"></Icon>
                        {{ $t('modals.share.more.buttons.email') }}
                    </DefaultButton>
                </div>
            </BTab>
        </BTabs>
        <div class="d-block">
            <BInputGroup>
                <BFormInput v-model="itemLink" readonly />
                <BButton variant="outline-primary" @click="copyToClipboard(itemLink)">
                    {{ $t('buttons.copy') }}
                </BButton>
            </BInputGroup>
        </div>
    </VueFinalModal>
</template>

<style scoped>
#embed_iframe {
    width: 100%;
    pointer-events: none;
    border: 1px solid #ddd;
    box-sizing: border-box;
    border-radius: 4px;

    &.lg {
        height: 300px;
    }

    &.md {
        height: 180px;
    }

    &.sm {
        height: 60px;
    }
}
</style>
