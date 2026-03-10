{{-- @extends('homepage_layout') --}}
@extends('index')
@section('content')
    <div class="min-vh-100 d-flex flex-column gap-5">
        <div class="py-3 p-md-5 p-lg-6">
            <div class="container">
                <div class="row">
                    <div class="col text-start block-title">
                        Popular Audios
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="d-flex flex-column justify-content-start gap-4 px-2 px-lg-0">
                    <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                        @include('frontend.components.play-icon-big')
                        <div class="ms-3">
                            <div class="block-title">
                                Top 20 categories
                            </div>
                            <div class="block-description">
                                Top 20 in different categories
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                        @include('frontend.components.buttons.btn-rnd-left', ['class' => 'trending__navigation__item--prev btn-pink'])
                        @include('frontend.components.buttons.btn-rnd-right', ['class' => 'trending__navigation__item--next btn-pink'])
                    </div>
                    <div class="trending-carousel d-flex flex-row justify-content-start align-items-center">
                        @foreach($top as $category)
                            @foreach($category as $song)
                                @include('frontend.components.cards.song-card-lg', ['song' => $song, 'class' => 'top_male_voice__carousel__item'])
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-default bg-rounded">
            <div class="container py-5">
                <div class="d-flex flex-column justify-content-start gap-4 px-2 px-lg-0">
                    <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                        <div class="ms-3 color-light">
                            <div class="block-title color-light">
                                Top by Genre
                            </div>
                            <div class="block-description">
                                Top by Genre
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                        @include('frontend.components.buttons.btn-rnd-left', ['class' => 'top_genre__navigation__item--prev btn-white'])
                        @include('frontend.components.buttons.btn-rnd-right', ['class' => 'top_genre__navigation__item--next btn-white'])
                    </div>
                    <div class="top-genre-carousel d-flex flex-row justify-content-start align-items-center">
                        @foreach($topByGenre as $topByGenreItem)
                            <a href="{{ route('frontend.trending.genre', $topByGenreItem['genre_id']) }}"
                               class="top_genre__carousel__item play-object">
                                <div class="top_genre__carousel__item__img">
                                    @foreach($topByGenreItem['songs'] as $song)
                                        <img class=trending__carousel__item__img__item src="{{ $song->artwork_url}}"
                                             alt="">
                                    @endforeach
                                </div>
                                <div class="top_genre__carousel__item__info">
                                    <div class="top_genre__carousel__item__info__title">
                                        {{ $topByGenreItem['name'] }}
                                    </div>
                                    <div class="top_genre__carousel__item__info__desc">
                                        {{ $topByGenreItem['description'] }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="d-flex flex-column justify-content-start gap-4 px-2 px-lg-0">
                <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                    @include('frontend.components.play-icon-big')
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
                    @include('frontend.components.buttons.btn-rnd-left', ['class' => 'popular_audios__navigation__item--prev btn-pink'])
                    @include('frontend.components.buttons.btn-rnd-right', ['class' => 'popular_audios__navigation__item--next btn-pink'])
                </div>
                @foreach($popularAudios as $tabName => $popularGenres)
                    <div data-carousel="{{ $tabName }}"
                         class="popular-audios-carousel d-flex flex-row justify-content-start align-items-center {{ $tabName == 'daily' ? 'active' : 'hide' }}">
                        @foreach($popularGenres as $genre)
                            @if (count($genre['songs']))
                                <a href="{{ route('frontend.trending.genre', $genre['id']) }}"
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
                                            {{ $genre['description'] }}
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endforeach
                <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                    <a href="{{ route('frontend.trending.songs') }}" class="btn-default btn-pink btn-wide">
                        See All
                    </a>
                </div>
            </div>
        </div>
        <div class="bg-gradient-default bg-rounded">
            <div class="container py-5">
                <div class="d-flex flex-column justify-content-start gap-4 px-2 px-lg-0">
                    <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                        <div class="ms-3 color-light">
                            <div class="block-title color-light">
                                Top 20 Female Voice
                            </div>
                            <div class="block-description">
                                Top 20 Female Voice
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                        @include('frontend.components.buttons.btn-rnd-left', ['class' => 'top_female_voice__navigation__item--prev btn-white'])
                        @include('frontend.components.buttons.btn-rnd-right', ['class' => 'top_female_voice__navigation__item--next btn-white'])
                    </div>
                    <div class="top-female-voice-carousel d-flex flex-row justify-content-start align-items-center">
                        @foreach ($top['female'] as $song)
                            @include('frontend.components.cards.song-card-lg', ['song' => $song, 'class' => 'top_female_voice__carousel__item'])
                        @endforeach
                    </div>
                    <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                        <a href="{{ route('frontend.trending.voices', ['voice' => array_search("[F] female", __('web.GENDER_TAGS'))]) }}"
                           class="btn-default btn-outline btn-wide">
                            See All
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="d-flex flex-column justify-content-start gap-4 px-2 px-lg-0">
                <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                    @include('frontend.components.play-icon-big')
                    <div class="ms-3">
                        <div class="block-title">
                            Top 20 Male Voice
                        </div>
                        <div class="block-description">
                            Top 20 Male Voice
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                    @include('frontend.components.buttons.btn-rnd-left', ['class' => 'top_male_voice__navigation__item--prev btn-pink'])
                    @include('frontend.components.buttons.btn-rnd-right', ['class' => 'top_male_voice__navigation__item--next btn-pink'])
                </div>
                <div class="top-male-voice-carousel d-flex flex-row justify-content-start align-items-center">
                    @foreach ($top['male'] as $song)
                        @include('frontend.components.cards.song-card-lg', ['song' => $song, 'class' => 'top_male_voice__carousel__item'])
                    @endforeach
                </div>
                <div class="d-flex flex-row justify-content-center align-items-center my-5">
                    <a href="{{ route('frontend.trending.voices', ['voice' => array_search("[M] male", __('web.GENDER_TAGS'))]) }}"
                       class="btn-default btn-pink btn-wide">
                        See All
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-banner">
        <div class="footer-banner__text">
            <p>
                Do you want to be the first to listen to the latest audio on
            </p>
            <p>
                erocast? <a href="">click below</a> and be the first to be the first
            </p>
        </div>
        <a href="{{ route('frontend.discover.new') }}?release[]=recent" class="btn-default btn-pink btn-wide mt-5">
            Check out new content
        </a>
    </div>
@endsection
