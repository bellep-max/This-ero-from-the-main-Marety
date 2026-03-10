@extends('index')
@section('content')
    <div class="profile_page">
        <div class="container-fluid">
            <div class="d-flex flex-row justify-content-start gap-5">
                @include('frontend.default.profile.layout.menu')
                <div class="profile_page__content favorite">
                    <div class="container">
                        <div class="d-flex flex-column w-100 gap-4">
                            <div class="profile_page__content__title">Albums</div>
                            <div id="songs-grid" class="profile_page__content__songs__container d-flex flex-column gap-4">
                                Albums
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection