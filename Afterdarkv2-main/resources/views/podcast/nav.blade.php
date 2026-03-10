<div class="d-flex flex-row justify-content-center align-items-center w-100 border-bottom text-center">
    <a href="{{ $podcast->permalink_url }}" class="pb-3 navigation-link @if(Route::currentRouteName() == 'frontend.podcast.show') active @endif" data-translate-text="OVERVIEW">{{ __('web.OVERVIEW') }}<div class="arrow"></div></a>
    <a href="{{ route('frontend.podcast.subscribers', ['podcast' => $podcast, 'slug' => $podcast->slug]) }}" class="pb-3 navigation-link @if(Route::currentRouteName() == 'frontend.podcast.subscribers') active @endif" data-translate-text="SUBSCRIBERS">{{ __('web.SUBSCRIBERS') }}<div class="arrow"></div></a>
</div>
{!! Advert::get('header') !!}