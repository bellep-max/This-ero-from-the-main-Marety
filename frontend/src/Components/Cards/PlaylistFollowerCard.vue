<script setup>
import { useAuthStore } from '@/stores/auth';
import { computed, defineAsyncComponent } from 'vue';
const UserFollowButton = defineAsyncComponent(() => import('@/Components/Buttons/UserFollowButton.vue'));
import route from "@/helpers/route"

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    controllable: {
        type: Boolean,
        default: false,
    },
});

const currentUser = useAuthStore().user;
const isMe = computed(() => props.user.uuid === currentUser.uuid);
</script>

<template>
    <div class="col-12 col-md-6 p-1">
        <div class="playlist-subscriber-card d-flex flex-row justify-content-between align-items-center gap-2 p-3">
            <a :href="route('users.show', user)">
                <img :src="user.artwork" class="playlist-subscriber-card-avatar" :alt="user.title" />
            </a>
            <div class="d-flex flex-column justify-content-between gap-3">
                <a
                    :href="route('users.show', user)"
                    class="title font-default default-text-color text-center text-decoration-none"
                >
                    {{ user.username }}
                </a>
                <UserFollowButton v-if="!isMe" :current-user="currentUser" :user="user" />
            </div>
        </div>
    </div>
</template>
