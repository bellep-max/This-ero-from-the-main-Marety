<div class="lightbox lightbox-login hide">
    <div class="lbcontainer">
        <div class="lightbox-header">
            <h2 class="title" data-translate-text="SIGN_IN">{{ __('web.SIGN_IN') }}</h2>
            @yield('lightbox-close')
        </div>
        <div class="lightbox-content">
            <div class="lightbox-content-block">
                @if(config('settings.social_login'))
                    <div class="lb-nav-outer">
                        <div class="lb-nav-container no-center">
                            <div class="row">
                                <div class="col">
                                    <a style="height: unset;" href="{{route('frontend.reddit.login')}}" class="lb-reddit-login btn share-btn third-party reddit w-100 p-2" data-action="social-login" data-service="reddit">
                                        <svg class="icon icon-reddit-plus-white-active" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m21.325 9.308c-.758 0-1.425.319-1.916.816-1.805-1.268-4.239-2.084-6.936-2.171l1.401-6.406 4.461 1.016c0 1.108.89 2.013 1.982 2.013 1.113 0 2.008-.929 2.008-2.038s-.889-2.038-2.007-2.038c-.779 0-1.451.477-1.786 1.129l-4.927-1.108c-.248-.067-.491.113-.557.365l-1.538 7.062c-2.676.113-5.084.928-6.895 2.197-.491-.518-1.184-.837-1.942-.837-2.812 0-3.733 3.829-1.158 5.138-.091.405-.132.837-.132 1.268 0 4.301 4.775 7.786 10.638 7.786 5.888 0 10.663-3.485 10.663-7.786 0-.431-.045-.883-.156-1.289 2.523-1.314 1.594-5.115-1.203-5.117zm-15.724 5.41c0-1.129.89-2.038 2.008-2.038 1.092 0 1.983.903 1.983 2.038 0 1.109-.89 2.013-1.983 2.013-1.113.005-2.008-.904-2.008-2.013zm10.839 4.798c-1.841 1.868-7.036 1.868-8.878 0-.203-.18-.203-.498 0-.703.177-.18.491-.18.668 0 1.406 1.463 6.07 1.488 7.537 0 .177-.18.491-.18.668 0 .207.206.207.524.005.703zm-.041-2.781c-1.092 0-1.982-.903-1.982-2.011 0-1.129.89-2.038 1.982-2.038 1.113 0 2.008.903 2.008 2.038-.005 1.103-.895 2.011-2.008 2.011z"></path></svg>
                                        <span class="text desktop" data-translate-text="SIGN_IN_REDDIT">With Reddit</span>
                                        <span class="text mobile" data-translate-text="REDDIT">web.REDDIT</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="lightbox-error error hide"></div>
                <div id="lightbox-login-form-goes-here">
                    <div class="error hide"></div>
                    <div class="positive hide">
                        <div class="message"></div>
                    </div>
                    <p id="login-msg"></p>
                    <form id="lightbox-login-form" class="vertical" method="post">
                        <div class="row">
                            @if(config('settings.authorization_method', 0) == 0)
                                <div class="control-group col-lg-6 col-12">
                                    <label class="control-label" for="username" data-translate-text="FORM_USERNAME">{{ __('web.FORM_USERNAME') }}</label>
                                    <div class="controls">
                                        <input class="login-text" id="login-username" name="username" type="text" autocapitalize="none" autocorrect="off">
                                    </div>
                                    <a class="open-signup small desktop" data-translate-text="LB_SIGNUP_LOGIN_DONT_HAVE_ACCOUNT_SUB">{{ __('web.LB_SIGNUP_LOGIN_DONT_HAVE_ACCOUNT_SUB') }}</a>
                                </div>
                            @else
                                <div class="control-group col-lg-6 col-12">
                                    <label class="control-label" for="email" data-translate-text="FORM_EMAIL">{{ __('web.FORM_EMAIL') }}</label>
                                    <div class="controls">
                                        <input class="login-text" id="login-email" name="email" type="text">
                                    </div>
                                    <a class="open-signup small desktop" data-translate-text="LB_SIGNUP_LOGIN_DONT_HAVE_ACCOUNT_SUB">{{ __('web.LB_SIGNUP_LOGIN_DONT_HAVE_ACCOUNT_SUB') }}</a>
                                </div>
                            @endif
                            <div class="control-group col-lg-6 col-12">
                                <label class="control-label" for="password" data-translate-text="FORM_PASSWORD">{{ __('web.FORM_PASSWORD') }}</label>
                                <div class="controls">
                                    <input class="login-text" id="login-password" name="password" type="password">
                                </div>
                                <a class="forgot small" data-translate-text="FORM_FORGOT_PASSWORD">{{ __('web.FORM_FORGOT_PASSWORD') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="lightbox-footer">
            <div class="right">
                <a class="btn btn-primary submit" data-translate-text="SIGN_IN">Sign In</a>
            </div>
            <div id="lightbox-footer-left" class="left mobile">
                <a class="btn btn-secondary open-signup" data-translate-text="LB_SIGNUP_LOGIN_DONT_HAVE_ACCOUNT_SUB">{{ __('web.LB_SIGNUP_LOGIN_DONT_HAVE_ACCOUNT_SUB') }}</a>
            </div>
        </div>
    </div>
</div>