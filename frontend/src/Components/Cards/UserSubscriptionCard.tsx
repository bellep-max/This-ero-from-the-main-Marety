import React from 'react';
import { $t } from '@/i18n';
import route from '@/helpers/route';

interface UserSubscriptionCardProps {
    user: any;
}

export default function UserSubscriptionCard({ user }: UserSubscriptionCardProps) {
    return (
        <div className="col-12 p-1">
            <div className="playlist-subscriber-card d-flex flex-row justify-content-start align-items-center gap-4 p-3">
                <a href={route('users.show', user)}>
                    <img src={user.artwork} className="playlist-subscriber-card-avatar" alt={user.title} />
                </a>
                <div className="d-flex flex-column justify-content-start gap-2 w-100">
                    <a
                        href={route('users.show', user)}
                        className="title font-default default-text-color text-start text-decoration-none"
                    >
                        {user.username}
                    </a>
                    <div className="d-flex flex-row justify-content-between align-items-start w-100">
                        <div className="d-flex flex-column justify-content-start align-items-start w-100">
                            <span className="font-merge color-grey">
                                {$t('pages.user.my_orders.status', { status: user.status })}
                            </span>
                            <span className="font-merge color-grey">
                                {$t('pages.user.my_orders.amount', { amount: user.amount })}
                            </span>
                        </div>
                        <div className="d-flex flex-column justify-content-between align-items-start w-100">
                            <span className="font-merge color-grey">
                                {$t('pages.user.my_orders.last_payment_date', { date: user.last_payment_date })}
                            </span>
                            <span className="font-merge color-grey">
                                {$t('pages.user.my_orders.next_payment_date', { date: user.next_payment_date })}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
