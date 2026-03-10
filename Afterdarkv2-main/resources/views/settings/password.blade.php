@extends('frontend.default.settings.layout.layout')

@section('title')
    Settings / Change Password
@endsection

@section('main-section')
    <form id="update-password-form" class="profile_page__content__form row gy-4">
        <div class="col-12">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                <span class="font-default fs-14 color-text">Current Password:</span>
                <input class="form-control"
                       name="old-password"
                       type="password"
                       required
                >
                <span class="fs-12 font-merge color-grey">
                    Your password should be at least 5 characters and not a dictionary word or common name. You should change your password once a year.
                </span>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                <span class="font-default fs-14 color-text">New Password:</span>
                <input class="form-control"
                       name="password"
                       type="password"
                       required
                >
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                <span class="font-default fs-14 color-text">Retype Password:</span>
                <input class="form-control"
                       name="password_confirmation"
                       type="password"
                       required
                >
            </div>
        </div>
        <div class="col-12 d-flex flex-row justify-content-center align-items-center gap-4 mt-5">
            <button type="submit" class="btn-default btn-pink btn-wide">
                Save Changes
            </button>
        </div>
    </form>
@endsection
