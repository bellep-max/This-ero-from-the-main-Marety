import { create } from 'zustand';
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
    unread_notifications: any[];
    role: string;
    subscription: any;
    [key: string]: any;
}

interface AuthState {
    user: AuthUser | null;
    isAdult: boolean;
    authLoaded: boolean;
    pageMenu: any[];
    userMenu: any[];
    loading: boolean;
    isLogged: boolean;
    isOwner: (uuid: string) => boolean;
    fetchUser: () => Promise<void>;
    login: (credentials: { email: string; password: string; remember?: boolean }) => Promise<any>;
    register: (data: Record<string, any>) => Promise<any>;
    logout: () => Promise<void>;
}

export const useAuthStore = create<AuthState>((set, get) => ({
    user: null,
    isAdult: false,
    authLoaded: false,
    pageMenu: [],
    userMenu: [],
    loading: false,
    isLogged: false,

    isOwner: (uuid: string) => get().user?.uuid === uuid,

    fetchUser: async () => {
        set({ loading: true });
        try {
            const response = await apiClient.get(AUTH.USER);
            set({
                user: response.data.user,
                isAdult: response.data.is_adult ?? false,
                authLoaded: true,
                pageMenu: response.data.pageMenu ?? [],
                userMenu: response.data.userMenu ?? [],
                isLogged: true,
                loading: false,
            });
        } catch {
            set({ user: null, isAdult: false, authLoaded: true, pageMenu: [], userMenu: [], isLogged: false, loading: false });
        }
    },

    login: async (credentials) => {
        await getCsrfCookie();
        const response = await apiClient.post(AUTH.LOGIN, credentials);
        set({ user: response.data.user, isLogged: true });
        return response.data;
    },

    register: async (data) => {
        await getCsrfCookie();
        const response = await apiClient.post(AUTH.REGISTER, data);
        set({ user: response.data.user, isLogged: true });
        return response.data;
    },

    logout: async () => {
        await apiClient.post(AUTH.LOGOUT);
        set({ user: null, isAdult: false, pageMenu: [], userMenu: [], isLogged: false });
    },
}));
