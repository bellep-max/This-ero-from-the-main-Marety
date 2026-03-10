@extends('index')
@section('content')
    @include('homepage.nav')
    <div class="page-header no-separator desktop">
        <h1 data-translate-text="GENRES">{{ __('web.GENRES') }}</h1>
    </div>
    <div id="column1" class="full">
        @include('commons.slideshow', ['slides' => $discover->slides])
        <div class="content home-section">
            <div class="sub-header">
                <h2 class="section-title">
                    <span>Story by genre</span>
                </h2>
            </div>
            <div id="grid" class="genre">
                @foreach ($discover->genres as $index => $genre)
                    <div class="module module-cell genre grid-item">
                        <div class="img-container" style="background: url({{$genre->artwork}})">
                            <div class="module-inner">
                                <a class="title" href="{{$genre->permalink_url}}" title="{!! $genre->name !!}">
                                    <span>{!! $genre->name !!}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @include('commons.channel', ['channels' => $discover->channels])
    </div>
    {!! Advert::get('footer') !!}
@endsection