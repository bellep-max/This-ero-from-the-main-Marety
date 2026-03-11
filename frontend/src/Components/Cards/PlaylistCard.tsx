import React, { useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import { $t } from '@/i18n';
import apiClient from '@/api/client';
import route from '@/helpers/route';

interface PlaylistCardProps {
    playlist: any;
    controllable?: boolean;
}

export default function PlaylistCard({ playlist, controllable = false }: PlaylistCardProps) {
    const [isClicked, setIsClicked] = useState(false);

    const deletePlaylist = () => {
        apiClient.delete(route('playlists.destroy', playlist.uuid));
    };

    const trimmedTitle = `${playlist.title?.substring(0, 25)}...`;

    if (controllable) {
        return (
            <div className="col-12 col-md-4 p-1">
                <div className="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3">
                    <a
                        className="d-flex flex-column align-items-center gap-2 text-decoration-none"
                        href={route('playlists.show', playlist)}
                    >
                        <img src={playlist.artwork} className="card-item-avatar" alt={playlist.title} />
                        <div className="title font-default default-text-color text-center">
                            {trimmedTitle}
                        </div>
                    </a>
                    <div className="d-flex flex-row justify-content-center align-items-center mt-auto gap-3">
                        {!isClicked && (
                            <DefaultButton classList="btn-outline btn-rounded" onClick={() => setIsClicked(true)}>
                                <FontAwesomeIcon icon={['fas', 'trash']} />
                            </DefaultButton>
                        )}
                        {isClicked && (
                            <div className="d-flex flex-column text-center gap-2">
                                <span className="w-100 font-merge">
                                    <FontAwesomeIcon icon={['fas', 'question']} /> {$t('misc.you_sure')}
                                </span>
                                <div className="d-flex flex-row justify-content-between align-items-center gap-4">
                                    <DefaultButton classList="btn-pink btn-narrow" onClick={deletePlaylist}>
                                        {$t('buttons.yes')}
                                    </DefaultButton>
                                    <DefaultButton classList="btn-outline btn-narrow" onClick={() => setIsClicked(false)}>
                                        {$t('buttons.no')}
                                    </DefaultButton>
                                </div>
                            </div>
                        )}
                        {!isClicked && (
                            <a href={route('playlists.edit', playlist)}>
                                <DefaultButton classList="btn-outline btn-rounded">
                                    <FontAwesomeIcon icon={['fas', 'pen']} />
                                </DefaultButton>
                            </a>
                        )}
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="col-12 col-md-4 p-1">
            <a
                href={route('playlists.show', playlist)}
                className="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3"
            >
                <img src={playlist.artwork} className="card-item-avatar" alt={playlist.title} />
                <div className="title font-default default-text-color text-center">
                    {trimmedTitle}
                </div>
            </a>
        </div>
    );
}
