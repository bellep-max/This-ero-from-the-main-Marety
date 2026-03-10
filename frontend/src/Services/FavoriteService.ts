import apiClient from '@/api/client';
import { FAVORITES } from '@/api/endpoints';

export const addToFavorites = async (event: { uuid: string; type: string }) => {
    await apiClient.post(FAVORITES.STORE, {
        uuid: event.uuid,
        type: event.type,
    });
};

export const removeFromFavorites = async (event: { uuid: string; type: string }) => {
    await apiClient.delete(FAVORITES.STORE, {
        data: {
            uuid: event.uuid,
            type: event.type,
        },
    });
};

export default { addToFavorites, removeFromFavorites };
