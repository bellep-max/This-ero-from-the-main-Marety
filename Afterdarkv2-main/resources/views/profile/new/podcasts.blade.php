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
                        <div class="d-flex flex-row justify-content-start align-items-center gap-4">
                            @if (auth()->id() === $profile->id)
                                <a id="create-podcast" class="btn-default btn-outline btn-rnd border-2">
                                    <svg width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                        <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                                    </svg>
                                </a>
                            @endif
                            <div class="font-default fs-4">
                                Podcasts
                            </div>
                        </div>
                        <div class="container-fluid overflow-y-auto">
                            <div id="podcasts-grid" class="row gy-3">
                                @foreach ($profile->podcasts as $podcast)
                                    @include('frontend.components.podcast-item', ['podcast' => $podcast])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
