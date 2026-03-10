<div class="lightbox lightbox-import-postcast-rss hide">
    <div class="lbcontainer">
        <div id="create-playlist">
            <form id="import-podcast-rss-form" class="ajax-form" method="post"
                  action="{{ route('frontend.auth.user.artist.manager.podcasts.import') }}"
                  enctype="multipart/form-data" novalidate>
                <div class="lightbox-header">
                    <h2 class="title"
                        data-translate-text="ADD_PODCAST_FEED_URL">{{ __('web.ADD_PODCAST_FEED_URL') }}</h2>
                    @yield('lightbox-close')
                </div>
                <div class="lightbox-content">
                    <div class="lightbox-content-block">
                        <div class="error hide">
                            <div class="message"></div>
                        </div>
                        <div class="alert alert-info hide">This will take a while, please hold on...</div>
                        <div class="control field">
                            <label for="title">
                                <span data-translate-text="ENTER_PODCAST_FEED_URL">{{ __('web.ENTER_PODCAST_FEED_URL') }}</span>
                            </label>
                            <input name="rss_url" class="form-control" type="text" required autocomplete="off">
                        </div>
                        <div class="control field">
                            <label for="description">
                                <span data-translate-text="{{ __('FORM_COUNTRY') }}">{{ __('FORM_COUNTRY') }}</span>
                            </label>
                            {!! \App\Helpers\Helper::makeCountryDropdown('country', 'select2 podcast-import-country-select2', null) !!}
                        </div>
                        <div class="control field podcast-import-language-container d-none">
                            <label for="description">
                                <span data-translate-text="{{ __('FORM_LANGUAGE') }}">{{ __('FORM_LANGUAGE') }}</span>
                            </label>
                            <select class="select2" name="language" placeholder="Select language"></select>
                        </div>
                        <div class="control field">
                            <label for="created_at">
                                <span data-translate-text="FORM_SCHEDULE_PUBLISH">{{ __('FORM_SCHEDULE_PUBLISH') }}</span>
                            </label>
                            <input class="datepicker form-control" name="created_at" type="text" placeholder="Immediately"
                                   autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="lightbox-footer">
                    <div class="right">
                        <button class="btn btn-primary" type="submit"
                                data-translate-text="IMPORT">{{ __('web.IMPORT') }}</button>
                    </div>
                    <div class="left">
                        <div class="row ml-0 mr-0 mt-2">
                            <input class="hide custom-checkbox" id="import-rss-checkbox" type="checkbox"
                                   name="is_visible" checked>
                            <label class="cbx" for="import-rss-checkbox"></label>
                            <label class="lbl" for="import-rss-checkbox"
                                   data-translate-text="MAKE_PUBLIC">{{ __('MAKE_PUBLIC') }}</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>