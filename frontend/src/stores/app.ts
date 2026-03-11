import { create } from 'zustand';
import apiClient from '@/api/client';
import { INIT } from '@/api/endpoints';
import { useAuthStore } from '@/stores/auth';

export interface MenuItem {
    path: string;
    active: boolean;
    key: string;
    permissions: string | null;
}

export interface FilterPresets {
    tags: any[];
    genres: any[];
    vocals: any[];
    podcast_categories: any[];
    languages: any[];
    countries: any[];
}

interface AppState {
    appName: string;
    menu: MenuItem[];
    filterPresets: FilterPresets;
    flashMessage: string | null;
    version: string | null;
    loading: boolean;
    initialized: boolean;
    fetchInitData: () => Promise<void>;
    clearFlashMessage: () => void;
}

export const useAppStore = create<AppState>((set) => ({
    appName: '',
    menu: [],
    filterPresets: {
        tags: [],
        genres: [],
        vocals: [],
        podcast_categories: [],
        languages: [],
        countries: [],
    },
    flashMessage: null,
    version: null,
    loading: false,
    initialized: false,

    fetchInitData: async () => {
        set({ loading: true });
        try {
            const response = await apiClient.get(INIT);
            const data = response.data;
            set({
                appName: data.name ?? '',
                menu: data.menu ?? [],
                filterPresets: data.filter_presets ?? {
                    tags: [], genres: [], vocals: [],
                    podcast_categories: [], languages: [], countries: [],
                },
                flashMessage: data.flash_message ?? null,
                version: data.version ?? null,
                initialized: true,
                loading: false,
            });

            if (data.auth) {
                const authStore = useAuthStore.getState();
                if (!authStore.authLoaded) {
                    useAuthStore.setState({
                        isAdult: data.auth.is_adult ?? false,
                        user: data.auth.user ?? null,
                        isLogged: !!data.auth.user,
                        pageMenu: data.auth.pageMenu ?? [],
                        userMenu: data.auth.userMenu ?? [],
                        authLoaded: true,
                        loading: false,
                    });
                }
            }
        } catch (error) {
            console.error('Failed to load init data:', error);
            set({ loading: false });
        }
    },

    clearFlashMessage: () => set({ flashMessage: null }),
}));
