import React from 'react';
import SettingsPageMenu from '@/Components/Sections/SettingsPageMenu';

interface SettingsLayoutProps {
    title: string;
    children: React.ReactNode;
}

export default function SettingsLayout({ title, children }: SettingsLayoutProps) {
    return (
        <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
            <div className="container">
                <div className="row">
                    <div className="col-12 col-xl-3 pb-3 pb-xl-0">
                        <SettingsPageMenu />
                    </div>
                    <div className="col col-xl-9">
                        <div className="d-flex flex-column w-100 gap-4 bg-default rounded-5 p-3 p-lg-5">
                            <div className="font-default fs-4">{title}</div>
                            <div className="container-fluid overflow-x-hidden">{children}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
