@extends('index')

@section('pagination')
    @if(count($songs))
        @foreach($songs as $song)
            @include('frontend.components.track', ['song' => $song, 'discover' => false])
        @endforeach
    @endif
@endsection

@section('content')
<div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
    <div class="container">
        <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
            <div class="d-block">
                <div class="block-title color-light text-truncate">
                    {{ $pageData['title'] }}
                </div>
                <div class="block-description color-light" data-translate-text="SEE_ALL_GENRES">
                    {{ $pageData['desc'] }}
                </div>
            </div>
            <div id="songs-grid" class="infinity-load-more">
                @yield('pagination')
            </div>
        </div>
    </div>
</div>
@endsection