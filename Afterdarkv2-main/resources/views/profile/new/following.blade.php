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
                        <div class="font-default fs-4">
                            Following
                        </div>
                        <div class="profile_page__content__songs__container d-flex flex-column justify-content-start align-items-center gap-4 overflow-y-auto">
                            <div class="container-fluid">
                                <div class="row gy-3">
                                    @foreach (auth()->user()->following as $following)
                                        @include('frontend.components.cards.subscriber-item', ['follower' => $following, 'controls' => true])
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
