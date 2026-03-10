<div class="lightbox lightbox-create-show hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5" data-translate-text="Description">
                {{ __('web.FORM_DESCRIPTION') }}
            </div>
            <form id="create-podcast-show" class="container-fluid ajax-form" method="POST"
                  action="{{ route('frontend.auth.user.artist.manager.podcasts.create') }}"
                  enctype="multipart/form-data" novalidate>
                <div class="error hide">
                    <div class="message"></div>
                </div>
                <div class="lightbox-content">
                    <div class="lightbox-content-block">

                        <input name="id" type="hidden">
                        <div class="lightbox-with-artwork-block">
                            <div class="img-container">
                                <img class="img" src="{{ asset('common/default/podcast.png') }}"
                                     data-default-artwork="{{ asset('artworks/defaults/podcast.png') }}"/>
                                <div class="control artwork-select">
                                    <span>Edit</span>
                                    <input class="edit-artwork-input" name="artwork" accept="image/*" title=""
                                           type="file">
                                </div>
                            </div>
                            <div class="input-container">
                                <div class="control field">
                                    <label for="title">
                                        <span data-translate-text="FORM_TITLE">{{ __('FORM_TITLE') }}</span>
                                    </label>
                                    <input name="title" type="text" required>
                                </div>
                                <div class="control field">
                                    <label for="description">
                                        <span data-translate-text="{{ __('FORM_CATEGORY') }}">{{ __('FORM_CATEGORY') }}</span>
                                    </label>
                                    <select class="select2" name="category[]" placeholder="Select categories" multiple
                                            autocomplete="off"></select>
                                </div>
                                <div class="control field">
                                    <label for="description">
                                        <span data-translate-text="{{ __('FORM_COUNTRY') }}">{{ __('FORM_COUNTRY') }}</span>
                                    </label>
                                    <select class="select2 podcast-country-select2" name="country"
                                            placeholder="Select country"></select>
                                </div>
                                <div class="control field podcast-language-container d-none">
                                    <label for="description">
                                        <span data-translate-text="{{ __('FORM_LANGUAGE') }}">{{ __('FORM_LANGUAGE') }}</span>
                                    </label>
                                    <select class="select2 podcast-language-select2" name="language"
                                            placeholder="Select language"></select>
                                </div>
                                <div class="control field">
                                    <label for="created_at">
                                        <span data-translate-text="FORM_SCHEDULE_PUBLISH">{{ __('FORM_SCHEDULE_PUBLISH') }}</span>
                                    </label>
                                    <input class="datepicker" name="created_at" type="text" placeholder="Immediately"
                                           autocomplete="off">
                                </div>
                                <div class="control field row mb-0">
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2 visibility-check-box">
                                            <input class="hide custom-checkbox" type="checkbox" name="is_visible"
                                                   id="create-podcast-visibility" checked>
                                            <label class="cbx" for="create-podcast-visibility"></label>
                                            <label class="lbl"
                                                   for="create-podcast-visibility">{{ __('web.MAKE_PUBLIC') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2 comments-check-box">
                                            <input class="hide custom-checkbox" type="checkbox" name="comments"
                                                   id="create-podcast-comments" checked>
                                            <label class="cbx" for="create-podcast-comments"></label>
                                            <label class="lbl"
                                                   for="create-podcast-comments">{{ __('web.ALLOW_COMMENTS') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="control field row mb-0">
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2 notification-check-box">
                                            <input class="hide custom-checkbox" type="checkbox" name="notification"
                                                   id="create-podcast-notification" checked>
                                            <label class="cbx" for="create-podcast-notification"></label>
                                            <label class="lbl"
                                                   for="create-podcast-notification">{{ __('web.NOTIFY_MY_FANS') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2 notification-check-box">
                                            <input class="hide custom-checkbox" type="checkbox" name="downloadable"
                                                   id="create-podcast-downloadable" checked>
                                            <label class="cbx" for="create-podcast-downloadable"></label>
                                            <label class="lbl"
                                                   for="create-podcast-downloadable">{{ __('web.ALLOW_DOWNLOAD') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="control field row mb-0">
                                    <div class="col-12">
                                        <div class="row ml-0 mr-0 mt-2 notification-check-box">
                                            <input class="hide custom-checkbox" type="checkbox" name="downloadable"
                                                   id="create-podcast-explicit">
                                            <label class="cbx" for="create-podcast-explicit"></label>
                                            <label class="lbl" for="create-podcast-explicit"
                                                   data-translate-text="PODCAST_EXPLICIT">{{ __('web.PODCAST_EXPLICIT') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    </div>
</div>