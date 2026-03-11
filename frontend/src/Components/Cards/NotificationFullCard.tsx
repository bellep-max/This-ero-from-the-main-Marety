import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import IconButton from '@/Components/Buttons/IconButton';
import { $t } from '@/i18n';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/client';
import Activities from '@/Enums/Activities.js';
import route from '@/helpers/route';

interface NotificationFullCardProps {
    notification: any;
    darkFont?: boolean;
    onRead?: (notification: any) => void;
}

export default function NotificationFullCard({ notification, darkFont = false, onRead }: NotificationFullCardProps) {
    const user = useAuthStore((s) => s.user);
    const notificationLink = Activities.getLink(notification);
    const isLink = notificationLink && notificationLink.length > 0;
    const isCollaborationInvite = notification.action === Activities.InviteCollaboration;
    const isRead = notification.is_read;

    const markAsRead = (follow = false) => {
        apiClient.post(
            route('notifications.read', { notification: notification.id }),
            {},
        );
        onRead?.(notification);
    };

    const submit = (response: boolean) => {
        apiClient.post(
            route('playlists.collaborators.response', {
                playlist: notification.subject?.uuid,
                user: notification.user?.uuid,
            }),
            {
                collaborator_uuid: user?.uuid,
                response: response,
            },
        ).then(() => {
            markAsRead();
        });
    };

    return (
        <div className="track no-wrap w-100 justify-content-between">
            <div
                className="d-flex flex-row justify-content-start align-items-center gap-3 pe-auto cursor-pointer"
                onClick={() => markAsRead(!!isLink)}
            >
                <img
                    className="track-image rounded-4 d-md-block d-none"
                    alt={notification.user?.name}
                    src={notification.user?.artwork}
                />
                <div
                    className={`description font-default ${!darkFont ? 'color-light' : 'default-text-color'}`}
                >
                    {Activities.getText(notification)}
                </div>
            </div>
            {isCollaborationInvite && (
                <div className="d-flex flex-column justify-content-between gap-1">
                    <DefaultButton classList="btn-pink btn-narrow" onClick={() => submit(true)}>
                        {$t('buttons.accept')}
                    </DefaultButton>
                    <DefaultButton classList="btn-outline btn-narrow" onClick={() => submit(false)}>
                        {$t('buttons.decline')}
                    </DefaultButton>
                </div>
            )}
            <div className="d-flex flex-row justify-content-end align-items-center gap-3">
                <span className={`font-merge ${!darkFont ? 'color-light' : 'color-grey'}`}>
                    <FontAwesomeIcon icon={['fas', 'clock']} /> {notification.created_at}
                </span>
                <IconButton icon={['fas', 'xmark']} onClick={() => markAsRead()} disabled={isRead} />
            </div>
        </div>
    );
}
