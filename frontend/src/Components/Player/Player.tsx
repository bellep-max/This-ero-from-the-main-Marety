import React, { useState, useEffect, useRef, useMemo } from 'react';
import { useAudioPlayerStore } from '@/stores/track';
import { useAuthStore } from '@/stores/auth';
import Icon from '@/Components/Icons/Icon';
import IconButton from '@/Components/Buttons/IconButton';
import VolumeController from '@/Components/Player/VolumeController';

const formatTime = (seconds: number) => {
    if (isNaN(seconds)) return '0:00';
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
};

export default function Player() {
    const audioPlayer = useAudioPlayerStore();
    const { isPlaying, queue, currentTrack, isShuffle, isRepeat, currentTime, duration } = audioPlayer;

    const [isHover, setIsHover] = useState(false);
    const [isExtended, setIsExtended] = useState(false);

    const extensionIcon = isExtended ? 'angles-down' : 'angles-up';
    const hasSingleTrackQueue = queue.length === 1;

    useEffect(() => {
        if (currentTrack && !audioPlayer.audio) {
            audioPlayer.initializeAudio();
        }
    }, []);

    useEffect(() => {
        if (queue.length === 0) {
            setIsExtended(false);
        }
    }, [queue.length]);

    if (!currentTrack) return null;

    const seekerValue = duration ? (currentTime / duration) * 100 : 0;

    return (
        <div className="d-flex flex-column fixed-bottom justify-content-end">
            <div
                id="audioPlayer"
                className="w-100 rounded-top-4 bg-default d-flex flex-row justify-content-between align-items-center player p-3"
            >
                <div className="d-flex flex-row align-items-center gap-3 w-25 h-100">
                    <img
                        alt={currentTrack.title || 'No track'}
                        src={currentTrack.artwork}
                        className="track-image rounded-4"
                    />
                    <span className="title font-default default-text-color d-none d-md-inline-block">
                        {currentTrack.title}
                    </span>
                </div>
                <div className="d-flex flex-column w-50 gap-2 gap-lg-3">
                    <div className="d-flex flex-row justify-content-center align-items-center gap-2 gap-lg-4">
                        <IconButton
                            icon="shuffle"
                            classList={isShuffle ? 'color-active' : ''}
                            onClick={() => useAudioPlayerStore.setState({ isShuffle: !isShuffle })}
                        />
                        <IconButton
                            icon="backward-step"
                            disabled={hasSingleTrackQueue}
                            onClick={() => audioPlayer.playPrevious()}
                        />
                        <button className="btn-play" onClick={() => audioPlayer.togglePlayPause()}>
                            {!isPlaying ? (
                                <Icon classList="ms-1" icon={['fas', 'play']} size="xl" />
                            ) : (
                                <Icon icon={['fas', 'pause']} size="xl" />
                            )}
                        </button>
                        <IconButton
                            icon="forward-step"
                            disabled={hasSingleTrackQueue}
                            onClick={() => audioPlayer.playNext()}
                        />
                        <IconButton
                            icon="repeat"
                            classList={isRepeat ? 'color-active' : ''}
                            onClick={() => useAudioPlayerStore.setState({ isRepeat: !isRepeat })}
                        />
                    </div>
                    <div className="d-flex flex-row justify-content-between gap-4 w-100">
                        {currentTime > 0 && (
                            <div className="font-merge color-grey">{formatTime(currentTime)}</div>
                        )}
                        <div
                            className="w-100 seeker-control"
                            onMouseOver={() => setIsHover(true)}
                            onMouseLeave={() => setIsHover(false)}
                        >
                            <div className="seeker-bar rounded" style={{ width: `${seekerValue}%` }} />
                            <input
                                value={seekerValue}
                                type="range"
                                className="seeker-slider w-100"
                                min="0"
                                max="100"
                                onChange={(e) => audioPlayer.seek(e.target.value)}
                                disabled={!currentTrack}
                            />
                        </div>
                        {duration > 0 && (
                            <div className="font-merge color-grey">{formatTime(duration)}</div>
                        )}
                    </div>
                </div>
                <div className="d-flex flex-row justify-content-start align-items-center w-25 gap-4">
                    <VolumeController />
                    <IconButton classList="ms-auto" icon={extensionIcon} onClick={() => setIsExtended(!isExtended)} />
                </div>
            </div>
            {isExtended && (
                <div className="d-flex flex-column flex-lg-row justify-content-start align-items-center bg-default w-100 overflow-hidden border-top p-2" style={{ height: '100px' }}>
                    <div className="d-flex flex-row gap-2 w-100 overflow-auto">
                        {queue.map((song: any) => (
                            <div key={song.uuid || song.title} className="flex-shrink-0">
                                <div className="d-flex flex-row align-items-center gap-2 p-1">
                                    <img src={song.artwork} alt={song.title} className="img-thumb rounded-2" style={{ width: 50, height: 50 }} />
                                    <span className="font-merge small text-truncate" style={{ maxWidth: 120 }}>{song.title}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
