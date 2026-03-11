import React, { useState } from 'react';
import { $t } from '@/i18n';
import { useAuthStore } from '@/stores/auth';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import ProfileButton from '@/Components/Buttons/ProfileButton';
import AvatarInput from '@/Components/AvatarInput';
import Icon from '@/Components/Icons/Icon';
import LinktreeButton from '@/Components/Buttons/LinktreeButton';
import SubscriptionButton from '@/Components/Buttons/SubscriptionButton';
import UserFollowButton from '@/Components/Buttons/UserFollowButton';

interface UserPageMenuProps {
    user: {
        name: string;
        own_profile?: boolean;
        menu?: any[];
        linktree_link?: string;
        allow_upload?: boolean;
        [key: string]: any;
    };
}

export default function UserPageMenu({ user }: UserPageMenuProps) {
    const [showMenu, setShowMenu] = useState(false);
    const currentUser = useAuthStore((s) => s.user);

    return (
        <>
            <DefaultButton classList="btn-outline d-xl-none" onClick={() => setShowMenu(!showMenu)}>
                {$t('buttons.show')}
            </DefaultButton>
            <div className={`d-flex flex-column w-100 bg-default rounded-5 px-lg-3 py-lg-4 gap-3 ${showMenu ? '' : 'd-none d-xl-flex'}`}>
                <div className="d-flex flex-column justify-content-start align-items-center gap-4 text-center">
                    <AvatarInput user={user} />
                    <div className="fs-4 font-default">{user.name}</div>
                </div>
                <div className="d-flex flex-column justify-content-start align-items-center gap-1">
                    {!user.own_profile && currentUser && (
                        <UserFollowButton user={user} />
                    )}
                    {!user.own_profile && currentUser?.allow_upload && (
                        <SubscriptionButton />
                    )}
                    <DefaultButton classList="btn-outline w-100" onClick={() => {}}>
                        <Icon icon={['fas', 'share-nodes']} />
                        {' '}{$t('buttons.share.title')}
                    </DefaultButton>
                    <LinktreeButton link={user.linktree_link} />
                </div>
                <div className="w-100 d-flex flex-column gap-1">
                    {user.menu?.map((menuItem: any, i: number) => (
                        <ProfileButton key={i}>
                            {menuItem.label || menuItem.title || menuItem.name}
                        </ProfileButton>
                    ))}
                </div>
            </div>
        </>
    );
}
