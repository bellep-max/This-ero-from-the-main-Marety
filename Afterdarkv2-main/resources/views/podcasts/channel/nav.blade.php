<div id="page-nav">
    <div class="outer">
        <ul>
            <li><a href="{{ $channel->permalink_url }}" class="page-nav-link @if(Route::currentRouteName() == 'frontend.playlist.show') active @endif" data-translate-text="OVERVIEW">{{ __('web.OVERVIEW') }}<div class="arrow"></div></a></li>
            @if($channel->is_visible)
                <li><a href="{{ $channel->permalink_url }}/subscribers" class="page-nav-link @if(Route::currentRouteName() == 'frontend.playlist.subscribers') active @endif" data-translate-text="SUBSCRIBERS">{{ __('web.SUBSCRIBERS') }}<div class="arrow"></div></a></li>
            @endif
        </ul>
    </div>
</div>