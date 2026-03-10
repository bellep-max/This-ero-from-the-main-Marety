<div class="lightbox lightbox-edit-song hide">
    <form id="edit-song-form" class="ajax-form" method="post"
          action="{{ route('frontend.auth.user.artist.manager.song.edit.post') }}" enctype="multipart/form-data"
          novalidate>
        <div class="lbcontainer">
            <div id="upload-song">
                <div class="lightbox-header">
                    <h2 class="title"></h2>
                    @yield('lightbox-close')
                </div>
                <div class="lightbox-content">
                    <div class="lightbox-content-block">
                        <div class="error hide">
                            <div class="message"></div>
                        </div>

                        <div class="img-container">
                            <img class="img"/>
                            <div class="edit-artwork edit-song-artwork" data-type="song" data-id="900">
                                <span>Edit</span>
                                <input class="edit-artwork-input" name="artwork" type="file" accept="image/*">
                            </div>
                        </div>
                        <div class="song-info-container">
                            <div class="control field">
                                <label for="name">
                                    <span data-translate-text="NAME">Name:</span>
                                </label>
                                <input name="title" type="text" required>
                            </div>
                            <div class="control field">
                                <label for="description">
                                    <span data-translate-text="GENRES">Genres:</span>
                                </label>
                                <select class="select2" name="genre[]" multiple>
                                </select>
                            </div>
                            <div class="control field mb-0">
                                <div class="row ml-0 mr-0 mt-2 explicit-check-box">
                                    <input class="hide custom-checkbox" type="checkbox" name="is_explicit"
                                           id="edit-song-explicit">
                                    <label class="cbx" for="edit-song-explicit"></label>
                                    <label class="lbl"
                                           for="edit-song-explicit">{{ __('web.SONG_EXPLICIT') }}</label>
                                </div>
                            </div>
                            <div class="control field mb-0">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2 visibility-check-box">
                                            <input class="hide custom-checkbox" type="checkbox" name="is_visible"
                                                   id="edit-song-visibility">
                                            <label class="cbx" for="edit-song-visibility"></label>
                                            <label class="lbl"
                                                   for="edit-song-visibility">{{ __('web.MAKE_PUBLIC') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2 comments-check-box">
                                            <input class="hide custom-checkbox" type="checkbox" name="comments"
                                                   id="edit-song-comments">
                                            <label class="cbx" for="edit-song-comments"></label>
                                            <label class="lbl"
                                                   for="edit-song-comments">{{ __('web.ALLOW_COMMENTS') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control field">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2 downloadable-check-box">
                                            <input class="hide custom-checkbox" type="checkbox" name="downloadable"
                                                   id="edit-song-downloadable">
                                            <label class="cbx" for="edit-song-downloadable"></label>
                                            <label class="lbl"
                                                   for="edit-song-downloadable">{{ __('web.ALLOW_DOWNLOAD') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2 selling-check-box" data-toggle="collapse"
                                             href="#edit-song-collapse-id" role="button" aria-expanded="false"
                                             aria-controls="edit-song-collapse-id">
                                            <input class="hide custom-checkbox" type="checkbox" name="selling"
                                                   id="edit-song-selling">
                                            <label class="cbx" for="edit-song-selling"></label>
                                            <label class="lbl"
                                                   for="edit-song-selling">{{ __('web.SELL_THIS_SONG') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control field collapse" id="edit-song-collapse-id">
                                <label for="created_at">
                                    <span data-translate-text="FORM_PRICE">{{ __('web.FORM_PRICE') }} </span>
                                </label>
                                <input name="price" type="number" step="1"
                                       min="{{ \App\Models\Group::getValue('monetization_song_min_price') }}"
                                       max="{{ \App\Models\Group::getValue('monetization_song_max_price') }}"
                                       placeholder="{{ __('web.SELL_THIS_SONG_TIP') }}" autocomplete="off">
                            </div>
                        </div>
                        <input name="id" type="hidden">
                        <input name="type" value="song" type="hidden">
                    </div>
                </div>
                <div class="lightbox-footer">
                    <div class="right">
                        <button id="edit-song-save-btn" class="btn btn-primary" type="submit"
                                data-translate-text="SAVE">Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
