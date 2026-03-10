<div class="lightbox lightbox-vipOnlyFeature hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5" data-translate-text="LB_VIP_ONLY_TITLE">
                {{ __('web.LB_VIP_ONLY_TITLE') }}
            </div>
            <div class="my-auto text-center">
                <h3 class="font-default" data-translate-text="LB_VIP_ONLY_SUBTITLE">{{ __('web.LB_VIP_ONLY_SUBTITLE') }}</h3>
                <p class="font-default" data-translate-text="LB_VIP_ONLY_MSG">{{ __('web.LB_VIP_ONLY_MSG') }}</p>
            </div>
        </div>
        <div class="d-flex flex-row justify-content-end align-items-center mt-3 w-100 gap-4">
            @guest
                <a class="btn-default btn-outline login" data-translate-text="LOGIN">
                    {{ __('web.LOGIN') }}
                </a>
            @endguest
            <a class="btn-default btn-pink" href="{{ route('frontend.settings.subscription') }}" data-translate-text="SUBSCRIBE">
                {{ __('web.SUBSCRIBE') }}
            </a>
        </div>
    </div>
</div>