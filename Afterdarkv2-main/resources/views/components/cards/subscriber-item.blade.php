<div class="col-12 col-md-4 p-1">
    <a href="{{ $follower->permalink_url }}" class="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3">
        <img src="{{ $follower->artwork_url }}"
             alt="" class="card-item-avatar"
        >
        <div class="title font-default color-text text-center">
            {{ $follower->name }}
        </div>
        <div class="mt-auto d-flex flex-row justify-content-between align-items-end text-center font-merge gap-2">
            <div class="d-flex flex-column gap-1">
                <div class="color-pink fs-5">
                    {{ $follower->song_count }}
                </div>
                <div class="color-grey">
                    Songs
                </div>
            </div>
            <div class="d-flex flex-column gap-1">
                <div class="color-pink fs-5">
                    {{ $follower->playlist_count }}
                </div>
                <div class="color-grey">
                    Playlists
                </div>
            </div>
            <div class="d-flex flex-column gap-1">
                <div class="color-pink fs-5">
                    {{ $follower->follower_count }}
                </div>
                <div class="color-grey">
                    Followers
                </div>
            </div>
        </div>
    </a>
    @if ($controls)
        <a class="btn-default btn-outline mt-2 favorite @if ($follower->favorite) on @endif"
           data-type="user" data-text-on="{{ __('web.UNFOLLOW') }}"
           data-text-off="{{ __('web.FOLLOW') }}" data-title="{{ $follower->name }}"
           data-id="{{ $follower->id }}"
        >
            <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3zM472 200l144 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-144 0c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
            </svg>
            @if ($follower->favorite)
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