@extends('frontend.default.settings.layout.layout')

@section('title')
    Settings / Edit Account
@endsection

@section('main-section')
    <form class="profile_page__content__form ajax-form row gy-4" id="settings-account-form" method="POST" action="{{ route('frontend.auth.user.settings.account') }}" novalidate>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                <span class="font-default fs-14 color-text">Profile Address & Username:</span>
                <input class="form-control"
                       name="username"
                       value="{{ auth()->user()->username }}"
                       type="text"
                       required
                >
                <span class="fs-12 font-merge color-grey">
                    <a href="{{ route('frontend.user.show', auth()->user()->username) }}" target="_blank">{{ route('frontend.user.show', ['user' => auth()->user()->username]) }}</a> Your custom URL can also be used as your login username. You can edit this URL once per day.
                </span>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                <span class="font-default fs-14 color-text">Email:</span>
                <input
                        class="form-control"
                        name="email"
                        value="{{ auth()->user()->email }}"
                        type="text"
                        required
                >
                <span class="fs-12 font-merge color-grey">
                    {!! __('web.SETTINGS_EMAIL_TIP') !!}
                </span>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1 control">
                <span class="font-default fs-14 color-text">Current Password:</span>
                <input class="form-control"
                       name="password"
                       type="password"
                       required
                >
                <span class="fs-12 font-merge color-grey">
                    {{ __('web.SETTINGS_CURRENT_PASSWORD_TIP') }}
                </span>
            </div>
            <a href="{{ route('frontend.settings.subscription') }}" class="fs-14 font-default color-pink">
                Upgrade my account
            </a>
        </div>
        <div class="col-12 mt-5">
            <div class="d-flex flex-row justify-content-center align-items-center gap-4">
                <button type="submit" class="btn-default btn-pink btn-wide">
                    Save Changes
                </button>
                <span class="btn-default btn-outline btn-wide" id="delete-profile">
                    Delete Account
                </span>
            </div>
        </div>
    </form>
@endsection
