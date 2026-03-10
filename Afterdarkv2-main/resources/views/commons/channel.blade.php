@isset($channel->objects)
    <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
        <div class="d-flex flex-row justify-content-start align-items-center gap-3 w-100">
            @if($channel->object_type == \App\Constants\TypeConstants::SONG)
                @include('frontend.components.play-icon-button', ['target' => '#channel-'.$channel->id])
            @endif
            <div class="">
                <div class="block-title color-light">
                    {{ $channel->title }}
                </div>
                @if($channel->description)
                    <div class="block-description color-light">
                        {{ $channel->description }}
                    </div>
                @endif
            </div>
            <a class="btn-default btn-outline ms-auto mt-auto" href="{{ route('frontend.channel', $channel->alt_name) }}">
                <span data-translate-text="SEE_ALL">{{ __('web.SEE_ALL') }}</span>
            </a>
        </div>

        <div class="home-content-container">
            <div class="swiper-container-channel">
                <div id="channel-{{ $channel->id }}" class="swiper-wrapper gap-3">
                    @if($channel->object_type == \App\Constants\TypeConstants::SONG)
                        @foreach ($channel->objects as $index => $song)
                            @include('frontend.components.activity.song', [\App\Constants\TypeConstants::SONG => $song, 'index' => $index, 'element' => 'carousel'])
                        @endforeach
                    @elseif($channel->object_type == 'artist')
                        @include('commons.artist', ['artists' => $channel->objects->data, 'element' => 'carousel'])
                    @elseif($channel->object_type == 'album')
                        @include('commons.album', ['albums' => $channel->objects->data, 'element' => 'carousel'])
                    @elseif($channel->object_type == 'playlist' && isset($channel->objects->data))
                        @include('commons.playlist', ['playlists' => $channel->objects->data, 'element' => 'carousel'])
                    @elseif($channel->object_type == 'station')
                        @include('commons.station', ['stations' => $channel->objects->data, 'element' => 'carousel'])
                    @elseif($channel->object_type == 'user')
                        @include('commons.user', ['users' => $channel->objects->data, 'element' => 'carousel'])
                    @elseif($channel->object_type == 'podcast')
                        @include('commons.podcast', ['podcasts' => $channel->objects->data, 'element' => 'carousel'])
                    @endif
                </div>
            </div>
            <a class="home-pageable-nav previous-pageable-nav btn-default btn-outline btn-rounded swiper-arrow-left">
                <svg height="16" viewBox="0 0 501.5 501.5" width="16" xmlns="http://www.w3.org/2000/svg"><g><path d="M302.67 90.877l55.77 55.508L254.575 250.75 358.44 355.116l-55.77 55.506L143.56 250.75z"></path></g></svg>
            </a>
            <a class="home-pageable-nav next-pageable-nav btn-default btn-outline btn-rounded swiper-arrow-right">
                <svg height="16" viewBox="0 0 501.5 501.5" width="16" xmlns="http://www.w3.org/2000/svg"><g><path d="M302.67 90.877l55.77 55.508L254.575 250.75 358.44 355.116l-55.77 55.506L143.56 250.75z"></path></g></svg>
            </a>
        </div>
    </div>
@endisset