@extends('index')

@section('pagination')
    @isset($browse->podcasts)
        @foreach($browse->podcasts as $index => $podcast)
            @include('frontend.components.podcast-card', ['podcast' => $podcast, 'index' => $index, 'element' => 'genre'])
        @endforeach
    @endisset
@endsection

@section('content')
<div class="bg-gradient-default p-md-5 p-lg-6 min-vh-100">
    <div class="container">
        <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
            <div class="">
                @if(isset($browse->category))
                    <div class="block-title color-light">
                        {{ $browse->category->name }}
                    </div>
                    <a class="block-description color-light" href="{{ route('frontend.podcasts.index') }}" data-translate-text="SEE_ALL">
                        {{ __('web.SEE_ALL') }}
                    </a>
                @endif
                @if(Route::currentRouteName() == 'frontend.podcasts.browse.by.region')
                    <div class="block-title color-light">
                        {{ $browse->region->name }}
                    </div>
                    <a class="block-description color-light" href="{{ route('frontend.podcasts.browse.regions') }}" data-translate-text="SEE_ALL">
                        {{ __('web.SEE_ALL') }}
                    </a>
                @elseif(Route::currentRouteName() == 'frontend.podcasts.browse.regions')
                    <div class="block-title color-light">
                        By location
                    </div>
                    <a class="block-description color-light" href="{{ route('frontend.podcasts.index') }}" data-translate-text="SEE_ALL">
                        {{ __('web.SEE_ALL') }}
                    </a>
                @elseif(Route::currentRouteName() == 'frontend.podcasts.browse.countries')
                    <div class="block-title color-light">
                        By countries
                    </div>
                    <a class="block-description color-light" href="{{ route('frontend.podcasts.index') }}" data-translate-text="SEE_ALL">
                        {{ __('web.SEE_ALL') }}
                    </a>
                @elseif(Route::currentRouteName() == 'frontend.podcasts.browse.by.country')
                    <div class="block-title color-light">
                        {{ $browse->country->name }}
                    </div>
                    <a class="block-description color-light" href="{{ route('frontend.podcasts.browse.countries') }}" data-translate-text="SEE_ALL">
                        {{ __('web.SEE_ALL') }}
                    </a>
                @elseif(Route::currentRouteName() == 'frontend.podcasts.browse.by.language')
                    <div class="block-title color-light">
                        {{ $browse->language->name }}
                    </div>
                    <a class="block-description color-light" href="{{ route('frontend.podcasts.index') }}" data-translate-text="SEE_ALL">
                        {{ __('web.SEE_ALL') }}
                    </a>
                @elseif(Route::currentRouteName() == 'frontend.podcasts.browse.by.city')
                    <div class="block-title color-light">
                        {{ $browse->city->name }}
                    </div>
                    <a class="block-description color-light" href="{{ route('frontend.podcasts.browse.by.country', ['code' => $browse->city->country->code]) }}" data-translate-text="SEE_ALL">
                        {{ __('web.SEE_ALL') }}
                    </a>
                @endif
            </div>
            @if(isset($browse->slides))
                @include('commons.slideshow', ['slides' => $browse->slides])
            @endif
            @if(isset($browse->channels))
                @include('commons.channel', ['channels' => $browse->channels])
            @endif
            @if(isset($browse->regions) && count($browse->regions))
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
                        @foreach ($browse->regions as $region)
                            <a href="{{ route('frontend.podcasts.browse.by.region', $region->alt_name) }}" class="hoverable-image" style="background: url({{ $region->artwork_url }})">
                                <div class="hoverable-image-title">
                                    {{ $item->name }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            @if(isset($browse->countries) && count($browse->countries))
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
                        @foreach ($browse->countries as $country)
                            <a href="{{ route('frontend.podcasts.browse.by.country', $country->code) }}" class="hoverable-image" style="background: url({{ $country->artwork_url }})">
                                <div class="hoverable-image-title">
                                    {{ $country->name }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            @if(isset($browse->languages) && count($browse->languages))
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
                        @foreach ($browse->languages as $language)
                            <a href="{{ route('frontend.podcasts.browse.by.language', $language->id) }}" class="hoverable-image" style="background: url({{ $language->artwork_url }})">
                                <div class="hoverable-image-title">
                                    {{ $language->name }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            @if(isset($browse->podcasts))
                @include('frontend.components.toolbars.podcast')
                <div id="shows-grid" class="grid-view justify-content-center gap-3 infinity-load-more">
                    @yield('pagination')
                </div>
            @endif
        </div>
    </div>
</div>

@endsection