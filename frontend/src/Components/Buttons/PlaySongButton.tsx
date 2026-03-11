import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useAudioPlayerStore } from '@/stores/track';

interface PlaySongButtonProps {
    song?: any;
    songs?: any[];
    classList?: string;
}

export default function PlaySongButton({ song, songs, classList }: PlaySongButtonProps) {
    const setTracks = useAudioPlayerStore((s) => s.setTracks);
    const currentTrack = useAudioPlayerStore((s) => s.currentTrack);
    const isPlaying = useAudioPlayerStore((s) => s.isPlaying);
    const togglePlayPause = useAudioPlayerStore((s) => s.togglePlayPause);

    const isCurrentTrack = currentTrack?.uuid === song?.uuid;

    const handleClick = () => {
        if (isCurrentTrack) {
            togglePlayPause();
        } else if (songs) {
            setTracks(songs);
        } else if (song) {
            setTracks(song);
        }
    };

    return (
        <button className={`btn btn-play ${classList || ''}`} onClick={handleClick} type="button">
            <FontAwesomeIcon icon={['fas', isCurrentTrack && isPlaying ? 'pause' : 'play']} />
        </button>
    );
}
