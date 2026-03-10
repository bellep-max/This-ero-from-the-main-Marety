@extends('index')
@section('content')
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    @include('frontend.default.profile.layout.menu')
                </div>
                <div class="col col-xl-9">
                    <div class="d-flex flex-column w-100 gap-4 bg-light rounded-5 p-3 p-lg-5 vh-100">
                        <div class="font-default fs-4" data-translate-text="PURCHASED">{{ __('web.PURCHASED') }}</div>
                        @if(count($profile->purchased))
                            @foreach($profile->purchased AS $item)
                                @if(isset($item->object))
                                    @if($item->orderable_type === 'App\Models\Album')
                                        <h1 class="time-purchased-h1 text-secondary mb-2 mt-3">{{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y h:m') }}</h1>
                                        <div class="purchased-album-header">
                                            <img class="purchased-album-cover" src="{{ $item->object->artwork_url }}" title="{{ $item->object->title }}" alt="{{ $item->object->title }}">
                                            <div class="purchased-album-info"><span class="css-bsi9cv">{{ $item->object->title }}</span>
                                                <p class="purchased-album-artist">
                                                    @foreach($item->object->artists as $artist)<a href="{{ $artist->permalink_url }}" title="{{ $artist->name }}">{{ $artist->name }}</a>@if(!$loop->last), @endif @endforeach
                                                </p>
                                                <!-- <button class="btn btn-primary"><span>{{ __('web.DOWNLOAD') }}</span></button> -->
                                            </div>
                                        </div>
                                        <div class="mb-5">
                                            @foreach($item->object->songs()->get() AS $song)
                                                @include('commons.purchased_song', ['song' => $song])
                                            @endforeach
                                        </div>
                                    @else
                                        <h1 class="time-purchased-h1 text-secondary mb-2 mt-3">{{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y h:m') }}</h1>
                                        @include('commons.purchased_song', ['song' => $item->object])
                                    @endif
                                @endif
                            @endforeach
                        @else
                            <div class="empty-page following">
                                <div class="empty-inner">
                                    <h2 data-translate-text="YOU_DIDNT_BOUGHT_ANYTHING_YET">{{ __('web.YOU_DIDNT_BOUGHT_ANYTHING_YET') }}</h2>
                                    <p data-translate-text="YOU_DIDNT_BOUGHT_TIP">{{ __('web.YOU_DIDNT_BOUGHT_TIP') }}</p>
                                </div>
                            </div>
                        @endif
{{--                        @include('frontend.components.toolbars.tracks', $profile)--}}
{{--                        <div id="songs-grid" class="d-flex flex-column w-100 overflow-y-auto">--}}
{{--                            @foreach($profile->tracks as $song)--}}
{{--                                @include('frontend.components.track-dark', ['song' => $song, 'discover' => false, 'adventure' => false])--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection