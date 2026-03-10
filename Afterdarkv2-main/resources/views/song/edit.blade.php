@extends('index')
@section('content')
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <form class="container upload-edit-song-form" method="POST" action="{{ route('frontend.auth.user.artist.manager.song.edit.post') }}" enctype="multipart/form-data">
            <div class="row gy-3">
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-column w-100 gap-4 bg-light rounded-5 p-4 p-lg-4">
                        <div class="profile_page__content__title text-center">Artwork</div>
                        <img src="{{ $song->artwork }}" class="img-fluid" alt="artwork"/>
                        <div class="position-relative">
                            <input class="edit-song-artwork-input" name="artwork" type="file" accept="image/*"
                                   style="position: absolute; opacity: 0; bottom: 0; top: 0; width: 100%; height: 100%; cursor: pointer;">
                            <div class="btn-default btn-pink fw-bolder">{{ __('web.UPLOAD_ARTWORK') }}</div>
                        </div>
                        <div class="fw-light text-secondary text-center">
                            Artwork Dimension <br/>
                            Preferred: 1500x1500px,<br/>
                            Minimum: 500x500px
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-column w-100 gap-4 bg-light rounded-5 p-4 p-lg-5">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="title" data-translate-text="FORM_TITLE">
                                {{ __('web.FORM_TITLE') }}
                            </label>
                            <input class="form-control has-meta" name="title" type="text" autocomplete="off"
                                   value="{{ $song->title }}" required>
                            <span class="fs-12 font-merge color-grey">9 out of 60 Maximum characters allowed</span>
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <div class="input-row w-100">
                                <label class="font-default fs-14 color-text">{{ __('web.TAGS') }}</label>
                                {!! \App\Helpers\Helper::makeTagSelector('tags[]', !old('tags') ? array_column($song->tags->toArray(), 'tag')  : old('tags')) !!}
                            </div>
                            <span class="fs-12 font-merge color-grey">Use only alphanumeric characters</span>
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <div class="input-row new-select2 modified w-100">
                                <label class="font-default fs-14 color-text">Gender</label>
                                {!! \App\Helpers\Helper::makeDropdown( __('web.GENDER_TAGS') , "vocal", $song->vocal ?? true) !!}
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <div class="input-row new-select2 no-default-arrow w-100">
                                <label class="new-label" for="description">
                                    <span data-translate-text="FROM_DEFAULT_GENRE">{{ __('web.FROM_DEFAULT_GENRE') }}</span>
                                </label>
                                <select class="genre-selection" name="genre[]" placeholder="Select genres" multiple
                                        autocomplete="off">
                                    {!! \App\Helpers\Helper::genreSelection(isset(auth()->user()->artist) ? explode(',', auth()->user()->artist->genre) : 0) !!}
                                </select>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-between align-items-center gap-3 flex-wrap">
                            <div class="input-row new-checkbox">
                                <input class="hide custom-checkbox" type="checkbox" name="is_visible" id="visibility"
                                       @if(old('is_visible', $song->is_visible)) checked @endif>
                                <label class="cbx" for="visibility"></label>
                                <label class="lbl" for="visibility">{{ __('web.MAKE_PUBLIC') }}</label>
                            </div>

                            <div class="input-row new-checkbox">
                                <input class="hide custom-checkbox" type="checkbox" name="allow_comments"
                                       id="allow_comments"
                                       @if(old('allow_comments', $song->allow_comments)) checked @endif>
                                <label class="cbx" for="allow_comments"></label>
                                <label class="lbl" for="allow_comments">{{ __('web.ALLOW_COMMENTS') }}</label>
                            </div>
                            <div class="input-row new-checkbox">
                                <input class="hide custom-checkbox" type="checkbox" name="allow_download"
                                       id="allow_download"
                                       @if(old('allow_download', $song->allow_download)) checked @endif>
                                <label class="cbx" for="allow_download"></label>
                                <label class="lbl" for="allow_download">{{ __('web.ALLOW_DOWNLOAD') }}</label>
                            </div>
                            <div class="input-row new-checkbox">
                                <input class="hide custom-checkbox" type="checkbox" name="is_explicit" id="is_explicit"
                                       @if(old('is_explicit', $song->is_explicit)) checked @endif>
                                <label class="cbx" for="is_explicit"></label>
                                <label class="lbl" for="is_explicit">{{ __('web.SONG_EXPLICIT') }}</label>
                            </div>
                            @if (auth()->user()->activeSubscription())
                                <div class="input-row new-checkbox">
                                    <input class="hide custom-checkbox" type="checkbox" name="is_patron" id="is_patron"
                                           @if(old('is_patron', $song->is_patron)) checked @endif>
                                    <label class="cbx" for="is_patron"></label>
                                    <label class="lbl" for="is_patron">{{ __('web.ONLY_FOR_PATRONS') }}</label>
                                </div>
                            @endif
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="title" data-translate-text="DESCRIPTION">
                                {{ __('web.DESCRIPTION') }}
                            </label>
                            <input class="form-control" id="description" name="description"
                                   placeholder="{{ __('web.DESCRIPTION') }}" type="text" autocomplete="off"
                                   value="{{ $song->description }}">
                        </div>
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="liner_notes" data-translate-text="LINER_NOTES">
                                {{ __('web.LINER_NOTES') }}
                            </label>
                            <input class="form-control" id="liner_notes" name="liner_notes" type="text"
                                   placeholder="{{ __('web.LINER_NOTES') }}" autocomplete="off" value="{{ $song->liner_notes }}">
                        </div>
                        <div class="upload-info-footer gap-4">
                            <input name="id" type="hidden" value="{{ $song->id }}">
                            <input name="redirect" type="hidden" value="true">
                            <button class="btn-default btn-pink save" type="submit" data-translate-text="SAVE">
                                {{ __('SAVE') }}
                            </button>
                            {{-- btn btn-secondary --}}
                            <a href="{{ $song->permalink_url }}" class="btn-default btn-outline" data-translate-text="CANCEL">
                                <svg width="19" height="20" viewBox="0 0 19 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.5 3C17.7652 3 18.0196 3.10536 18.2071 3.29289C18.3946 3.48043 18.5 3.73478 18.5 4C18.5 4.26522 18.3946 4.51957 18.2071 4.70711C18.0196 4.89464 17.7652 5 17.5 5H16.5L16.497 5.071L15.564 18.142C15.5281 18.6466 15.3023 19.1188 14.9321 19.4636C14.5619 19.8083 14.0749 20 13.569 20H5.43C4.92414 20 4.43707 19.8083 4.06688 19.4636C3.6967 19.1188 3.47092 18.6466 3.435 18.142L2.502 5.072C2.50048 5.04803 2.49982 5.02402 2.5 5H1.5C1.23478 5 0.98043 4.89464 0.792893 4.70711C0.605357 4.51957 0.5 4.26522 0.5 4C0.5 3.73478 0.605357 3.48043 0.792893 3.29289C0.98043 3.10536 1.23478 3 1.5 3H17.5ZM11.5 0C11.7652 0 12.0196 0.105357 12.2071 0.292893C12.3946 0.48043 12.5 0.734784 12.5 1C12.5 1.26522 12.3946 1.51957 12.2071 1.70711C12.0196 1.89464 11.7652 2 11.5 2H7.5C7.23478 2 6.98043 1.89464 6.79289 1.70711C6.60536 1.51957 6.5 1.26522 6.5 1C6.5 0.734784 6.60536 0.48043 6.79289 0.292893C6.98043 0.105357 7.23478 0 7.5 0H11.5Z"
                                    />
                                </svg>
                                {{ __('web.CANCEL') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <script>
            $(document).find('.upload-edit-song-form .select2-tags').select2({
                tags: true,
                minimumInputLength: 0
            });
            $(document).find('.upload-edit-song-form .genre-selection, .upload-edit-song-form').select2({
                placeholder: "Select one or multi",
                maximumSelectionLength: 4,
            });
            $(document).find(".genre-selection").val(`{{ $song->genre }}`.split(',')).change();
        </script>
    </div>
@endsection
