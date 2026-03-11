import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconProp } from '@fortawesome/fontawesome-svg-core';

interface IconProps {
    icon: IconProp;
    classList?: string;
    className?: string;
    [key: string]: any;
}

export default function Icon({ icon, classList, className, ...rest }: IconProps) {
    return <FontAwesomeIcon icon={icon} className={classList || className || ''} {...rest} />;
}
