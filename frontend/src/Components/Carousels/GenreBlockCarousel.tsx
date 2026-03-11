import React from 'react';
import { SwiperSlide } from 'swiper/react';
import DefaultCarousel from '@/Components/Carousels/DefaultCarousel';
import ImageCard from '@/Components/Cards/ImageCard';
import route from '@/helpers/route';

interface GenreBlockCarouselProps {
    items: any[];
}

export default function GenreBlockCarousel({ items = [] }: GenreBlockCarouselProps) {
    const carouselConfig = {
        itemsToShow: 'auto' as const,
        wrapAround: true,
        gap: 24,
    };

    return (
        <DefaultCarousel config={carouselConfig}>
            {items.map((slide: any, i: number) => (
                <SwiperSlide key={i}>
                    <ImageCard model={slide} route={route('genres.show', slide.slug)} />
                </SwiperSlide>
            ))}
        </DefaultCarousel>
    );
}
