<div class="lightbox lightbox-contact hide">
    <form id="feedback-lightbox">
        <div class="lightbox-header">
            <h2 class="title">Contact Support</h2>
            @yield('lightbox-close')
        </div>
        <input type="hidden" name="feedbackType" value="">
        <div class="lightbox-content">
            <div class="lightbox-content-block">
                <div class="error hide"></div>
                <p data-translate-text="LB_BILLING_FEEDBACK_MSG">{{ __('web.LB_BILLING_FEEDBACK_MSG') }}</p>
                <div class="field">
                    <label for="email" class="label"
                           data-translate-text="LB_FEEDBACK_EMAIL">{{ __('web.LB_FEEDBACK_EMAIL') }}</label>
                    <input type="text" name="email" value=""></div>
                <div class="field">
                    <label for="feedback" class="label"
                           data-translate-text="LB_BILLING_FEEDBACK_REPORT">{{ __('web.LB_BILLING_FEEDBACK_REPORT') }}</label>
                    <div class="textarea_wrapper clear">
                        <div class="top">
                            <div class="cap"></div>
                        </div>
                        <div class="inner">
                            <div class="cap"><textarea id="feedback" type="text" name="feedback"></textarea>
                            </div>
                        </div>
                        <div class="bottom">
                            <div class="cap"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lightbox-footer">
            <div class="right"><a class="btn btn-primary submit"
                                  data-translate-text="SUBMIT">{{ __('web.SUBMIT') }}</a></div>
            <div class="left"></div>
        </div>
        <button class="hide" type="submit"></button>
    </form>
</div>