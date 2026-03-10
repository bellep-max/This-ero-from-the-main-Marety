<div class="d-flex flex-row justify-content-center align-items-center w-100 border-bottom text-center">
    <a href="{{ $playlist->permalink_url }}" class="pb-3 navigation-link @if(Route::currentRouteName() == 'frontend.playlist.show') active @endif" data-translate-text="OVERVIEW">{{ __('web.OVERVIEW') }}<div class="arrow"></div></a>
    @if($playlist->is_visible)
        <a href="{{ $playlist->permalink_url }}/subscribers" class="pb-3 navigation-link @if(Route::currentRouteName() == 'frontend.playlist.subscribers') active @endif" data-translate-text="SUBSCRIBERS">{{ __('web.SUBSCRIBERS') }}<div class="arrow"></div></a>
        <a href="{{ $playlist->permalink_url }}/collaborators" class="pb-3 navigation-link @if(Route::currentRouteName() == 'frontend.playlist.collaborators') active @endif" data-translate-text="PLAYLIST_COLLABORATORS">{{ __('web.PLAYLIST_COLLABORATORS') }}<div class="arrow"></div></a>
    @endif
</div>
{!! Advert::get('header') !!}