<script>var song_data_{{ $song->id }} = {!! json_encode($song) !!}</script>
<li class="track no-released sponsored ng-star-inserted">
    <div class="col-lg-5 d-flex align-items-center">
        <div class="col-play">
            <span class="number">{{ $song->id }}</span>
            <a class="mat-focus-indicator btn-play mat-fab mat-button-base mat-accent ng-star-inserted play-object" data-type="song" data-id="{{ $song->id }}">
                <span class="mat-button-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="59" height="60" viewBox="0 0 59 60" fill="none">
                        <circle cx="29.5" cy="30" r="29.5" fill="white"/>
                        <path d="M45.1177 30L21.6912 43.5252L21.6912 16.4747L45.1177 30Z" fill="#E836C5"/>
                    </svg>
                </span>
                <div matripple="" class="mat-ripple mat-button-ripple mat-button-ripple-round"></div>
                <div class="mat-button-focus-overlay"></div>
            </a>
        </div>
        <img alt="Track image cover" src="{{ $song->artwork_url }}" onerror="this.src = '/common/default/song.png'" class="track-image">
        <div class="track-info">
            <a class="title" href="{{ $song->permalink_url }}">
                <span class="title-content">{{ $song->title }}</span>
            </a>
            @if ($song->user)
                <div class="musician-container">
                    <div class="musician desktop">
                        <span class="ng-star-inserted">by</span>
                        @auth
                            <a href="{{ $song->user->permalink_url ?? '' }}" title="{{ $song->user->name ?? '' }}">{{ $song->user->name ?? '' }}</a>
                        @elseguest
                            <div onclick="$.engineLightBox.show('lightbox-login')" class="guest__content__login-button__login">
                                {{ $song->user->name ?? '' }}
                            </div>
                        @endauth
                    </div>
                    <div class="musician mobile">
                        <span class="ng-star-inserted">by {{ $song->user->name }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-6 d-flex">
        <span class="tags-container" loop-iteration="{{ $loop->iteration }}">
            @if($song->genres && count($song->genres))
                @foreach($song->genres as $index => $genre)
                    <a class="tag ng-star-inserted" href="{{ $genre->permalink_url }}"><p>#{!! $genre->name !!}</p></a>
                @endforeach
            @endif
            @if($song->tags && count($song->tags))
                @foreach($song->tags as $index => $tag)
                    @if($loop->iteration < 4)
                        <a class="tag ng-star-inserted" href="{{ $tag->permalink_url }}"><p>#{!! $tag->tag !!}</p></a>
                    @endif
                @endforeach
            @endif
        </span>
    </div>
    @auth
        <div class="track__action col-lg-1 d-flex align-items-center justify-content-end">
            <button class="btn btn-link d-flex ml-2" data-toggle="contextmenu" data-trigger="left" data-type="song" data-id="{{ $song->id }}">
                <svg height="512pt" viewBox="-192 0 512 512" width="512pt" xmlns="http://www.w3.org/2000/svg">
                    <path d="m128 256c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0"/>
                    <path d="m128 64c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0"/>
                    <path d="m128 448c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0"/>
                </svg>
            </button>
        </div>
    @endauth
</li>