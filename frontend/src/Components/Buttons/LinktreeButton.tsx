import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

interface LinktreeButtonProps {
    link: any;
    classList?: string;
}

export default function LinktreeButton({ link, classList }: LinktreeButtonProps) {
    return (
        <a href={link?.url} target="_blank" rel="noreferrer" className={`btn btn-linktree ${classList || ''}`}>
            <FontAwesomeIcon icon={['fas', 'link']} className="me-2" />
            {link?.title || link?.url}
        </a>
    );
}
