import React from 'react';
import { useAuthStore } from '@/stores/auth';
import { isNotEmpty } from '@/Services/MiscService';
import UserPageMenu from '@/Components/Sections/UserPageMenu';

interface UserLayoutProps {
    title?: string;
    user?: any;
    overflow?: boolean;
    slides?: any[];
    children: React.ReactNode;
    controls?: React.ReactNode;
    comments?: React.ReactNode;
}

export default function UserLayout({ title = '', user = {}, overflow = true, slides, children, controls, comments }: UserLayoutProps) {
    const isLogged = useAuthStore((s) => s.isLogged);

    return (
        <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
            <div className="container">
                {slides && isNotEmpty(slides) && (
                    <div className="row mb-3">
                        <div className="col-12">
                            {/* SlideCarousel placeholder */}
                        </div>
                    </div>
                )}
                <div className="row">
                    {isLogged && (
                        <div className="col-12 col-xl-3 pb-3 pb-xl-0">
                            <UserPageMenu user={user} />
                        </div>
                    )}
                    <div className={`col ${isLogged ? 'col-xl-9' : 'col-xl-12'}`}>
                        <div className="d-flex flex-column w-100 gap-4 bg-default rounded-5 p-3 p-lg-5">
                            {isNotEmpty(title) && (
                                <div className="d-flex flex-row justify-content-between w-100">
                                    <div className="font-default fs-4">{title}</div>
                                    {controls}
                                </div>
                            )}
                            <div className={`container-fluid w-100 max-height-75 ${overflow ? 'overflow-y-auto' : ''}`}>
                                {children}
                            </div>
                        </div>
                        {comments && (
                            <div className="container-fluid p-0 mt-3">{comments}</div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
