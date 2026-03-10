<script setup>
import { computed, defineAsyncComponent, ref } from 'vue';
import { useAudioPlayerStore } from '@/stores/track.js';
import { storeToRefs } from 'pinia';
import { BDropdown, BDropdownGroup, BDropdownHeader, BDropdownItem, BDropdownItemButton } from 'bootstrap-vue-next';
import { $t } from '@/i18n.js';
import { usePage } from '@inertiajs/vue3';
import { useModal, useVfm } from 'vue-final-modal';
import { isLogged } from '@/Services/AuthService.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { isNotEmpty } from '@/Services/MiscService.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));
const ShareModal = defineAsyncComponent(() => import('@/Components/Modals/ShareModal.vue'));
const DownloadModal = defineAsyncComponent(() => import('@/Components/Modals/DownloadModal.vue'));
const LoginModal = defineAsyncComponent(() => import('@/Components/Modals/LoginModal.vue'));
const SubscriptionModal = defineAsyncComponent(() => import('@/Components/Modals/SubscriptionModal.vue'));
const ReportModal = defineAsyncComponent(() => import('@/Components/Modals/ReportModal.vue'));

const audioPlayer = useAudioPlayerStore();
const { isPlaying, currentTrack, error: playerError } = storeToRefs(audioPlayer);

const emit = defineEmits(['add-to-favorites', 'remove-from-favorites', 'add-to-playlist']);

const props = defineProps({
    song: {
        type: Object,
        required: true,
        validator: (value) => {
            return value.uuid && value.path && value.title && value.streamable !== undefined;
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
const playlists = ref(user?.playlists);
const isHover = ref(false);
const collaboratedPlaylists = ref(user?.collaborated_playlists);

const isCurrentTrackPlaying = computed(() => currentTrack && currentTrack.uuid === props.song.uuid);
const canDownload = computed(() => props.is_owned || props.song.allow_download);
const isCurrentTrack = computed(() => currentTrack.value?.uuid === props.song.uuid);
const tags = computed(() => props.song.tags?.slice(0, 4));
const hasCollaboratedPlaylists = computed(() => collaboratedPlaylists.value.length > 0);
const hasOwnPlaylists = computed(() => playlists.value.length > 0);
const type = computed(() => ObjectTypes.getObjectType(props.song.type));

const openShareModal = () =>
    useModal({
        component: ShareModal,
        attrs: {
            title: props.song.title,
            item: props.song,
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
            title: props.song.title,
            item: props.song,
            type: type.value,
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
            item: props.song,
            type: ObjectTypes.Song,
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

const handlePlayClick = () => {
    if (props.song.is_patron) {
        openSubscriptionModal();
        return;
    }

    try {
        if (isCurrentTrack.value) {
            audioPlayer.togglePlayPause();
        } else {
            audioPlayer.setTracks(props.song);
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
                <button class="btn-play-track" @click="handlePlayClick" :disabled="!song.streamable && !isLogged">
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
                    v-if="song.is_patron"
                    icon="star"
                    class-list="position-absolute top-0 start-100 translate-middle color-yellow"
                />
                <Icon icon="play" class-list="position-absolute top-100 start-100 translate-middle color-yellow" />
                <img :alt="song.title" :src="song.artwork" class="track-image rounded-4 d-md-block d-none" />
            </div>
            <div class="d-flex flex-column justify-content-center align-items-start text-truncate">
                <a
                    class="title font-default link"
                    :class="{
                        'color-light': !darkFont,
                        'default-text-color': darkFont,
                    }"
                    :href="route('songs.show', song.uuid)"
                >
                    {{ song.title }}
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
                            :href="route('users.show', song.user.uuid)"
                            class="link"
                            :class="{
                                'color-light': !darkFont,
                                'default-text-color': darkFont,
                            }"
                            :title="song.user.name"
                        >
                            {{ song.user?.name ?? '' }}
                        </a>
                        <div v-else class="link" @click="console.log('click')">
                            {{ song.user?.name ?? '' }}
                        </div>
                    </div>
                    <div class="d-block d-md-none">
                        <span>by {{ song.user?.name }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="isNotEmpty(song.tags)"
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
                {{ song.duration }}
            </span>
            <BDropdown v-if="canView && playlists" no-wrapper no-caret toggle-class="btn-default p-2 btn-icon">
                <template #button-content>
                    <Icon :icon="['fas', 'file-circle-plus']" />
                </template>
                <template v-if="hasOwnPlaylists">
                    <BDropdownHeader>
                        {{ $t('misc.my_playlists') }}
                    </BDropdownHeader>
                    <BDropdownItemButton
                        v-for="playlist in playlists"
                        :key="playlist.uuid"
                        @click="
                            $emit('add-to-playlist', {
                                playlist_uuid: playlist.uuid,
                                song_uuid: song.uuid,
                            })
                        "
                    >
                        {{ playlist.title }}
                    </BDropdownItemButton>
                </template>
                <template v-if="hasCollaboratedPlaylists">
                    <BDropdownHeader>
                        {{ $t('misc.collaborated_playlists') }}
                    </BDropdownHeader>
                    <BDropdownItemButton
                        v-for="playlist in collaboratedPlaylists"
                        :key="playlist.uuid"
                        @click="
                            $emit('add-to-playlist', {
                                playlist_uuid: playlist.uuid,
                                song_uuid: song.uuid,
                            })
                        "
                    >
                        {{ playlist.title }}
                    </BDropdownItemButton>
                </template>
            </BDropdown>
            <BDropdown v-if="canView" no-wrapper no-caret toggle-class="btn-default p-2 btn-icon">
                <template #button-content>
                    <Icon :icon="['fas', 'ellipsis-vertical']" />
                </template>
                <BDropdownItemButton
                    v-if="song.is_liked"
                    @click="$emit('remove-from-favorites', { uuid: song.uuid, type: type })"
                >
                    {{ $t('menus.track.remove_from_favorites') }}
                </BDropdownItemButton>
                <BDropdownItemButton v-else @click="$emit('add-to-favorites', { uuid: song.uuid, type: type })">
                    {{ $t('menus.track.add_to_favorites') }}
                </BDropdownItemButton>
                <BDropdownItemButton @click="openShareModal">
                    {{ $t('menus.track.share') }}
                </BDropdownItemButton>
                <BDropdownItemButton @click="openReportModal">
                    {{ $t('menus.track.report') }}
                </BDropdownItemButton>
                <BDropdownGroup :header="$t('menus.track.add_to_queue.title')">
                    <BDropdownItemButton @click="audioPlayer.addToQueueNext(song)">
                        {{ $t('menus.track.add_to_queue.play_next') }}
                    </BDropdownItemButton>
                    <BDropdownItemButton @click="audioPlayer.addToQueueEnd(song)">
                        {{ $t('menus.track.add_to_queue.play_last') }}
                    </BDropdownItemButton>
                </BDropdownGroup>
                <BDropdownItem v-if="is_owned" :href="route('songs.edit', song)">
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
