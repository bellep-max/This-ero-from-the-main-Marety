<div class="lightbox lightbox-profile-delete hide">
    @yield('lightbox-close')
    <div id="deletePlaylist">
        <form class="delete-profile" method="post" action="/delete-profile" novalidate>
            <div class="lightbox-header">
                <h2 class="title" data-translate-text="POPUP_DELETE_PROFILE">Delete Account</h2>
            </div>
            <div class="lightbox-content ">
                <div class="lightbox-content-block">
                    <div class="error hide response">
                        <div class="message"></div>
                    </div>
                    <p data-translate-text="POPUP_DELETE_PROFILE_DELETE_WARNING">{{ __('web.POPUP_DELETE_PROFILE_DELETE_WARNING') }}</p>
                    <input name="id" type="hidden">
                    <div class="preloader hide">
                        <img src="{{ asset('svg/Spinner-1s-200px.gif') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="lightbox-footer">
                <div class="right">
                    <button class="custom-white-button" type="submit"
                            data-translate-text="POPUP_DELETE_PROFILE">{{ __('web.DELETE_ACCOUNT') }}</button>
                </div>
                <div class="left"><a class="custom-pink-button close"
                                     data-translate-text="CANCEL">{{ __('web.CANCEL') }}</a></div>
            </div>
        </form>
    </div>
</div>