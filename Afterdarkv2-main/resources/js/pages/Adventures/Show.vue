<script setup>
import { $t } from '@/i18n.js';
import { computed, defineAsyncComponent } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { useAudioPlayerStore } from '@/stores/track.js';
import { storeToRefs } from 'pinia';
import { useModal, useVfm } from 'vue-final-modal';
import { BDropdown, BDropdownItem, BDropdownItemButton } from 'bootstrap-vue-next';
import { isNotEmpty } from '@/Services/MiscService.js';
import { isLogged } from '@/Services/AuthService.js';
import ObjectTypes from '@/Enums/ObjectTypes.js';
const UserLayout = defineAsyncComponent(() => import('@/Layouts/UserLayout.vue'));
const CircledText = defineAsyncComponent(() => import('@/Components/CircledText.vue'));
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const CommentsSection = defineAsyncComponent(() => import('@/Components/Sections/CommentsSection.vue'));
const ConfirmDeletionModal = defineAsyncComponent(() => import('@/Components/Modals/ConfirmDeletionModal.vue'));
const ShareModal = defineAsyncComponent(() => import('@/Components/Modals/ShareModal.vue'));
const ReportModal = defineAsyncComponent(() => import('@/Components/Modals/ReportModal.vue'));
const AdventureMapModal = defineAsyncComponent(() => import('@/Components/Modals/AdventureMapModal.vue'));

const audioPlayer = useAudioPlayerStore();
const { isPlaying, currentTrack } = storeToRefs(audioPlayer);

const props = defineProps({
    adventure: {
        type: Object,
        default: {},
    },
    user: {
        type: Object,
        default: {},
    },
    comments: {
        type: Array,
    },
});

const loggedUser = usePage().props.auth.user;
const vfm = useVfm();

const hasDescription = computed(() => props.adventure.description?.length > 0);
const isOwned = computed(() => loggedUser && loggedUser.uuid === props.user.uuid);
const isCurrentTrack = computed(() => currentTrack.value?.uuid === props.adventure.uuid);
const totalTracks = computed(() => {
    let total = props.adventure.children.length;

    for (const root of props.adventure.children) {
        if (isNotEmpty(root.children)) {
            total += root.children.length;
        }
    }

    return total + 1;
});

const addToFavorites = () => {
    router.post(
        route('users.favorites.store', {
            user: loggedUser.uuid,
        }),
        {
            uuid: props.adventure.uuid,
            type: ObjectTypes.Adventure,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const openDeleteModal = () =>
    useModal({
        component: ConfirmDeletionModal,
        attrs: {
            title: props.adventure.title,
            type: ObjectTypes.Adventure,
            onClose() {
                vfm.close('delete-modal');
            },
            onConfirm() {
                router.delete(route('adventures.destroy', props.adventure));
                vfm.close('delete-modal');
            },
        },
        clickToClose: true,
        escToClose: true,
    }).open();

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

const updateComments = () => {
    router.reload({
        only: ['comments'],
    });
};

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
    <Head :title="adventure.title" />
    <UserLayout :user="user">
        <div class="row gy-3">
            <div
                class="col-12 col-lg-4 d-flex flex-column gap-3 justify-content-center justify-content-xl-start align-items-start"
            >
                <img :src="adventure.artwork" :alt="adventure.title" class="img-fluid rounded-4 border-pink" />
            </div>
            <div class="col">
                <div class="d-flex flex-column gap-3">
                    <div
                        v-if="isLogged"
                        class="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-start flex-wrap"
                    >
                        <CircledText :title="totalTracks" :description="$t('pages.adventure.total_tracks')" />
                        <CircledText :title="adventure.likes" :description="$t('misc.likes')" />
                        <BDropdown no-wrapper no-caret toggle-class="btn-default p-2 btn-icon ms-auto">
                            <template #button-content>
                                <Icon :icon="['fas', 'ellipsis-vertical']" />
                            </template>
                            <BDropdownItemButton @click="addToFavorites">
                                {{ $t('menus.track.add_to_favorites') }}
                            </BDropdownItemButton>
                            <BDropdownItemButton @click="openMapModal">
                                {{ $t('buttons.show_map') }}
                            </BDropdownItemButton>
                            <BDropdownItemButton @click="openShareModal">
                                {{ $t('menus.track.share') }}
                            </BDropdownItemButton>
                            <BDropdownItemButton @click="openReportModal">
                                {{ $t('menus.track.report') }}
                            </BDropdownItemButton>
                            <BDropdownItem v-if="isOwned" :href="route('adventures.edit', adventure)">
                                {{ $t('buttons.edit') }}
                            </BDropdownItem>
                            <BDropdownItemButton v-if="isOwned" @click="openDeleteModal">
                                {{ $t('buttons.delete') }}
                            </BDropdownItemButton>
                        </BDropdown>
                    </div>
                    <div class="d-flex flex-row align-items-center py-3 border-top border-bottom gap-3">
                        <div class="d-block">
                            <button class="btn-play lg" @click="handlePlayClick" :disabled="!isLogged">
                                <Icon
                                    :class="{ 'ms-1': !isPlaying || !isCurrentTrack }"
                                    :icon="['fas', isPlaying && isCurrentTrack ? 'pause' : 'play']"
                                    size="2xl"
                                />
                            </button>
                        </div>
                        <div class="d-flex flex-column align-items-start justify-content-start gap-2">
                            <span class="font-default fs-4">{{ adventure.title }}</span>
                            <span class="font-merge">{{ adventure.user.name }}</span>
                            <div class="d-flex flex-row justify-content-start align-items-center gap-2">
                                <Icon :icon="['fas', 'calendar-check']" class-list="color-pink" />
                                <span class="font-merge">
                                    {{ adventure.created_at }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-if="isNotEmpty(adventure.tags)" class="d-flex flex-row gap-2 border-bottom pb-3 flex-wrap">
                        <a
                            v-for="tag in adventure.tags"
                            :href="route('discover.index', { tags: tag.tag })"
                            class="btn-default btn-outline btn-narrow"
                        >
                            {{ tag.tag }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="hasDescription" class="d-flex flex-row text-start my-3">
            <span class="font-default">{{ $t('misc.summary', { text: adventure.description }) }}</span>
        </div>
        <template v-if="isLogged && adventure.allow_comments" #comments>
            <CommentsSection
                :comments="comments"
                :type="adventure.type"
                :uuid="adventure.uuid"
                @commented="updateComments"
            />
        </template>
    </UserLayout>
</template>
