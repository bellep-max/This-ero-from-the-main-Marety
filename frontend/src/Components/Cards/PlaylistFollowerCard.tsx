import React from 'react';
import UserFollowButton from '@/Components/Buttons/UserFollowButton';
import { useAuthStore } from '@/stores/auth';
import route from '@/helpers/route';

interface PlaylistFollowerCardProps {
    user: any;
    controllable?: boolean;
}

export default function PlaylistFollowerCard({ user, controllable = false }: PlaylistFollowerCardProps) {
    const currentUser = useAuthStore((s) => s.user);
    const isMe = user.uuid === currentUser?.uuid;

    return (
        <div className="col-12 col-md-6 p-1">
            <div className="playlist-subscriber-card d-flex flex-row justify-content-between align-items-center gap-2 p-3">
                <a href={route('users.show', user)}>
                    <img src={user.artwork} className="playlist-subscriber-card-avatar" alt={user.title} />
                </a>
                <div className="d-flex flex-column justify-content-between gap-3">
                    <a
                        href={route('users.show', user)}
                        className="title font-default default-text-color text-center text-decoration-none"
                    >
                        {user.username}
                    </a>
                    {!isMe && <UserFollowButton user={user} />}
                </div>
            </div>
        </div>
    );
}
