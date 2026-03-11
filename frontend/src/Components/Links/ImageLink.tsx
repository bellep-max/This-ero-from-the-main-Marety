import React from 'react';
import { Link } from 'react-router-dom';

interface ImageLinkProps {
    item: any;
    type?: string;
    classList?: string;
}

export default function ImageLink({ item, type, classList }: ImageLinkProps) {
    const getLink = () => {
        if (type === 'genre') return `/genre/${item.slug}`;
        if (type === 'post') return `/blog/${item.slug}`;
        return item.url || '#';
    };

    return (
        <Link to={getLink()} className={`image-link ${classList || ''}`}>
            <div className="image-link__image">
                {item.artwork && <img src={item.artwork} alt={item.title || item.name} />}
            </div>
            <div className="image-link__title">{item.title || item.name}</div>
        </Link>
    );
}
