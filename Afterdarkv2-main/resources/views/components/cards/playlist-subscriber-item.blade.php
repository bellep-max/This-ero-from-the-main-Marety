<div class="col-12 col-md-6 p-1">
    <div href="{{ $subscriber->permalink_url }}" class="playlist-subscriber-card-item d-flex flex-row justify-content-start align-items-center gap-4 p-2">
        <a href="{{ $subscriber->permalink_url }}">
            <img src="{{ $subscriber->artwork_url }}"
                 alt="{{ $subscriber->name }}" class="playlist-subscriber-card-item-avatar"
            >
        </a>
        <div class="d-flex flex-column justify-content-start align-items-start gap-2">
            <a href="{{ $subscriber->permalink_url }}" class="title font-default color-text text-start">
                {{ $subscriber->name }}
            </a>
            <div class="mt-auto d-flex flex-row justify-content-between align-items-end text-center font-merge gap-2">
                <div class="d-flex flex-column gap-1">
                    <div class="color-pink fs-5">
                        {{ $subscriber->song_count }}
                    </div>
                    <div class="color-grey">
                        Songs
                    </div>
                </div>
                <div class="d-flex flex-column gap-1">
                    <div class="color-pink fs-5">
                        {{ $subscriber->playlist_count }}
                    </div>
                    <div class="color-grey">
                        Playlists
                    </div>
                </div>
                <div class="d-flex flex-column gap-1">
                    <div class="color-pink fs-5">
                        {{ $subscriber->follower_count }}
                    </div>
                    <div class="color-grey">
                        Followers
                    </div>
                </div>
            </div>
            @if ($controls && auth()->id() !== $subscriber->id)
                <a class="d-lg-none d-flex w-100 btn-default btn-outline btn-narrow favorite @if ($subscriber->favorite) on @endif"
                   data-type="user" data-text-on="{{ __('web.UNFOLLOW') }}"
                   data-text-off="{{ __('web.FOLLOW') }}" data-title="{{ $subscriber->name }}"
                   data-id="{{ $subscriber->id }}"
                >
                    <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                        <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                        <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3zM472 200l144 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-144 0c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
                    </svg>
                    @if ($subscriber->favorite)
                        <span class="favorite-label" data-translate-text="UNFOLLOW">
                    {{ __('web.UNFOLLOW') }}
                </span>
                    @else
                        <span class="favorite-label" data-translate-text="FOLLOW">
                    {{ __('web.FOLLOW') }}
                </span>
                    @endif
                </a>
            @endif
        </div>
        @if ($controls && auth()->id() !== $subscriber->id)
            <a class="d-none d-lg-flex btn-default btn-outline btn-narrow favorite @if ($subscriber->favorite) on @endif"
               data-type="user" data-text-on="{{ __('web.UNFOLLOW') }}"
               data-text-off="{{ __('web.FOLLOW') }}" data-title="{{ $subscriber->name }}"
               data-id="{{ $subscriber->id }}"
            >
                <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                    <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3zM472 200l144 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-144 0c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
                </svg>
                @if ($subscriber->favorite)
                    <span class="favorite-label" data-translate-text="UNFOLLOW">
                    {{ __('web.UNFOLLOW') }}
                </span>
                @else
                    <span class="favorite-label" data-translate-text="FOLLOW">
                    {{ __('web.FOLLOW') }}
                </span>
                @endif
            </a>
        @endif
    </div>
</div>

{{--<div class="col-lg-6 col-12">--}}
{{--    <div class="module-cell user">--}}
{{--        <a href="{{ $user->permalink_url }}" title="{{ $user->username }}" class="img-container">--}}
{{--            <img class="img" src="{{ $user->artwork_url }}" alt="{{ $user->name }}">--}}
{{--        </a>--}}
{{--        <div class="module-inner">--}}
{{--            <a href="/{{ $user->username }}" class="headline">{{ $user->name }}</a>--}}
{{--            <ul class="metadata">--}}
{{--                <li><a href="{{ route('frontend.user.favorites', $user) }}"><span id="song-count" class="num">{{ $user->favorite_count }}</span> <span class="label" data-translate-text="SONGS">{{ __('web.SONGS') }}</span></a></li>--}}
{{--                <li><a href="{{ route('frontend.user.playlists', $user) }}"><span id="playlist-count" class="num">{{ $user->playlist_count }}</span> <span class="label" data-translate-text="PLAYLISTS">{{ __('web.PLAYLISTS') }}</span></a></li>--}}
{{--                <li><a href="{{ route('frontend.user.followers', $user) }}"><span id="follower-count" class="num">{{ $user->follower_count }}</span> <span class="label" data-translate-text="FOLLOWERS">{{ __('web.FOLLOWERS') }}</span></a></li>--}}
{{--            </ul>--}}
{{--        </div>--}}
{{--        <div class="module-actions">--}}
{{--            @if (! auth()->check() || (auth()->check() && auth()->user()->username != $user->username))--}}
{{--                <a class="btn favorite @if($user->favorite) btn-success @endif" data-id="{{ $user->id }}" data-type="1" data-title="{{ $user->name }}" data-url="{{ $user->url }}">--}}
{{--                    <svg class="off" height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>--}}
{{--                    <svg class="on" height="26" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>--}}
{{--                    @if($user->favorite)--}}
{{--                        <span class="favorite-label" data-translate-text="UNFOLLOW">{{ __('web.UNFOLLOW') }}</span>--}}
{{--                    @else--}}
{{--                        <span class="favorite-label" data-translate-text="FOLLOW">{{ __('web.FOLLOW') }}</span>--}}
{{--                    @endif--}}
{{--                </a>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}