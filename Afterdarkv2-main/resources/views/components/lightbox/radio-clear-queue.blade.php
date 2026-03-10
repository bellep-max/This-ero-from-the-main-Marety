<div class="lightbox lightbox-radioClearQueue hide">
    <div class="lbcontainer">
        <form class="generic radioClearQueue">
            <div class="lightbox-header">
                <h2 class="title"
                    data-translate-text="LB_START_RADIO_TITLE">{{ __('web.LB_START_RADIO_TITLE') }}</h2>
                @yield('lightbox-close')

            </div>
            <div class="lightbox-content">
                <div class="lightbox-content-block">
                    <p data-translate-text="LB_START_RADIO_MESSAGE">{{ __('web.LB_START_RADIO_MESSAGE') }}</p>
                </div>
            </div>
            <div class="lightbox-footer">
                <div class="left-btns">
                    <a class="btn btn-secondary close" data-translate-text="CANCEL">
                        {{ __('web.CANCEL') }}
                    </a>
                </div>
                <div class="right-btns">
                    <a class="btn btn-primary submit" data-translate-text="LB_START_RADIO_TITLE">
                        {{ __('web.LB_START_RADIO_TITLE') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>