<script id="template-upload" type="text/x-tmpl">
{% for (var g = 0, file; file = o.files[g]; g++) { %}
    <div class="template-upload upload-info mt-4">
        <form class="container-fluid px-0 d-flex flex-column gap-4 upload-edit-song-form" method="POST" action="{{ route('frontend.auth.user.artist.manager.song.edit.post') }}" enctype="multipart/form-data">
            <div class="d-flex flex-column w-100 gap-4 bg-light rounded-5 p-4 p-lg-4">
                <div class="error hide"></div>
                <div class="upload-info-progress-outer">
                    <div class="upload-info-progress progress"></div>
                </div>
                <div class="upload-info-file">
                    <span>Speed <span class="upload-info-bitrate"></span></span>
                    <span class="upload-info-extra"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-column w-100 gap-4 bg-light rounded-5 p-4 p-lg-4 img-container">
                        <div class="profile_page__content__title text-center">Artwork</div>
                        <img class="img img-fluid" alt="artwork"/>
                        <div class="position-relative">
                            <input class="edit-song-artwork-input" name="artwork" type="file" accept="image/*"
                                   style="position: absolute; opacity: 0; bottom: 0; top: 0; width: 100%; height: 100%; cursor: pointer;">
                            <div class="btn-default btn-pink fw-bolder">{{ __('web.UPLOAD_ARTWORK') }}</div>
                        </div>
                        <div class="fw-light text-secondary text-center font-merge">
                            Artwork Dimension <br/>
                            Preferred: 1500x1500px,<br/>
                            Minimum: 500x500px
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-column w-100 gap-4 bg-light rounded-5 p-4 p-lg-5">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="title">
                                <span data-translate-text="FORM_TITLE">{{ __('web.FORM_TITLE') }}</span>
                            </label>
                            <input class="form-control" name="title" type="text" autocomplete="off" value="{%=file.name%}" required>
                            <span class="fs-12 font-merge color-grey">9 out of 60 Maximum characters allowed</span>
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label class="font-default fs-14 color-text">{{ __('web.TAGS') }}</label>
                            {!! \App\Helpers\Helper::makeTagSelector('tags[]', isset($track) && ! old('tags') ? array_column($track->tags->toArray(), 'tag')  : old('tags')) !!}
                            <span class="fs-12 font-merge color-grey">Use only alphanumeric characters</span>
                        </div>
                        <div class="d-flex flex-row justify-content-between align-items-end gap-3">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 flex-wrap w-100">
                                <label class="font-default fs-14 color-text">Gender</label>
                                {!! \App\Helpers\Helper::makeDropdown( __('web.GENDER_TAGS') , "vocal", $track->vocal ?? true) !!}
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-between align-items-start gap-3">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <label class="font-default fs-14 color-text" for="description">
                                    <span data-translate-text="FROM_DEFAULT_GENRE">{{ __('web.FROM_DEFAULT_GENRE') }}</span>
                                </label>
                                <select class="genre-selection" name="genre[]" placeholder="Select genres" multiple
                                        autocomplete="off">
                                    {!! \App\Helpers\Helper::genreSelection(isset(auth()->user()->artist) ? explode(',', auth()->user()->artist->genre) : 0) !!}}
                                </select>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-between align-items-end gap-3">
                            <div class="d-flex flex-column w-50 justify-content-start align-items-start gap-1">
                                <label class="font-default fs-14 color-text" for="released_at">
                                    <span data-translate-text="Released At">{{ __('Released At') }}</span>
                                </label>
                                <input class="form-control datetimepicker hasDatepicker new-field" name="released_at" type="text" placeholder="{{ __('Released At') }}" autocomplete="off">
                            </div>
                            <div class="d-flex flex-column w-50 justify-content-start align-items-start gap-1">
                                <label class="font-default fs-14 color-text">
                                    <span data-translate-text="FORM_SCHEDULE_PUBLISH">
                                        {{ __('web.FORM_SCHEDULE_PUBLISH') }}
                                    </span>
                                </label>
                                <input class="form-control datetimepicker hasDatepicker new-field" name="created_at" type="text" placeholder="{{ __('web.IMMEDIATELY') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-around align-items-center gap-5 row-gap-2 flex-wrap">
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" type="checkbox" name="is_visible" id="upload-visibility-id-{%=btoa(file.name).replace(/=/g, '')%}" >
                                <label class="cbx" for="upload-visibility-id-{%=btoa(file.name).replace(/=/g, '')%}"></label>
                                <label class="lbl" for="upload-visibility-id-{%=btoa(file.name).replace(/=/g, '')%}">{{ __('web.MAKE_PUBLIC') }}</label>
                            </div>
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" type="checkbox" name="comments" id="upload-comments-id-{%=btoa(file.name).replace(/=/g, '')%}" checked>
                                <label class="cbx" for="upload-comments-id-{%=btoa(file.name).replace(/=/g, '')%}"></label>
                                <label class="lbl" for="upload-comments-id-{%=btoa(file.name).replace(/=/g, '')%}">{{ __('web.ALLOW_COMMENTS') }}</label>
                            </div>
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" type="checkbox" name="notification" id="upload-notification-id-{%=btoa(file.name).replace(/=/g, '')%}" checked>
                                <label class="cbx" for="upload-notification-id-{%=btoa(file.name).replace(/=/g, '')%}"></label>
                                <label class="lbl" for="upload-notification-id-{%=btoa(file.name).replace(/=/g, '')%}">{{ __('web.NOTIFY_MY_FANS') }}</label>
                            </div>
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" type="checkbox" name="downloadable" id="upload-downloadable-id-{%=btoa(file.name).replace(/=/g, '')%}" checked>
                                <label class="cbx" for="upload-downloadable-id-{%=btoa(file.name).replace(/=/g, '')%}"></label>
                                <label class="lbl" for="upload-downloadable-id-{%=btoa(file.name).replace(/=/g, '')%}">{{ __('web.ALLOW_DOWNLOAD') }}</label>
                            </div>
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" type="checkbox" name="explicit" id="upload-explicit-id-{%=btoa(file.name).replace(/=/g, '')%}">
                                <label class="cbx" for="upload-explicit-id-{%=btoa(file.name).replace(/=/g, '')%}"></label>
                                <label class="lbl" for="upload-explicit-id-{%=btoa(file.name).replace(/=/g, '')%}">{{ __('web.SONG_EXPLICIT') }}</label>
                            </div>
                            @if(auth()->user()->activeSubscription())
                                <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                    <input class="hide custom-checkbox" type="checkbox" name="is_patron" id="upload-is_patron-id-{%=btoa(file.name).replace(/=/g, '')%}" >
                                    <label class="cbx" for="upload-is_patron-id-{%=btoa(file.name).replace(/=/g, '')%}"></label>
                                    <label class="lbl" for="upload-is_patron-id-{%=btoa(file.name).replace(/=/g, '')%}">{{ __('web.ONLY_FOR_PATRONS') }}</label>
                                </div>
                            @endif
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="description">
                                <span data-translate-text="DESCRIPTION">{{ __('web.DESCRIPTION') }}</span>
                            </label>
                            <input class="form-control" id="description" name="description" placeholder="{{ __('web.DESCRIPTION') }}" type="text" autocomplete="off" value="{%=file.description%}" >
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="script">
                                <span data-translate-text="SCRIPT">{{ __('web.SCRIPT') }}</span>
                            </label>
                            <input class="script form-control" id="script" name="script" type="text" placeholder="{{ __('web.SCRIPT') }}" autocomplete="off" value="{%=file.script%}" >
                        </div>
                        <div class="song-info-container-overlay">
                            <div class="wrapper no-margin">
                                <div class="wrapper-cell upload">
                                    <div class="text">
                                        <div class="text-line"> </div>
                                        <div class="text-line"></div>
                                    </div>
                                    <div class="text">
                                        <div class="text-line"> </div>
                                        <div class="text-line"></div>
                                    </div>
                                    <div class="text">
                                        <div class="text-line"> </div>
                                        <div class="text-line"></div>
                                    </div>
                                    <div class="text">
                                        <div class="text-line"> </div>
                                        <div class="text-line"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="upload-info-footer d-flex flex-row justify-content-center align-items-center w-100 gap-4">
                            <input name="id" type="hidden">
                            <button class="btn-default btn-pink save" type="submit" data-translate-text="SAVE">
                                {{ __('SAVE') }}
                            </button>
                            <a class="btn-default btn-outline draft" data-translate-text="CANCEL">
                                <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                    <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                </svg>
                                {{ __('web.CANCEL') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
{% } %}
</script>
