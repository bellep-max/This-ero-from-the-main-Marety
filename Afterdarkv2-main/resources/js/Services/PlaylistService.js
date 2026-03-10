import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export const addToPlaylist = (event) => {
    router.post(
        route('playlists.songs.store', {
            playlist: event.playlist_uuid,
        }),
        {
            song_uuid: event.song_uuid,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

export const removeFromPlaylist = (event) => {
    router.delete(
        route('playlists.songs.destroy', {
            _method: 'DELETE',
            playlist: event.playlist_uuid,
            song: event.song_uuid,
        }),
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

export default { addToPlaylist, removeFromPlaylist };
