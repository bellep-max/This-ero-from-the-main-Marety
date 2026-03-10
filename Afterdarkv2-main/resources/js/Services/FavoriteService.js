import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export const addToFavorites = (event, userUuid) => {
    router.post(
        route('users.favorites.store', {
            user: userUuid,
        }),
        {
            uuid: event.uuid,
            type: event.type,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['songs', 'adventures', 'episodes', 'filters'],
        },
    );
};

export const removeFromFavorites = (event, userUuid) => {
    router.delete(
        route('users.favorites.destroy', {
            user: userUuid,
        }),
        {
            data: {
                _method: 'DELETE',
                uuid: event.uuid,
                type: event.type,
            },
            preserveState: true,
            preserveScroll: true,
            only: ['songs', 'adventures', 'episodes', 'filters'],
        },
    );
};

export default { addToFavorites, removeFromFavorites };
