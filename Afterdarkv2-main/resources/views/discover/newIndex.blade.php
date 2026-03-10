@extends('index')
@section('pagination')
    @if (count($songs))
        @foreach($songs as $song)
            @include('frontend.components.track', ['song' => $song, 'discover' => true])
        @endforeach
    @else
        {{-- <div class="empty-page following">
            <div class="empty-inner">
                <h2 data-translate-text="NO_RESULTS_FOUND">{{ __('web.NO_RESULTS_FOUND') }}</h2>
            </div>
        </div> --}}
    @endif
@endsection
@section('content')
<div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
    <div class="container">
        <div class="row">
            <div class="col text-start">
                <div class="d-block">
                    <div class="block-title color-light text-truncate">
                        Discover
                    </div>
                    <div class="block-description color-light">
                        Discover new tracks
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                @include('frontend.default.discover.components.filters')
            </div>
            <div class="col col-xl-9">
                <div id="songs-grid" class="d-flex flex-column w-100 h-100 store-item-list infinity-load-more">
                    @yield('pagination')
                </div>
            </div>
        </div>
    </div>
{{--    <div class="container position-relative">--}}
{{--        <div class="store-container d-flex flex-row justify-content-start gap-4">--}}
{{--            @include('frontend.default.discover.components.filters')--}}
{{--            <div class="store-item-list-container vh-100">--}}
{{--                --}}{{-- <h1>Tracks</h1> --}}
{{--                <div id="songs-grid" class="store-item-list infinity-load-more" >--}}
{{--                    @yield('pagination')--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
@endsection