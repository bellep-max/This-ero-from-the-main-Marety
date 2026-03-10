{{-- @extends('homepage_layout') --}}
@extends('index')
@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex flex-row justify-content-between align-items-center">
            <div class="d-flex flex-column justify-content-center align-items-start p-2 ps-lg-8">
                <div class="d-flex flex-column gap-2 gap-lg-5 ms-lg-5">
                    <div class="block-title text-lg-start text-center main_info__left-section">
                        Lorem ipsum dolor
                    </div>
                    <div class="font-merge color-text lh-lg">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                        labore et dolore magna aliqua.
                    </div>
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start gap-4">
                        <div class="btn-default btn-pink btn-wide">
                            Lorem ipsum
                        </div>
                        <div class="btn-default btn-outline btn-wide ml-4">
                            Lorem ipsum
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-md-none d-lg-flex flex-column">
                <img src="{{ asset('svg/homepage.png') }}" class="img-fluid d-none d-lg-block" alt="Login Icon">
            </div>
        </div>
    </div>
    <div class="container mb-5">
        <div class="d-flex flex-column justify-content-start gap-4 px-2 px-lg-0">
            <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                @include('components.play-icon-big')
                <div class="ms-3">
                    <div class="block-title">
                        New Audios
                    </div>
                    <div class="block-description">
                        New Erotic Audios
                    </div>
                </div>
            </div>
            <div class="new-audios-carousel-tabs tabs-header d-flex flex-row justify-content-between text-center gap-4 px-4">
                @foreach ($newAudios as $key => $audio)
                    <div data-carousel="{{ $key }}"
                         class="px-3 pt-1 tab-item {{ $key == 'fresh' ? 'tab-item-active' : ''}}">
                        {{ $audio['tabTitle'] }}
                    </div>
                @endforeach
            </div>
            <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                @include('components.buttons.btn-rnd-left', ['class' => 'new-audios__navigation__item--prev btn-pink'])
                @include('components.buttons.btn-rnd-right', ['class' => 'new-audios__navigation__item--next btn-pink'])
            </div>
            @foreach ($newAudios as $key => $audio)
                <div data-carousel="{{ $key }}" class="new-audios-carousel {{ $key == 'fresh' ? 'active' : 'hide' }}">
                    @foreach ($audio['data'] as $item)
                        @include('components.cards.song-card-lg', ['song' => $item, 'class' => 'audio-carousel-item'])
                    @endforeach
                </div>
            @endforeach
            <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                <a href="{{ route('channel', 'new-audios') }}" class="btn-default btn-pink btn-wide">
                    See All
                </a>
            </div>
        </div>
    </div>
    <div class="bg-gradient-default rounded-5">
        <div class="container py-5">
            <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
                <div class="">
                    <div class="block-title color-light">
                        Genres
                    </div>
                    <div class="block-description color-light">
                        Story by genre
                    </div>
                </div>
                <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                    @include('components.buttons.btn-rnd-left', ['class' => 'genres__navigation__item--prev btn-white'])
                    @include('components.buttons.btn-rnd-right', ['class' => 'genres__navigation__item--next btn-white'])
                </div>
                <div class="genres__carousel">
                    @foreach ($genres as $genre)
                        <a href="{{ $genre['permalink_url'] }}" class="image-carousel-item"
                           style="background: url({{ $genre['artwork_url'] }})">
                            <div class="image-carousel-item__title">
                                {{ $genre['name'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                    <a href="{{ route('genres') }}" class="btn-default btn-outline btn-wide">
                        See All
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="container py-5">
        <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
            <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                @include('components.play-icon-big')
                <div class="ms-3">
                    <div class="block-title">
                        Popular Audios
                    </div>
                    <div class="block-description">
                        25 audios from the previous 7 day period
                    </div>
                </div>
            </div>
            <div class="popular-audios-carousel-tabs tabs-header d-flex flex-row justify-content-between text-center gap-4 px-4">
                <div data-carousel="daily" class="px-3 pt-1 tab-item tab-item-active">
                    Top Played Daily
                </div>
                <div data-carousel="weekly" class="px-3 pt-1 tab-item">
                    Top Played Weekly
                </div>
                <div data-carousel="monthly" class="px-3 pt-1 tab-item">
                    Top Played Monthly
                </div>
            </div>
            <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                @include('components.buttons.btn-rnd-left', ['class' => 'popular_audios__navigation__item--prev btn-pink'])
                @include('components.buttons.btn-rnd-right', ['class' => 'popular_audios__navigation__item--next btn-pink'])
            </div>
            @foreach ($popularAudios as $tabName => $popularGenres)
                <div data-carousel="{{ $tabName }}"
                     class="popular-audios-carousel d-flex flex-row justify-content-start align-items-center {{ $tabName == 'daily' ? 'active' : 'hide' }}">
                    @foreach($popularGenres as $genre)
                        @if(count($genre['songs']))
                            <a href="{{ route('trending.genre', $genre['id']) }}"
                               class="popular_audios__carousel__item">
                                <div class="popular_audios__carousel__item__img">
                                    @foreach($genre['songs'] as $song)
                                        <img class=popular_audios__carousel__item__img__item
                                             src="{{ $song->artwork_url }}" alt="">
                                    @endforeach
                                </div>
                                <div class="popular_audios__carousel__item__info">
                                    <div class="popular_audios__carousel__item__info__title">
                                        {{ $genre['name'] }}
                                    </div>
                                    <div class="popular_audios__carousel__item__info__desc">
                                        Lorem ipsum
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endforeach
            <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                <a href="{{ route('trending.songs') }}" class="btn-default btn-pink btn-wide">
                    See All
                </a>
            </div>
        </div>
    </div>

    {{--    @if ($playlists->count())--}}
    {{--        <div class="bg-gradient-default rounded-5">--}}
    {{--            <div class="container py-5">--}}
    {{--                <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">--}}
    {{--                    <div class="">--}}
    {{--                        <div class="block-title color-light">--}}
    {{--                            New Playlists--}}
    {{--                        </div>--}}
    {{--                        <div class="block-description color-light">--}}
    {{--                            New Playlists--}}
    {{--                        </div>--}}
    {{--                    </div>--}}
    {{--                    <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">--}}
    {{--                        @include('components.buttons.btn-rnd-left', ['class' => 'playlists__navigation__item--prev btn-white'])--}}
    {{--                        @include('components.buttons.btn-rnd-right', ['class' => 'playlists__navigation__item--next btn-white'])--}}
    {{--                    </div>--}}
    {{--                    <div class="playlists-carousel">--}}
    {{--                        @foreach ($playlists as $playlist)--}}
    {{--                            <a href="{{ $playlist->permalink_url }}" class="image-carousel-item"--}}
    {{--                               style="background: url({{ $playlist->artwork_url }})">--}}
    {{--                                <div class="image-carousel-item__title">--}}
    {{--                                    {{ $playlist->title }}--}}
    {{--                                </div>--}}
    {{--                            </a>--}}
    {{--                        @endforeach--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    @endif--}}

    @if($posts->count())
        <div class="container py-5">
            <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
                <div>
                    <div class="block-title">
                        New Posts
                    </div>
                    <div class="block-description">
                        New Posts
                    </div>
                </div>
                <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                    @include('components.buttons.btn-rnd-left', ['class' => 'posts__navigation__item--prev btn-pink'])
                    @include('components.buttons.btn-rnd-right', ['class' => 'posts__navigation__item--next btn-pink'])
                </div>
                <div class="posts-carousel">
                    @foreach ($posts as $post)
                        <a href="{{ $post->permalink_url }}" class="image-carousel-item"
                           style="background: url({{ $post->artwork_url }})">
                            <div class="image-carousel-item__title">
                                {{ $post->title }}
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                    <a href="{{ route('blog') }}" class="btn-default btn-pink btn-wide">
                        See All
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if($adventures->count())
        <div class="bg-gradient-default rounded-5">
            <div class="block-title">
                New Adventures
            </div>
            <div class="block-description">
                New Adventures
            </div>
            <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                    @include('components.buttons.btn-rnd-left', ['class' => 'adventures__navigation__item--prev btn-white'])
                    @include('components.buttons.btn-rnd-right', ['class' => 'adventures__navigation__item--next btn-white'])
                </div>
            </div>
            <div class="adventures__carousel">
                @foreach ($adventures as $adventure)
                    <a href="{{ $adventure->permalink_url }}" class="image-carousel-item"
                       style="background: url({{ $adventure->artwork_url }})">
                        <div class="image-carousel-item__title">
                            {{ $adventure->title }}
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="adventures__carousel__see-all-section">
                <a class="adventures__carousel__see-all-section__item">
                    See All
                </a>
            </div>
        </div>
    @endif

    <div class="testimonials container-fluid">
        <div class="container py-5">
            <div class="d-flex flex-column justify-content-center align-items-center gap-5">
                <div class="d-flex flex-column align-items-center">
                    <div class="h2 font-default fw-bold">
                        Testimonials
                    </div>
                    <div class="block-description">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit
                    </div>
                </div>
                <div class="testimonials__carousel">
                    <div class="testimonials__carousel__navigation__left">
                        <svg width="47" height="48" viewBox="0 0 47 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="44.7266" y="46.5" width="43.1012" height="45" rx="21.5506"
                                  transform="rotate(-180 44.7266 46.5)" fill="white"/>
                            <rect x="44.7266" y="46.5" width="43.1012" height="45" rx="21.5506"
                                  transform="rotate(-180 44.7266 46.5)" stroke="#E836C5" stroke-width="3"/>
                            <path d="M24.7695 32.8594L23.7383 33.8906C23.2695 34.3125 22.5664 34.3125 22.1445 33.8906L13.0039 24.7969C12.582 24.3281 12.582 23.625 13.0039 23.2031L22.1445 14.0625C22.5664 13.6406 23.2695 13.6406 23.7383 14.0625L24.7695 15.0937C25.1914 15.5625 25.1914 16.2656 24.7695 16.7344L19.0977 22.125L32.5508 22.125C33.207 22.125 33.6758 22.5938 33.6758 23.25L33.6758 24.75C33.6758 25.3594 33.207 25.875 32.5508 25.875L19.0977 25.875L24.7695 31.2187C25.1914 31.6875 25.2383 32.3906 24.7695 32.8594Z"
                                  fill="#E836C5"/>
                        </svg>
                    </div>

                    <div class="testimonials__carousel__section">
                        @for ($i = 0; $i < 3; $i += 1)
                            <div class="testimonials__carousel__navigation__item">
                                <div class="testimonials__carousel__navigation__item__label">
                                    <svg width="37" height="30" viewBox="0 0 37 30" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.832 0.187996L10.156 20.828L8.26399 13.088C10.7293 13.088 12.736 13.8047 14.284 15.238C15.832 16.6713 16.606 18.6493 16.606 21.172C16.606 23.6373 15.8033 25.644 14.198 27.192C12.65 28.6827 10.7007 29.428 8.34999 29.428C5.94198 29.428 3.93532 28.6827 2.32998 27.192C0.781985 25.644 0.00798453 23.6373 0.00798453 21.172C0.00798453 20.4267 0.0653179 19.71 0.179985 19.022C0.294651 18.2767 0.523985 17.4167 0.867985 16.442C1.21198 15.4673 1.69932 14.1773 2.32998 12.572L7.31798 0.187996H15.832ZM36.128 0.187996L30.452 20.828L28.56 13.088C31.0253 13.088 33.032 13.8047 34.58 15.238C36.128 16.6713 36.902 18.6493 36.902 21.172C36.902 23.6373 36.0993 25.644 34.494 27.192C32.946 28.6827 30.9967 29.428 28.646 29.428C26.238 29.428 24.2313 28.6827 22.626 27.192C21.078 25.644 20.304 23.6373 20.304 21.172C20.304 20.4267 20.3613 19.71 20.476 19.022C20.5907 18.2767 20.82 17.4167 21.164 16.442C21.508 15.4673 21.9953 14.1773 22.626 12.572L27.614 0.187996H36.128Z"
                                              fill="white"/>
                                    </svg>
                                </div>
                                <div class="testimonials__carousel__navigation__item__text">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                                    exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </div>
                                <div class="testimonials__carousel__navigation__item__stars">
                                    @for ($j = 0; $j < 4; $j += 1)
                                        <div class="testimonials__carousel__navigation__item__stars__item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="26"
                                                 viewBox="0 0 28 26" fill="none">
                                                <path d="M14 20.8958L22.652 26L20.356 16.38L28 9.90737L17.934 9.07263L14 0L10.066 9.07263L0 9.90737L7.644 16.38L5.348 26L14 20.8958Z"
                                                      fill="#FFD910"/>
                                            </svg>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div class="testimonials__carousel__navigation__right">
                        <svg width="47" height="48" viewBox="0 0 47 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2.27393" y="1.5" width="43.1012" height="45" rx="21.5506" fill="white"/>
                            <rect x="2.27393" y="1.5" width="43.1012" height="45" rx="21.5506" stroke="#E836C5"
                                  stroke-width="3"/>
                            <path d="M22.231 15.1406L23.2622 14.1094C23.731 13.6875 24.4341 13.6875 24.856 14.1094L33.9966 23.2031C34.4185 23.6719 34.4185 24.375 33.9966 24.7969L24.856 33.9375C24.4341 34.3594 23.731 34.3594 23.2622 33.9375L22.231 32.9062C21.8091 32.4375 21.8091 31.7344 22.231 31.2656L27.9028 25.875H14.4497C13.7935 25.875 13.3247 25.4062 13.3247 24.75V23.25C13.3247 22.6406 13.7935 22.125 14.4497 22.125H27.9028L22.231 16.7812C21.8091 16.3125 21.7622 15.6094 22.231 15.1406Z"
                                  fill="#E836C5"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
