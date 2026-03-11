import React from 'react';
import route from '@/helpers/route';

interface PostCardProps {
    post: any;
}

export default function PostCard({ post }: PostCardProps) {
    const styleImage = { background: `url(${post.artwork})` };

    return (
        <div className="blog-card bg-default rounded-4 p-3 gap-4 d-flex flex-column flex-md-row">
            <div className="blog_container__data__content__item__img" style={styleImage}></div>
            <div className="d-flex flex-column gap-4 align-items-start justify-content-start">
                <p className="h3 font-default">
                    {post.title}
                </p>
                <div className="d-flex flex-row gap-5 justify-content-start align-items-center">
                    <div className="blog-card-author text-desc">
                        {post.author}
                    </div>
                    <div className="blog-card-author text-desc">
                        {post.created_at}
                    </div>
                </div>
                <div className="text-description text-secondary">
                    {post.preview}
                </div>
                <a className="text-description color-pink" href="">erocast.com</a>
                <a href={route('posts.show', post.uuid)} className="btn-default btn-pink mt-auto">Read More</a>
            </div>
        </div>
    );
}
