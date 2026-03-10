@extends('index')
@section('content')
    <div class="bg-gradient-default p-md-5 p-lg-6">
        <div class="container">
            <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
                <div class="d-block">
                    <div class="block-title color-light text-truncate">
                        Genres
                    </div>
                    <div class="block-description color-light">
                        Story by genre
                    </div>
                </div>
                <div class="d-flex flex-row justify-content-center flex-wrap align-items-center gap-2 gap-lg-4">
                    @foreach ($genres as $genre)
                        <a href="{{ $genre->permalink_url }}" class="hoverable-image"
                           style="background: url({{ $genre->artwork }})">
                            <div class="hoverable-image-title">
                                {{ $genre->name }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4 mt-5">
                <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                    @include('frontend.components.play-icon-big')
                    <div class="">
                        <div class="block-title color-light">
                            New Audios
                        </div>
                        <div class="block-description color-light">
                            New Erotic Audios
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-row justify-content-between justify-content-lg-end align-items-center gap-4">
                    @include('frontend.components.buttons.btn-rnd-left', ['class' => 'new-audios__navigation__item--prev btn-white'])
                    @include('frontend.components.buttons.btn-rnd-right', ['class' => 'new-audios__navigation__item--next btn-white'])
                </div>
                <div class="new-audios-carousel active">
                    @foreach ($tracks as $track)
                        @include('frontend.components.cards.song-card-lg', ['song' => $track, 'class' => 'audio-carousel-item'])
                    @endforeach
                </div>
                <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                    <a href="{{ route('frontend.channel', 'new-audios') }}" class="btn-default btn-pink btn-wide">
                        See All
                    </a>
                </div>
            </div>
        </div>
    </div>
    {!! Advert::get('footer') !!}
@endsection
