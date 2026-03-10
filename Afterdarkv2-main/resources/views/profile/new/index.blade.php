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
                        <div class="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-center flex-wrap">
                            @include('frontend.components.circled-text', ['title' => $profile->favorite_count, 'description' => 'Favorites'])
                            @include('frontend.components.circled-text', ['title' => $profile->total_plays, 'description' => 'Total Plays'])
                            @include('frontend.components.circled-text', ['title' => $profile->follower_count, 'description' => 'Followers'])
                        </div>
                        @if(count($profile->recent))
                            <div class="d-flex flex-row justify-content start align-items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="39" height="40" viewBox="0 0 39 40" fill="none">
                                    <circle cx="19.5" cy="20.4336" r="18.5" fill="white" stroke="#E836C5" stroke-width="2"/>
                                    <path d="M29.8232 20.4329L14.3379 29.3733L14.3379 11.4925L29.8232 20.4329Z" fill="#E836C5"/>
                                </svg>
                                <span class="fs-4 font-default">Tracks</span>
                            </div>
                            <div class="d-flex flex-column w-100 overflow-y-auto">
                                @foreach($profile->recent as $song)
                                    @include('frontend.components.track-dark', ['song' => $song, 'discover' => false, 'adventure' => false])
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
