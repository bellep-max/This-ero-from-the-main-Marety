@extends('index')
@section('content')
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    @include('frontend.default.profile.layout.menu')
                </div>
                <div class="col col-xl-9">
                    <div class="d-flex flex-column w-100 gap-4 bg-light rounded-5 p-3 p-lg-5 vh-100">
                        <div class="d-flex flex-row justify-content-start align-items-center gap-4">
                            @if (auth()->id() === $profile->id)
                                <a id="create-playlist" class="btn-default btn-outline btn-rnd border-2">
                                    <svg width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                        <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                                    </svg>
                                </a>
                            @endif
                            <div class="font-default fs-4">
                                Playlists
                            </div>
                        </div>
                        <div class="profile_page__content__songs__container d-flex flex-column justify-content-start align-items-center gap-4 overflow-y-auto">
                            @if (count($profile->playlists))
                                <div class="container-fluid">
                                    <div class="row">
                                        @foreach ($profile->playlists as $playlist)
                                            <div class="col-12 col-md-4 p-1">
                                                <a href="{{ $playlist->permalink_url }}" class="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3">
                                                    <img src="{{ $playlist->artwork_url }}"
                                                         class="card-item-avatar" alt=""/>
                                                    <div class="title font-default color-text text-center">
                                                        {{ \Illuminate\Support\Str::limit($playlist->title, 25) }}
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="profile_page__content__songs__playlists__empty__title">
                                    @if (auth()->id() === $profile->id) You @else {{ $profile->username }} @endif don't have any playlists yet
                                </div>
                                @include('frontend.components.art')
                                @if (auth()->id() === $profile->id)
                                    <div class="profile_page__content__songs__playlists__empty__desc">
                                        Playlists are great for capturing a certain theme, or group of relevant tracks you love to hear
                                        over and over again. Go forth, create your own.
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
