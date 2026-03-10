@extends('index')
@section('content')
    <div class="get-verify-now">
        <div class="claim-hero">
            <div class="container claim-container">
                <div class="row">
                    <div class="col">
                        <div class="vertical-align">
                            <p class="claim-subtitle">{{ __('web.CLAIM_FOR_ARTIST') }}</p>
                            <h1 class="claim-display-title">{{ __('web.CLAIM_DISPLAY_TITLE') }}</h1>
                            <p class="claim-h3 right">{{ __('web.CLAIM_DISPLAY_DESCRIPTION') }}</p>
                            <a class="button-white orange w-button claim-artist-access">{{ __('web.CLAIM_GET_VERIFIED_NOW') }}</a>
                        </div>
                    </div>
                    <div class="claim-column-right col">
                        <img src="{{ asset('skins/default/images/artist-request-landing.png') }}" width="540" alt="Request landing" class="claim-landing-image">
                    </div>
                </div>
            </div>
        </div>
        <div class="container claim-container mt-5">
            <h2 class="claim-h2 mb-5">Become a verified artist</h2>
            <div class="row">
                <div class="card-info w-col col-lg-4 col-12 text-center">
                    <img src="{{ asset('skins/default/images/artist-request-connect-sample.png') }}" alt="" class="card-image">
                    <h3 class="claim-feature-h3 text-center">Making emotional connections</h3>
                    <p class="claim-h3-regular text-secondary">Creating an opportunity to strengthen the emotional connection with your fan base.</p>
                </div>
                <div class="card-info w-col col-lg-4 col-12 text-center">
                    <img src="{{ asset('skins/default/images/artist-request-verified-button.png') }}" alt="" class="card-image">
                    <h3 class="claim-feature-h3 text-center">Make your songs official</h3>
                    <p class="claim-h3-regular text-secondary">Add, edit and sync your songs. <br>They will be locked in place and shown as your official ones.</p>
                </div>
                <div class="card-info w-col col-lg-4 col-12 text-center">
                    <img src="{{ asset('skins/default/images/artist-request-profile-sample.png') }}" alt="" class="card-image">
                    <h3 class="claim-feature-h3 text-center">Manage your catalog</h3>
                    <p class="claim-h3-regular text-secondary">Control your songs catalog, credits, profile and more.</p>
                </div>
            </div>

        </div>

        <div class="container claim-container mt-5 desktop">
            <h2 class="claim-h2 mb-5">Join over 1B verified artists</h2>
            <img src="{{ asset('skins/default/images/artist-request-verified-sample-white.png') }}" width="1140" alt="" class="verified-artists">
        </div>

        <div class="va-section-footer mt-5">
            <div class="container">
                <h2 class="claim-h2-white padding-bottom-40px">Get verified now</h2>
                <p class="claim-h3">If you are an artist or part of their management team, <br>we’ll help you getting the most out of your verified profile.</p>
                <div class="d-flex justify-content-center">
                    <a class="button-white w-button claim-artist-access text-primary">Get Verified Now</a>
                </div>
            </div>
        </div>
    </div>
@endsection