<a class="btn awesome-play-button play-object desktop" data-type="playlist" data-id="{{ $playlist->id }}">
    <svg height="26" viewBox="0 0 24 24" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M8 5v14l11-7z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
    <span data-translate-text="START_STATION">Play Playlist</span>
</a>
@if(! auth()->check() || auth()->check() && auth()->id() != $playlist->user->id)
    <a class="btn btn-favorite favorite @if($playlist->favorite) on @endif" data-type="playlist" data-id="{{ $playlist->id }}" data-title="{{ $playlist->title }}" data-url="{{ $playlist->permalink_url }}" data-text-on="{{ __('web.PLAYLIST_UNSUBSCRIBE') }}" data-text-off="{{ __('web.PLAYLIST_SUBSCRIBE') }}" style="color: #E836C5; border: 1px solid #E836C5">
        <svg width="13" height="15" viewBox="0 0 13 15" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.0714 8.96631H7.42857V13.9663C7.42857 14.5163 7.01071 14.9663 6.5 14.9663C5.98929 14.9663 5.57143 14.5163 5.57143 13.9663V8.96631H0.928571C0.417857 8.96631 0 8.51631 0 7.96631C0 7.41631 0.417857 6.96631 0.928571 6.96631H5.57143V1.96631C5.57143 1.41631 5.98929 0.966309 6.5 0.966309C7.01071 0.966309 7.42857 1.41631 7.42857 1.96631V6.96631H12.0714C12.5821 6.96631 13 7.41631 13 7.96631C13 8.51631 12.5821 8.96631 12.0714 8.96631Z" fill="#E836C5"/>
        </svg>
        <svg class="on" height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        @if($playlist->favorite)
            <span class="label desktop" data-translate-text="PLAYLIST_UNSUBSCRIBE">{{ __('web.PLAYLIST_UNSUBSCRIBE') }}</span>
        @else
            <span class="label desktop" data-translate-text="PLAYLIST_SUBSCRIBE"> {{ __('web.PLAYLIST_SUBSCRIBE') }} </span>
        @endif
    </a>
@endif

@if($playlist->is_visible)
    <a class="btn share desktop" data-type="playlist" data-id="{{ $playlist->id }}" style="color: #E836C5; border: 1px solid #E836C5">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 19" fill="none">
            <path d="M13.5 12.56C12.93 12.56 12.42 12.785 12.03 13.1375L6.6825 10.025C6.72 9.8525 6.75 9.68 6.75 9.5C6.75 9.32 6.72 9.1475 6.6825 8.975L11.97 5.8925C12.375 6.2675 12.9075 6.5 13.5 6.5C14.745 6.5 15.75 5.495 15.75 4.25C15.75 3.005 14.745 2 13.5 2C12.255 2 11.25 3.005 11.25 4.25C11.25 4.43 11.28 4.6025 11.3175 4.775L6.03 7.8575C5.625 7.4825 5.0925 7.25 4.5 7.25C3.255 7.25 2.25 8.255 2.25 9.5C2.25 10.745 3.255 11.75 4.5 11.75C5.0925 11.75 5.625 11.5175 6.03 11.1425L11.37 14.2625C11.3325 14.42 11.31 14.585 11.31 14.75C11.31 15.9575 12.2925 16.94 13.5 16.94C14.7075 16.94 15.69 15.9575 15.69 14.75C15.69 13.5425 14.7075 12.56 13.5 12.56Z" fill="#E836C5"/>
        </svg>
        <span data-translate-text="SHARE">{{ __('web.SHARE') }}</span>
    </a>
@endif
@if (auth()->check() && auth()->id() == $playlist->user->id)
    <a class="btn dropdown desktop edit-playlist-context-trigger" data-type="playlist" data-id="{{ $playlist->id }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="26" viewBox="0 0 20 20">
            <path fill="none" d="M0 0h20v20H0V0z"/>
            <path d="M15.95 10.78c.03-.25.05-.51.05-.78s-.02-.53-.06-.78l1.69-1.32c.15-.12.19-.34.1-.51l-1.6-2.77c-.1-.18-.31-.24-.49-.18l-1.99.8c-.42-.32-.86-.58-1.35-.78L12 2.34c-.03-.2-.2-.34-.4-.34H8.4c-.2 0-.36.14-.39.34l-.3 2.12c-.49.2-.94.47-1.35.78l-1.99-.8c-.18-.07-.39 0-.49.18l-1.6 2.77c-.1.18-.06.39.1.51l1.69 1.32c-.04.25-.07.52-.07.78s.02.53.06.78L2.37 12.1c-.15.12-.19.34-.1.51l1.6 2.77c.1.18.31.24.49.18l1.99-.8c.42.32.86.58 1.35.78l.3 2.12c.04.2.2.34.4.34h3.2c.2 0 .37-.14.39-.34l.3-2.12c.49-.2.94-.47 1.35-.78l1.99.8c.18.07.39 0 .49-.18l1.6-2.77c.1-.18.06-.39-.1-.51l-1.67-1.32zM10 13c-1.65 0-3-1.35-3-3s1.35-3 3-3 3 1.35 3 3-1.35 3-3 3z"/>
        </svg>
        <span class="caret"></span>
    </a>
@endif

@if (auth()->check() && auth()->id() == $playlist->user->id)
    <a class="btn edit-playlist-context-trigger mobile" data-type="playlist" data-id="{{ $playlist->id }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="26" viewBox="0 0 20 20"><path fill="none" d="M0 0h20v20H0V0z"/><path d="M15.95 10.78c.03-.25.05-.51.05-.78s-.02-.53-.06-.78l1.69-1.32c.15-.12.19-.34.1-.51l-1.6-2.77c-.1-.18-.31-.24-.49-.18l-1.99.8c-.42-.32-.86-.58-1.35-.78L12 2.34c-.03-.2-.2-.34-.4-.34H8.4c-.2 0-.36.14-.39.34l-.3 2.12c-.49.2-.94.47-1.35.78l-1.99-.8c-.18-.07-.39 0-.49.18l-1.6 2.77c-.1.18-.06.39.1.51l1.69 1.32c-.04.25-.07.52-.07.78s.02.53.06.78L2.37 12.1c-.15.12-.19.34-.1.51l1.6 2.77c.1.18.31.24.49.18l1.99-.8c.42.32.86.58 1.35.78l.3 2.12c.04.2.2.34.4.34h3.2c.2 0 .37-.14.39-.34l.3-2.12c.49-.2.94-.47 1.35-.78l1.99.8c.18.07.39 0 .49-.18l1.6-2.77c.1-.18.06-.39-.1-.51l-1.67-1.32zM10 13c-1.65 0-3-1.35-3-3s1.35-3 3-3 3 1.35 3 3-1.35 3-3 3z"/></svg>
    </a>
@endif
<a class="btn play play-object mobile" data-type="playlist" data-id="{{ $playlist->id }}">
    <span data-translate-text="PLAY_SONG">Play</span>
</a>
<a class="btn options mobile" data-toggle="contextmenu" data-trigger="left" data-type="playlist" data-id="{{ $playlist->id }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
</a>
