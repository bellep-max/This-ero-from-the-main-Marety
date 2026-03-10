<div class="lightbox lightbox-edit-album hide">
    <div id="create-playlist">
        <form id="edit-album-form" class="ajax-form" method="post"
              action="{{ route('frontend.auth.user.artist.manager.albums.edit') }}"
              enctype="multipart/form-data" novalidate>
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
                            <img class="img" src="{{ asset('common/default/album.png') }}"
                                 data-default-artwork="{{ asset('artworks/defaults/album.png') }}"/>
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
                                    <span data-translate-text="{{ __('FORM_GENRE') }}">{{ __('FORM_GENRE') }}</span>
                                </label>
                                <select class="select2" name="genre[]" placeholder="Select genres" multiple
                                        autocomplete="off">
                                </select>
                            </div>
                            <div class="control field">
                                <label for="type">
                                    <span data-translate-text="FORM_TYPE">{{ __('web.FORM_TYPE') }}</span>
                                </label>
                                {!! \App\Helpers\Helper::makeDropdown(array(
                                    1 => __('web.ALBUM_TYPE_LP'),
                                    2 => __('web.ALBUM_TYPE_SINGLE'),
                                    3 => __('web.ALBUM_TYPE_EP'),
                                    4 => __('web.ALBUM_TYPE_COMPILATION'),
                                    5 => __('web.ALBUM_TYPE_SOUNDTRACK'),
                                    6 => __('web.ALBUM_TYPE_SPOKENWORD'),
                                    7 => __('web.ALBUM_TYPE_INTERVIEW'),
                                    8 => __('web.ALBUM_TYPE_LIVE'),
                                    9 => __('web.ALBUM_TYPE_REMIX'),
                                    10 => __('web.ALBUM_TYPE_OTHER'),
                                ), 'type') !!}
                            </div>
                            <div class="control field">
                                <label for="description">
                                    <span data-translate-text="FORM_DESCRIPTION">{{ __('FORM_DESCRIPTION') }}</span>
                                </label>
                                <textarea type="text" name="description" maxlength="180"></textarea>
                            </div>
                            <div class="control field">
                                <label for="name">
                                    <span data-translate-text="FORM_RELEASED_AT">{{ __('FORM_RELEASED_AT') }}</span>
                                </label>
                                <input class="datepicker" name="released_at" type="text" placeholder="Today"
                                       autocomplete="off">
                            </div>
                            <div class="control field">
                                <label for="created_at">
                                    <span data-translate-text="FORM_SCHEDULE_PUBLISH">{{ __('FORM_SCHEDULE_PUBLISH') }}</span>
                                </label>
                                <input class="datepicker" name="created_at" type="text"
                                       placeholder="Immediately" autocomplete="off">
                            </div>
                            <div class="control field">
                                <label for="name">
                                    <span data-translate-text="FORM_COPYRIGHT">{{ __('FORM_COPYRIGHT') }}</span>
                                </label>
                                <input name="copyright" type="text" placeholder="Option" autocomplete="off">
                            </div>
                            <div class="control field mb-0">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2">
                                            <input class="hide custom-checkbox" type="checkbox"
                                                   name="is_visible" id="edit-album-visibility">
                                            <label class="cbx" for="edit-album-visibility"></label>
                                            <label class="lbl"
                                                   for="edit-album-visibility">{{ __('web.MAKE_PUBLIC') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row ml-0 mr-0 mt-2">
                                            <input class="hide custom-checkbox" type="checkbox" name="comments"
                                                   id="edit-album-comments">
                                            <label class="cbx" for="edit-album-comments"></label>
                                            <label class="lbl"
                                                   for="edit-album-comments">{{ __('web.ALLOW_COMMENTS') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control field">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row ml-0 mr-0 mt-2" data-toggle="collapse"
                                             href="#edit-album-collapse-id" role="button" aria-expanded="false"
                                             aria-controls="edit-album-collapse-id">
                                            <input class="hide custom-checkbox" type="checkbox" name="selling"
                                                   id="edit-album-selling">
                                            <label class="cbx" for="edit-album-selling"></label>
                                            <label class="lbl"
                                                   for="edit-album-selling">{{ __('web.SELL_THIS_ALBUM') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control field collapse" id="edit-album-collapse-id">
                                <label for="created_at">
                                    <span data-translate-text="FORM_PRICE">{{ __('web.FORM_PRICE') }} </span>
                                </label>
                                <input name="price" type="number" step="1"
                                       min="{{ \App\Models\Group::getValue('monetization_album_min_price') }}"
                                       max="{{ \App\Models\Group::getValue('monetization_album_max_price') }}"
                                       placeholder="{{ __('web.SELL_THIS_SONG_TIP') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lightbox-footer">
                <div class="right">
                    <button class="btn btn-primary" type="submit"
                            data-translate-text="SAVE">{{ __('web.SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>