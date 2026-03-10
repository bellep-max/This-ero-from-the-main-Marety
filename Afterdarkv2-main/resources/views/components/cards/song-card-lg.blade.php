<a class="{{ $class }} play-object" data-type="song" data-id="{{ $song['id'] }}">
    <script style="display: none">var song_data_{{ $song['id'] }} = {!! json_encode($song) !!}</script>
    <div class="{{ $class }}__img">
        <img src="{{ $song['artwork_url'] }}" alt="">
    </div>
    <div class="{{ $class }}__info">
        <div class="{{ $class }}__info__title">
            {{ $song['title'] }}
        </div>
        <div class="{{ $class }}__info__desc overflow-hidden text-truncate">
            {{ $song['description'] }}
        </div>
    </div>
</a>