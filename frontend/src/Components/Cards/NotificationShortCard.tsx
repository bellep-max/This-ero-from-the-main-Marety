import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import IconButton from '@/Components/Buttons/IconButton';
import apiClient from '@/api/client';
import Activities from '@/Enums/Activities.js';
import route from '@/helpers/route';

interface NotificationShortCardProps {
    notification: any;
    darkFont?: boolean;
    onRead?: (notification: any) => void;
}

export default function NotificationShortCard({ notification, darkFont = false, onRead }: NotificationShortCardProps) {
    const notificationLink = Activities.getLink(notification);
    const isLink = notificationLink && notificationLink.length > 0;

    const markAsRead = () => {
        apiClient.post(
            route('notifications.read', { notification: notification.id }),
            {},
        ).then(() => {
            onRead?.(notification);
        });
    };

    const Wrapper = isLink ? 'a' : 'div';

    return (
        <div className="track no-wrap w-100 justify-content-between p-2">
            <Wrapper
                className="d-flex flex-row justify-content-start align-items-center gap-3 text-decoration-none"
                {...(isLink ? { href: notificationLink } : {})}
            >
                <img
                    className="notification-image rounded-4 d-md-block d-none"
                    alt={notification.user?.name}
                    src={notification.user?.artwork}
                    style={{ width: '35px', height: '35px' }}
                />
                <div
                    className={`description flex-wrap font-default ${!darkFont ? 'color-light' : 'default-text-color'}`}
                >
                    {Activities.getText(notification)}
                </div>
            </Wrapper>
            <div className="d-flex flex-row justify-content-end align-items-center gap-3">
                <span className={`font-merge ${!darkFont ? 'color-light' : 'color-grey'}`}>
                    <FontAwesomeIcon icon={['fas', 'clock']} />{' '}
                    {notification.created_at}
                </span>
                <IconButton icon={['fas', 'xmark']} onClick={markAsRead} />
            </div>
        </div>
    );
}
