import React from 'react';

interface DefaultButtonProps {
    classList?: string;
    className?: string;
    disabled?: boolean;
    type?: 'button' | 'submit' | 'reset';
    onClick?: (e: React.MouseEvent) => void;
    children?: React.ReactNode;
}

export default function DefaultButton({ classList, className, disabled, type = 'button', onClick, children }: DefaultButtonProps) {
    return (
        <button
            type={type}
            className={`btn btn-default ${classList || className || ''}`}
            disabled={disabled}
            onClick={onClick}
        >
            {children}
        </button>
    );
}
