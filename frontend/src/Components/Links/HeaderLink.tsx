import React from 'react';
import { Link } from 'react-router-dom';
import { $t } from '@/i18n';

interface HeaderLinkProps {
    linkItem: any;
}

export default function HeaderLink({ linkItem }: HeaderLinkProps) {
    return (
        <Link to={linkItem.route} className="header-link nav-link">
            {$t(linkItem.key)}
        </Link>
    );
}
