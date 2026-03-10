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
                            Followers
                        </div>
                        <div class="followers_container__tabs" id="followers-tab">
                            <div class="followers_container__tabs__item active" data-followers="all">
                                <span class="fw-bolder">{{ $allFollowers->count() }}</span>
                                <span class="mt-1">All Followers</span>
                            </div>
                            <div class="followers_container__tabs__item" data-followers="paid">
                                <span class="fw-bolder">{{ $paidFollowers->count() }}</span>
                                <span class="mt-1">Paid Followers</span>
                            </div>
                            <div class="followers_container__tabs__item" data-followers="free">
                                <span class="fw-bolder">{{ $freeFollowers->count() }}</span>
                                <span class="mt-1">Free Followers</span>
                            </div>
                        </div>
                        <div class="profile_page__content__songs__container d-flex flex-column justify-content-start align-items-center gap-4 overflow-y-auto">
                            <div class="followers-tab followers-all">
                                <div class="container-fluid">
                                    <div class="row gy-3">
                                        @foreach ($allFollowers as $follower)
                                            @include('frontend.components.cards.subscriber-item', ['follower' => $follower, 'controls' => false])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="followers-tab followers-paid hide">
                                <div class="container-fluid">
                                    <div class="row gy-3">
                                        @foreach ($paidFollowers as $follower)
                                            @include('frontend.components.cards.subscriber-item', ['follower' => $follower, 'controls' => false])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="followers-tab followers-free hide">
                                <div class="container-fluid">
                                    <div class="row gy-3">
                                        @foreach ($freeFollowers as $follower)
                                            @include('frontend.components.cards.subscriber-item', ['follower' => $follower, 'controls' => false])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
