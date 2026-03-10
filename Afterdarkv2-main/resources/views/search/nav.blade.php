<div id="page-nav">
    <div class="outer">
        <ul>
            <li><a href="{{ route('frontend.search.song', ['slug' => $term]) }}" class="page-nav-link @if(Route::currentRouteName() == 'frontend.search.song') active @endif" data-translate-text="SEARCH_OPTION_SONGS">Songs<div class="arrow"></div></a></li>
            <li><a href="{{ route('frontend.search.artist', ['slug' => $term]) }}" class="page-nav-link @if(Route::currentRouteName() == 'frontend.search.artist') active @endif" data-translate-text="SEARCH_OPTION_ARTISTS">Artists<div class="arrow"></div></a></li>
            <li><a href="{{ route('frontend.search.album', ['slug' => $term]) }}" class="page-nav-link @if(Route::currentRouteName() == 'frontend.search.album') active @endif" data-translate-text="SEARCH_OPTION_ALBUMS">Albums<div class="arrow"></div></a></li>
            <li><a href="{{ route('frontend.search.playlist', ['slug' => $term]) }}" class="page-nav-link @if(Route::currentRouteName() == 'frontend.search.playlist') active @endif" data-translate-text="SEARCH_OPTION_PLAYLISTS">Playlists<div class="arrow"></div></a></li>
            <li><a href="{{ route('frontend.search.station', ['slug' => $term]) }}" class="page-nav-link @if(Route::currentRouteName() == 'frontend.search.station') active @endif" data-translate-text="SEARCH_OPTION_STATIONS">Stations<div class="arrow"></div></a></li>
            <li><a href="{{ route('frontend.search.user', ['slug' => $term]) }}" class="page-nav-link @if(Route::currentRouteName() == 'frontend.search.user') active @endif" data-translate-text="SEARCH_OPTION_PEOPLE">People<div class="arrow"></div></a></li>
            <li><a href="{{ route('frontend.search.event', ['slug' => $term]) }}" class="page-nav-link @if(Route::currentRouteName() == 'frontend.search.event') active @endif" data-translate-text="SEARCH_OPTION_EVENTS">Events<div class="arrow"></div></a></li>
        </ul>
    </div>
</div>