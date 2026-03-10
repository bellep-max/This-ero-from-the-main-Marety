<div class="lightbox lightbox-download hide">
    <div class="lightbox-header">
        <h2 class="title" data-translate-text="DOWNLOAD">{{ __('web.DOWNLOAD') }}</h2>
        @yield('lightbox-close')
    </div>
    <div class="lightbox-content">
        <div class="lightbox-content-block">
            <div class="download-tip">
                <h1 data-translate-text="DOWNLOAD_TIP_TITLE">{{ __('web.DOWNLOAD_TIP_TITLE') }}</h1>
                <a class="download-tip-learn" data-translate-text="SUBSCRIBE_NOW">{{ __('web.SUBSCRIBE') }}</a>
            </div>
            <div class="row">
                <div class="col-6">
                    <button class="btn btn-block btn-download standard-download">
                        <span data-translate-text="STANDARD">{{ __('web.STANDARD') }}</span>
                        <span>{{ config('settings.audio_default_bitrate', 128) }} Kbps</span>
                    </button>
                </div>
                <div class="col-6">
                    <button class="btn btn-block btn-download hq-download">
                        <span data-translate-text="HIGH_QUALITY">{{ __('web.HIGH_QUALITY') }}</span>
                        <span>{{ config('settings.audio_hd_bitrate', 320) }} Kbps</span>
                        <label data-translate-text="NOT_AVAILABLE">{{ __('web.NOT_AVAILABLE') }}</label>
                        <span class="download-badge bg-danger"
                              data-translate-text="SUBSCRIPTION">{{ __('web.SUBSCRIPTION') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>