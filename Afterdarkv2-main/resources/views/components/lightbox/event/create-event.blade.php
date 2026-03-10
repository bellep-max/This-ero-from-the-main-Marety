<div class="lightbox lightbox-create-event hide">
    <form id="create-event-form" class="ajax-form" method="post"
          action="{{ route('frontend.auth.user.artist.manager.event.create') }}" enctype="multipart/form-data"
          novalidate>
        <div class="lightbox-header">
            <h2 class="title">{{ __('web.CREATE_EVENT') }}</h2>
            @yield('lightbox-close')
        </div>
        <div class="lightbox-content">
            <div class="lightbox-content-block">
                <div class="error hide">
                    <div class="message"></div>
                </div>
                <div class="control field">
                    <label for="title">
                        <span data-translate-text="FORM_TITLE">{{ __('web.FORM_TITLE') }}</span>
                    </label>
                    <input name="title" type="text" required>
                </div>
                <div class="control field">
                    <label for="location">
                        <span data-translate-text="{{ __('web.FORM_LOCATION') }}">{{ __('web.FORM_LOCATION') }}</span>
                    </label>
                    <input name="location" type="text" required>
                </div>
                <div class="control field">
                    <label for="link">
                        <span data-translate-text="FORM_OUTSIDE_LINK">{{ __('web.FORM_OUTSIDE_LINK') }}</span>
                    </label>
                    <input name="link" type="text">
                </div>
                <div class="control field">
                    <label for="started_at">
                        <span data-translate-text="{{ __('web.FORM_STARTING_AT') }}">{{ __('web.FORM_STARTING_AT') }}</span>
                    </label>
                    <input class="datepicker" name="started_at" type="text"
                           placeholder="{{ __('web.IMMEDIATELY') }}" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="lightbox-footer">
            <div class="right">
                <button class="btn btn-primary" type="submit"
                        data-translate-text="CREATE">{{ __('web.CREATE') }}</button>
            </div>
        </div>
    </form>
</div>