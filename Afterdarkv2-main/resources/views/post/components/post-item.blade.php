<div class="blog-card bg-light rounded-4 p-3 gap-4 d-flex flex-column flex-md-row">
    <div class="blog_container__data__content__item__img" style="background: url({{ $post->getFirstMediaUrl('artwork', 'thumbnail') }})"></div>
    <div class="d-flex flex-column gap-4 align-items-start justify-content-center">
        <div class="filter-title">
            {{ $post->title }}
        </div>
        <div class="d-flex flex-row gap-5 justify-content-start align-items-center">
            <div class="blog-card-author text-desc">
                {{ $post->user->name }}
            </div>
            <div class="blog-card-author text-desc">
                {{ \Carbon\Carbon::parse($post->created_at)->format('F j Y') }}
            </div>
        </div>
        <div class="text-description text-secondary">
            {{ \Illuminate\Support\ }}
        </div>
        <a class="text-description color-pink" href="">
            erocast.com
        </a>
        <a href="{{ $post->permalink_url }}" class="btn-default btn-pink">
            Read More
        </a>
    </div>
</div>