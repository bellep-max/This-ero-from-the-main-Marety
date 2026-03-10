<div class="lightbox lightbox-rename hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5" data-translate-text="POPUP_PLAYLIST_META_TITLE">
                {{ __('web.POPUP_PLAYLIST_META_TITLE') }}
            </div>
            <form id="edit-playlist-form" class="container-fluid ajax-form" method="POST"
                  action="{{ route('frontend.auth.user.playlist.edit') }}" novalidate>
                <div class="row gy-3 gy-lg-4">
                    <div class="col-12 col-md-5">
                        <div class="d-flex flex-column justify-content-start align-items-center gap-4">
                            <img class="img-fluid rounded-4 border-pink" id="playlist-image-preview" src="{{ asset('common/default/playlist.png') }}">
                            <label for="update-playlist-pic" class="btn-default btn-pink">
                                {{ __('web.UPLOAD_ARTWORK') }}
                            </label>
                            <input id="update-playlist-pic" class="input-playlist-artwork hide" name="artwork" accept="image/*" title="" type="file">
                        </div>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="d-flex flex-column justify-content-start align-items-center gap-4">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <span class="font-default fs-14 color-text" data-translate-text="NAME">{{ __('web.NAME') }}</span>
                                <input name="id" type="hidden">
                                <input class="form-control" type="text" name="title" maxlength="175" required="">
                            </div>
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <span class="font-default fs-14 color-text" data-translate-text="GENRES">
                                    {{ __('web.GENRES') }}
                                </span>
                                <select class="select2" name="genre[]" multiple autocomplete="off"></select>
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
                        <div class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center gap-5 flex-wrap">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" id="edit-playlist-checkbox" type="checkbox" name="is_visible" checked="checked">
                                <label class="cbx" for="edit-playlist-checkbox"></label>
                                <label class="lbl" for="edit-playlist-checkbox">{{ __('web.MAKE_PUBLIC') }}</label>
                            </div>
                            <button type="submit" class="btn-default btn-pink" id="playlist-update-submit" data-translate-text="SAVE_CHANGES">
                                {{ __('web.SAVE_CHANGES') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>