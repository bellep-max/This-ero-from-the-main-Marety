<script setup>
import { $t } from '@/i18n.js';
import { BDropdown, BDropdownHeader, BDropdownItem, BDropdownItemButton } from 'bootstrap-vue-next';
import { computed, defineAsyncComponent } from 'vue';
import ObjectTypes from '@/Enums/ObjectTypes.js';
import { isNotEmpty } from '@/Services/MiscService.js';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const IconButton = defineAsyncComponent(() => import('@/Components/Buttons/IconButton.vue'));

const props = defineProps({
    track: {
        type: Object,
        required: true,
    },
    podcast_uuid: {
        type: String,
        default: null,
    },
    likeable: {
        type: Boolean,
        default: false,
    },
    playlists: {
        type: Array,
        default: [],
    },
    collaboratedPlaylists: {
        type: Array,
        default: [],
    },
    draggable: {
        type: Boolean,
        default: false,
    },
    controllable: {
        type: Boolean,
        default: false,
    },
    darkFont: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['like', 'deleted', 'shared', 'reported', 'add-to-playlist']);

const hasCollaboratedPlaylists = computed(() => isNotEmpty(props.collaboratedPlaylists));
const hasOwnPlaylists = computed(() => isNotEmpty(props.playlists));
const type = computed(() => ObjectTypes.getObjectType(props.track.type));

const link = computed(() =>
    type.value === ObjectTypes.Song ? route('songs.show', props.track.uuid) : route('episodes.show', props.track.uuid),
);
</script>

<template>
    <div class="d-flex flex-row w-100 align-items-center justify-content-start gap-3 p-2 module">
        <!--    @if(isset($sortable) && $sortable)-->
        <!--    <div class="drag-handle btn-icon-only color-text d-none d-sm-flex">-->
        <!--      <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">-->
        <!--        &lt;!&ndash;!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.&ndash;&gt;-->
        <!--        <path d="M32 288c-17.7 0-32 14.3-32 32s14.3 32 32 32l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 288zm0-128c-17.7 0-32 14.3-32 32s14.3 32 32 32l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 160z"/>-->
        <!--      </svg>-->
        <!--    </div>-->
        <!--    @endif-->
        <IconButton
            v-if="likeable"
            :icon="track.favorite ? 'heart-circle-check' : 'heart'"
            @click="$emit('like', { uuid: track.uuid, type: ObjectTypes.Song })"
        />
        <div class="position-relative flex-shrink-0">
            <img class="img-thumb rounded-2" :src="track.artwork" :alt="track.title" />
        </div>
        <div class="text-truncate">
            <a class="color-text font-default flex-nowrap text-decoration-none" :href="link">
                {{ track.title }}
            </a>
        </div>
        <div class="ms-auto d-flex flex-row gap-3 align-items-center">
            <div class="font-merge fs-12 color-grey">{{ track.duration }}</div>
            <BDropdown
                v-if="likeable && (hasOwnPlaylists || hasCollaboratedPlaylists)"
                no-wrapper
                no-caret
                toggle-class="btn-default p-2 btn-icon"
            >
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
                                song_uuid: track.uuid,
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
                                song_uuid: track.uuid,
                            })
                        "
                    >
                        {{ playlist.title }}
                    </BDropdownItemButton>
                </template>
            </BDropdown>
            <BDropdown v-if="likeable" no-caret no-wrapper toggle-class="btn-default p-2 btn-icon">
                <template #button-content>
                    <Icon :icon="['fas', 'ellipsis-vertical']" />
                </template>
                <BDropdownHeader>
                    {{ track.title }}
                </BDropdownHeader>
                <BDropdownItem link-class="font-merge" @click="$emit('shared', track)">
                    {{ $t('pages.playlist.menus.share') }}
                </BDropdownItem>
                <BDropdownItem link-class="font-merge" @click="$emit('reported', track)">
                    {{ $t('pages.playlist.menus.report') }}
                </BDropdownItem>
                <BDropdownItem
                    v-if="controllable & !podcast_uuid"
                    link-class="font-merge"
                    @click="$emit('deleted', { uuid: track.uuid, type: type })"
                >
                    {{ $t('pages.playlist.menus.remove_from_playlist') }}
                </BDropdownItem>
            </BDropdown>
        </div>
    </div>
</template>
