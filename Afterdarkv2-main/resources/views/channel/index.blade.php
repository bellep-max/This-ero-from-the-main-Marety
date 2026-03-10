@extends('index')
@section('pagination')
    @if($channel->object_type == \App\Constants\TypeConstants::SONG)
        @foreach($channel->objects as $song)
            @include('frontend.components.track', ['song' => $song, 'discover' => false])
        @endforeach
    @elseif($channel->object_type == \App\Constants\TypeConstants::ARTIST)
        @include('commons.artist', ['artists' => $channel->objects, 'element' => 'collection'])
    @elseif($channel->object_type == \App\Constants\TypeConstants::ALBUM)
        @include('commons.album', ['albums' => $channel->objects, 'element' => 'carousel'])
    @elseif($channel->object_type == \App\Constants\TypeConstants::PLAYLIST)
        @include('commons.playlist', ['playlists' => $channel->objects, 'element' => null])
    @elseif($channel->object_type == \App\Constants\TypeConstants::STATION)
        @include('commons.station', ['stations' => $channel->objects, 'element' => 'carousel'])
    @elseif($channel->object_type == \App\Constants\TypeConstants::USER)
        @include('commons.user', ['users' => $channel->objects, 'element' => 'grid'])
    @endif
@endsection

@section('content')
{!! Advert::get('header') !!}
<div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
    <div class="container">
        <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
            <div class="d-block">
                <div class="block-title color-light text-truncate">
                    {{ $channel->title }}
                </div>
                <div class="block-description color-light">
                    {{ $channel->description }}
                </div>
            </div>
            @if($channel->object_type == 'song')
                @include('frontend.components.toolbars.song', ['type' => 'channel', 'id' => $channel->id])
            @elseif($channel->object_type == 'station')
                @include('commons.toolbar.station')
            @endif

            <div @if($channel->object_type == 'song') id="songs-grid" @endif
                class="infinity-load-more items-sort-able
                    @if(in_array($channel->object_type, [\App\Constants\TypeConstants::PLAYLIST, \App\Constants\TypeConstants::USER]))
                        playlists-grid
                    @endif"
            >
                @yield('pagination')
            </div>
        </div>
    </div>
    {!! Advert::get('footer') !!}
@endsection