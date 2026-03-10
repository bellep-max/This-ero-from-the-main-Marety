<script>var song_data_{{ $song->id }} = {!! json_encode($song) !!}</script>
<div class="track no-released sponsored">
    <div class="col-10 order-first col-lg-7 d-flex flex-row align-items-center gap-3">
        <div class="d-block">
            <a class="btn-play play-object"
               data-type="song"
               data-id="{{ $song->id }}"
            >
                <span class="description color-light text-center font-default hide-on-hover">{{ $song->id }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="59" height="60" viewBox="0 0 59 60" fill="none">
                    <circle cx="29.5" cy="30" r="29.5" fill="white"/>
                    <path d="M45.1177 30L21.6912 43.5252L21.6912 16.4747L45.1177 30Z" fill="#E836C5"/>
                </svg>
            </a>
        </div>
        <img alt="Track image cover"
             src="{{ $song->artwork_url }}"
             onerror="this.src = '/common/default/song.png'"
             class="track-image rounded-4 d-md-block d-none"
        >
        <div class="d-flex flex-column justify-content-center align-items-start text-truncate">
            <a class="title color-light font-default link" href="{{ $song->permalink_url ?? '' }}">
                {{ $song->title }}
            </a>
            <div class="description color-light">
                <div class="d-none d-md-block">
                    <span>by</span>
                    @auth
                        <a href="{{ $song->user->permalink_url ?? '' }}" class="color-light link" title="{{ $song->user->name ?? '' }}">{{ $song->user->name ?? '' }}</a>
                    @elseguest
                        <div onclick="$.engineLightBox.show('lightbox-login')" class="link">
                            {{ $song->user->name ?? '' }}
                        </div>
                    @endauth
                </div>
                <div class="d-block d-md-none">
                    <span>by {{ $song->user->name ?? '' }}</span>
                </div>
            </div>
        </div>
    </div>
    @if ($discover)
        <div class="col-12 order-last order-lg-1 col-lg-4 d-flex flex-row justify-content-start align-items-center flex-wrap gap-2 py-2 py-md-0" loop-iteration="{{ $loop->iteration }}">
            @if ($song->genres && count($song->genres))
                @foreach($song->genres as $index => $genre)
                    <a class="btn-default btn-outline-white text-lowercase btn-narrow" href="{{ $genre->permalink_url }}">#{!! $genre->name !!}</a>
                @endforeach
            @endif
            @if ($song->tags && count($song->tags))
                @foreach ($song->tags as $index => $tag)
                    @if ($loop->iteration < 4)
                        <a class="btn-default btn-outline-white text-lowercase btn-narrow" href="{{ $tag->permalink_url }}">#{!! $tag->tag !!}</a>
                    @endif
                @endforeach
            @endif
        </div>
    @endif
    @auth
        <div class="col-2 order-1 order-lg-last col-lg-1 d-flex flex-row align-items-center justify-content-end">
            <a class="btn-icon-only"
                    data-toggle="contextmenu"
                    data-trigger="left"
                    data-type="song"
                    data-id="{{ $song->id }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 128 512">
                    <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path d="M64 360a56 56 0 1 0 0 112 56 56 0 1 0 0-112zm0-160a56 56 0 1 0 0 112 56 56 0 1 0 0-112zM120 96A56 56 0 1 0 8 96a56 56 0 1 0 112 0z"/>
                </svg>
            </a>
        </div>
    @endauth
</div>