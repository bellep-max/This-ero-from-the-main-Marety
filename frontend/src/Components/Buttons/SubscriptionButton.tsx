import React from 'react';

interface SubscriptionButtonProps {
    classList?: string;
    onClick?: () => void;
    children?: React.ReactNode;
}

export default function SubscriptionButton({ classList, onClick, children }: SubscriptionButtonProps) {
    return (
        <button className={`btn btn-subscription ${classList || ''}`} onClick={onClick} type="button">
            {children}
        </button>
    );
}
