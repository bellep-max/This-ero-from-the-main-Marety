<div class="lightbox lightbox-edit-show hide">
    <div class="lbcontainer">
        <form id="edit-podcast-show-form" class="ajax-form" method="post"
              action="{{ route('frontend.auth.user.artist.manager.podcasts.edit') }}" enctype="multipart/form-data"
              novalidate>
            <div class="lightbox-header">
                <h2 class="title"></h2>
                @yield('lightbox-close')
            </div>
            <div class="lightbox-content">
                <div class="lightbox-content-block">
                    <div class="error hide">
                        <div class="message"></div>
                    </div>
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
                                <input name="title" type="text" class="form-control" required>
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
                                <select class="select2 podcast-edit-country-select2" name="country"
                                        placeholder="Select country"></select>
                            </div>
                            <div class="control field podcast-edit-language-container d-none">
                                <label for="description">
                                    <span data-translate-text="{{ __('FORM_LANGUAGE') }}">{{ __('FORM_LANGUAGE') }}</span>
                                </label>
                                <select class="select2 podcast-edit-language-select2" name="language"
                                        placeholder="Select language"></select>
                            </div>
                            <div class="control field">
                                <label for="created_at">
                                    <span data-translate-text="FORM_SCHEDULE_PUBLISH">{{ __('FORM_SCHEDULE_PUBLISH') }}</span>
                                </label>
                                <input class="datepicker form-control" name="created_at" type="text" placeholder="Immediately"
                                       autocomplete="off">
                            </div>
                            <div class="control field row mb-0">
                                <div class="col-6">
                                    <div class="row ml-0 mr-0 mt-2 visibility-check-box">
                                        <input class="hide custom-checkbox" type="checkbox" name="is_visible"
                                               id="edit-podcast-visibility">
                                        <label class="cbx" for="edit-podcast-visibility"></label>
                                        <label class="lbl"
                                               for="edit-podcast-visibility">{{ __('web.MAKE_PUBLIC') }}</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row ml-0 mr-0 mt-2 comments-check-box">
                                        <input class="hide custom-checkbox" type="checkbox" name="allow_comments"
                                               id="edit-podcast-comments">
                                        <label class="cbx" for="edit-podcast-comments"></label>
                                        <label class="lbl"
                                               for="edit-podcast-comments">{{ __('web.ALLOW_COMMENTS') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="control field row mb-0">
                                <div class="col-6">
                                    <div class="row ml-0 mr-0 mt-2 notification-check-box">
                                        <input class="hide custom-checkbox" type="checkbox" name="allow_download"
                                               id="edit-podcast-downloadable">
                                        <label class="cbx" for="edit-podcast-downloadable"></label>
                                        <label class="lbl"
                                               for="edit-podcast-downloadable">{{ __('web.ALLOW_DOWNLOAD') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="control field row mb-0">
                                <div class="col-12">
                                    <div class="row ml-0 mr-0 mt-2 notification-check-box">
                                        <input class="hide custom-checkbox" type="checkbox" name="explicit"
                                               id="edit-podcast-explicit">
                                        <label class="cbx" for="edit-podcast-explicit"></label>
                                        <label class="lbl" for="edit-podcast-explicit"
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
                    <input name="id" type="hidden">
                    <button class="btn btn-primary" type="submit"
                            data-translate-text="SAVE">{{ __('web.SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>