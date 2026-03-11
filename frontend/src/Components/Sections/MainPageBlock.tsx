import React, { useMemo } from 'react';
import PlayIconBig from '@/Components/Icons/PlayIconBig';

interface MainPageBlockProps {
    title: string;
    description?: string;
    gradientBackground?: boolean;
    classList?: string;
    hasIcon?: boolean;
    link?: string;
    children?: React.ReactNode;
}

export default function MainPageBlock({ title, description, gradientBackground = false, classList = '', hasIcon = false, link = '', children }: MainPageBlockProps) {
    const hasLink = link.length > 0;

    return (
        <div className="bg-default">
            <div className={`${gradientBackground ? 'bg-gradient-default' : ''} ${classList}`}>
                <div className="container py-5">
                    <div className="d-flex flex-column justify-content-start gap-4 px-2 px-lg-0">
                        <div className="d-flex flex-row justify-content-start align-items-center gap-3">
                            {hasIcon && <PlayIconBig />}
                            <div className={!gradientBackground ? 'ms-3' : ''}>
                                <div className={`block-title ${gradientBackground ? 'color-light' : 'default-text-color'}`}>
                                    {title}
                                </div>
                                {description && (
                                    <div className={`block-description ${gradientBackground ? 'color-light' : ''}`}>
                                        {description}
                                    </div>
                                )}
                            </div>
                        </div>
                        {children}
                        {hasLink && (
                            <div className="d-flex flex-row justify-content-center align-items-center">
                                <a
                                    href={link}
                                    className={`btn-default btn-wide ${gradientBackground ? 'btn-pink' : 'btn-outline'}`}
                                >
                                    See All
                                </a>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
