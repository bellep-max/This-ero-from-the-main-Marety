import React from 'react';

interface ProfileButtonProps {
    classList?: string;
    onClick?: () => void;
    children?: React.ReactNode;
}

export default function ProfileButton({ classList, onClick, children }: ProfileButtonProps) {
    return (
        <button className={`btn btn-profile ${classList || ''}`} onClick={onClick} type="button">
            {children}
        </button>
    );
}
