@extends('index')
@section('content')
<div class="bg-gradient-default p-md-5 p-lg-6">
    <div class="container">
        <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
            <div class="">
                <div class="block-title color-light" data-translate-text="PODCASTS">
                    {{ __('web.PODCASTS') }}
                </div>
                <div class="block-description color-light" data-translate-text="PODCASTS_TIP">
                    {{ __('web.PODCASTS_TIP') }}
                </div>
            </div>
            @include('commons.slideshow', ['slides' => $slides, 'style' => 'featured'])
            @include('commons.channel', ['channels' => $channels])
            <div class="block-title color-light fs-4" data-translate-text="CATEGORIES">
                {{ __('web.CATEGORIES') }}
            </div>
            <div class="hoverable-image-container hoverable-image-container-lg gap-2 gap-lg-4">
                @foreach ($podcasts->categories as $category)
                    <a href="{{ $category->permalink_url }}" class="hoverable-image" style="background: url({{ $category->artwork_url }})">
                        <div class="hoverable-image-title">
                            {{ $category->name }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        @if(isset($podcasts->regions) && count($podcasts->regions))
            <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4 mt-5">
                <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                    @include('frontend.components.play-icon-big')
                    <div class="">
                        <div class="block-title color-light" data-translate-text="BY_LOCATION">
                            {{ __('web.BY_LOCATION') }}
                        </div>
                    </div>
                </div>
                <div class="hoverable-image-container hoverable-image-container-lg gap-2 gap-lg-4">
                    @foreach ($podcasts->regions as $region)
                        <a href="{{ route('frontend.podcasts.browse.by.region', $region->alt_name) }}" class="hoverable-image" style="background: url({{ $region->artwork_url }})">
                            <div class="hoverable-image-title">
                                {{ $region->name }}
                            </div>
                        </a>
                    @endforeach
                </div>
    {{--            <div class="new-audios__carousel active">--}}
    {{--                @foreach ($tracks as $track)--}}
    {{--                    <a class="new-audios__carousel__item play-object" data-type="song" data-id="{{ $track->id }}">--}}
    {{--                        <script style="display: none">var song_data_{{ $track->id }} = {!! json_encode($track) !!}</script>--}}
    {{--                        <div class="new-audios__carousel__item__img">--}}
    {{--                            <img src="{{ $track->artwork_url }}" alt="">--}}
    {{--                        </div>--}}
    {{--                        <div class="new-audios__carousel__item__info">--}}
    {{--                            <div class="new-audios__carousel__item__info__title">--}}
    {{--                                {{ $track->title }}--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                    </a>--}}
    {{--                @endforeach--}}
    {{--            </div>--}}
                <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                    <a href="{{ route('frontend.channel', 'new-audios') }}" class="btn-default btn-pink btn-wide">
                        See All
                    </a>
                </div>
            </div>
        @endif
        @if(isset($podcasts->countries) && count($podcasts->countries))
            <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4 mt-5">
                <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                    @include('frontend.components.play-icon-big')
                    <div class="">
                        <div class="block-title color-light" data-translate-text="BY_COUNTRY">
                            {{ __('web.BY_COUNTRY') }}
                        </div>
                    </div>
                </div>
                <div class="hoverable-image-container hoverable-image-container-lg gap-2 gap-lg-4">
                    @foreach ($podcasts->countries as $country)
                        <a href="{{ route('frontend.podcasts.browse.by.country', $country->code) }}" class="hoverable-image" style="background: url({{ $country->artwork_url }})">
                            <div class="hoverable-image-title">
                                {{ $country->name }}
                            </div>
                        </a>
                    @endforeach
                </div>
                {{--            <div class="new-audios__carousel active">--}}
                {{--                @foreach ($tracks as $track)--}}
                {{--                    <a class="new-audios__carousel__item play-object" data-type="song" data-id="{{ $track->id }}">--}}
                {{--                        <script style="display: none">var song_data_{{ $track->id }} = {!! json_encode($track) !!}</script>--}}
                {{--                        <div class="new-audios__carousel__item__img">--}}
                {{--                            <img src="{{ $track->artwork_url }}" alt="">--}}
                {{--                        </div>--}}
                {{--                        <div class="new-audios__carousel__item__info">--}}
                {{--                            <div class="new-audios__carousel__item__info__title">--}}
                {{--                                {{ $track->title }}--}}
                {{--                            </div>--}}
                {{--                        </div>--}}
                {{--                    </a>--}}
                {{--                @endforeach--}}
                {{--            </div>--}}
                <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                    <a href="{{ route('frontend.podcasts.browse.countries') }}" class="btn-default btn-pink btn-wide">
                        {{ __('web.SEE_ALL') }}
                    </a>
                </div>
            </div>
        @endif
        @if(isset($podcasts->languages) && count($podcasts->languages))
            <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4 mt-5">
                <div class="d-flex flex-row justify-content-start align-items-center gap-3">
                    @include('frontend.components.play-icon-big')
                    <div class="">
                        <div class="block-title color-light" data-translate-text="BY_LANGUAGE">
                            {{ __('web.BY_LANGUAGE') }}
                        </div>
                    </div>
                </div>
                <div class="hoverable-image-container hoverable-image-container-lg gap-2 gap-lg-4">
                    @foreach ($podcasts->languages as $language)
                        <a href="{{ route('frontend.podcasts.browse.by.language', $language->id) }}" class="hoverable-image" style="background: url({{ $language->artwork_url }})">
                            <div class="hoverable-image-title">
                                {{ $language->name }}
                            </div>
                        </a>
                    @endforeach
                </div>
                {{--            <div class="new-audios__carousel active">--}}
                {{--                @foreach ($tracks as $track)--}}
                {{--                    <a class="new-audios__carousel__item play-object" data-type="song" data-id="{{ $track->id }}">--}}
                {{--                        <script style="display: none">var song_data_{{ $track->id }} = {!! json_encode($track) !!}</script>--}}
                {{--                        <div class="new-audios__carousel__item__img">--}}
                {{--                            <img src="{{ $track->artwork_url }}" alt="">--}}
                {{--                        </div>--}}
                {{--                        <div class="new-audios__carousel__item__info">--}}
                {{--                            <div class="new-audios__carousel__item__info__title">--}}
                {{--                                {{ $track->title }}--}}
                {{--                            </div>--}}
                {{--                        </div>--}}
                {{--                    </a>--}}
                {{--                @endforeach--}}
                {{--            </div>--}}
                <div class="d-flex flex-row justify-content-center align-items-center mt-5">
                    <a href="{{ route('frontend.podcasts.browse.languages') }}" class="btn-default btn-pink btn-wide">
                        {{ __('web.SEE_ALL') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection