import React, { useState } from 'react';
import { $t } from '@/i18n';
import { useAuthStore } from '@/stores/auth';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import ProfileButton from '@/Components/Buttons/ProfileButton';
import AvatarInput from '@/Components/AvatarInput';

export default function SettingsPageMenu() {
    const [showMenu, setShowMenu] = useState(false);
    const user = useAuthStore((s) => s.user);
    const pageMenu = useAuthStore((s) => s.pageMenu);

    if (!user) return null;

    return (
        <>
            <DefaultButton classList="btn-outline d-xl-none" onClick={() => setShowMenu(!showMenu)}>
                {$t('buttons.filters')}
            </DefaultButton>
            <div className={`d-flex flex-column w-100 bg-default rounded-5 px-3 py-4 gap-3 ${showMenu ? '' : 'd-none d-xl-flex'}`}>
                <div className="d-flex flex-column justify-content-start align-items-center gap-4 text-center">
                    <AvatarInput user={user} />
                    <div className="fs-4 font-default">{user.name}</div>
                </div>
                <div className="w-100 d-flex flex-column gap-1">
                    {pageMenu.map((menuItem: any, i: number) => (
                        <ProfileButton key={i}>
                            {menuItem.label || menuItem.title || menuItem.name}
                        </ProfileButton>
                    ))}
                </div>
            </div>
        </>
    );
}
