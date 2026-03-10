<div class="accordion-section adventure final final-item rounded-4 p-3 w-100" data-child-number="{{ $childNumber }}" data-final-number="{{ $innerIndex }}">
    <div class="accordion-head d-flex flex-row justify-content-between align-items-center w-100">
        <span class="font-default fw-bolder fs-6">Final {{ $innerIndex + 1 }} Audio</span>
        <svg class="fill-pink" height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/>
        </svg>
    </div>
    <div class="panel p-3 flex-column justify-content-start align-items-center gap-5">
        <div class="d-flex flex-column justify-content-start align-items-center gap-3">
            <div class="file-form-container d-flex flex-column gap-5">
                <div class="parent-uploader-block">
                    <div class="file-error"></div>
                    <img src="{{ asset('svg/cloud.svg') }}" class="img-fluid w-50 image-placeholder" alt="">
                    @php
                        if (isset($child['id'])) {
                            $song = \App\Models\FinalSong::withoutGlobalScopes()->find($child['id']);
                        }
                    @endphp
                    <div class="file-uploader hide">
                        <div class="uploaded-file-info">
                            <label for="upload-artwork-input{{ $childNumber.$innerIndex }}" class="header__content__user_action_section__upload">
                                Upload Artwork
                            </label>
                            <form action="/upload-adventure/upload-artwork"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  class="artwork-form"
                            >
                                <input
                                        id="upload-artwork-input{{ $childNumber.$innerIndex }}"
                                        type="file"
                                        accept="image/*"
                                        name="artwork"
                                        class="hide custom-artwork-input"
                                >
                                <input type="hidden" name="id" value="{{ @$song->id }}" />
                                <input type="hidden" name="type" value="final" />
                                <input type="hidden" name="order" value="{{ $innerIndex }}" />
                                <input type="hidden" name="child_order" value="{{ $childNumber }}" />
                            </form>
                        </div>
                        <div class="uploaded-title-progress">
                            <div class="uploaded-file-title"></div>

                            <div class="progress-container hide">
                                <div class="progress-bar">
                                    <img src="{{ asset('svg/progress-arrow.svg') }}" alt="">
                                    <progress value="0" min="0" max="100" style="visibility:hidden;height:0;width:0;"></progress>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="track-uploading-buttons">
                    <form class="upload_page__contant__main-img adventureupload"
                          id="adventure-final-accordions"
                          data-template="template-upload"
                          action="/adventure-beat/upload-child"
                          method="POST"
                          enctype="multipart/form-data"
                    >
                        <label class="cursor-pointer text-center w-100" for="upload-file-input{{ $childNumber.$innerIndex }}">
                            <span class="fw-bolder link">Upload First Part</span>
                        </label>
                        <input id="upload-file-input{{ $childNumber.$innerIndex }}"
                               type="file"
                               accept="audio/*"
                               name="file"
                               class="hide custom-file-input"
                        >
                        <input type="hidden" name="file_child_value" value="{{ $childNumber }}">
                        <input type="hidden" name="file_final_value" value="{{ $innerIndex }}">
                        {{-- <div class="progress-container hide">
                            <div class="progress-bar">
                                <img src="/svg/progress-arrow.svg" alt="">
                                <progress value="0" min="0" max="100" style="visibility:hidden;height:0;width:0;"></progress>
                            </div>
                        </div> --}}
                    </form>
                </div>
            </div>

        </div>
        <form action="" class="final-text-form d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                <label class="font-default fs-14 color-text">
                    Title of adventure
                </label>
                <input class="form-control final-field" type="text" data-id="adventure-title" placeholder="Title of adventure" value="{{ $final['title'] }}"/>
            </div>
            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                <label class="font-default fs-14 color-text">
                    Short Description
                </label>
                <input class="form-control final-field" type="text" data-id="adventure-description" placeholder="Description of adventure" value="{{ $final['short_description'] }}" maxlength="100">
            </div>
        </form>
    </div>
</div>