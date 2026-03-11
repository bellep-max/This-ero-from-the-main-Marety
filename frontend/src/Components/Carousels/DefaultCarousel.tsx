import React from 'react';
import { Swiper } from 'swiper/react';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

interface DefaultCarouselProps {
    config?: {
        itemsToShow?: number | 'auto';
        wrapAround?: boolean;
        gap?: number;
        [key: string]: any;
    };
    children?: React.ReactNode;
    className?: string;
}

export default function DefaultCarousel({ config = {}, children, className }: DefaultCarouselProps) {
    const slidesPerView = config.itemsToShow === 'auto' ? 'auto' : (config.itemsToShow ?? 3);
    const loop = config.wrapAround ?? true;
    const spaceBetween = config.gap ?? 24;

    return (
        <Swiper
            modules={[Navigation, Pagination]}
            slidesPerView={slidesPerView}
            loop={loop}
            spaceBetween={spaceBetween}
            navigation
            className={className}
            style={{ padding: '0.25rem 0' }}
        >
            {children}
        </Swiper>
    );
}
