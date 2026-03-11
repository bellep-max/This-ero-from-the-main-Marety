import React, { useState, useMemo } from 'react';
import { $t } from '@/i18n';
import { isNotEmpty } from '@/Services/MiscService';
import ObjectTypes from '@/Enums/ObjectTypes';
import Icon from '@/Components/Icons/Icon';
import IconButton from '@/Components/Buttons/IconButton';
import route from '@/helpers/route';

interface PlaylistTrackProps {
    track: {
        uuid: string;
        title: string;
        artwork?: string;
        duration?: string;
        type?: string;
        favorite?: boolean;
        [key: string]: any;
    };
    podcast_uuid?: string | null;
    likeable?: boolean;
    playlists?: any[];
    collaboratedPlaylists?: any[];
    draggable?: boolean;
    controllable?: boolean;
    darkFont?: boolean;
    onLike?: (data: { uuid: string; type: string }) => void;
    onDeleted?: (data: { uuid: string; type: string }) => void;
    onShared?: (track: any) => void;
    onReported?: (track: any) => void;
    onAddToPlaylist?: (data: { playlist_uuid: string; song_uuid: string }) => void;
}

export default function PlaylistTrack({
    track,
    podcast_uuid = null,
    likeable = false,
    playlists = [],
    collaboratedPlaylists = [],
    draggable = false,
    controllable = false,
    darkFont = false,
    onLike,
    onDeleted,
    onShared,
    onReported,
    onAddToPlaylist,
}: PlaylistTrackProps) {
    const [showPlaylistDropdown, setShowPlaylistDropdown] = useState(false);
    const [showOptionsDropdown, setShowOptionsDropdown] = useState(false);

    const hasCollaboratedPlaylists = isNotEmpty(collaboratedPlaylists);
    const hasOwnPlaylists = isNotEmpty(playlists);
    const type = useMemo(() => ObjectTypes.getObjectType(track.type || ''), [track.type]);

    const link = useMemo(
        () => type === ObjectTypes.Song ? route('songs.show', track.uuid) : route('episodes.show', track.uuid),
        [type, track.uuid]
    );

    return (
        <div className="d-flex flex-row w-100 align-items-center justify-content-start gap-3 p-2 module">
            {likeable && (
                <IconButton
                    icon={track.favorite ? 'heart-circle-check' : 'heart'}
                    onClick={() => onLike?.({ uuid: track.uuid, type: ObjectTypes.Song })}
                />
            )}
            <div className="position-relative flex-shrink-0">
                <img className="img-thumb rounded-2" src={track.artwork} alt={track.title} />
            </div>
            <div className="text-truncate">
                <a className="color-text font-default flex-nowrap text-decoration-none" href={link}>
                    {track.title}
                </a>
            </div>
            <div className="ms-auto d-flex flex-row gap-3 align-items-center">
                <div className="font-merge fs-12 color-grey">{track.duration}</div>
                {likeable && (hasOwnPlaylists || hasCollaboratedPlaylists) && (
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
                                            <button key={playlist.uuid} className="dropdown-item" onClick={() => { onAddToPlaylist?.({ playlist_uuid: playlist.uuid, song_uuid: track.uuid }); setShowPlaylistDropdown(false); }}>
                                                {playlist.title}
                                            </button>
                                        ))}
                                    </>
                                )}
                                {hasCollaboratedPlaylists && (
                                    <>
                                        <h6 className="dropdown-header">{$t('misc.collaborated_playlists')}</h6>
                                        {collaboratedPlaylists.map((playlist: any) => (
                                            <button key={playlist.uuid} className="dropdown-item" onClick={() => { onAddToPlaylist?.({ playlist_uuid: playlist.uuid, song_uuid: track.uuid }); setShowPlaylistDropdown(false); }}>
                                                {playlist.title}
                                            </button>
                                        ))}
                                    </>
                                )}
                            </div>
                        )}
                    </div>
                )}
                {likeable && (
                    <div className="dropdown">
                        <button className="btn-default p-2 btn-icon" onClick={() => setShowOptionsDropdown(!showOptionsDropdown)}>
                            <Icon icon={['fas', 'ellipsis-vertical']} />
                        </button>
                        {showOptionsDropdown && (
                            <div className="dropdown-menu show">
                                <h6 className="dropdown-header">{track.title}</h6>
                                <button className="dropdown-item font-merge" onClick={() => { onShared?.(track); setShowOptionsDropdown(false); }}>
                                    {$t('pages.playlist.menus.share')}
                                </button>
                                <button className="dropdown-item font-merge" onClick={() => { onReported?.(track); setShowOptionsDropdown(false); }}>
                                    {$t('pages.playlist.menus.report')}
                                </button>
                                {controllable && !podcast_uuid && (
                                    <button className="dropdown-item font-merge" onClick={() => { onDeleted?.({ uuid: track.uuid, type }); setShowOptionsDropdown(false); }}>
                                        {$t('pages.playlist.menus.remove_from_playlist')}
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
