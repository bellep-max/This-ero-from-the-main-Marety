@extends('index')
@section('content')
    @include('artist-management.nav', ['artist' => $artist])
    <form class="ajax-form" id="settings-profile-form" method="post" action="{{ url('auth/user/settings/profile') }}" enctype="multipart/form-data" novalidate>
        <div class="page-header ">
            <h1 data-translate-text="SETTINGS_TITLE">Artist Manager / <span data-translate-text="SETTINGS_TITLE_PROFILE">{{ __('web.REPORTS') }}</span></h1>
        </div>
        <div id="row" class="settings">


        </div>
    </form>
@endsection