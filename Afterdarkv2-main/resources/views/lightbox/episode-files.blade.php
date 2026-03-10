<div class="lightbox new-designed-popup lightbox-episode limited-height-popup hide">
    @yield('lightbox-close')
    <div class="lbcontainer">
        <div class="lightbox-content">
            <div class="lightbox-content-block new-design-popup">
                <h2 class="title" data-translate-text="Description">{{ __('Description') }}</h2>
                <div class="">
                    <div class="stepper-wrapper">
                        <div class="stepper-item active">
                            <div class="step-counter"></div>
                            <div class="step-name">Description</div>
                        </div>
                        <div class="stepper-item">
                            <div class="step-counter"></div>
                            <div class="step-name">Track</div>
                        </div>
                    </div>
                    <div class="error-container"></div>
                    <div class="preloader hide">
                        <img src="{{ asset('svg/Spinner-1s-200px.gif') }}"  alt="">
                    </div>
                    <div class="flex flex-column-center file-form-container">
                        <div class="file-uploader flex-column">
                            <span style="text-align: center; font-size: 18px; font-weight: 500; font-family: Poppins, sans-serif; margin-bottom: 14px">Audio</span>
                            <div class="uploaded-file-info" style="width: 193px; height: 194px">
                                <form
                                        data-template="template-upload"
                                        class="adventureupload parent-file"
                                        style="display: flex; flex-direction: column; align-items: center; height: 100%; width: 100%; justify-content: flex-end; position: relative"
                                        action="/adventure-beat/upload"
                                        method="POST"
                                        enctype="multipart/form-data"
                                >
                                    <img src="{{ asset('svg/upload-audio.svg') }}" style="position: absolute; top: 16px" />
                                    <label for="upload-file-input3" class="upload-audio-preview" style="width: 57px; height: 57px; display: inline-flex; justify-content: center; align-items: center; border: 2px solid #E836C5; border-radius: 30px; z-index: 1; position: absolute; top: calc(50% - 28px); cursor: pointer">
                                        <img src="{{ asset('svg/upload-arrow.svg') }}" />
                                    </label>
                                    <div class="upload-audio-counter hide" style="width: 57px; height: 57px; display: inline-flex; justify-content: center; align-items: center; border: 2px solid #E836C5; border-radius: 30px; z-index: 1; position: absolute; top: calc(50% - 28px);">
                                        <span style="font-size: 15px; font-weight: 600; color: #E836C5;">100%</span>
                                    </div>
                                    <div class="hide upload-audio-actions" style="padding-top: 18px; padding-bottom: 8px; z-index: 1;">
                                        <label for="upload-file-input3" style="border: 2px solid #E836C5; background: #FFFFFF; width: 37px; height: 37px; display: inline-flex; align-items: center; justify-content: center; border-radius: 20px; cursor: pointer">
                                            <img src="{{ asset('svg/pen.svg') }}" />
                                        </label>
                                        <label class="upload-audio-remove" style="border: 2px solid #E836C5; background: #FFFFFF; width: 37px; height: 37px; display: inline-flex; align-items: center; justify-content: center; border-radius: 20px; cursor: pointer; margin-left: 16px">
                                            <img src="{{ asset('svg/trash.svg') }}" />
                                        </label>
                                        <input id="upload-file-input3" type="file" accept="audio/*" name="file" class="hide custom-file-input">
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="parent-uploader-block">
                            <div class="file-error"></div>
                            <div class="file-uploader flex-column">
                                <span style="font-size: 18px; font-weight: 500; font-family: Poppins, sans-serif; margin-bottom: 14px">Photo</span>
                                <div class="uploaded-file-info" style="width: 193px; height: 194px">
                                    <img class="uploaded-artwork-preview" style="border-radius: 14px; width: 104px; height: 83px" src="{{ asset('svg/upload.svg') }}" />
                                    <form
                                            action="/upload-adventure/upload-artwork"
                                            method="POST"
                                            enctype="multipart/form-data"
                                            class="artwork-form"
                                            style="position: relative; height: 100%"
                                    >
                                        <input id="upload-artwork-input" type="file" accept="image/*" name="artwork" class="hide custom-artwork-input">
                                        <input type="hidden" name="id" value="{{ !empty(session()->get('adventure')['adventure_id']) ? session()->get('adventure')['adventure_id'] : null }}" />
                                        <input type="hidden" name="type" value="parent" />
                                        <div class="hide upload-image-actions" style="position: absolute; display: flex; left: -140px; bottom: 12px;">
                                            <label for="upload-artwork-input" style="border: 2px solid #E836C5; background: #FFFFFF; width: 37px; height: 37px; display: inline-flex; align-items: center; justify-content: center; border-radius: 20px; cursor: pointer">
                                                <img src="{{ asset('svg/pen.svg') }}" />
                                            </label>
                                            <label class="upload-image-remove" style="border: 2px solid #E836C5; background: #FFFFFF; width: 37px; height: 37px; display: inline-flex; align-items: center; justify-content: center; border-radius: 20px; cursor: pointer; margin-left: 16px">
                                                <img src="{{ asset('svg/trash.svg') }}" />
                                            </label>
                                        </div>
                                    </form>
                                </div>
                                <label for="upload-artwork-input" class="header__content__user_action_section__upload button-width-auto" style="width: 100%; margin-top: 30px">
                                    Upload Photo
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="new-adventure-popup-buttons flex-right">
                        <a id="to-episode-file-upload" class="popup-button primary-values-setter next">Finish</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>