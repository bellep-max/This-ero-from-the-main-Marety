@extends('index')
@section('pagination')
    @include('commons.activity', ['activities' => $activities, 'type' => 'full'])
@stop
@section('content')
    {!! Advert::get('header') !!}
    <div class="container">
        <div class="page-header community main no-separator desktop">
            <h1>#{{ $tag }}</h1>
            <div class="byline"><span>{{ $total }} people are listening about this.</span></div>
        </div>
        <div id="column1" class="community-feed full">
            <div id="community" class="content infinity-load-more" data-total-page="{{ 20 }}">
                @yield('pagination')
            </div>
        </div>
    </div>
    {!! Advert::get('footer') !!}
@endsection