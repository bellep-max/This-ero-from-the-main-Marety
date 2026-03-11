import React from 'react';

interface ImageCardProps {
    model?: any;
    route: string;
    title?: string;
}

export default function ImageCard({ model, route: routePath, title }: ImageCardProps) {
    const styleImage = model ? { background: `url(${model.artwork})` } : {};
    const cardTitle = title ?? model?.title ?? '';

    return (
        <a href={routePath} className="hoverable-image" style={styleImage}>
            <div className="hoverable-image-title">
                {cardTitle}
            </div>
        </a>
    );
}
