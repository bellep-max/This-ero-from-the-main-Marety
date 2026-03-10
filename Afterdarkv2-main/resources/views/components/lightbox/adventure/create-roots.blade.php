<div class="lightbox lightbox-adventure-roots limited-height-popup hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5" data-translate-text="Description">{{ __('Roots') }}</div>
            <div class="d-flex flex-row justify-content-between align-items-end w-100 font-default">
                <div class="stepper-item completed">
                    <div class="step-counter"></div>
                    <div class="font-default">Description</div>
                </div>
                <div class="stepper-item completed">
                    <div class="step-counter"></div>
                    <div class="font-default">Parent track</div>
                </div>
                <div class="stepper-item active">
                    <div class="step-counter"></div>
                    <div class="font-default">Roots</div>
                </div>
            </div>
            <div class="error-container"></div>
            <div class="preloader hide">
                <img src="{{ asset('svg/Spinner-1s-200px.gif') }}" alt="">
            </div>
            <div id="adventure-children" class="d-flex flex-column justify-content-start align-items-center w-100 gap-3"></div>
            <div class="d-flex flex-row justify-content-between align-items-center mt-3 w-100">
                <a id="back-to-parent-popup" href="" class="btn-default btn-outline back">
                    <svg height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                        <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                    </svg>
                    Back
                </a>
                <div id="to-adventure-diagram" href="" class="btn-default btn-pink next active">
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