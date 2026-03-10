@extends('index')
@section('content')
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    @include('frontend.default.profile.layout.menu', ['profile' => $profile, 'stats' => $stats])
                </div>
                <div class="col col-xl-9">
                    <div class="d-flex flex-column w-100 gap-3 bg-light rounded-5 p-3 p-lg-5 vh-100">
                        <div class="font-default fs-4">Favorites</div>
                        <div class="d-flex flex-column w-100 overflow-y-auto">
                            @foreach($profile->lovedSongs as $song)
                                @include('frontend.components.track-dark', ['song' => $song, 'discover' => false, 'adventure' => false])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection