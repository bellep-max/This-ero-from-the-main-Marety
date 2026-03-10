@extends('frontend.default.settings.layout.layout')

@section('title')
    Profile
@endsection

@section('main-section')
    {{--        <div class="profile_page__content__desc" style="width: 100%;">--}}
    {{--            Find a problem with our player or report a bug? Fill out this form and we'll check it--}}
    {{--            <div style="margin-top: 20px; display: flex; justify-content: center;">--}}
    {{--                <button id="feedback-modal" class="profile_page__content__form__actions__save">--}}
    {{--                    Feedback--}}
    {{--                </button>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    <form class="profile_page__content__form ajax-form row gy-4" id="settings-profile-form" method="POST" action="{{ route('api.auth.user.settings.profile') }}" enctype="multipart/form-data" novalidate>
        <input class="uploader invisible-input hide" id="upload-user-pic" name="artwork" accept="image/*" title="" type="file">
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                <span class="font-default fs-14 color-text">Display Name:</span>
                <input class="form-control"
                       type="text"
                       name="name"
                       maxlength="175"
                       value="{{ auth()->user()->name }}"
                       required
                >
                <span class="fs-12 font-merge color-grey">
                    This will be displayed on your profile, so be sure to use a name people will be able to recognize and search for.
                </span>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1 input-row new-select2">
                <span class="font-default fs-14 color-text">Country:</span>
                {!! makeCountryDropdown('country', 'span3 select2', auth()->user()->country) !!}
                <span class="fs-12 font-merge color-grey">
                    Represent your region and discover music, friends, and events in your area.
                </span>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                <span class="font-default fs-14 color-text">Short Bio:</span>
                <input class="form-control" name="bio" value="{{ auth()->user()->bio }}"  maxlength="180" type="text">
                <span class="fs-12 font-merge color-grey">
                    Allow other users to learn a little bit more about the person behind the Playlist.
                </span>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                <span class="font-default fs-14 color-text">Date of Birth:</span>
                <input class="form-control datepicker" name="birth" maxlength="175" type="text" value="{{ \Carbon\Carbon::parse(auth()->user()->birth)->format('m/d/Y') }}" autocomplete="off">
                <span class="fs-12 font-merge color-grey">
                    This is not displayed on your profile.
                </span>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="profile_page__content__form__item input-row new-select2">
                <span class="font-default fs-14 color-text">I identify as:</span>
                <select name="gender" class="form-gender select2">
                    <option value="M" selected="selected">Male</option>
                    <option value="F">Female</option>
                    <option value="O">Other</option>
                </select>
                <span class="fs-12 font-merge color-grey">
                    Allow other users to learn a little bit more about the person behind the Playlist.
                </span>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                <span class="font-default fs-14 color-text">Change profile photo:</span>
                <label for="upload-user-pic" class="btn-default btn-outline btn-narrow w-100">
                    Select photo
                </label>
            </div>
        </div>
        <div class="col-12 d-flex flex-row justify-content-center align-items-center gap-4 mt-5">
            <button type="submit" class="btn-default btn-pink btn-wide">
                Save Changes
            </button>
        </div>
    </form>
@endsection
