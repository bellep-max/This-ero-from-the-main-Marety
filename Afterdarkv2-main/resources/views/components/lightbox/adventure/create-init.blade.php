<div class="lightbox lightbox-adventure limited-height-popup hide">
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
                            <label class="font-default fs-14 color-text" for="adventure-title">
                                Title of adventure:
                            </label>
                            <input type="text"
                                   id="adventure-title"
                                   placeholder="Title of adventure"
                                   class="form-control"
                            />
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-end align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="number-of-roots">
                                Number of roots:
                            </label>
                            <select class="form-select" name="number-of-roots" id="number-of-roots">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-end align-items-start gap-1">
                            <label class="font-default fs-14 color-text" for="select-tags">
                                {{ __('web.TAGS') }}:
                            </label>
                            <div class="old-control-fixer new-select2 w-100" id="select-tags">
                                {!! \App\Helpers\Helper::makeTagSelector('tags[]', isset($track) && ! old('tags') ? array_column($track->tags->toArray(), 'tag')  : old('tags')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1"
                             id="gender-select">
                            <label class="font-default fs-14 color-text" for="vocal">
                                Gender
                            </label>
                            {!! \App\Helpers\Helper::makeDropdown( __('web.GENDER_TAGS') , "vocal", $track->vocal ?? true) !!}
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1"
                             id="genre-select">
                            <label class="font-default fs-14 color-text" for="genre-select-input">
                                Genre
                            </label>
                            <select class="genre-selection" name="genre[]" multiple autocomplete="off"
                                    id="genre-select-input">
                                {!! \App\Helpers\Helper::genreSelection(isset(auth()->user()->artist) ? explode(',', auth()->user()->artist->genre) : 0) !!}
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-row justify-content-start align-items-center gap-5">
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" type="checkbox" name="is_visible"
                                       id="allow-visibility">
                                <label class="cbx" for="allow-visibility"></label>
                                <label class="lbl" for="allow-visibility">{{ __('web.MAKE_PUBLIC') }}</label>
                            </div>
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" type="checkbox" name="comments"
                                       id="allow-comments">
                                <label class="cbx" for="allow-comments"></label>
                                <label class="lbl" for="allow-comments">{{ __('web.ALLOW_COMMENTS') }}</label>
                            </div>
                            @if(auth()->user() && auth()->user()->activeSubscription())
                                <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                    <input class="hide custom-checkbox" type="checkbox" name="patrons"
                                           id="patrons">
                                    <label class="cbx" for="patrons"></label>
                                    <label class="lbl" for="patrons">{{ __('web.ONLY_FOR_PATRONS') }}</label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            <div class="d-flex flex-row justify-content-end align-items-center mt-3 w-100">
                <a id="to-adventure-parent" class="btn-default btn-pink primary-values-setter">Next</a>
            </div>
        </div>
    </div>
</div>