<div class="lightbox lightbox-podcast hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5">
                Create New Podcast
            </div>
            <form id="create-podcast-form" class="container-fluid" method="POST" action="{{ route('frontend.auth.user.create.podcast') }}" enctype="multipart/form-data" novalidate>
                <div class="row gy-3 gy-lg-4">
                    <div class="col-12 col-md-5">
                        <div class="d-flex flex-column justify-content-start align-items-center gap-4">
                            <img class="img-fluid rounded-4 border-pink" id="podcast-image-preview" src="{{ asset('common/default/podcast.png') }}">
                            <label for="upload-podcast-pic" class="btn-default btn-pink fw-bolder">
                                {{ __('web.UPLOAD_ARTWORK') }}
                            </label>
                            <input id="upload-podcast-pic" class="input-podcast-artwork hide" name="artwork" accept="image/*" title="" type="file">
                        </div>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="d-flex flex-column justify-content-start align-items-center gap-4">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <span class="font-default fs-14 color-text">{{ __('web.FORM_TITLE') }}</span>
                                <input class="form-control" type="text" name="title" maxlength="175" required="">
                            </div>
{{--                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">--}}
{{--                                <span class="font-default fs-14 color-text">{{ __('web.FORM_RSS_FEED') }}</span>--}}
{{--                                <input class="form-control" type="url" name="rss_feed_url">--}}
{{--                            </div>--}}
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100 input-row new-select2">
                                <label for="country_code" class="font-default fs-14 color-text" data-translate-text="COUNTRY">
                                    {{ __('web.FORM_COUNTRY') }}
                                </label>
                                {!! BackendService::makeCountryDropdown('country_id', 'span3 select2', auth()->user()->country ?? '') !!}
                            </div>
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <label for="description" class="font-default fs-14 color-text" data-translate-text="DESCRIPTION">
                                    {{ __('web.FORM_DESCRIPTION') }}
                                </label>
                                <textarea type="text" name="description" class="form-control" maxlength="180"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-row justify-content-center align-items-center gap-3 flex-wrap">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" id="podcast-visibility" type="checkbox" name="is_visible" checked="checked">
                                <label class="cbx" for="podcast-visibility"></label>
                                <label class="lbl" for="podcast-visibility">{{ __('web.MAKE_PUBLIC') }}</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" id="podcast-explicity" type="checkbox" name="explicit" checked="checked">
                                <label class="cbx" for="podcast-explicity"></label>
                                <label class="lbl" for="podcast-explicity">{{ __('web.SONG_EXPLICIT') }}</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" id="podcast-comments" type="checkbox" name="allow_comments" checked="checked">
                                <label class="cbx" for="podcast-comments"></label>
                                <label class="lbl" for="podcast-comments">{{ __('web.ALLOW_COMMENTS') }}</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" id="podcast-download" type="checkbox" name="allow_download" checked="checked">
                                <label class="cbx" for="podcast-download"></label>
                                <label class="lbl" for="podcast-download">{{ __('web.ALLOW_DOWNLOAD') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="d-flex flex-row justify-content-center justify-content-md-end align-items-center mt-3 w-100">
                <div class="btn-default btn-pink" id="podcast-store-submit" data-translate-text="POPUP_PODCAST_METADATA_CREATE">
                    {{ __('web.POPUP_PODCAST_METADATA_CREATE') }}
                </div>
            </div>
        </div>
    </div>
</div>
