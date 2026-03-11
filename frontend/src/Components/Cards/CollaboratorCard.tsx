import React, { useState, useEffect } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import { $t } from '@/i18n';
import route from '@/helpers/route';

interface CollaboratorCardProps {
    user: any;
    playlist_uuid: string;
    onInvited?: (user: any) => void;
}

export default function CollaboratorCard({ user, playlist_uuid, onInvited }: CollaboratorCardProps) {
    const [isInvited, setIsInvited] = useState(false);

    useEffect(() => {
        setIsInvited(user.playlists?.some((playlist: any) => playlist.uuid === playlist_uuid) ?? false);
    }, [user, playlist_uuid]);

    const sendInvitation = () => {
        onInvited?.(user);
        setIsInvited(true);
    };

    return (
        <div className="col-12 col-md-6 p-1">
            <div className="playlist-subscriber-card d-flex flex-row justify-content-between align-items-center gap-2 p-3">
                <a href={route('users.show', user)}>
                    <img src={user.artwork} className="playlist-subscriber-card-avatar" alt={user.title} />
                </a>
                <div className="d-flex flex-column justify-content-between gap-3 text-truncate px-2">
                    <a
                        href={route('users.show', user)}
                        className="title font-default default-text-color text-center text-decoration-none"
                    >
                        {user.username}
                    </a>
                    <DefaultButton
                        classList={`w-100 btn-default ${isInvited ? 'btn-pink' : 'btn-outline'}`}
                        disabled={isInvited}
                        onClick={sendInvitation}
                    >
                        <FontAwesomeIcon icon={['fas', 'user-plus']} />{' '}
                        {$t('buttons.invite')}
                    </DefaultButton>
                </div>
            </div>
        </div>
    );
}
