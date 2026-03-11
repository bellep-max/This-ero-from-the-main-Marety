import React from 'react';
import { SwiperSlide } from 'swiper/react';
import DefaultCarousel from '@/Components/Carousels/DefaultCarousel';

interface SlideCarouselProps {
    items: any[] | Record<string, any>;
}

export default function SlideCarousel({ items = [] }: SlideCarouselProps) {
    const carouselConfig = {
        itemsToShow: 'auto' as const,
        wrapAround: true,
        gap: 24,
    };

    const itemsArray = Array.isArray(items) ? items : Object.values(items);

    return (
        <DefaultCarousel config={carouselConfig}>
            {itemsArray.map((slide: any, i: number) => (
                <SwiperSlide key={i}>
                    <img src={slide.artwork} alt={slide.title} className="img-fluid rounded-4" />
                </SwiperSlide>
            ))}
        </DefaultCarousel>
    );
}
