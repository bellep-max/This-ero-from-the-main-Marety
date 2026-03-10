@extends('index')
@section('content')
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    @include('frontend.default.profile.layout.menu')
                </div>
                <div class="col col-xl-9">
                    <div class="container d-flex flex-column w-100 gap-4 bg-light rounded-5 p-3 p-lg-5">
                        <div class="row">
                            <div class="col-12">
                                @include('playlist.nav', $playlist)
                            </div>
                        </div>
                        <div class="row gy-3 border-bottom pb-3">
                            <div class="col-12 col-lg-4 d-flex justify-content-center justify-content-xl-start align-items-start">
                                <img src="{{ $playlist->artwork_url }}" alt="{{ $playlist->title }}"
                                     class="img-fluid rounded-4 border-pink">
                            </div>
                            <div class="col">
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-center flex-wrap">
                                        @include('frontend.components.circled-text', ['title' => $playlist->playlist_songs_count, 'description' => 'Songs'])
                                        @include('frontend.components.circled-text', ['title' => intval($playlist->loves) ? $playlist->loves : '-', 'description' => 'Subscribers'])
                                        @include('frontend.components.circled-text', ['title' => $duration, 'description' => 'Duration'])
                                    </div>
                                    <script>var playlist_data_{{ $playlist->id }} = {!! json_encode($playlist->makeHidden('songs')->makeHidden('related')) !!}</script>
                                    <div class="d-flex flex-row justify-content-start align-items-center playlist py-3 border-top border-bottom gap-3">
                                        <a class="mat-focus-indicator btn-white rounded-circle mat-fab mat-button-base mat-accent ng-star-inserted play-object"
                                           style="filter: drop-shadow(0 0px 5px #DEDEDE)" data-type="playlist"
                                           data-id="{{ $playlist->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="59" height="60" viewBox="0 0 59 60" fill="none">
                                                <path d="M45.1177 30L21.6912 43.5252L21.6912 16.4747L45.1177 30Z"/>
                                            </svg>
                                        </a>
                                        <div class="d-flex flex-column align-items-start justify-content-start gap-2">
                                            <span class="font-default fs-5">{{ $playlist->title }}</span>
                                            @isset ($playlist->user)
                                                <span class="font-merge fs-14">
                                                    Playlist by <a href="{{ $playlist->user->permalink_url }}" class="color-pink">
                                                        {{ $playlist->user->name }}
                                                    </a>
                                                </span>
                                            @endisset
                                            <div class="d-flex flex-row justify-content-start align-items-center gap-2">
                                                <svg width="19" height="20" viewBox="0 0 19 20" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6 12.1915C5.78699 12.235 5.56438 12.2226 5.35456 12.1526C5.03993 12.0477 4.77985 11.8222 4.63154 11.5255C4.48322 11.2289 4.45881 10.8855 4.56369 10.5709C4.66857 10.2562 4.89413 9.99616 5.19077 9.84784L6.69063 9.09792C6.88127 9.00252 7.09314 8.95746 7.30611 8.96703C7.51907 8.9766 7.72605 9.04048 7.90738 9.15259C8.08869 9.2647 8.23833 9.42132 8.34206 9.60757C8.44578 9.79381 8.50015 10.0035 8.5 10.2167M6 12.1915L8.5 10.2167M6 12.1915V16.2163C6 16.5478 6.1317 16.8658 6.36612 17.1002C6.60054 17.3346 6.91848 17.4663 7.25 17.4663C7.58152 17.4663 7.89946 17.3346 8.13388 17.1002C8.3683 16.8658 8.5 16.5478 8.5 16.2163V10.2167M6 12.1915L8.5 10.2167M14.5 11.7155C14.4997 11.1105 14.2998 10.5225 13.9315 10.0426C13.563 9.56255 13.0464 9.21743 12.4618 9.06078C11.8772 8.90413 11.2573 8.94469 10.6981 9.17617C10.1408 9.40689 9.67487 9.81441 9.37199 10.3359C9.28676 10.4767 9.23014 10.6329 9.20541 10.7956C9.18033 10.9606 9.18855 11.129 9.22959 11.2908C9.27063 11.4526 9.34367 11.6045 9.44438 11.7376C9.5451 11.8707 9.67146 11.9823 9.816 12.0658L10.0661 11.6329L9.816 12.0658C9.96054 12.1493 10.1203 12.203 10.286 12.2237C10.4516 12.2445 10.6197 12.2318 10.7804 12.1865C10.941 12.1412 11.091 12.0642 11.2214 11.96L10.9093 11.5694L11.2214 11.96C11.3491 11.858 11.4556 11.7319 11.5348 11.5889C11.5516 11.5612 11.5738 11.5369 11.5999 11.5176C11.6273 11.4974 11.6586 11.4829 11.6917 11.4752C11.7249 11.4675 11.7593 11.4666 11.7928 11.4726C11.8263 11.4787 11.8583 11.4915 11.8867 11.5104C11.915 11.5292 11.9393 11.5536 11.9579 11.5821C11.9765 11.6107 11.9891 11.6427 11.9948 11.6763C12.0006 11.7098 11.9995 11.7442 11.9915 11.7774C11.9835 11.8105 11.9688 11.8416 11.9483 11.8688L11.9481 11.8691L9.25001 15.4663L9.25 15.4663C9.11072 15.652 9.0259 15.8728 9.00505 16.1041C8.9842 16.3353 9.02815 16.5677 9.13197 16.7753L9.53852 16.572L9.13197 16.7753C9.23578 16.983 9.39536 17.1576 9.59283 17.2796L9.8557 16.8543L9.59284 17.2796C9.79031 17.4017 10.0179 17.4663 10.25 17.4663H13.25C13.5815 17.4663 13.8995 17.3346 14.1339 17.1002C14.3683 16.8658 14.5 16.5478 14.5 16.2163C14.5 15.8848 14.3683 15.5668 14.1339 15.3324C13.8995 15.098 13.5815 14.9663 13.25 14.9663H12.7499L13.9444 13.3735C13.9447 13.373 13.9451 13.3726 13.9454 13.3721C14.3069 12.8957 14.5018 12.3136 14.5 11.7155ZM14.5 11.7155C14.5 11.7152 14.5 11.715 14.5 11.7147L14 11.7163L14.5 11.7161C14.5 11.7159 14.5 11.7157 14.5 11.7155ZM14.25 1.96631V2.46631H14.75H17C17.2652 2.46631 17.5196 2.57167 17.7071 2.7592C17.8946 2.94674 18 3.20109 18 3.46631V18.4663C18 18.7315 17.8946 18.9859 17.7071 19.1734C17.5196 19.361 17.2652 19.4663 17 19.4663H2C1.73478 19.4663 1.48043 19.361 1.29289 19.1734C1.10536 18.9859 1 18.7315 1 18.4663V3.46631C1 3.20109 1.10536 2.94674 1.29289 2.7592C1.48043 2.57167 1.73478 2.46631 2 2.46631H4.25H4.75V1.96631V1.21631C4.75 1.15 4.77634 1.08642 4.82322 1.03953C4.87011 0.992648 4.9337 0.966309 5 0.966309C5.0663 0.966309 5.12989 0.992648 5.17678 1.03953C5.22366 1.08642 5.25 1.15 5.25 1.21631V1.96631V2.46631H5.75H13.25H13.75V1.96631V1.21631C13.75 1.15 13.7763 1.08642 13.8232 1.03953C13.8701 0.992648 13.9337 0.966309 14 0.966309C14.0663 0.966309 14.1299 0.992648 14.1768 1.03953C14.2237 1.08642 14.25 1.15 14.25 1.21631V1.96631ZM1.5 6.46631V6.96631H2H17H17.5V6.46631V3.46631V2.96631H17H14.75H14.25V3.46631V4.21631C14.25 4.28261 14.2237 4.3462 14.1768 4.39308L14.5303 4.74664L14.1768 4.39309C14.1299 4.43997 14.0663 4.46631 14 4.46631C13.9337 4.46631 13.8701 4.43997 13.8232 4.39309L13.4697 4.74664L13.8232 4.39308C13.7763 4.3462 13.75 4.28261 13.75 4.21631V3.46631V2.96631H13.25H5.75H5.25V3.46631V4.21631C5.25 4.28261 5.22366 4.3462 5.17678 4.39308L5.53033 4.74664L5.17678 4.39309C5.12989 4.43997 5.0663 4.46631 5 4.46631C4.9337 4.46631 4.87011 4.43997 4.82322 4.39309C4.77634 4.3462 4.75 4.28261 4.75 4.21631V3.46631V2.96631H4.25H2H1.5V3.46631V6.46631Z"
                                                          stroke="#E836C5"/>
                                                </svg>
                                                <span class="fs-14 font-merge">
                                                    {{ $playlist->updated_at->format('d.m.Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row align-items-center flex-wrap row-gap-2 justify-content-start gap-4">
                                        @if(! auth()->check() || auth()->check() && auth()->id() != $playlist->user->id)
                                            <a class="btn-default btn-narrow favorite @if($playlist->favorite) on btn-pink @else btn-outline @endif" data-type="playlist" data-id="{{ $playlist->id }}" data-title="{{ $playlist->title }}" data-url="{{ $playlist->permalink_url }}" data-text-on="{{ __('web.PLAYLIST_UNSUBSCRIBE') }}" data-text-off="{{ __('web.PLAYLIST_SUBSCRIBE') }}" style="color: #E836C5; border: 1px solid #E836C5">
                                                <svg height="18" width="18" class="off @if ($playlist->favorite) hide @endif" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                                                <svg height="18" width="18" class="on @if (!$playlist->favorite) hide @endif" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                                                @if ($playlist->favorite)
                                                    <span class="favorite-label" data-translate-text="PLAYLIST_UNSUBSCRIBE">{{ __('web.PLAYLIST_UNSUBSCRIBE') }}</span>
                                                @else
                                                    <span class="favorite-label" data-translate-text="PLAYLIST_SUBSCRIBE"> {{ __('web.PLAYLIST_SUBSCRIBE') }} </span>
                                                @endif
                                            </a>
                                        @endif

                                        @if($playlist->is_visible)
                                            <a class="btn-default btn-narrow btn-outline share" data-type="playlist" data-id="{{ $playlist->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 19"
                                                     fill="none">
                                                    <path d="M13.5 12.56C12.93 12.56 12.42 12.785 12.03 13.1375L6.6825 10.025C6.72 9.8525 6.75 9.68 6.75 9.5C6.75 9.32 6.72 9.1475 6.6825 8.975L11.97 5.8925C12.375 6.2675 12.9075 6.5 13.5 6.5C14.745 6.5 15.75 5.495 15.75 4.25C15.75 3.005 14.745 2 13.5 2C12.255 2 11.25 3.005 11.25 4.25C11.25 4.43 11.28 4.6025 11.3175 4.775L6.03 7.8575C5.625 7.4825 5.0925 7.25 4.5 7.25C3.255 7.25 2.25 8.255 2.25 9.5C2.25 10.745 3.255 11.75 4.5 11.75C5.0925 11.75 5.625 11.5175 6.03 11.1425L11.37 14.2625C11.3325 14.42 11.31 14.585 11.31 14.75C11.31 15.9575 12.2925 16.94 13.5 16.94C14.7075 16.94 15.69 15.9575 15.69 14.75C15.69 13.5425 14.7075 12.56 13.5 12.56Z"/>
                                                </svg>
                                                <span data-translate-text="SHARE">Share</span>
                                            </a>
                                        @endif
                                        @if (auth()->check() && auth()->id() == $playlist->user->id)
                                            <a class="btn-default btn-narrow btn-outline dropdown edit-playlist-context-trigger" data-type="playlist" data-id="{{ $playlist->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="20" viewBox="0 0 20 20">
                                                    <path fill="none" d="M0 0h20v20H0V0z"/>
                                                    <path d="M15.95 10.78c.03-.25.05-.51.05-.78s-.02-.53-.06-.78l1.69-1.32c.15-.12.19-.34.1-.51l-1.6-2.77c-.1-.18-.31-.24-.49-.18l-1.99.8c-.42-.32-.86-.58-1.35-.78L12 2.34c-.03-.2-.2-.34-.4-.34H8.4c-.2 0-.36.14-.39.34l-.3 2.12c-.49.2-.94.47-1.35.78l-1.99-.8c-.18-.07-.39 0-.49.18l-1.6 2.77c-.1.18-.06.39.1.51l1.69 1.32c-.04.25-.07.52-.07.78s.02.53.06.78L2.37 12.1c-.15.12-.19.34-.1.51l1.6 2.77c.1.18.31.24.49.18l1.99-.8c.42.32.86.58 1.35.78l.3 2.12c.04.2.2.34.4.34h3.2c.2 0 .37-.14.39-.34l.3-2.12c.49-.2.94-.47 1.35-.78l1.99.8c.18.07.39 0 .49-.18l1.6-2.77c.1-.18.06-.39-.1-.51l-1.67-1.32zM10 13c-1.65 0-3-1.35-3-3s1.35-3 3-3 3 1.35 3 3-1.35 3-3 3z"/>
                                                </svg>
                                                <span class="caret"></span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-7">
                                <div id="grid-toolbar-container" class="d-flex flex-row flex-wrap justify-content-between align-items-center p-2 gap-4 row-gap-2 bg-light rounded-3">
                                    <div class="d-flex flex-row align-items-center flex-wrap row-gap-2 @if (!auth()->check() || auth()->check() && auth()->id() != $playlist->user->id) justify-content-between w-100 @else justify-content-start gap-4 @endif">
                                        <div class="d-flex flex-row gap-1">
                                            <a class="btn-default btn-outline btn-narrow play-button play-now" data-target="#songs-grid">
                                                <svg height="18" viewBox="0 0 24 24" width="18"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 5v14l11-7z"/>
                                                    <path d="M0 0h24v24H0z" fill="none"/>
                                                </svg>
                                                <span data-translate-text="SELECTION_PLAY_ALL">{{ __('web.SELECTION_PLAY_ALL') }}</span>
                                            </a>
                                            <a class="btn-default btn-outline btn-narrow play-menu"
                                               data-target="#songs-grid"
                                               data-type="playlist"
                                               data-id="{{ $playlist->id }}"
                                            >
                                                <span class="caret"></span>
                                            </a>
                                        </div>
                                        <div class="d-flex flex-row gap-1">
                                            <a class="btn-default btn-outline btn-narrow add-button add-now" data-target="#songs-grid">
                                                <svg height="18" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                                    <path d="M0 0h24v24H0z" fill="none"/>
                                                </svg>
                                                <span class="add-label"
                                                      data-translate-text="SELECTION_ADD_TO">{{ __('web.SELECTION_ADD_TO') }}</span>
                                            </a>
                                            <a class="btn-default btn-outline btn-narrow add-menu" data-target="#songs-grid">
                                                <span class="caret"></span>
                                            </a>
                                        </div>
                                        @if (!auth()->check() || auth()->check() && auth()->id() != $playlist->user->id)
                                            <a class="btn-default btn-outline btn-narrow sort-button" data-sort-popularity="true" data-sort-song="true"
                                               data-sort-artist="true" data-sort-album="true"
                                            >
                                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                                    <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8L32 224c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8l256 0c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                                </svg>
{{--                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="26" viewBox="0 0 24 24">--}}
{{--                                                    <path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z"/>--}}
{{--                                                    <path d="M0 0h24v24H0z" fill="none"/>--}}
{{--                                                </svg>--}}
                                                <span data-translate-text="POPULARITY">{{ __('web.POPULARITY') }}</span>
                                                <span class="caret"></span>
                                            </a>
                                        @endif
                                    </div>
                                    @if (!auth()->check() || auth()->check() && auth()->id() != $playlist->user->id)
                                        <form class="search-bar w-100">
                                            <svg class="icon search" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24">
                                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
                                                <path d="M0 0h24v24H0z" fill="none"></path>
                                            </svg>
                                            <input autocomplete="off" value="" name="q"
                                                   class="filter"
                                                   id="filter-search" type="text" placeholder="Filter">
                                            <a class="icon ex clear-filter">
                                                <svg height="16px" width="16px" viewBox="0 0 512 512"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="m437.019531 74.980469c-48.351562-48.351563-112.640625-74.980469-181.019531-74.980469s-132.667969 26.628906-181.019531 74.980469c-48.351563 48.351562-74.980469 112.640625-74.980469 181.019531 0 68.382812 26.628906 132.667969 74.980469 181.019531 48.351562 48.351563 112.640625 74.980469 181.019531 74.980469s132.667969-26.628906 181.019531-74.980469c48.351563-48.351562 74.980469-112.636719 74.980469-181.019531 0-68.378906-26.628906-132.667969-74.980469-181.019531zm-70.292969 256.386719c9.761719 9.765624 9.761719 25.59375 0 35.355468-4.882812 4.882813-11.28125 7.324219-17.679687 7.324219s-12.796875-2.441406-17.679687-7.324219l-75.367188-75.367187-75.367188 75.371093c-4.882812 4.878907-11.28125 7.320313-17.679687 7.320313s-12.796875-2.441406-17.679687-7.320313c-9.761719-9.765624-9.761719-25.59375 0-35.355468l75.371093-75.371094-75.371093-75.367188c-9.761719-9.765624-9.761719-25.59375 0-35.355468 9.765624-9.765625 25.59375-9.765625 35.355468 0l75.371094 75.367187 75.367188-75.367187c9.765624-9.761719 25.59375-9.765625 35.355468 0 9.765625 9.761718 9.765625 25.589844 0 35.355468l-75.367187 75.367188zm0 0"/>
                                                </svg>
                                            </a>
                                        </form>
                                    @endif
                                </div>
                                <div id="songs-grid"
                                     class="d-flex flex-column justify-content-start align-items-start gap-2 playlist-container overflow-y-auto @if(auth()->check() && auth()->id() == $playlist->user->id) sortable @endif"
                                     data-type="playlist" data-id="{{ $playlist->id }}">
                                    @foreach($playlist->playlistSongs as $index => $song)
                                        @if(isset($song->title))
                                            @include('frontend.components.playlist-track', ['song' => $song, 'playlist_id' => $playlist->id, 'index' => $index, 'sortable' => auth()->check() && auth()->id() == $playlist->user->id])
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-5">
                                <div class="content">
                                    {!! Advert::get('sidebar') !!}
                                    <div class="d-flex flex-row justify-content-between align-items-center border-bottom text-center p-2">
                                        <a id="activity-tab" class="navigation-link column2-tab active">
                                            <span data-translate-text="ACTIVITY">{{ __('web.ACTIVITY') }}</span>
                                        </a>
                                        <a id="comments-tab" class="navigation-link column2-tab">
                                            <span data-translate-text="COMMENTS">{{ __('web.COMMENTS') }}</span>
                                        </a>
                                    </div>
                                    <div id="activity" class="tread-container overflow-y-auto d-flex flex-column">
                                        @foreach($playlist->activities as $index => $activity)
                                            @if(isset($activity->details->objects) && count($activity->details->objects) && $activity->user)
                                                @include('frontend.components.activity', ['activity' => $activity, 'index' => $index])
                                            @endif
                                        @endforeach
{{--                                        @include('commons.activity', ['activities' => $playlist->activities, 'type' => 'full'])--}}
                                    </div>
                                    <div id="comments" class="tread-container overflow-y-auto d-flex flex-column gap-3 hide">
                                        @if(config('settings.playlist_comments') && $playlist->allow_comments)
                                            @include('comments.index', ['object' => (Object) ['id' => $playlist->id, 'type' => 'App\Models\Playlist', 'title' => $playlist->title]])
                                        @else
                                            <p class="text-center mt-5">Comments are turned off.</p>
                                        @endif
                                    </div>
                                </div>
                                @if (isset($related) && count($related))
                                    <div class="d-flex flex-row w-100 text-center">
                                        <div id="collaborators_digest"></div>
                                        <div id="subscriber_digest"></div>
                                        <div id="playlist_digest" class="d-flex flex-column justify-content-start align-items-start gap-3 flex-grow-0 w-100">
                                            <span class="font-default text-start color-text" data-translate-text="OTHER_PLAYLISTS">Other Playlists</span>
                                            <div class="d-flex flex-column justify-content-start align-items-start gap-2">
                                                @include('frontend.components.activity.playlist', ['playlists' => $related, 'element' => 'search'])
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
