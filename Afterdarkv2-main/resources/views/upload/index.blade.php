@extends('index')
@section('content')
<div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
    <div class="container">
        <div class="row">
            <div class="col text-start block-title color-light">
                Upload Audio
            </div>
        </div>
        <div class="row mt-3">
            <div class="col d-flex flex-column justify-content-center align-items-center">
                <div class="container d-flex flex-column align-items-center justify-content-center gap-4 bg-light rounded-5 p-3 p-lg-5 h-100">
                    @if ($hasUploadAccess)
                        <img src="{{ asset('svg/upload.svg') }}" alt="">
                        <div class="text-start font-default">
                            <p>Recommended format: MP3 @128kbs or better</p>
                            <p>Max size: 92.97MB</p>
                            <p>Time allowed: 01:40:00</p>
                            <p>File formats allowed: MP3, M4A, WAV, AIFF, FLAC, AAC, OGG, MP2, MP4 and WMA</p>
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3">
                            <form class="btn-default btn-pink fw-bolder"
                                  id="fileupload"
                                  data-template="template-upload"
                                  action="{{ route('frontend.auth.upload.beat.post') }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                            >
                                <label for="upload-file-input">
                                    Upload Track
                                </label>
                                <input id="upload-file-input" type="file" accept="audio/*" name="file" class="hide">
                            </form>
                            <a class="btn-default btn-pink fw-bolder" id="adventure-upload">
                                Upload Adventure
                            </a>
                            <a class="btn-default btn-pink fw-bolder" id="episode-upload" data-exists="{{ auth()->user()->podcasts()->exists() ? 1 : 0 }}">
                                Upload Podcast Episode
                            </a>
                        </div>
                    @else
                        <div class="text-center">
                            <p class="font-default color-text">You can only upload up to 3 tracks per week.</p>
                            <p class="font-default color-text">Upgrade your account to have unlimited number of uploads per week/day</p>
                        </div>
                        <div class="text-center fs-5 font-default fw-bolder">{{ $sitePlan->name }}</div>
                        @include('frontend.components.circled-text', ['title' => "$".$sitePlan->price, 'description' => "/".__('web.POPULAR_MONTH')])
                        <a href="{{ route('frontend.settings.subscription') }}" class="btn-default btn-pink fw-bolder">
                            {{ __('web.BUY_NOW') }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="uploaded-files"></div>
        </div>
    </div>
</div>
@include('commons.upload-item', ['genres' => $allowGenres])
@endsection
