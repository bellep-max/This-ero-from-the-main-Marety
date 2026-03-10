@if(Route::currentRouteName() == 'frontend.homepage')
    {{-- <div id="landing-hero">
        <div class="claim-hero">
            <div class="container claim-container">
                <div class="row">
                    <div class="col">
                        <div class="vertical-align">
                            <p class="claim-subtitle text-uppercase">Premium</p>
                            <h1 class="claim-display-title">You bring the passion. We bring the music.</h1>
                            <p class="claim-h3 text-left text-white">Start your 30 day premium trial now. Cancel at any time.</p>
                            <a class="button-white orange w-button claim-artist-access">Start Premium Trial</a>
                        </div>
                    </div>
                    <div class="claim-column-right col">
                        <img src="{{ asset('skins/default/images/main-landing.png') }}" width="540" alt="Main landing" class="claim-landing-image">
                    </div>
                </div>
            </div>
        </div>
        @if(Cache::has('trending_week'))
            <div class="va-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 col-12 align-items-center d-flex">
                            <div class="position-relative">
                                <h1>The music you love</h1>
                                <p>With over 60 million tracks and tons of exclusive interviews and videos, we are here to bring you closer to the artists you listen to.</p>
                                <a href="{{ route('frontend.trending.week') }}" class="cta-link mt-3">
                                <span>
                                    <i class="landing-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M6.5 13.5L5.5 12.5 9.9 8 5.5 3.5 6.5 2.5 12.1 8 6.5 13.5z"></path></svg>
                                    </i>
                                </span>
                                    <span class="cta-text font-weight-bolder">More Featured Content</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-7 col-12">
                            <div class="custom-grid-wrapper row">
                                @foreach(Cache::get('trending_week')->slice(0, 6) as $song)
                                    <a href="{{ $song->permalink_url }}" class="custom-grid text-dec-none col-lg-4 col-6">
                                        <div class="position-relative overflow-hidden">
                                            <div class="custom-grid-image block placeholder position-relative" style="padding-bottom:100%;">
                                                <img src="{{ $song->artwork_url }}" alt="{{ $song->title }}" class="block position-absolute" />
                                            </div>
                                            <div class="custom-grid-cover ">
                                                <div class="position-center-content justify-content-center align-items-center text-center p-2">
                                                    <h5 class="mb-2">{{ $song->title }}</h5>
                                                    <p class="mb-0">@foreach($song->artists as $artist){{$artist->name}}@if(!$loop->last), @endif @endforeach</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="va-section-footer secondary">
            <div class="container claim-container">
                <h2 class="claim-h2 mb-5">Why Us?</h2>
                <div class="row">
                    <div class="card-info w-col col-lg-4 col-12">
                        <div class="position-relative d-flex justify-content-center mb-3">
                            <img src="{{ asset('skins/default/images/landing-collection.svg') }}" alt="" class="card-image">
                        </div>
                        <h3 class="claim-feature-h3 text-center">A world of music in your pocket.</h3>
                        <p class="claim-h3-regular text-secondary">Find new loves and old favorites from over 56 million tracks.</p>
                    </div>
                    <div class="card-info w-col col-lg-4 col-12">
                        <div class="position-relative d-flex justify-content-center mb-3">
                            <img src="{{ asset('skins/default/images/landing-pocket.svg') }}" alt="" class="card-image">
                        </div>
                        <h3 class="claim-feature-h3 text-center">Craft your collection.</h3>
                        <p class="claim-h3-regular text-secondary">Create playlists from millions of tracks and take them with you wherever you go.</p>
                    </div>
                    <div class="card-info w-col col-lg-4 col-12">
                        <div class="position-relative d-flex justify-content-center mb-3">
                            <img src="{{ asset('skins/default/images/landing-foryou.svg') }}" alt="" class="card-image">
                        </div>
                        <h3 class="claim-feature-h3 text-center">Made for you.</h3>
                        <p class="claim-h3-regular text-secondary">Flow gets to know what you like and what you don't. Discover your personal soundtrack.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="va-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-12 d-flex justify-content-center align-items-center">
                        <img src="{{ asset('skins/default/images/landing-community.svg') }}" alt="" class="card-image">
                    </div>
                    <div class="col-lg-6 col-12">
                        <h2 class="claim-h2-white padding-bottom-40px">Join our community</h2>
                        <p class="claim-h3">Our community is made of millions of music fans worldwide building the best knowledge for music.</p>
                        <div class="d-flex justify-content-center">
                            <a class="button-white w-button claim-artist-access text-primary">Join Now</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="va-section-footer secondary">
            <div class="container">
                <h2 class="claim-h2-white padding-bottom-40px">Get verified now</h2>
                <p class="claim-h3">If you are an artist or part of their management team, <br>we’ll help you getting the most out of your verified profile.</p>
                <div class="d-flex justify-content-center">
                    <a class="button-white w-button claim-artist-access text-primary">Get Verified Now</a>
                </div>
            </div>
        </div>
        @include('homepage.footer')
    </div> --}}
@endif
