import React, { useState, useEffect, useRef, useMemo } from 'react';
import { $t } from '@/i18n';
import Icon from '@/Components/Icons/Icon';
import DefaultButton from '@/Components/Buttons/DefaultButton';
import apiClient from '@/api/client';
import route from '@/helpers/route';

interface AvatarInputProps {
    user: {
        name: string;
        artwork?: string;
        own_profile?: boolean;
        role?: string;
        subscription?: { status: string } | null;
        [key: string]: any;
    };
}

export default function AvatarInput({ user }: AvatarInputProps) {
    const [avatarPreview, setAvatarPreview] = useState<string>(user.artwork || '');
    const [artwork, setArtwork] = useState<File | null>(null);
    const [processing, setProcessing] = useState(false);
    const avatarInputRef = useRef<HTMLInputElement>(null);

    const imageSelected = artwork !== null;
    const isSubscribed = !!user.subscription;

    useEffect(() => {
        setAvatarPreview(user.artwork || '');
    }, [user.artwork]);

    const setImagePreview = (e: React.ChangeEvent<HTMLInputElement>) => {
        const image = e.target.files?.[0];
        if (image) {
            setArtwork(image);
            setAvatarPreview(URL.createObjectURL(image));
        }
    };

    const resetImage = () => {
        setAvatarPreview(user.artwork || '');
        setArtwork(null);
    };

    const saveImage = async () => {
        if (!artwork) return;
        setProcessing(true);
        try {
            const formData = new FormData();
            formData.append('_method', 'patch');
            formData.append('artwork', artwork);
            await apiClient.post(route('settings.profile.avatar.update'), formData);
            setArtwork(null);
        } catch (error) {
            console.error('Failed to save avatar:', error);
        } finally {
            setProcessing(false);
        }
    };

    return (
        <div className="d-flex flex-column justify-content-start align-items-center gap-2 text-center">
            {user.own_profile && (
                <span className="font-default fs-14">{$t('pages.settings.profile_image_title')}</span>
            )}
            <div className="position-relative">
                {user.own_profile && isSubscribed && (
                    <Icon
                        icon={['fas', 'star']}
                        classList={`position-absolute top-0 start-0 translate-middle ${user.subscription?.status === 'active' ? 'color-yellow' : 'color-grey'}`}
                    />
                )}
                <img src={avatarPreview} alt={user.name} className="profile-img-lg rounded-circle" />
                <span className="p-1 rounded-pill bg-pink color-light position-absolute top-0 start-100 translate-middle">
                    {user.role}
                </span>
            </div>
            {user.own_profile && (
                <div className="mt-3 d-flex flex-column gap-2">
                    {!imageSelected ? (
                        <label htmlFor="avatar-input" className="cursor-pointer btn-default btn-pink btn-narrow">
                            {$t('buttons.select_photo')}
                        </label>
                    ) : (
                        <div className="d-flex flex-row justify-content-center align-items-center gap-2 w-100">
                            <DefaultButton classList="btn-outline btn-narrow" onClick={resetImage} disabled={processing}>
                                {$t('buttons.cancel')}
                            </DefaultButton>
                            <DefaultButton classList="btn-pink btn-narrow" onClick={saveImage} disabled={processing}>
                                {$t('buttons.save_changes')}
                            </DefaultButton>
                        </div>
                    )}
                    <input
                        type="file"
                        id="avatar-input"
                        ref={avatarInputRef}
                        className="d-none"
                        accept="image/*"
                        onChange={setImagePreview}
                    />
                    <div className="font-merge color-grey fs-14">
                        {$t('pages.settings.profile_image_description')}
                    </div>
                </div>
            )}
        </div>
    );
}
