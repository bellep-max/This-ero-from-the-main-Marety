import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

interface TestimonialCardProps {
    item: any;
}

export default function TestimonialCard({ item }: TestimonialCardProps) {
    return (
        <div className="testimonials_card_item">
            <div className="text-center fs-3 color-light">
                <FontAwesomeIcon icon={['fas', 'quote-left']} />
                {' '}{item.title}{' '}
                <FontAwesomeIcon icon={['fas', 'quote-right']} />
            </div>
            <div className="testimonials_card_item_text">
                {item.description}
            </div>
            <div className="d-flex flex-row justify-content-center align-items-center gap-1">
                {Array.from({ length: item.rating }, (_, i) => (
                    <FontAwesomeIcon key={i} icon={['fas', 'star']} className="rating_star" />
                ))}
            </div>
        </div>
    );
}
