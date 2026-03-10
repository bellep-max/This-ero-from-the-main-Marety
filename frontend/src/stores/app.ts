import { defineStore } from 'pinia';
import { ref } from 'vue';
import apiClient from '@/api/client';
import { INIT } from '@/api/endpoints';

export interface MenuItem {
    route: string;
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

export const useAppStore = defineStore('app', () => {
    const appName = ref('');
    const menu = ref<MenuItem[]>([]);
    const filterPresets = ref<FilterPresets>({
        tags: [],
        genres: [],
        vocals: [],
        podcast_categories: [],
        languages: [],
        countries: [],
    });
    const flashMessage = ref<string | null>(null);
    const version = ref<string | null>(null);
    const loading = ref(false);
    const initialized = ref(false);

    async function fetchInitData() {
        loading.value = true;
        try {
            const response = await apiClient.get(INIT);
            const data = response.data;
            appName.value = data.name ?? '';
            menu.value = data.menu ?? [];
            filterPresets.value = data.filter_presets ?? {
                tags: [],
                genres: [],
                vocals: [],
                podcast_categories: [],
                languages: [],
                countries: [],
            };
            flashMessage.value = data.flash_message ?? null;
            version.value = data.version ?? null;
            initialized.value = true;
        } catch (error) {
            console.error('Failed to load init data:', error);
        } finally {
            loading.value = false;
        }
    }

    function clearFlashMessage() {
        flashMessage.value = null;
    }

    return {
        appName,
        menu,
        filterPresets,
        flashMessage,
        version,
        loading,
        initialized,
        fetchInitData,
        clearFlashMessage,
    };
});
