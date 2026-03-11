import React, { useState, useMemo } from 'react';
import { useAudioPlayerStore } from '@/stores/track';
import { useAuthStore } from '@/stores/auth';
import { $t } from '@/i18n';
import ObjectTypes from '@/Enums/ObjectTypes';
import route from '@/helpers/route';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import Icon from '@/Components/Icons/Icon';
import IconButton from '@/Components/Buttons/IconButton';

interface AdventureProps {
    adventure: {
        uuid: string;
        path: string;
        title: string;
        artwork?: string;
        duration?: string;
        is_patron?: boolean;
        is_liked?: boolean;
        allow_download?: boolean;
        type?: string;
        tags?: { tag: string }[];
        children?: any[];
        user?: { uuid: string; name: string };
        [key: string]: any;
    };
    canView?: boolean;
    darkFont?: boolean;
    is_owned?: boolean;
    onAddToFavorites?: (data: { uuid: string; type: string }) => void;
    onRemoveFromFavorites?: (data: { uuid: string; type: string }) => void;
}

export default function Adventure({ adventure, canView = false, darkFont = false, is_owned = false, onAddToFavorites, onRemoveFromFavorites }: AdventureProps) {
    const [isHover, setIsHover] = useState(false);
    const [showOptionsDropdown, setShowOptionsDropdown] = useState(false);

    const audioPlayer = useAudioPlayerStore();
    const { isPlaying, currentTrack, error: playerError } = audioPlayer;
    const isLogged = useAuthStore((s) => s.isLogged);

    const isCurrentTrack = currentTrack?.uuid === adventure.uuid;
    const hasTags = (adventure.tags?.length ?? 0) > 0;
    const canDownload = is_owned || adventure.allow_download;
    const tags = useMemo(() => adventure.tags?.slice(0, 4) || [], [adventure.tags]);

    const handlePlayClick = () => {
        try {
            if (isCurrentTrack) {
                audioPlayer.togglePlayPause();
            } else {
                audioPlayer.setTracks(adventure);
            }
        } catch (error) {
            console.error('Failed to play track:', error);
        }
    };

    const textColorClass = darkFont ? 'default-text-color' : 'color-light';

    return (
        <div
            className={`track no-wrap${isCurrentTrack ? ' border border-danger-subtle' : ''}${playerError && isCurrentTrack ? ' error' : ''}`}
            onMouseOver={() => setIsHover(true)}
            onMouseLeave={() => setIsHover(false)}
        >
            <div className="col-8 order-first col-lg-5 d-flex flex-row align-items-center gap-1 gap-lg-3">
                <div className="d-block">
                    <button className="btn-play-track" onClick={handlePlayClick} disabled={!isLogged}>
                        <Icon
                            className={!isPlaying || !isCurrentTrack ? 'ms-1' : ''}
                            icon={['fas', isPlaying && isCurrentTrack ? 'pause' : 'play']}
                            size="2x"
                        />
                    </button>
                    {playerError && isCurrentTrack && (
                        <span className="text-danger small d-block mt-1">{playerError}</span>
                    )}
                </div>
                <div className="position-relative d-none d-lg-inline-block">
                    {adventure.is_patron && (
                        <Icon icon="star" classList="position-absolute top-0 start-100 translate-middle color-yellow" />
                    )}
                    <Icon icon="route" classList="position-absolute top-100 start-100 translate-middle color-yellow" />
                    <img alt={adventure.title} src={adventure.artwork} className="track-image rounded-4 d-md-block d-none" />
                </div>
                <div className="d-flex flex-column justify-content-center align-items-start text-truncate">
                    <a className={`title font-default link ${textColorClass}`} href={route('adventures.show', adventure)}>
                        {adventure.title}
                    </a>
                    <div className={`description ${textColorClass}`}>
                        <div className="d-none d-md-block">
                            <span>by </span>
                            {canView ? (
                                <a href={route('users.show', adventure.user?.uuid)} className={`link ${textColorClass}`} title={adventure.user?.name}>
                                    {adventure.user?.name ?? ''}
                                </a>
                            ) : (
                                <div className="link">{adventure.user?.name ?? ''}</div>
                            )}
                        </div>
                        <div className="d-block d-md-none">
                            <span>by {adventure.user?.name}</span>
                        </div>
                    </div>
                </div>
            </div>
            {hasTags && (
                <div className="d-none d-lg-flex col flex-row flex-wrap justify-content-center align-items-center gap-2">
                    {tags.map((tag: any, i: number) => (
                        <DefaultButton key={i} classList="btn-narrow btn-pink">
                            {tag.tag}
                        </DefaultButton>
                    ))}
                </div>
            )}
            <div className="col order-lg-last d-flex flex-row justify-content-end align-items-center gap-1 gap-lg-3 ms-auto">
                <span className={`font-merge ${textColorClass}`}>{adventure.duration}</span>
                <button className="btn btn-icon" onClick={() => {}} type="button">
                    <Icon icon={['fas', 'code-branch']} /> {adventure.children?.length ?? 0}
                </button>
                {canView && (
                    <div className="dropdown">
                        <button className="btn-default p-2 btn-icon" onClick={() => setShowOptionsDropdown(!showOptionsDropdown)}>
                            <Icon icon={['fas', 'ellipsis-vertical']} />
                        </button>
                        {showOptionsDropdown && (
                            <div className="dropdown-menu show">
                                {adventure.is_liked ? (
                                    <button className="dropdown-item" onClick={() => { onRemoveFromFavorites?.({ uuid: adventure.uuid, type: ObjectTypes.Adventure }); setShowOptionsDropdown(false); }}>
                                        {$t('menus.track.remove_from_favorites')}
                                    </button>
                                ) : (
                                    <button className="dropdown-item" onClick={() => { onAddToFavorites?.({ uuid: adventure.uuid, type: ObjectTypes.Adventure }); setShowOptionsDropdown(false); }}>
                                        {$t('menus.track.add_to_favorites')}
                                    </button>
                                )}
                                <button className="dropdown-item" onClick={() => setShowOptionsDropdown(false)}>
                                    {$t('menus.track.share')}
                                </button>
                                <button className="dropdown-item" onClick={() => setShowOptionsDropdown(false)}>
                                    {$t('menus.track.report')}
                                </button>
                                {is_owned && (
                                    <a className="dropdown-item" href={route('adventures.edit', adventure)}>
                                        {$t('buttons.edit')}
                                    </a>
                                )}
                                {canDownload && (
                                    <button className="dropdown-item" onClick={() => setShowOptionsDropdown(false)}>
                                        {$t('buttons.download')}
                                    </button>
                                )}
                            </div>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
