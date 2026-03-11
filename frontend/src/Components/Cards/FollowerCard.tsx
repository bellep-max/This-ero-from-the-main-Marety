import React from 'react';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import { $t } from '@/i18n';
import route from '@/helpers/route';

interface FollowerCardProps {
    user: any;
    controllable?: boolean;
    showStats?: boolean;
    onUnfollow?: (user: any) => void;
}

export default function FollowerCard({ user, controllable = false, showStats = false, onUnfollow }: FollowerCardProps) {
    return (
        <div className="col-12 col-md-4 p-1">
            <a
                href={route('users.show', user)}
                className="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3 text-decoration-none"
            >
                <img src={user.artwork} className="card-item-avatar" alt={user.title} />
                <div className="title font-default default-text-color text-center">
                    {user.username}
                </div>
                {showStats && (
                    <div className="mt-auto d-flex flex-row justify-content-between align-items-end text-center font-merge gap-2">
                        <div className="d-flex flex-column gap-1">
                            <span className="color-pink fs-5">{user.tracks}</span>
                            <span className="color-grey">{$t('pages.user.stats.tracks')}</span>
                        </div>
                        <div className="d-flex flex-column gap-1">
                            <span className="color-pink fs-5">{user.playlists}</span>
                            <span className="color-grey">{$t('pages.user.stats.playlists')}</span>
                        </div>
                        <div className="d-flex flex-column gap-1">
                            <span className="color-pink fs-5">{user.adventures}</span>
                            <span className="color-grey">{$t('pages.user.stats.adventures')}</span>
                        </div>
                    </div>
                )}
            </a>
            {controllable && (
                <DefaultButton classList="mt-2 btn-outline w-100" onClick={() => onUnfollow?.(user)}>
                    {$t('buttons.unfollow')}
                </DefaultButton>
            )}
        </div>
    );
}
