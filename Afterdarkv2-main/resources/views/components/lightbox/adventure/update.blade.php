<div class="lightbox lightbox-adventure-update limited-height-popup hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5" data-translate-text="Description">
                {{ __('web.FORM_DESCRIPTION') }}
            </div>
            <div class="d-flex flex-row justify-content-between align-items-end w-100 font-default">
                <div class="stepper-item active">
                    <div class="step-counter"></div>
                    <div class="font-default">Description</div>
                </div>
                <div class="stepper-item">
                    <div class="step-counter"></div>
                    <div class="font-default">Parent track</div>
                </div>
                <div class="stepper-item">
                    <div class="step-counter"></div>
                    <div class="font-default">Roots</div>
                </div>
            </div>
            <div class="error-container"></div>
            <div class="preloader hide">
                <img src="{{ asset('svg/Spinner-1s-200px.gif') }}" alt="">
            </div>
            <form class="container-fluid" action="">
                <div class="row gy-3 gy-lg-4">
                    <div class="col-12">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="adventure-update-title">
                                Title of adventure:
                            </label>
                            <input type="text"
                                   id="adventure-update-title"
                                   placeholder="Title of adventure"
                                   class="profile_page__content__form__item__input"
                            />
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-end align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="adventure-update-number-of-roots">
                                Number of roots:
                            </label>
                            <select class="new-field" name="adventure-update-number-of-roots" id="adventure-update-number-of-roots">
                                @for ($i = 1; $i < 6; $i += 1)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-end align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="adventure-update-tags">
                                {{ __('web.TAGS') }}:
                            </label>
                            <div class="old-control-fixer new-select2" id="adventure-update-tags">
                                {!! makeTagSelector('adventure-update-tags[]', isset($track) && ! old('adventure-update-tags') ? array_column($track->tags->toArray(), 'tag') : old('adventure-update-tags')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1"
                             id="adventure-update-gender">
                            <label class="font-default fs-14 color-text" for="vocal">
                                Gender
                            </label>
                            {!! makeDropdown( __('web.GENDER_TAGS') , "vocal", $track->vocal ?? true) !!}
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1"
                             id="adventure-update-genres">
                            <label class="font-default fs-14 color-text" for="adventure-update-genres-input">
                                Genre
                            </label>
                            <select class="genre-selection" name="adventure-update-genres[]" multiple autocomplete="off"
                                    id="adventure-update-genres-input">
                                {!! genreSelection(isset(auth()->user()->artist) ? explode(',', auth()->user()->artist->genre) : 0) !!}
                                }
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-row justify-content-start align-items-center gap-5">
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" type="checkbox" name="adventure-update-visibility"
                                       id="adventure-update-allow-visibility">
                                <label class="cbx" for="adventure-update-allow-visibility"></label>
                                <label class="lbl" for="adventure-update-allow-visibility">{{ __('web.MAKE_PUBLIC') }}</label>
                            </div>
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" type="checkbox" name="adventure-update-comments"
                                       id="adventure-update-allow-comments">
                                <label class="cbx" for="adventure-update-allow-comments"></label>
                                <label class="lbl" for="adventure-update-allow-comments">{{ __('web.ALLOW_COMMENTS') }}</label>
                            </div>
                            @if(auth()->user() && auth()->user()->activeSubscription())
                                <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                    <input class="hide custom-checkbox" type="checkbox" name="adventure-update-patrons"
                                           id="adventure-update-patrons">
                                    <label class="cbx" for="adventure-update-patrons"></label>
                                    <label class="lbl" for="adventure-update-patrons">{{ __('web.ONLY_FOR_PATRONS') }}</label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            <div class="d-flex flex-row justify-content-end align-items-center mt-3 w-100">
                <a id="to-adventure-update-parent" class="btn-default btn-pink primary-values-setter">Next</a>
            </div>
        </div>
    </div>
</div>