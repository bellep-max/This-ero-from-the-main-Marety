@extends('index')

@section('pagination')
    @include('commons.song', ['songs' => $tag->songs, 'element' => 'genre'])
@endsection

@section('content')
    {!! Advert::get('header') !!}
    <div class="container">
        <div class="page-header tag-header small desktop pb-0">
            <div class="inner">
                <h1><span title="{{ $tag->tag }}">#{{ $tag->tag }}</span></h1>
            </div>
        </div>
        @include('commons.toolbar.song', ['type' => 'genre', 'id' => null])
        <div id="songs-grid" class="infinity-load-more">
            @yield('pagination')
        </div>
    </div>
    {!! Advert::get('footer') !!}
@endsection