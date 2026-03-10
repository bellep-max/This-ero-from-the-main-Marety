<div class="accordion-section adventure child-item rounded-4 p-3 w-100" data-child-number="{{ $index }}">
    <div class="accordion-head d-flex flex-row justify-content-between align-items-center w-100">
        <span class="font-default fw-bold fs-5">Root {{ $index + 1 }} Audio</span>
        <svg class="fill-pink" height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/>
        </svg>
    </div>
    <div class="panel p-3 d-flex flex-column justify-content-start align-items-center gap-4">
        <div class="d-flex flex-column justify-content-start align-items-center gap-3">
            <div class="file-form-container d-flex flex-column gap-5">
                <div class="parent-uploader-block">
                    <div class="file-error"></div>
                    <img src="{{ asset('svg/cloud.svg') }}" class="img-fluid w-50 image-placeholder" alt="">
                    @php
                        if (isset($child['id'])) {
                            $song = \App\Models\ChildSong::withoutGlobalScopes()->find($child['id']);
                        }
                    @endphp
                    <div class="file-uploader hide">
                        <div class="uploaded-file-info">
                            <label for="upload-artwork-input{{ $index }}" class="header__content__user_action_section__upload">
                                Upload Artwork
                            </label>
                            <form action="{{ route('frontend.auth.adventure-upload.adventure-upload-artwork') }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  class="artwork-form"
                            >
                                <input id="upload-artwork-input{{ $index }}"
                                       type="file"
                                       accept="image/*"
                                       name="artwork"
                                       class="hide custom-artwork-input"
                                >
                                <input type="hidden" name="id" value="{{ @$song->id }}" />
                                <input type="hidden" name="type" value="child" />
                                <input type="hidden" name="order" value="{{ $index }}" />
                            </form>
                        </div>
                        <div class="uploaded-title-progress">
                            <div class="uploaded-file-title">
                                @if (!empty($song->media) && $song->media->count())
                                    {{ $song->media[0]->name }}
                                @endif
                            </div>

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
                    <form class="cursor-pointer adventureupload"
                          id="adventure-children"
                          data-template="template-upload"
                          action="{{ route('frontend.auth.adventure-upload.child.post') }}"
                          method="POST"
                          enctype="multipart/form-data"
                    >
                        <label class="cursor-pointer text-center w-100" for="child-file-input{{ $index }}">
                            <span class="fw-bolder link">Upload First Part</span>
                        </label>
                        <input type="hidden" name="file_child_value" value="{{ $index }}">
                        <input id="child-file-input{{ $index }}"
                               type="file"
                               accept="audio/*"
                               name="file"
                               multiple=""
                               class="hide custom-file-input"
                        >
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
        <form action="" class="child-text-form d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                <label class="font-default fs-14 color-text">
                    Title of adventure
                </label>
                <input class="form-control child-field" type="text" data-id="adventure-title" placeholder="Title of adventure" value="{{ $child['title'] }}">
            </div>
            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                <label class="font-default fs-14 color-text">
                    Short Description
                </label>
                <input class="form-control child-field" type="text" data-id="adventure-description" placeholder="Description of adventure" value="{{ $child['short_description'] }}" maxlength="100">
            </div>
            <div class="d-flex flex-column justify-content-start align-items-start gap-1 w-100">
                <label class="font-default fs-14 color-text">
                    Number of final roots
                </label>
                <select class="form-select final-option" name="" data-id="number-of-roots">
                    @for ($l = 1; $l < 4; $l += 1)
                        <option value="{{ $l }}" @if(count($child['finals']) == $l) selected @endif>{{ $l }}</option>
                    @endfor
                </select>
            </div>
        </form>
        <div class="final-block w-100">
            <div data-id="acc-children">
                @foreach($child['finals'] as $j => $final)
                    @include('frontend.components.adventure.final-accordion', ['childNumber' => $index, 'innerIndex' => $j, 'final' => $final])
                @endforeach
            </div>
{{--            @include('lightbox.adventure-final-accordions', ['childNumber' => $index, 'finals' => ])--}}
        </div>
    </div>
</div>