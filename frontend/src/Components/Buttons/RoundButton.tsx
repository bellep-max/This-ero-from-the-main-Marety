import React from 'react';

interface RoundButtonProps {
    classList?: string;
    onClick?: () => void;
    children?: React.ReactNode;
}

export default function RoundButton({ classList, onClick, children }: RoundButtonProps) {
    return (
        <button className={`btn btn-round ${classList || ''}`} onClick={onClick} type="button">
            {children}
        </button>
    );
}
