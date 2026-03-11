import React from 'react';
import { Link } from 'react-router-dom';

interface DefaultLinkProps {
    to: string;
    classList?: string;
    children?: React.ReactNode;
}

export default function DefaultLink({ to, classList, children }: DefaultLinkProps) {
    return (
        <Link to={to} className={`default-link ${classList || ''}`}>
            {children}
        </Link>
    );
}
