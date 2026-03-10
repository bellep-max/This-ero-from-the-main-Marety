@foreach ($playlists as $index => $playlist)
    <script>var playlist_data_{{ $playlist->id }} = {!! json_encode($playlist) !!}</script>
    @if ($element == "carousel")
        <div class="module module-cell playlist block swiper-slide draggable" data-toggle="contextmenu" data-trigger="right" data-type="playlist" data-id="{{ $playlist->id }}">
            <div class="img-container" data-type="playlist" data-id="{{ $playlist->id }}">
                <img class="img" src="{{ $playlist->artwork_url }}">
                <a class="overlay-link" href="{{$playlist->permalink_url}}"></a>
                <div class="actions primary">
                    <a class="btn play play-lg play-scale play-object" data-type="playlist" data-id="{{ $playlist->id }}">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" height="24" width="24"><path d="M8 5v14l11-7z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
                    </a>
                </div>
            </div>
            <div class="module-inner">
                <a class="title" href="{{ $playlist->permalink_url }}" title="{{ $playlist->title }}">{{ $playlist->title }}</a>
                @if(isset($playlist->user))
                    <span class="byline">by <a href="{{ route('frontend.user.show', $playlist->user->username) }}" class="playlist-link" title="{{ $playlist->user->name }}">{{ $playlist->user->name }}</a></span>
                @endif
            </div>
        </div>
    @elseif ($element == "search")
        <div class="d-flex flex-row justify-content-start align-items-start gap-3 w-100" data-toggle="contextmenu" data-trigger="right" data-type="playlist" data-id="{{ $playlist->id }}" data-index="{{ $index }}">
            <img class="img-thumb rounded-2" src="{{ $playlist->artwork_url }}" alt="{{ $playlist->title }}">
            <div class="d-flex flex-column text-start fs-13">
                <a href="{{ $playlist->permalink_url }}" class="fw-bolder color-text" data-playlist-id="8991588">{{ $playlist->title }}</a>
                @isset($playlist->user)
                    <span data-translate-text="BY">by <a href="{{ route('frontend.user.show', $playlist->user->name) }}" class="meta-text font-default fw-bolder color-pink">{{ $playlist->user->name }}</a></span>
                @endisset
            </div>
            <div class="ms-auto">
                @if(! auth()->check() || auth()->check() && auth()->id() != $playlist->user->id)
                    <a class="btn-default btn-outline btn-narrow favorite @if($playlist->favorite) on @endif" data-type="playlist" data-id="{{ $playlist->id }}" data-title="{{ $playlist->title }}" data-url="{{ $playlist->permalink_url }}" data-text-on="{{ __('web.PLAYLIST_UNSUBSCRIBE') }}" data-text-off="{{ __('web.PLAYLIST_SUBSCRIBE') }}">
                        <svg class="off" height="18" width="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                        <svg class="on" height="18" width="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        @if ($playlist->favorite)
                            <span class="label desktop" data-translate-text="PLAYLIST_UNSUBSCRIBE">{{ __('web.PLAYLIST_UNSUBSCRIBE') }}</span>
                        @else
                            <span class="label desktop" data-translate-text="PLAYLIST_SUBSCRIBE"> {{ __('web.PLAYLIST_SUBSCRIBE') }} </span>
                        @endif
                    </a>
                @endif
            </div>
        </div>
    @elseif ($element == "activity")
        @if (count($playlists) > 1)
            <a href="{{ $playlist->permalink_url }}" class="feed-item-img show-playlist-tooltip small playlist-link " data-toggle="contextmenu" data-trigger="right" data-type="playlist" data-id="{{ $playlist->id }}">
                <img src="{{ $playlist->artwork_url }}" class="img-thumb rounded-2">
            </a>
        @else
            <div class="d-flex flex-row gap-2 justify-content-start align-items-start">
                <img class="img-thumb rounded-2" src="{{ $playlist->artwork_url }}">
                <div class="d-flex flex-column justify-content-start gap-2 fs-13">
                    <a href="{{ $playlist->permalink_url }}" class="font-default fw-bolder color-text">{{ $playlist->title }}</a>
                    @if(isset($playlist->user))
                        <a href="{{ route('frontend.user.show', ['user' => $playlist->user->username]) }}" class="fw-bolder font-default color-pink">{{ $playlist->user->name }}</a>
                    @endif
                    <a class="btn-default btn-outline btn-narrow play play-object" data-type="playlist" data-id="{{ $playlist->id }}">
                        <svg height="18" width="18" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 5v14l11-7z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                        <span data-translate-text="PLAY_PLAYLIST">{{ __('web.PLAY_PLAYLIST') }}</span>
                    </a>
                </div>
            </div>
        @endif
    @else
        <div class="module module-cell playlist small grid-item">
            <div class="img-container">
                <img class="img" src="{{ $playlist->artwork_url }}" alt="{{ $playlist->title }}"/>
                <div class="actions primary">
                    <a class="btn btn-secondary btn-icon-only btn-options" data-toggle="contextmenu" data-trigger="left" data-type="playlist" data-id="{{ $playlist->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    </a>
                    <a class="btn btn-secondary btn-icon-only btn-rounded btn-play play-or-add play-object" data-type="playlist" data-id="{{ $playlist->id }}">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" height="26" width="20"><path d="M8 5v14l11-7z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
                    </a>
                    <a class="btn btn-favorite favorite @if($playlist->favorite) on @endif" data-type="playlist" data-id="{{ $playlist->id }}" data-title="{{ $playlist->title }}" data-url="{{ $playlist->permalink_url }}">
                        <svg class="off" height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                        <svg class="on" height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </a>
                </div>
            </div>
            <div class="module-inner playlist">
                <a href="{{ $playlist->permalink_url }}" class="headline title">{{ $playlist->title }}</a>
                @if(isset($playlist->user))
                    <span class="byline">by <a href="{{ route('frontend.user.show', ['user' => $playlist->user->username]) }}" class="secondary-text">{{ $playlist->user->name }}</a></span>
                @endif
            </div>
        </div>
    @endif
@endforeach