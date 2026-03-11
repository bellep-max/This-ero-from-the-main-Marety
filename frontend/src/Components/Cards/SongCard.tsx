import React, { useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useAudioPlayerStore } from '@/stores/track';
import { useAuthStore } from '@/stores/auth';
import { setItemLink } from '@/Services/FormService';

interface SongCardProps {
    track: any;
}

export default function SongCard({ track }: SongCardProps) {
    const { isPlaying, currentTrack, togglePlayPause, setTracks } = useAudioPlayerStore();
    const isLogged = useAuthStore((s) => s.isLogged);
    const [isHover, setIsHover] = useState(false);

    const isCurrentTrack = currentTrack?.uuid === track.uuid;

    const handlePlayClick = () => {
        if (track.is_patron) {
            return;
        }

        try {
            if (isCurrentTrack) {
                togglePlayPause();
            } else {
                setTracks(track);
            }
        } catch (error) {
            console.error('Failed to play track:', error);
        }
    };

    return (
        <div
            className="audio-carousel-item"
            onMouseOver={() => setIsHover(true)}
            onMouseLeave={() => setIsHover(false)}
        >
            <div className="position-relative">
                <div
                    className={`position-absolute top-50 start-50 translate-middle ${isHover ? 'd-block' : 'd-none'}`}
                >
                    <button
                        className="btn-play lg"
                        onClick={handlePlayClick}
                        disabled={!track.streamable && !isLogged}
                    >
                        <FontAwesomeIcon
                            className={!isPlaying || !isCurrentTrack ? 'ms-1' : ''}
                            icon={['fas', isPlaying && isCurrentTrack ? 'pause' : 'play']}
                            size="2x"
                        />
                    </button>
                </div>
                <img src={track.artwork} className="audio-carousel-item__img" alt={track.title} />
            </div>
            <div className="audio-carousel-item__info">
                <a href={setItemLink(track)}>
                    <div className="audio-carousel-item__info__title">
                        {track.title}
                    </div>
                    <div className="audio-carousel-item__info__desc overflow-hidden text-truncate">
                        {track.description}
                    </div>
                </a>
            </div>
        </div>
    );
}
