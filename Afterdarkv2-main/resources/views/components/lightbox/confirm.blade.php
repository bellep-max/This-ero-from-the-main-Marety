<div class="lightbox lightbox-confirm hide">
    <div class="lbcontainer">
        <div class="lightbox-header">
            <h2 class="title">{{ __('web.LB_START_RADIO_TITLE') }}</h2>
            @yield('lightbox-close')
        </div>
        <div class="lightbox-content">
            <div class="lightbox-content-block">
                <p></p>
            </div>
        </div>
        <div class="lightbox-footer">
            <div class="left-btns"><a class="btn btn-secondary close"
                                      data-translate-text="CANCEL">{{ __('web.CANCEL') }}</a></div>
            <div class="right-btns"><a class="btn btn-primary submit">{{ __('web.OK') }}</a></div>
        </div>
    </div>
</div>