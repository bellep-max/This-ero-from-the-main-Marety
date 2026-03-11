import React, { useState, useEffect, useRef } from 'react';
import { useAudioPlayerStore } from '@/stores/track';
import IconButton from '@/Components/Buttons/IconButton';

export default function VolumeController() {
    const audioPlayer = useAudioPlayerStore();
    const storeVolume = audioPlayer.volume;

    const [isHover, setIsHover] = useState(false);
    const [isMuted, setIsMuted] = useState(false);
    const [volumeLevel, setVolumeLevel] = useState(Math.round(storeVolume * 100));
    const [volumePercentage, setVolumePercentage] = useState(Math.round(storeVolume * 100));

    const toggleVolume = () => {
        const newMuted = !isMuted;
        setIsMuted(newMuted);
        const newVolume = newMuted ? 0 : volumeLevel / 100;
        audioPlayer.setVolume(newVolume);
        setVolumePercentage(newMuted ? 0 : volumeLevel);
    };

    const handleVolumeInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = parseInt(e.target.value);
        setVolumePercentage(value);
        audioPlayer.setVolume(value / 100);
        if (value === 0) {
            setIsMuted(true);
        } else {
            setIsMuted(false);
            setVolumeLevel(value);
        }
    };

    useEffect(() => {
        const newPercentage = Math.round(storeVolume * 100);
        setVolumePercentage(newPercentage);
        if (storeVolume === 0) {
            setIsMuted(true);
        } else {
            setIsMuted(false);
            setVolumeLevel(newPercentage);
        }
    }, [storeVolume]);

    const volumeIcon = isMuted || volumePercentage === 0
        ? 'volume-xmark'
        : volumePercentage < 50
            ? 'volume-low'
            : 'volume-high';

    return (
        <div className="d-none d-lg-flex flex-row justify-content-end align-items-center gap-3 w-75">
            <IconButton icon={volumeIcon} onClick={toggleVolume} classList="w-25" />
            <div
                className="volume-control w-75"
                onMouseOver={() => setIsHover(true)}
                onMouseLeave={() => setIsHover(false)}
                style={{ display: 'grid', gridTemplateAreas: "'stack'", alignItems: 'center', position: 'relative', width: '200px', height: '20px' }}
            >
                <div
                    className="volume-bar rounded"
                    style={{ width: `${volumePercentage}%`, gridArea: 'stack', backgroundColor: 'var(--primary-color, #e836c5)', height: '8px', zIndex: 2, pointerEvents: 'none' }}
                />
                <input
                    value={volumePercentage}
                    type="range"
                    className="volume-slider w-100"
                    min="0"
                    max="100"
                    onChange={handleVolumeInput}
                    style={{ gridArea: 'stack', WebkitAppearance: 'none', background: 'transparent', width: '100%', height: '100%', zIndex: 1, cursor: 'pointer' }}
                />
            </div>
        </div>
    );
}
