import React, { useState, useMemo } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useAudioPlayerStore } from '@/stores/track';
import { useAuthStore } from '@/stores/auth';
import { $t } from '@/i18n';
import { isNotEmpty } from '@/Services/MiscService';
import ObjectTypes from '@/Enums/ObjectTypes';
import route from '@/helpers/route';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import Icon from '@/Components/Icons/Icon';

interface SongProps {
    song: {
        uuid: string;
        path: string;
        title: string;
        streamable: boolean;
        artwork?: string;
        duration?: string;
        is_patron?: boolean;
        is_liked?: boolean;
        allow_download?: boolean;
        type?: string;
        tags?: { tag: string }[];
        user?: { uuid: string; name: string };
        [key: string]: any;
    };
    canView?: boolean;
    darkFont?: boolean;
    is_owned?: boolean;
    onAddToFavorites?: (data: { uuid: string; type: string }) => void;
    onRemoveFromFavorites?: (data: { uuid: string; type: string }) => void;
    onAddToPlaylist?: (data: { playlist_uuid: string; song_uuid: string }) => void;
}

export default function Song({ song, canView = false, darkFont = false, is_owned = false, onAddToFavorites, onRemoveFromFavorites, onAddToPlaylist }: SongProps) {
    const [isHover, setIsHover] = useState(false);
    const [showPlaylistDropdown, setShowPlaylistDropdown] = useState(false);
    const [showOptionsDropdown, setShowOptionsDropdown] = useState(false);

    const audioPlayer = useAudioPlayerStore();
    const { isPlaying, currentTrack, error: playerError } = audioPlayer;
    const user = useAuthStore((s) => s.user);
    const isLogged = useAuthStore((s) => s.isLogged);

    const playlists = user?.playlists || [];
    const collaboratedPlaylists = user?.approvedCollaboratedPlaylists || [];

    const isCurrentTrack = currentTrack?.uuid === song.uuid;
    const canDownload = is_owned || song.allow_download;
    const tags = useMemo(() => song.tags?.slice(0, 4) || [], [song.tags]);
    const hasCollaboratedPlaylists = collaboratedPlaylists.length > 0;
    const hasOwnPlaylists = playlists.length > 0;
    const type = useMemo(() => ObjectTypes.getObjectType(song.type || ''), [song.type]);

    const handlePlayClick = () => {
        try {
            if (isCurrentTrack) {
                audioPlayer.togglePlayPause();
            } else {
                audioPlayer.setTracks(song);
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
                    <button className="btn-play-track" onClick={handlePlayClick} disabled={!song.streamable && !isLogged}>
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
                    {song.is_patron && (
                        <Icon icon="star" classList="position-absolute top-0 start-100 translate-middle color-yellow" />
                    )}
                    <Icon icon="play" classList="position-absolute top-100 start-100 translate-middle color-yellow" />
                    <img alt={song.title} src={song.artwork} className="track-image rounded-4 d-md-block d-none" />
                </div>
                <div className="d-flex flex-column justify-content-center align-items-start text-truncate">
                    <a className={`title font-default link ${textColorClass}`} href={route('songs.show', song.uuid)}>
                        {song.title}
                    </a>
                    <div className={`description ${textColorClass}`}>
                        <div className="d-none d-md-block">
                            <span>by </span>
                            {canView ? (
                                <a href={route('users.show', song.user?.uuid)} className={`link ${textColorClass}`} title={song.user?.name}>
                                    {song.user?.name ?? ''}
                                </a>
                            ) : (
                                <div className="link">{song.user?.name ?? ''}</div>
                            )}
                        </div>
                        <div className="d-block d-md-none">
                            <span>by {song.user?.name}</span>
                        </div>
                    </div>
                </div>
            </div>
            {isNotEmpty(song.tags) && (
                <div className="d-none d-lg-flex col flex-row flex-wrap justify-content-center align-items-center gap-2">
                    {tags.map((tag: any, i: number) => (
                        <DefaultButton key={i} classList="btn-narrow btn-pink">
                            {tag.tag}
                        </DefaultButton>
                    ))}
                </div>
            )}
            <div className="col order-lg-last d-flex flex-row justify-content-end align-items-center gap-1 gap-lg-3 ms-auto">
                <span className={`font-merge ${textColorClass}`}>{song.duration}</span>
                {canView && playlists && (
                    <div className="dropdown">
                        <button className="btn-default p-2 btn-icon" onClick={() => setShowPlaylistDropdown(!showPlaylistDropdown)}>
                            <Icon icon={['fas', 'file-circle-plus']} />
                        </button>
                        {showPlaylistDropdown && (
                            <div className="dropdown-menu show">
                                {hasOwnPlaylists && (
                                    <>
                                        <h6 className="dropdown-header">{$t('misc.my_playlists')}</h6>
                                        {playlists.map((playlist: any) => (
                                            <button key={playlist.uuid} className="dropdown-item" onClick={() => { onAddToPlaylist?.({ playlist_uuid: playlist.uuid, song_uuid: song.uuid }); setShowPlaylistDropdown(false); }}>
                                                {playlist.title}
                                            </button>
                                        ))}
                                    </>
                                )}
                                {hasCollaboratedPlaylists && (
                                    <>
                                        <h6 className="dropdown-header">{$t('misc.collaborated_playlists')}</h6>
                                        {collaboratedPlaylists.map((playlist: any) => (
                                            <button key={playlist.uuid} className="dropdown-item" onClick={() => { onAddToPlaylist?.({ playlist_uuid: playlist.uuid, song_uuid: song.uuid }); setShowPlaylistDropdown(false); }}>
                                                {playlist.title}
                                            </button>
                                        ))}
                                    </>
                                )}
                            </div>
                        )}
                    </div>
                )}
                {canView && (
                    <div className="dropdown">
                        <button className="btn-default p-2 btn-icon" onClick={() => setShowOptionsDropdown(!showOptionsDropdown)}>
                            <Icon icon={['fas', 'ellipsis-vertical']} />
                        </button>
                        {showOptionsDropdown && (
                            <div className="dropdown-menu show">
                                {song.is_liked ? (
                                    <button className="dropdown-item" onClick={() => { onRemoveFromFavorites?.({ uuid: song.uuid, type }); setShowOptionsDropdown(false); }}>
                                        {$t('menus.track.remove_from_favorites')}
                                    </button>
                                ) : (
                                    <button className="dropdown-item" onClick={() => { onAddToFavorites?.({ uuid: song.uuid, type }); setShowOptionsDropdown(false); }}>
                                        {$t('menus.track.add_to_favorites')}
                                    </button>
                                )}
                                <button className="dropdown-item" onClick={() => setShowOptionsDropdown(false)}>
                                    {$t('menus.track.share')}
                                </button>
                                <button className="dropdown-item" onClick={() => setShowOptionsDropdown(false)}>
                                    {$t('menus.track.report')}
                                </button>
                                <h6 className="dropdown-header">{$t('menus.track.add_to_queue.title')}</h6>
                                <button className="dropdown-item" onClick={() => { audioPlayer.addToQueueNext(song); setShowOptionsDropdown(false); }}>
                                    {$t('menus.track.add_to_queue.play_next')}
                                </button>
                                <button className="dropdown-item" onClick={() => { audioPlayer.addToQueueNext(song); setShowOptionsDropdown(false); }}>
                                    {$t('menus.track.add_to_queue.play_last')}
                                </button>
                                {is_owned && (
                                    <a className="dropdown-item" href={route('songs.edit', song)}>
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
