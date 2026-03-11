import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import apiClient from '@/api/client';
import { FOLLOWERS_API } from '@/api/endpoints';

interface UserFollowButtonProps {
    user: any;
    isFollowing?: boolean;
    onToggle?: (following: boolean) => void;
}

export default function UserFollowButton({ user, isFollowing, onToggle }: UserFollowButtonProps) {
    const handleToggle = async () => {
        try {
            if (isFollowing) {
                await apiClient.delete(FOLLOWERS_API.DELETE(user.id));
                onToggle?.(false);
            } else {
                await apiClient.post(FOLLOWERS_API.STORE, { uuid: user.uuid });
                onToggle?.(true);
            }
        } catch (error) {
            console.error('Follow toggle error:', error);
        }
    };

    return (
        <button className={`btn ${isFollowing ? 'btn-outline' : 'btn-pink'}`} onClick={handleToggle} type="button">
            <FontAwesomeIcon icon={['fas', isFollowing ? 'user-minus' : 'user-plus']} className="me-2" />
            {isFollowing ? 'Unfollow' : 'Follow'}
        </button>
    );
}
