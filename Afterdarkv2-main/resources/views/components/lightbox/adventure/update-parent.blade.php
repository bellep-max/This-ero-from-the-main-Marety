<div class="lightbox lightbox-adventure-update-parent limited-height-popup hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5" data-translate-text="Description">
                {{ __('Parent track') }}
            </div>
            <div class="d-flex flex-row justify-content-between align-items-end w-100 font-default">
                <div class="stepper-item completed">
                    <div class="step-counter"></div>
                    <div class="font-default">Description</div>
                </div>
                <div class="stepper-item active">
                    <div class="step-counter"></div>
                    <div class="font-default">Parent track</div>
                </div>
                <div class="stepper-item">
                    <div class="step-counter"></div>
                    <div class="font-default">Roots</div>
                </div>
            </div>
            <div class="preloader hide">
                <img src="{{ asset('svg/Spinner-1s-200px.gif') }}" alt="">
            </div>
            <div class="d-flex flex-row justify-content-between align-items-start w-75 file-form-container">
                <div class="file-uploader d-flex flex-column justify-content-center align-items-center gap-2">
                    <span class="font-default text-center fs-5">Audio</span>
                    <div class="uploaded-file-info">
                        <form data-template="template-upload"
                              class="adventureupload parent-file d-flex flex-column h-100 w-100 position-relative justify-content-end"
                              action="{{ route('frontend.auth.adventure-upload.beat.post') }}"
                              method="POST"
                              enctype="multipart/form-data"
                        >
                            <img src="{{ asset('svg/upload-audio.svg') }}"
                                 class="position-absolute top-50 start-50 translate-middle"
                                 style="width: 104px; height: 83px"/>
                            <label for="upload-file-input3"
                                   class="upload-audio-preview border border-2 rounded-circle z-1 d-flex justify-content-center align-items-center position-absolute top-50 start-50 translate-middle"
                                   style="width: 57px; height: 57px; border-color: #E836C5; cursor: pointer">
                                <img src="{{ asset('svg/upload-arrow.svg') }}"/>
                            </label>
                            <div class="upload-audio-counter hide border border-2 rounded-circle z-1 d-flex justify-content-center align-items-center position-absolute top-50 start-50 translate-middle"
                                 style="width: 57px; height: 57px; border-color: #E836C5 !important;">
                                <span class="fs-5 font-default color-pink">100%</span>
                            </div>
                            <div class="hide upload-audio-actions d-flex flex-row justify-content-center align-items-center gap-3 mt-auto mb-2 z-1">
                                <label for="upload-file-input3"
                                       class="btn-default btn-outline rounded-circle p-2">
                                    <img src="{{ asset('svg/pen.svg') }}"/>
                                </label>
                                <label class="upload-audio-remove btn-default btn-outline rounded-circle p-2">
                                    <img src="{{ asset('svg/trash.svg') }}"/>
                                </label>
                                <input id="upload-file-input3" type="file" accept="audio/*" name="file"
                                       class="hide custom-file-input">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="parent-uploader-block">
                    <div class="file-error"></div>
                    <div class="file-uploader d-flex flex-column justify-content-center align-items-center gap-2">
                        <span class="font-default text-center fs-5">Photo</span>
                        <div class="uploaded-file-info">
                            <form action="{{ route('frontend.auth.adventure-upload.adventure-upload-artwork') }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  class="artwork-form d-flex flex-column h-100 w-100 position-relative justify-content-end"
                            >
                                <img src="{{ asset('svg/upload.svg') }}"
                                     class="uploaded-artwork-preview position-absolute top-50 start-50 translate-middle"/>
                                <input id="upload-artwork-input" type="file" accept="image/*" name="artwork"
                                       class="hide custom-artwork-input">
                                <input type="hidden" name="id"
                                       value="{{ !empty(session()->get('adventure')['adventure_id']) ? session()->get('adventure')['adventure_id'] : null }}"/>
                                <input type="hidden" name="type" value="parent"/>
                                <div class="hide upload-image-actions d-flex flex-row justify-content-center align-items-center gap-3 mt-auto mb-2 z-1">
                                    <label for="upload-artwork-input"
                                           class="btn-default btn-outline rounded-circle p-2">
                                        <img src="{{ asset('svg/pen.svg') }}"/>
                                    </label>
                                    <label class="upload-image-remove btn-default btn-outline rounded-circle p-2">
                                        <img src="{{ asset('svg/trash.svg') }}"/>
                                    </label>
                                </div>
                            </form>
                        </div>
                        <label for="upload-artwork-input"
                               class="header__content__user_action_section__upload btn-default btn-pink w-100">
                            Upload Photo
                        </label>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-between align-items-center mt-3 w-100">
                <a id="adventure-update-back-to-root" href="" class="btn-default btn-outline back">
                    <svg height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                        <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                    </svg>
                    Back
                </a>
                <div id="adventure-update-to-root" href="" class="btn-default btn-pink next active">
                    Next
                    <svg height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                        <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>