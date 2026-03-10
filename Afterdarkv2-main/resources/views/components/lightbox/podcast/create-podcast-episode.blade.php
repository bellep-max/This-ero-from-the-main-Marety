<div class="lightbox lightbox-episode limited-height-popup hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5" data-translate-text="Description">
                {{ __('web.FORM_DESCRIPTION') }}
            </div>
            <div class="d-flex flex-row justify-content-between align-items-end w-100 font-default">
                <div class="stepper-item active" data-tab="Description">
                    <div class="step-counter"></div>
                    <div class="font-default">Description</div>
                </div>
                <div class="stepper-item" data-tab="Track">
                    <div class="step-counter"></div>
                    <div class="font-default">Track</div>
                </div>
            </div>
            <div class="error-container"></div>
            <div class="preloader hide">
                <img src="{{ asset('svg/Spinner-1s-200px.gif') }}" alt="">
            </div>
            <div class="stepper-tab-content d-flex flex-column align-items-center w-100" data-name="Description">
                <form class="container-fluid" action="" id="podcast-episode-basic-form">
                    <div class="row gy-3 gy-lg-4">
                        <div class="col-12">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label class="font-default fs-14 color-text" for="title">
                                    {{ __('web.FORM_TITLE') }}
                                </label>
                                <input type="text" id="episode-title" placeholder="Title of episode" class="form-control" name="title"/>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label class="font-default fs-14 color-text" for="description">
                                    {{ __('web.FORM_DESCRIPTION') }}
                                </label>
                                <textarea id="episode-description" placeholder="Description of episode" class="form-control" name="description"></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label class="font-default fs-14 color-text" for="podcast-id">
                                    Podcast
                                </label>
                                <select class="form-select" name="podcast_id" id="podcast-id">
                                    <option selected disabled>SELECT PODCAST FIRST</option>
                                    @foreach(auth()->user()->podcasts as $podcast)
                                        <option value="{{ $podcast->id }}">
                                            {{ $podcast->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label class="font-default fs-14 color-text" for="season">
                                    Season
                                </label>
                                <select class="form-select" name="season" id="episode-season" disabled>
                                    <option selected disabled>SELECT PODCAST FIRST</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                                <label class="font-default fs-14 color-text" for="number">
                                    Episode number
                                </label>
                                <select class="form-select" name="number" id="episode-number" disabled>
                                    <option selected disabled>SELECT SEASON FIRST</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-row justify-content-start align-items-center gap-5 row-gap-2 flex-wrap">
                                <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                    <input class="hide custom-checkbox" type="checkbox" name="is_visible" id="episode-allow-visibility" checked>
                                    <label class="cbx" for="episode-allow-visibility"></label>
                                    <label class="lbl" for="episode-allow-visibility">{{ __('web.MAKE_PUBLIC') }}</label>
                                </div>
                                <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                    <input class="hide custom-checkbox" type="checkbox" name="explicit" id="episode-allow-explicit" checked>
                                    <label class="cbx" for="episode-allow-explicit"></label>
                                    <label class="lbl" for="episode-allow-explicit">{{ __('web.SONG_EXPLICIT') }}</label>
                                </div>
                                <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                    <input class="hide custom-checkbox" type="checkbox" name="allow_comments" id="episode-allow-comments" checked>
                                    <label class="cbx" for="episode-allow-comments"></label>
                                    <label class="lbl" for="episode-allow-comments">{{ __('web.ALLOW_COMMENTS') }}</label>
                                </div>
                                <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                    <input class="hide custom-checkbox" type="checkbox" name="allow_download" id="episode-allow-download" checked>
                                    <label class="cbx" for="episode-allow-download"></label>
                                    <label class="lbl" for="episode-allow-download">{{ __('web.ALLOW_DOWNLOAD') }}</label>
                                </div>
                                @if(auth()->user()->activeSubscription())
                                    <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                        <input class="hide custom-checkbox" type="checkbox" name="patrons" id="episode-patrons" checked>
                                        <label class="cbx" for="episode-patrons"></label>
                                        <label class="lbl" for="episode-patrons">{{ __('web.ONLY_FOR_PATRONS') }}</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                <div class="d-flex flex-row justify-content-end align-items-center mt-3 w-100">
                    <a id="to-episode-file-upload" class="btn-default btn-pink disabled">Next</a>
                </div>
            </div>
            <div class="stepper-tab-content d-flex flex-column align-items-center w-100 hide" data-name="Track">
                <div class="d-flex flex-row justify-content-between align-items-start w-75 file-form-container">
                    <div class="file-uploader d-flex flex-column justify-content-center align-items-center gap-2">
                        <span class="font-default text-center fs-5">Audio</span>
                        <div class="uploaded-file-info">
                            <form data-template="template-upload"
                                  class="adventureupload parent-file d-flex flex-column h-100 w-100 position-relative justify-content-end"
                                  action="{{ route('frontend.auth.episode-upload.track.store')  }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                            >
                                <img src="{{ asset('svg/upload-audio.svg') }}"
                                     class="position-absolute top-50 start-50 translate-middle"
                                     style="width: 104px; height: 83px"/>
                                <label for="podcast-episode-file-input"
                                       class="upload-audio-preview border border-2 rounded-circle z-1 d-flex justify-content-center align-items-center position-absolute top-50 start-50 translate-middle"
                                       style="width: 57px; height: 57px; border-color: #E836C5; cursor: pointer">
                                    <img src="{{ asset('svg/upload-arrow.svg') }}"/>
                                </label>
                                <div class="upload-audio-counter hide border border-2 rounded-circle z-1 d-flex justify-content-center align-items-center position-absolute top-50 start-50 translate-middle"
                                     style="width: 57px; height: 57px; border-color: #E836C5 !important;">
                                    <span class="fs-5 font-default color-pink">100%</span>
                                </div>
                                <div class="hide upload-audio-actions d-flex flex-row justify-content-center align-items-center gap-3 mt-auto mb-2 z-1">
                                    <label for="podcast-episode-file-input"
                                           class="btn-default btn-outline rounded-circle p-2">
                                        <img src="{{ asset('svg/pen.svg') }}"/>
                                    </label>
                                    <label class="upload-audio-remove btn-default btn-outline rounded-circle p-2">
                                        <img src="{{ asset('svg/trash.svg') }}"/>
                                    </label>
                                    <input id="podcast-episode-file-input" type="file" accept="audio/*" name="file"
                                           class="hide">
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="parent-uploader-block">
                        <div class="file-error"></div>
                        <div class="file-uploader d-flex flex-column justify-content-center align-items-center gap-2">
                            <span class="font-default text-center fs-5">Photo</span>
                            <div class="uploaded-file-info">
                                {{--                                    <img class="" style="width: 104px; height: 83px" src="{{ asset('svg/upload.svg') }}" />--}}
                                <form action="{{ route('frontend.auth.episode-upload.artwork.store') }}"
                                      method="POST"
                                      enctype="multipart/form-data"
                                      class="artwork-form d-flex flex-column h-100 w-100 position-relative justify-content-end"
                                >
                                    <img src="{{ asset('svg/upload.svg') }}"
                                         class="uploaded-artwork-preview position-absolute top-50 start-50 translate-middle"/>
                                    <input id="upload-episode-artwork-input" type="file" accept="image/*" name="artwork"
                                           class="hide" disabled>
                                    <div class="hide upload-image-actions d-flex flex-row justify-content-center align-items-center gap-3 mt-auto mb-2 z-1">
                                        <label for="upload-artwork-input"
                                               class="btn-default btn-outline rounded-circle p-2">>
                                            <img src="{{ asset('svg/pen.svg') }}"/>
                                        </label>
                                        <label class="upload-image-remove btn-default btn-outline rounded-circle p-2">
                                            <img src="{{ asset('svg/trash.svg') }}"/>
                                        </label>
                                    </div>
                                </form>
                            </div>
                            <label for="upload-episode-artwork-input"
                                   class="btn-default btn-pink w-100 disabled">
                                Upload Photo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-row justify-content-end align-items-center mt-3 w-100">
                    <a id="to-episode-finish" class="btn-default btn-pink disabled">Next</a>
                </div>
            </div>
        </div>
    </div>
</div>