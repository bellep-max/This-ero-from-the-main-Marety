<div class="lightbox lightbox-payments hide">
    <div class="lbcontainer">
        <div class="lightbox-header">
            <ul class="lightbox-nav lightbox-nav-container">
                <li class="lightbox-tab active" data-translate-text="CREDIT_CARD" rel="creditcard">Credit Card</li>
                <li class="lightbox-tab" data-translate-text="PAYPAL" rel="paypal">PayPal</li>
            </ul>
            @yield('lightbox-close')
        </div>
        <div class="lightbox-content">
            <div class="lightbox-error error hide"></div>
            <div class="lightbox-content-block">
                <div class="lightbox-trial alert alert-info hide">You will not be charged before the end of your
                    trial period which ends on the <span></span>. No commitments. You can cancel at any time.
                </div>
                <div id="confirm-container" class="control-group">
                    <div class="header">
                        <p class="description"
                           data-translate-text="DESCRIPTION">{{ __('web.FORM_DESCRIPTION') }}</p>
                        <p class="price" data-translate-text="AMOUNT">{{ __('web.FORM_AMOUNT') }}</p>
                    </div>
                    <div class="product">
                        <p class="description"></p>
                        <p class="price">0</p>
                    </div>
                    <div class="total">
                        <p class="description">{{ __('web.FORM_TOTAL') }}</p>
                        <p class="price"></p>
                    </div>
                </div>
                <p class="security-reassurance text-center">Your billing information will be submitted securely over
                    HTTPS.</p>
                <p class="text-secondary small mt-5">By processing the payment, you agree to immediately access the
                    service and to waive any right of withdrawal. You may terminate your subscription at any time by
                    going to "Settings" in your account. The termination will be applied at the end of the current
                    subscription period.</p>
                <div id="credit-card-container" class="control-group">
                    <form id="payment-form" method="post">
                        <div class="form-row">
                            <label for="card-element" class="card-element-helper">
                                Credit or debit card (Power by Stripe)
                            </label>
                            <div id="card-element">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>
                            <!-- Used to display form errors. -->
                            <div id="card-errors" role="alert"></div>
                        </div>
                        <button id="stripe-get-token-submit" class="hide" type="submit"></button>
                    </form>
                    <form id="stripe-form" class="ajax-form stripe-local-form" method="post"
                          action="{{ route('frontend.stripe.subscription') }}">
                        <input class="plan-id" name="planId" type="hidden">
                        <input class="stripeToken" name="stripeToken" type="hidden">
                    </form>
                </div>
                <div id="paypal-container" class="control-group hide">
                    <p data-translate-text="OPEN_PAYPAL_CTA">Click below to open PayPal so you can sign in and
                        continue the transaction.</p>
                    <a class="paypal-open btn btn-secondary btn-paypal mt-3" target="_blank"
                       data-translate-text="OPEN_PAYPAL">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                             width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g id="surface723641">
                                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(8.235294%,39.607843%,75.294118%);fill-opacity:1;"
                                      d="M 9.351562 6.882812 C 9.40625 6.664062 9.59375 6.5 9.828125 6.5 L 16.566406 6.5 C 16.574219 6.5 16.582031 6.496094 16.589844 6.496094 C 16.449219 4.109375 14.445312 3 12.675781 3 L 5.9375 3 C 5.703125 3 5.511719 3.167969 5.460938 3.386719 L 2.515625 16.90625 L 2.519531 16.90625 C 2.515625 16.9375 2.5 16.96875 2.5 17.003906 C 2.5 17.28125 2.726562 17.5 3 17.5 L 7.035156 17.5 Z M 9.351562 6.882812 "/>
                                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(1.176471%,60.784314%,89.803922%);fill-opacity:1;"
                                      d="M 16.589844 6.496094 C 16.617188 6.933594 16.589844 7.410156 16.476562 7.9375 C 15.835938 10.933594 13.519531 12.496094 10.660156 12.496094 C 10.660156 12.496094 8.925781 12.496094 8.503906 12.496094 C 8.242188 12.496094 8.121094 12.648438 8.0625 12.765625 L 7.191406 16.789062 L 7.039062 17.503906 L 6.40625 20.402344 L 6.414062 20.402344 C 6.40625 20.433594 6.394531 20.464844 6.394531 20.5 C 6.394531 20.777344 6.617188 21 6.894531 21 L 10.558594 21 L 10.566406 20.996094 C 10.800781 20.992188 10.988281 20.824219 11.039062 20.601562 L 11.046875 20.59375 L 11.953125 16.386719 C 11.953125 16.386719 12.015625 15.984375 12.4375 15.984375 C 12.859375 15.984375 14.527344 15.984375 14.527344 15.984375 C 17.390625 15.984375 19.726562 14.429688 20.367188 11.433594 C 21.089844 8.054688 18.679688 6.507812 16.589844 6.496094 Z M 16.589844 6.496094 "/>
                                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(15.686275%,20.784314%,57.647059%);fill-opacity:1;"
                                      d="M 9.828125 6.5 C 9.59375 6.5 9.402344 6.664062 9.351562 6.882812 L 8.0625 12.765625 C 8.117188 12.648438 8.242188 12.496094 8.503906 12.496094 C 8.925781 12.496094 10.621094 12.496094 10.621094 12.496094 C 13.480469 12.496094 15.835938 10.9375 16.476562 7.9375 C 16.589844 7.410156 16.617188 6.933594 16.589844 6.496094 C 16.582031 6.496094 16.574219 6.5 16.566406 6.5 Z M 9.828125 6.5 "/>
                            </g>
                        </svg>
                        <span>Open PayPal</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="lightbox-footer">
            <div class="left">
                <a class="btn close" data-translate-text="CANCEL">Cancel</a>
            </div>
            <div class="right">
                <button id="stripe-form-submit-button" class="btn btn-primary">
                    <span>{{ __('web.SUBMIT_PAYMENT') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>