import React from 'react';
import { SwiperSlide } from 'swiper/react';
import DefaultCarousel from '@/Components/Carousels/DefaultCarousel';

interface SongBlockCarouselProps {
    items: any[];
}

export default function SongBlockCarousel({ items = [] }: SongBlockCarouselProps) {
    const carouselConfig = {
        itemsToShow: 'auto' as const,
        wrapAround: true,
        gap: 24,
    };

    return (
        <DefaultCarousel config={carouselConfig}>
            {items.map((slide: any, i: number) => (
                <SwiperSlide key={i}>
                    <div className="d-flex flex-column align-items-center gap-2">
                        <img src={slide.artwork} alt={slide.title} className="img-fluid rounded-4" style={{ width: 150, height: 150, objectFit: 'cover' }} />
                        <span className="font-default small text-truncate" style={{ maxWidth: 150 }}>{slide.title}</span>
                    </div>
                </SwiperSlide>
            ))}
        </DefaultCarousel>
    );
}
