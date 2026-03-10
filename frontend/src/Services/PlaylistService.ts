import apiClient from '@/api/client';
import { PLAYLISTS } from '@/api/endpoints';

export const addToPlaylist = async (event: { playlist_uuid: string; song_uuid: string }) => {
    await apiClient.post(PLAYLISTS.SONGS(event.playlist_uuid), {
        song_uuid: event.song_uuid,
    });
};

export const removeFromPlaylist = async (event: { playlist_uuid: string; song_uuid: string }) => {
    await apiClient.delete(PLAYLISTS.SONGS(event.playlist_uuid), {
        data: {
            song_uuid: event.song_uuid,
        },
    });
};

export default { addToPlaylist, removeFromPlaylist };
