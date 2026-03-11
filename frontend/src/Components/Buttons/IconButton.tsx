import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconProp } from '@fortawesome/fontawesome-svg-core';

interface IconButtonProps {
    icon: IconProp;
    classList?: string;
    onClick?: (e: React.MouseEvent) => void;
    disabled?: boolean;
    title?: string;
}

export default function IconButton({ icon, classList, onClick, disabled, title }: IconButtonProps) {
    return (
        <button className={`btn btn-icon ${classList || ''}`} onClick={onClick} disabled={disabled} title={title} type="button">
            <FontAwesomeIcon icon={icon} />
        </button>
    );
}
