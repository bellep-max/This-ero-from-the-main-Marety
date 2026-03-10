<script setup>
import { computed, defineAsyncComponent, ref } from 'vue';
import { useAudioPlayerStore } from '@/stores/track.js';
import { storeToRefs } from 'pinia';
import { BDropdown, BDropdownItem, BDropdownItemButton } from 'bootstrap-vue-next';
import { $t } from '@/i18n.js';
import { usePage } from '@inertiajs/vue3';
import { useModal, useVfm } from 'vue-final-modal';
import { isLogged } from '@/Services/AuthService.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const ShareModal = defineAsyncComponent(() => import('@/Components/Modals/ShareModal.vue'));
const DownloadModal = defineAsyncComponent(() => import('@/Components/Modals/DownloadModal.vue'));
const LoginModal = defineAsyncComponent(() => import('@/Components/Modals/LoginModal.vue'));
const SubscriptionModal = defineAsyncComponent(() => import('@/Components/Modals/SubscriptionModal.vue'));
const ReportModal = defineAsyncComponent(() => import('@/Components/Modals/ReportModal.vue'));
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));
const AdventureMapModal = defineAsyncComponent(() => import('@/Components/Modals/AdventureMapModal.vue'));

const audioPlayer = useAudioPlayerStore();
const { isPlaying, currentTrack, error: playerError } = storeToRefs(audioPlayer);

const emit = defineEmits(['add-to-favorites', 'remove-from-favorites']);

const props = defineProps({
    adventure: {
        type: Object,
        required: true,
        validator: (value) => {
            return value.uuid && value.path && value.title;
        },
    },
    canView: {
        type: Boolean,
        default: false,
    },
    darkFont: {
        type: Boolean,
        default: false,
    },
    is_owned: {
        type: Boolean,
        default: false,
    },
});

const vfm = useVfm();

const user = usePage().props.auth.user;
const isHover = ref(false);

const isCurrentTrackPlaying = computed(() => currentTrack && currentTrack.uuid === props.adventure.uuid);
const hasTags = computed(() => props.adventure.tags?.length > 0);
const canDownload = computed(() => props.is_owned || props.adventure.allow_download);
const isCurrentTrack = computed(() => currentTrack.value?.uuid === props.adventure.uuid);
const tags = computed(() => props.adventure.tags?.slice(0, 4));

const openShareModal = () =>
    useModal({
        component: ShareModal,
        attrs: {
            title: props.adventure.title,
            item: props.adventure,
            onClose() {
                vfm.close('share-modal');
            },
            onConfirm() {
                vfm.close('share-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const openDownloadModal = () =>
    useModal({
        component: DownloadModal,
        attrs: {
            title: props.adventure.title,
            item: props.adventure,
            type: ObjectTypes.Adventure,
            onClose() {
                vfm.close('download-modal');
            },
            onConfirm() {
                vfm.close('download-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const openSubscriptionModal = () =>
    useModal({
        component: SubscriptionModal,
        attrs: {
            onClose() {
                vfm.close('subscription-modal');
            },
            onConfirm() {
                vfm.close('subscription-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const openReportModal = () =>
    useModal({
        component: ReportModal,
        attrs: {
            item: props.adventure,
            type: ObjectTypes.Adventure,
            onClose() {
                vfm.close('report-modal');
            },
            onConfirm() {
                vfm.close('report-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const openMapModal = () =>
    useModal({
        component: AdventureMapModal,
        attrs: {
            adventure: props.adventure,
            onClose() {
                vfm.close('adventure-map-modal');
            },
            onConfirm() {
                vfm.close('adventure-map-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

const handlePlayClick = () => {
    try {
        if (isCurrentTrack.value) {
            audioPlayer.togglePlayPause();
        } else {
            audioPlayer.setTracks(props.adventure);
        }
    } catch (error) {
        console.error('Failed to play track:', error);
    }
};
</script>

<template>
    <div
        class="track no-wrap"
        :class="{
            'border border-danger-subtle': isCurrentTrack,
            error: playerError && isCurrentTrack,
        }"
        @mouseover="isHover = true"
        @mouseleave="isHover = false"
    >
        <div class="col-8 order-first col-lg-5 d-flex flex-row align-items-center gap-1 gap-lg-3">
            <div class="d-block">
                <button class="btn-play-track" @click="handlePlayClick" :disabled="!isLogged">
                    <Icon
                        :class="{ 'ms-1': !isPlaying || !isCurrentTrack }"
                        :icon="['fas', isPlaying && isCurrentTrack ? 'pause' : 'play']"
                        size="2xl"
                    />
                </button>
                <span v-if="playerError && isCurrentTrack" class="text-danger small d-block mt-1">
                    {{ playerError }}
                </span>
            </div>
            <div class="position-relative d-none d-lg-inline-block">
                <Icon
                    v-if="adventure.is_patron"
                    icon="star"
                    class-list="position-absolute top-0 start-100 translate-middle color-yellow"
                />
                <Icon icon="route" class-list="position-absolute top-100 start-100 translate-middle color-yellow" />
                <img :alt="adventure.title" :src="adventure.artwork" class="track-image rounded-4 d-md-block d-none" />
            </div>
            <div class="d-flex flex-column justify-content-center align-items-start text-truncate">
                <a
                    class="title font-default link"
                    :class="{
                        'color-light': !darkFont,
                        'default-text-color': darkFont,
                    }"
                    :href="route('adventures.show', adventure)"
                >
                    {{ adventure.title }}
                </a>
                <div
                    class="description"
                    :class="{
                        'color-light': !darkFont,
                        'default-text-color': darkFont,
                    }"
                >
                    <div class="d-none d-md-block">
                        <span>by </span>
                        <a
                            v-if="canView"
                            :href="route('users.show', adventure.user.uuid)"
                            class="link"
                            :class="{
                                'color-light': !darkFont,
                                'default-text-color': darkFont,
                            }"
                            :title="adventure.user.name"
                        >
                            {{ adventure.user?.name ?? '' }}
                        </a>
                        <div v-else class="link" @click="console.log('click')">
                            {{ adventure.user?.name ?? '' }}
                        </div>
                    </div>
                    <div class="d-block d-md-none">
                        <span>by {{ adventure.user?.name }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="hasTags"
            class="d-none d-lg-flex col flex-row flex-wrap justify-content-center align-items-center gap-2"
        >
            <DefaultButton v-for="tag in tags" class-list="btn-narrow btn-pink">
                {{ tag.tag }}
            </DefaultButton>
        </div>
        <div class="col order-lg-last d-flex flex-row justify-content-end align-items-center gap-1 gap-lg-3 ms-auto">
            <span
                class="font-merge"
                :class="{
                    'color-light': !darkFont,
                    'default-text-color': darkFont,
                }"
            >
                {{ adventure.duration }}
            </span>
            <IconButton @click="openMapModal" icon="code-branch">
                {{ adventure.children.length }}
            </IconButton>
            <BDropdown v-if="canView" no-wrapper no-caret toggle-class="btn-default p-2 btn-icon">
                <template #button-content>
                    <Icon :icon="['fas', 'ellipsis-vertical']" />
                </template>
                <BDropdownItemButton
                    v-if="adventure.is_liked"
                    @click="
                        $emit('remove-from-favorites', {
                            uuid: adventure.uuid,
                            type: ObjectTypes.Adventure,
                        })
                    "
                >
                    {{ $t('menus.track.remove_from_favorites') }}
                </BDropdownItemButton>
                <BDropdownItemButton
                    v-else
                    @click="
                        $emit('add-to-favorites', {
                            uuid: adventure.uuid,
                            type: ObjectTypes.Adventure,
                        })
                    "
                >
                    {{ $t('menus.track.add_to_favorites') }}
                </BDropdownItemButton>
                <BDropdownItemButton @click="openShareModal">
                    {{ $t('menus.track.share') }}
                </BDropdownItemButton>
                <BDropdownItemButton @click="openReportModal">
                    {{ $t('menus.track.report') }}
                </BDropdownItemButton>
                <BDropdownItem v-if="is_owned" @click="openMapModal">
                    {{ $t('buttons.show_map') }}
                </BDropdownItem>
                <BDropdownItem v-if="is_owned" :href="route('adventures.edit', adventure)">
                    {{ $t('buttons.edit') }}
                </BDropdownItem>
                <BDropdownItem v-if="canDownload" @click="openDownloadModal">
                    {{ $t('buttons.download') }}
                </BDropdownItem>
            </BDropdown>
        </div>
    </div>
</template>

<style scoped>
.track.error {
    border-color: var(--bs-danger) !important;
}
</style>
