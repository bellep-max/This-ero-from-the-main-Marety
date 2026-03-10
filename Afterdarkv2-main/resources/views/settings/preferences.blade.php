@extends('frontend.default.settings.layout.layout')

@section('title')
    Settings / Preferences
@endsection

@section('main-section')
    <form class="profile_page__content__form ajax-form row gy-4" id="settings-preferences-form" method="POST" action="{{ route('frontend.auth.user.settings.preferences') }}" novalidate>
        <div id="settings-prefs-container" class="control-group preferences-group">
            <h2 data-translate-text="SETTINGS_LOCAL_SETTINGS_TITLE">Application Settings</h2>
            <ul class="controls">
                <li>
                    <input class="hide custom-checkbox" id="restore_queue" type="checkbox" name="restore_queue" @if(auth()->user()->restore_queue) checked="checked" @endif>
                    <label class="cbx" for="restore_queue"></label>
                    <label class="lbl" for="restore_queue" data-translate-text="SETTINGS_RESTORE_QUEUE">{{ __('web.SETTINGS_RESTORE_QUEUE') }}</label>
                </li>
                <li>
                    <input class="hide custom-checkbox" id="play_pause_fade" type="checkbox" name="play_pause_fade" @if(auth()->user()->play_pause_fade) checked="checked" @endif>
                    <label class="cbx" for="play_pause_fade"></label>
                    <label class="lbl" for="play_pause_fade" data-translate-text="SETTINGS_FADEIN">{{ __('web.SETTINGS_FADEIN') }}</label>
                </li>
                <li>
                    <input class="hide custom-checkbox" id="allow_comments" type="checkbox" name="allow_comments" @if(auth()->user()->allow_comments) checked="checked" @endif>
                    <label class="cbx" for="allow_comments"></label>
                    <label class="lbl" for="allow_comments" data-translate-text="SETTINGS_ALLOW_PROFILE_COMMENT_PROFILE">{{ __('web.SETTINGS_ALLOW_PROFILE_COMMENT_PROFILE') }}</label>
                </li>
            </ul>
        </div>
        <div class="col-12 d-flex flex-row justify-content-center align-items-center gap-4 mt-5">
            <button type="submit" class="btn-default btn-pink btn-wide">
                Save Changes
            </button>
        </div>
    </form>
@endsection
