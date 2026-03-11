import React from 'react';
import { SwiperSlide } from 'swiper/react';
import DefaultCarousel from '@/Components/Carousels/DefaultCarousel';
import ImageLink from '@/Components/Links/ImageLink';

interface ImageCarouselProps {
    items: any[] | Record<string, any>;
    type: string;
}

export default function ImageCarousel({ items = [], type }: ImageCarouselProps) {
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
                    <ImageLink item={slide} type={type} />
                </SwiperSlide>
            ))}
        </DefaultCarousel>
    );
}
