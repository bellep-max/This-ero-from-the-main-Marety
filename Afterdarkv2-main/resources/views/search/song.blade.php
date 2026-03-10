@extends('index')
@section('pagination')
    @include('commons.song', ['songs' => $result->songs, 'element' => 'genre'])
@stop
@section('content')
    @include('search.nav')
    <div id="page-content" class="search">
        <div class="container">
            <div class="page-header desktop">
                <h1>
                    <span data-translate-text="SEARCH_RESULTS">{{ __('web.SEARCH_RESULTS') }}</span>
                    <span>&nbsp;/&nbsp;</span> <strong>{{ $term }}</strong>
                </h1>
                <div class="did-you-mean hide" data-term="{{ $term }}">
                    <p class="did-you-mean-text">
                        <span data-translate-text="SEARCH_DID_YOU_MEAN">{{ __('web.SEARCH_DID_YOU_MEAN') }} </span>
                        <a class="did-you-mean-search-link search-link"></a> <span>?</span>
                    </p>
                    <a class="did-you-mean-remove">×</a>
                </div>
            </div>
            <div id="column1">
                <div class="content">
                    @if(count($result->songs))
                        <div class="sub-header">
                            <h2 data-translate-text="SONG_SEARCH_MATCHES">{!! __('web.SONG_SEARCH_MATCHES') !!}</h2>
                        </div>
                        @include('commons.toolbar.song', ['search' => true, 'type' => 'search', 'id' => null])
                        <div id="songs-grid" class="songs medium infinity-load-more" data-total-page="5">
                            @yield('pagination')
                        </div>
                    @else
                        <h2 data-translate-text="NO_SONG_MATCHES">{!! __('web.NO_SONG_MATCHES') !!}</h2>
                        <div>
                            <p data-translate-text="SEARCH_CHECK_SPELLING">{!! __('web.SEARCH_CHECK_SPELLING') !!}</p>
                        </div>
                    @endif
                </div>
            </div>
            <div id="column2">
                <div class="content">
                    {!! Advert::get('sidebar') !!}
                    @if(count($result->artists))
                        <div id="artists-snapshot">
                            <div class="sub-header">
                                <h3 data-translate-text="TOP_ARTIST_MATCHES">Artist Matches</h3>
                                <a href="{{ route('frontend.search.artist', ['slug' => $term]) }}" class="view-more search-link" data-translate-text="SEE_ALL">{{ __('web.SEE_ALL') }}</a>
                            </div>
                            <ul class="snapshot">
                                @include('commons.artist', ['artists' => $result->artists, 'element' => 'search'])
                            </ul>
                            <div class="divider"></div>
                        </div>
                    @endif
                    @if(count($result->albums))

                        <div id="albums-snapshot">
                            <div class="sub-header">
                                <h3 data-translate-text="TOP_ALBUM_MATCHES">Album Matches</h3>
                                <a href="{{ route('frontend.search.album', ['slug' => $term]) }}" class="view-more search-link" data-translate-text="SEE_ALL">{{ __('web.SEE_ALL') }}</a>
                            </div>
                            <ul class="snapshot">
                                @include('commons.album', ['albums' => $result->albums, 'element' => 'search'])
                            </ul>
                            <div class="divider"></div>
                        </div>
                    @endif
                    @if(count($result->playlists))
                        <div id="playlists-snapshot">
                            <div class="sub-header">
                                <h3 data-translate-text="TOP_PLAYLIST_MATCHES">Playlist Matches</h3>
                                <a href="{{ route('frontend.search.playlist', ['slug' => $term]) }}" class="view-more search-link" data-translate-text="SEE_ALL" data-searchquery="c" data-searchtype="playlist">{{ __('web.SEE_ALL') }}</a>
                            </div>
                            <ul class="snapshot">
                                @include('commons.playlist', ['playlists' => $result->playlists, 'element' => 'search'])
                            </ul>
                            <div class="divider"></div>
                        </div>
                    @endif
                    @if(count($result->users))
                        <div id="users-snapshot">
                            <div class="sub-header">
                                <h3 data-translate-text="TOP_USER_MATCHES">People Matches</h3>
                                <a href="{{ route('frontend.search.user', ['slug' => $term]) }}" class="view-more search-link" data-translate-text="SEE_ALL">{{ __('web.SEE_ALL') }}</a>
                            </div>
                            <ul class="snapshot">
                                @include('commons.user', ['users' => $result->users, 'element' => 'search'])
                            </ul>
                            <div class="divider"></div>
                        </div>
                    @endif
                    @if(count($result->events))
                        <div id="events-snapshot">
                            <div class="sub-header">
                                <h3 data-translate-text="TOP_EVENT_MATCHES">Event Matches</h3> <a class="view-more search-link" data-translate-text="SEE_ALL" data-searchquery="c" data-searchtype="event">{{ __('web.SEE_ALL') }}</a>
                            </div>
                            <ul class="snapshot">
                                @include('commons.event', ['events' => $result->events, 'element' => 'search'])
                            </ul>
                            <div class="divider"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection