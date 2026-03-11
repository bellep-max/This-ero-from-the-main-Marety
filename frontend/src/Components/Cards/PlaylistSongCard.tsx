import React from 'react';
import IconButton from '@/Components/Buttons/IconButton';
import { useAudioPlayerStore } from '@/stores/track';

interface PlaylistSongCardProps {
    song: any;
    onDeleted?: () => void;
}

export default function PlaylistSongCard({ song, onDeleted }: PlaylistSongCardProps) {
    const deleteFromQueue = useAudioPlayerStore((s) => s.deleteFromQueue);

    const trimmedTitle = `${song.title?.substring(0, 25)}...`;

    const handleDelete = () => {
        deleteFromQueue(song.uuid);
        onDeleted?.();
    };

    return (
        <div className="d-flex flex-row align-items-center justify-content-start gap-3 p-2 module position-relative w-100">
            <IconButton
                classList="position-absolute top-0 start-100 translate-middle"
                icon={['fas', 'xmark']}
                onClick={handleDelete}
            />
            <img className="img-thumb rounded-2" src={song.artwork} alt={song.title} />
            <span className="text-truncate color-text font-default flex-nowrap text-decoration-none">
                {trimmedTitle}
            </span>
        </div>
    );
}
