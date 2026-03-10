@extends('frontend.default.settings.layout.layout')

@section('title')
    Subscription
@endsection

@section('main-section')
    <div class="d-flex flex-column justify-content-center align-items-center gap-4">
        <div class="profile_page__content__subscription__img">
            <img src="{{ asset('svg/subscription.svg') }}" alt="">
        </div>
        @if($subscription)
            <form method="POST" action="{{ $subscription->status === 'active' ? route('frontend.subscription.suspend') : route('frontend.subscription.activate') }}">
                <button type="submit" class="btn-default btn-pink btn-wide">
                    {{ $subscription->status === 'active' ? 'Suspend' : 'Activate' }} Subscription
                </button>
            </form>
            <form method="POST" action="{{ route('frontend.subscription.cancel') }}">
                <button type="submit" class="btn-default btn-pink btn-wide">
                    Cancel Subscription
                </button>
            </form>
        @else
            <div class="font-default fs-5 fw-bolder">
                Upgrade your account and enjoy exclusive premium features.
            </div>
            <div class="text-start font-default fs-14">
                <p>- An access to having other people make a paid subscription to User's account</p>
                <p>- Hide uploaded content behind a paywall for other Users</p>
                <p>- Unlimited number of uploads per week/day</p>
                <p>- Upload Audios of over 35 min length</p>
                <p>- Link podcasts/audios to Spotify</p>
            </div>
            <form method="POST" action="{{ route('frontend.subscription.checkout') }}">
                <button type="submit" class="btn-default btn-pink btn-wide">
                    Subscribe to the Site
                </button>
            </form>
        @endif
    </div>
@endsection
