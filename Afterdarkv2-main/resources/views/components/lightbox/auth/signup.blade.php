<div class="lightbox lightbox-signup hide">
    @yield('lightbox-close')
    <div class="lightbox-content p-md-0">
        <div class="lightbox-content-block row">
            <form id="signup-form" class="ajax-form col col-md-5 d-flex flex-column justify-content-center align-items-center gap-4 ps-md-5" method="POST" action="{{ route('frontend.auth.info.validate') }}">
                <div class="text-center font-default fs-5">
                    Sign Up
                </div>
                <div class="lightbox-error error"></div>

                <div id="signup-stage-singup" class="d-flex flex-column justify-content-center align-items-center gap-3">
                    <div class="col-12">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label for="email" class="font-default fs-14 color-text">
                                Email Address
                            </label>
                            <input class="form-control" type="email" name="email" id="email"/>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label for="name" class="font-default fs-14 color-text">
                                Display Name
                            </label>
                            <input class="form-control" type="text" name="name" id="name"/>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label for="password" class="font-default fs-14 color-text">
                                Password
                            </label>
                            <input class="form-control" type="password" name="password" id="password"/>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-column justify-content-start align-items-start gap-1">
                            <label for="password_confirmation" class="font-default fs-14 color-text">
                                Retype Password
                            </label>
                            <input class="form-control" type="password" name="password_confirmation" id="password_confirmation"/>
                        </div>
                    </div>
                    <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                        <input class="hide custom-checkbox" type="checkbox" name="over_18" id="over_18" value="on">
                        <label class="cbx" for="over_18"></label>
                        <label class="lbl" for="over_18">I'm over 18 and I agree to the Terms of Service</label>
                    </div>

                    <div id="signup-stage-profile" class="lightbox-signup__left-section__signup-stage signup-stage hide">
                        <div class="control-group lightbox-signup__left-section__signup-stage__custom-url custom-url">
                            <label class="lightbox-signup__left-section__signup-stage__control-label control-label" for="username" data-translate-text="LB_SIGNUP_FORM_URL">
                                {{ __('web.LB_SIGNUP_FORM_URL') }}
                            </label>
                            <div class="lightbox-signup__left-section__signup-stage__custom-url__controls controls">
                                <div class="lightbox-signup__left-section__signup-stage__input-prepend input-prepend">
                                    <span class="lightbox-signup__left-section__signup-stage__add-on add-on">{{ route('frontend.homepage') }}/</span>
                                    <input id="signup-username" name="username" class="lightbox-signup__left-section__signup-stage__signup-text signup-text" size="16" value="" type="text" autocomplete="off" maxlength="30" autocapitalize="none" autocorrect="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-center align-items-center w-100">
                        <button type="submit" class="btn-default btn-pink lightbox-signup__left-section__form__sign-up submit">
                            Sign Up
                        </button>
                        <button  class="btn-default btn-pink lightbox-signup__left-section__form__finish hide">
                            I'm Finished
                        </button>
                    </div>
                </div>
            </form>
            <div class="col d-none d-md-flex">
                <img src="{{ asset('svg/signup.svg') }}" class="object-fit-scale" alt="Signup Icon">
            </div>
        </div>
    </div>
</div>
