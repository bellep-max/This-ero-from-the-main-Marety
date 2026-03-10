<div class="lightbox lightbox-playlist lightbox-createPlaylist hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5">
                Create New Playlist
            </div>
            <form id="create-playlist-form" class="container-fluid" method="POST" action="{{ route('frontend.auth.user.create.playlist') }}" enctype="multipart/form-data" novalidate>
                <div class="row gy-3 gy-lg-4">
                    <div class="col-12 col-md-5">
                        <div class="d-flex flex-column justify-content-start align-items-center gap-4">
                            <img class="img-fluid rounded-4 border-pink" id="playlist-image-preview" src="{{ asset('common/default/playlist.png') }}">
                            <label for="upload-playlist-pic" class="btn-default btn-pink fw-bolder">
                                {{ __('web.UPLOAD_ARTWORK') }}
                            </label>
                            <input id="upload-playlist-pic" class="input-playlist-artwork hide" name="artwork" accept="image/*" title="" type="file">
                        </div>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="d-flex flex-column justify-content-start align-items-center gap-4">
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <span class="font-default fs-14 color-text" data-translate-text="NAME">{{ __('web.NAME') }}</span>
                                <input class="form-control" type="text" name="playlistName" maxlength="175" required="">
                            </div>
                            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                                <label class="font-default fs-14 color-text" data-translate-text="GENRES">
                                    {{ __('web.GENRES') }}
                                </label>
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
                                <input class="hide custom-checkbox" id="create-playlist-checkbox" type="checkbox" name="is_visible" checked="checked">
                                <label class="cbx" for="create-playlist-checkbox"></label>
                                <label class="lbl" for="create-playlist-checkbox">{{ __('web.MAKE_PUBLIC') }}</label>
                            </div>
                            <button class="btn-default btn-pink" type="submit" data-translate-text="POPUP_PLAYLIST_METADATA_CREATE">
                                {{ __('web.POPUP_PLAYLIST_METADATA_CREATE') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>