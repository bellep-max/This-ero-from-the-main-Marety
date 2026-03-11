import React from 'react';
import { SwiperSlide } from 'swiper/react';
import DefaultCarousel from '@/Components/Carousels/DefaultCarousel';

interface TestimonialCarouselProps {
    items: any[] | Record<string, any>;
}

export default function TestimonialCarousel({ items = [] }: TestimonialCarouselProps) {
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
                    <div className="d-flex flex-column align-items-center gap-3 p-3 bg-default rounded-4">
                        {slide.artwork && <img src={slide.artwork} alt={slide.name || slide.title} className="rounded-circle" style={{ width: 60, height: 60, objectFit: 'cover' }} />}
                        <span className="font-default fw-bold">{slide.name || slide.title}</span>
                        {slide.content && <p className="font-merge color-grey text-center small">{slide.content}</p>}
                    </div>
                </SwiperSlide>
            ))}
        </DefaultCarousel>
    );
}
