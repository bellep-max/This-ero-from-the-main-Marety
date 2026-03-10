@extends('index')
@section('content')
    <div id="column1" class="full">
        @include('commons.slideshow', ['slides' => $home->slides])
        @if (auth()->check())
            @if(isset($home->recentListens) && count($home->recentListens))
                @include('commons.suggest', ['more_link' => auth()->user()->permalink_url, 'type' => 'recent', 'songs' => $home->recentListens, 'title' => '<span data-translate-text="LISTEN_AGAIN">' . __('web.LISTEN_AGAIN') . '</span>', 'description' => '<span class="section-tagline" data-translate-text="TAGLINE_LISTEN_AGAIN">' . __('web.TAGLINE_LISTEN_AGAIN') . '</span>'])
            @endif
            @if(isset($home->userCommunitySongs) && count($home->userCommunitySongs))
                @include('commons.suggest', ['more_link' => route('frontend.community'), 'type' => 'community', 'songs' => $home->userCommunitySongs, 'title' => '<span data-translate-text="TOP_COMMUNITY_ALBUMS">' . __('web.TOP_COMMUNITY_ALBUMS') . '</span>', 'description' => '<span class="section-tagline" data-translate-text="TAGLINE_COMMUNITY">' . __('web.TAGLINE_COMMUNITY') . '</span>'])
            @endif
            @if(isset($home->obsessedSongs) && count($home->obsessedSongs))
                @include('commons.suggest', ['more_link' => auth()->user()->permalink_url, 'type' => 'obsessed', 'songs' => $home->obsessedSongs, 'title' => '<span data-translate-text="YOU_ARE_OBSESSED_WITH_MUSIC">' . __('web.YOU_ARE_OBSESSED_WITH_MUSIC') . '</span>', 'description' => '<span class="section-tagline" data-translate-text="TAGLINE_SIMILAR_OBSESSED">' . __('web.TAGLINE_SIMILAR_OBSESSED') . '</span>'])
            @endif

            @if(isset($home->female_new) && count($home->female_new))
                @include('commons.suggest', ['more_link' => route('frontend.vocal', ['slug' => 1]), 'type' => 'recent', 'songs' => $home->female_new, 'title' => '<span>Female New</span>', 'description' => '<span class="section-tagline">Female New</span>'])
            @endif

            @if(isset($home->topWeekly) && count($home->topWeekly))
                @include('commons.suggest', ['more_link' => route('frontend.song.top.weekly'), 'type' => 'recent', 'songs' => $home->topWeekly, 'title' => '<span>Top Played Weekly</span>', 'description' => '<span class="section-tagline">Top Played Weekly</span>'])
            @endif

            @if(isset($home->topDaily) && count($home->topDaily))
                @include('commons.suggest', ['more_link' => route('frontend.song.top.daily'), 'type' => 'recent', 'songs' => $home->topDaily, 'title' => '<span>Top Played Daily</span>', 'description' => '<span class="section-tagline">Top Played Daily</span>'])
            @endif

            @if(isset($home->female_new) && count($home->female_new))
                @include('commons.suggest', ['more_link' => route('frontend.vocal', ['slug' => 1]), 'type' => 'recent', 'songs' => $home->female_new, 'title' => '<span>Female New</span>', 'description' => '<span class="section-tagline">Female New</span>'])
            @endif

            @if(isset($home->male_new) && count($home->male_new))
                @include('commons.suggest', ['more_link' => route('frontend.vocal', ['slug' => 2]), 'type' => 'recent', 'songs' => $home->male_new, 'title' => '<span>Male New</span>', 'description' => '<span class="section-tagline">Male New</span>'])
            @endif

        @endif
        @include('commons.channel', ['channels' => $home->channels])
        @if(isset($home->popularSongs) && count($home->popularSongs))
            @include('commons.suggest', ['more_link' => route('frontend.trending.index'), 'type' => 'popular', 'songs' => $home->popularSongs, 'title' => '<span data-translate-text="POPULAR">' . __('web.POPULAR') . '</span>', 'description' => '<span class="section-tagline" data-translate-text="TAGLINE_POPULAR">' . __('web.TAGLINE_POPULAR') . '</span>'])
        @endif
    </div>
    {!! Advert::get('footer') !!}
    @include('homepage.footer')
@endsection