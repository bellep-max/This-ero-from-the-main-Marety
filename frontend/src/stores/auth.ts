import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import apiClient, { getCsrfCookie } from '@/api/client';
import { AUTH } from '@/api/endpoints';

export interface AuthUser {
    uuid: string;
    name: string;
    username: string;
    email: string;
    avatar: string | null;
    banner: string | null;
    bio: string | null;
    group: any;
    playlists: any[];
    podcasts: any[];
    approvedCollaboratedPlaylists: any[];
    unreadNotifications: any[];
    [key: string]: any;
}

export const useAuthStore = defineStore('auth', () => {
    const user = ref<AuthUser | null>(null);
    const isAdult = ref(false);
    const pageMenu = ref<any[]>([]);
    const userMenu = ref<any[]>([]);
    const loading = ref(false);

    const isLogged = computed(() => user.value !== null);

    function isOwner(uuid: string): boolean {
        return user.value?.uuid === uuid;
    }

    async function fetchUser() {
        loading.value = true;
        try {
            const response = await apiClient.get(AUTH.USER);
            user.value = response.data.user;
            isAdult.value = response.data.is_adult ?? false;
            pageMenu.value = response.data.pageMenu ?? [];
            userMenu.value = response.data.userMenu ?? [];
        } catch {
            user.value = null;
            isAdult.value = false;
            pageMenu.value = [];
            userMenu.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function login(credentials: { email: string; password: string; remember?: boolean }) {
        await getCsrfCookie();
        const response = await apiClient.post(AUTH.LOGIN, credentials);
        user.value = response.data.user;
        return response.data;
    }

    async function register(data: Record<string, any>) {
        await getCsrfCookie();
        const response = await apiClient.post(AUTH.REGISTER, data);
        user.value = response.data.user;
        return response.data;
    }

    async function logout() {
        await apiClient.post(AUTH.LOGOUT);
        user.value = null;
        isAdult.value = false;
        pageMenu.value = [];
        userMenu.value = [];
    }

    return {
        user,
        isAdult,
        pageMenu,
        userMenu,
        loading,
        isLogged,
        isOwner,
        fetchUser,
        login,
        register,
        logout,
    };
});
