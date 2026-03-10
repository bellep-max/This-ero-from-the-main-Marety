<!-- ajax cart, version 1.2.0 -->
<div class="cart">
    <div role="menu" class="dropdown-menu dropdown-menu--xs-full">
        <div class="container">
            <a class="icon-close cart__close" data-action="cart-close">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
                <span>Close</span>
            </a>
            <div class="cart__top">Recently added item(s)</div>

            <ul id="cart-items"></ul>
            <div class="cart__bottom">
                <div class="cart__total">Sub-Total: <span class="text-primary">{{ config('settings.currency', 'USD') }} 0.00</span></div>
                <a class="btn btn-primary btn-checkout" href="{{ route('frontend.cart') }}">Checkout</a>
                <!--
                    <a class="btn btn--ys btn-secondary" href="{{ route('frontend.cart') }}">View Cart</a>
                -->
            </div>
        </div>
    </div>
</div>

<!-- mobile search and side menu  and login box-->
<div id="sticky_header" class="mobile">
    <div class="sticky_wrapper">
        <div class="left_menu">
            <a class="sticky-menu-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24.75 24.75" xml:space="preserve"><path d="M0,3.875c0-1.104,0.896-2,2-2h20.75c1.104,0,2,0.896,2,2s-0.896,2-2,2H2C0.896,5.875,0,4.979,0,3.875z M22.75,10.375H2c-1.104,0-2,0.896-2,2c0,1.104,0.896,2,2,2h20.75c1.104,0,2-0.896,2-2C24.75,11.271,23.855,10.375,22.75,10.375z M22.75,18.875H2c-1.104,0-2,0.896-2,2s0.896,2,2,2h20.75c1.104,0,2-0.896,2-2S23.855,18.875,22.75,18.875z"></path></svg>
            </a>
        </div>
        <div class="sticky_search">
            <input type="text" id="sticky_search" placeholder="Search songs, artists, playlists.." autocomplete="off">
            <span class="s_icon">
                    <svg class="icon" viewBox="0 0 13.141 14.398" xmlns="http://www.w3.org/2000/svg"><path data-name="Path 144" d="M12.634 14.3a.4.4 0 0 1-.3-.129l-3.58-3.926-.223.172a5.152 5.152 0 0 1-3.068 1.029A5.546 5.546 0 0 1 .1 5.762 5.513 5.513 0 0 1 5.463.1a5.513 5.513 0 0 1 5.363 5.662 5.889 5.889 0 0 1-1.26 3.646l-.183.236 3.535 3.882a.486.486 0 0 1 0 .643.391.391 0 0 1-.284.131zM5.463 1a4.657 4.657 0 0 0-4.51 4.762 4.643 4.643 0 0 0 4.51 4.761 4.644 4.644 0 0 0 4.51-4.761A4.644 4.644 0 0 0 5.463 1z"></path></svg>
                </span>
        </div>
        <div class="right_menu">
            <a id="static-header-user-menu" class="login_icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
            </a>
        </div>
    </div>
</div>

<div class="contact-sidebar">
    <div id="sidebar-community">
        <div class="sidebar-title">
            <span class="community-link" data-translate-text="COMMUNITY">{{ __('web.COMMUNITY') }}</span>
            <span class="drag-handle"></span>
        </div>
        <div id="chat-disconnected" class="hide">
            <p data-translate-text="MANATEE_DISCONNECTED">{{ __('web.MANATEE_DISCONNECTED') }}</p> <a id="manatee-reconnect" class="btn" data-translate-text="MANATEE_RECONNECT">{{ __('web.MANATEE_RECONNECT') }}</a> </div>
        <div id="friends-failed" class="hide">
            <p data-translate-text="POPUP_ERROR_LOAD_FRIENDS">{{ __('web.POPUP_ERROR_LOAD_FRIENDS') }}</p>
        </div>
        <div id="sidebar-friends" class="friend-list hide">

        </div>
        <div id="sidebar-no-friends" class="sidebar-empty">
            <p class="label" data-translate-text="SIDEBAR_NO_FRIENDS">{{ __('web.SIDEBAR_NO_FRIENDS') }}</p>
            <a class="btn btn-secondary share share-profile" data-translate-text="SHARE_YOUR_PROFILE" data-type="user">{{ __('web.SHARE_YOUR_PROFILE') }}</a>
        </div>
        <div id="sidebar-invite-cta" class="hide">
            <p data-translate-text="INVITE_YOUR_FRIENDS">{{ __('web.INVITE_YOUR_FRIENDS') }}</p>
            <a class="btn invite-friends share" data-type="user" data-translate-text="INVITE_FRIENDS">{{ __('web.INVITE_FRIENDS') }}</a>
        </div>
    </div>

    <div id="sidebar-offline-msg" class="hide"> <span data-translate-text="OFFLINE_MSG">{{ __('web.OFFLINE_MSG') }}</span><br> <a id="sidebar-go-online" data-translate-text="GO_ONLINE">{{ __('web.GO_ONLINE') }}</a> <i id="close-offline-msg" class="icon ex icon-ex-l-gray-flat"></i> </div>
    <div id="sidebar-filter-container" class="hide">
        <form class="search-bar">
            <svg class="icon search" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
            <span data-translate-text="FILTER" class="placeholder">{{ __('web.FILTER') }}</span>
            <input type="text" autocomplete="off" value="" name="q" id="sidebar-filter" class="filter">
            <a class="clear-filter">
                <svg class="icon" height="16px" width="16px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="m437.019531 74.980469c-48.351562-48.351563-112.640625-74.980469-181.019531-74.980469s-132.667969 26.628906-181.019531 74.980469c-48.351563 48.351562-74.980469 112.640625-74.980469 181.019531 0 68.382812 26.628906 132.667969 74.980469 181.019531 48.351562 48.351563 112.640625 74.980469 181.019531 74.980469s132.667969-26.628906 181.019531-74.980469c48.351563-48.351562 74.980469-112.636719 74.980469-181.019531 0-68.378906-26.628906-132.667969-74.980469-181.019531zm-70.292969 256.386719c9.761719 9.765624 9.761719 25.59375 0 35.355468-4.882812 4.882813-11.28125 7.324219-17.679687 7.324219s-12.796875-2.441406-17.679687-7.324219l-75.367188-75.367187-75.367188 75.371093c-4.882812 4.878907-11.28125 7.320313-17.679687 7.320313s-12.796875-2.441406-17.679687-7.320313c-9.761719-9.765624-9.761719-25.59375 0-35.355468l75.371093-75.371094-75.371093-75.367188c-9.761719-9.765624-9.761719-25.59375 0-35.355468 9.765624-9.765625 25.59375-9.765625 35.355468 0l75.371094 75.367187 75.367188-75.367187c9.765624-9.761719 25.59375-9.765625 35.355468 0 9.765625 9.761718 9.765625 25.589844 0 35.355468l-75.367187 75.367188zm0 0"></path></svg>
            </a>
        </form>
        <a id="hide-sidebar-filter">
            <i class="icon ex icon-ex-l-gray-flat"></i>
        </a>
    </div>
    <div id="sidebar-utility">
        <a id="filter-toggle" class="sidebar-util">
            <svg class="icon search" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
        </a>
        <a class="new-playlist sidebar-util create-playlist">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        </a>
        <a id="sidebar-settings" class="sidebar-util">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 20 20"><path fill="none" d="M0 0h20v20H0V0z"></path><path d="M15.95 10.78c.03-.25.05-.51.05-.78s-.02-.53-.06-.78l1.69-1.32c.15-.12.19-.34.1-.51l-1.6-2.77c-.1-.18-.31-.24-.49-.18l-1.99.8c-.42-.32-.86-.58-1.35-.78L12 2.34c-.03-.2-.2-.34-.4-.34H8.4c-.2 0-.36.14-.39.34l-.3 2.12c-.49.2-.94.47-1.35.78l-1.99-.8c-.18-.07-.39 0-.49.18l-1.6 2.77c-.1.18-.06.39.1.51l1.69 1.32c-.04.25-.07.52-.07.78s.02.53.06.78L2.37 12.1c-.15.12-.19.34-.1.51l1.6 2.77c.1.18.31.24.49.18l1.99-.8c.42.32.86.58 1.35.78l.3 2.12c.04.2.2.34.4.34h3.2c.2 0 .37-.14.39-.34l.3-2.12c.49-.2.94-.47 1.35-.78l1.99.8c.18.07.39 0 .49-.18l1.6-2.77c.1-.18.06-.39-.1-.51l-1.67-1.32zM10 13c-1.65 0-3-1.35-3-3s1.35-3 3-3 3 1.35 3 3-1.35 3-3 3z"></path></svg>
        </a>
        <a id="toggle-sidebar" class="sidebar-util last">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 8V4l8 8-8 8v-4H4V8z"/></svg>
        </a>
    </div>
</div>
<div id="user-settings-menu" class="mobile">
    <div class="user-section">
        <div class="inner-us">
            <a class="back-arrow ripple-wrap" data-icon="arrowBack">
                <svg width="26" height="26" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M19.7 11H7.5l5.6-5.6L11.7 4l-8 8 8 8 1.4-1.4L7.5 13h12.2z"></path>
                </svg>
            </a>

            <div class="user-subscription-helper after-login hide">
                <div class="user-subscription-text" data-translate-text="USER_SUBSCRIBED_DESCRIPTION">{{ __('web.USER_SUBSCRIBED_DESCRIPTION') }}</div>
                <a href="{{ route('frontend.settings.subscription') }}" class="user-subscription-button" data-translate-text="SUBSCRIBE">{{ __('web.SUBSCRIBE') }}</a>
            </div>

            <div class="user-auth user-info hide">
                <div class="info-profile">
                    <p class="info-name"></p>
                    <a class="info-link">View Profile</a>
                </div>
                <a class="info-artwork">
                    <img>
                </a>
            </div>
            <div class="setting-wrap separate">
                <ul class="user_options">
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                        <a href="{{ route('frontend.homepage') }}" data-translate-text="HOME">{{ __('web.HOME') }}</a>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2 0 .68.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56zm2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2 0-.68.07-1.35.16-2h4.68c.09.65.16 1.32.16 2 0 .68-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2 0-.68-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z"/></svg>
                        <a class="show-lightbox" data-lightbox="lightbox-locale" data-translate-text="LANGUAGE">{{ __('web.LANGUAGE') }}</a>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                        <a class="show-lightbox" data-lightbox="lightbox-feedback" data-translate-text="FEEDBACK">{{ __('web.FEEDBACK') }}</a>
                    </li>
                    <li>
                        <span class="th-ic _ic" data-icon="theme_icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"><path d="M9 2c-1.05 0-2.05.16-3 .46 4.06 1.27 7 5.06 7 9.54 0 4.48-2.94 8.27-7 9.54.95.3 1.95.46 3 .46 5.52 0 10-4.48 10-10S14.52 2 9 2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                        </span>
                        <span data-translate-text="BLACK_THEME">{{ __('web.DARK_MODE') }}</span>
                        <label class="switch"><input type="checkbox" class="themeSwitch" @if(isset($_COOKIE['darkMode']) &&  $_COOKIE['darkMode'] == 'true') checked @endif><span class="slider round"></span></label>
                    </li>
                </ul>
            </div>
            <div class="user-auth hide user-setting-wrap separate">
                <ul class="user_options">
                    <li>
                        <svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"></path>
                        </svg>
                        <a href="{{ route('frontend.auth.upload.index') }}">
                            <span>{{ __('web.UPLOAD') }}</span>
                        </a>
                    </li>
                    <li>
                        <svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"></path>
                        </svg>
                        <a class="auth-notifications-link">
                            <span>Notifications</span>
                        </a>
                        <span class="header-notification-count">0</span>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 5h-3v5.5c0 1.38-1.12 2.5-2.5 2.5S10 13.88 10 12.5s1.12-2.5 2.5-2.5c.57 0 1.08.19 1.5.51V5h4v2zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6z"/></svg>
                        <a class="auth-my-music-link">My Music</a>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512" xml:space="preserve"><path d="M497,128.533H15c-8.284,0-15,6.716-15,15V497c0,8.284,6.716,15,15,15h482c8.284,0,15-6.716,15-15V143.533C512,135.249,505.284,128.533,497,128.533z M340.637,332.748l-120.5,80.334c-2.51,1.672-5.411,2.519-8.321,2.519c-2.427,0-4.859-0.588-7.077-1.774c-4.877-2.611-7.922-7.693-7.922-13.226V239.934c0-5.532,3.045-10.615,7.922-13.225c4.879-2.611,10.797-2.324,15.398,0.744l120.5,80.334c4.173,2.781,6.68,7.465,6.68,12.48S344.81,329.967,340.637,332.748z"/><path d="M448.801,64.268h-385.6c-8.284,0-15,6.716-15,15s6.716,15,15,15h385.6c8.284,0,15-6.716,15-15S457.085,64.268,448.801,64.268z"/><path d="M400.6,0H111.4c-8.284,0-15,6.716-15,15s6.716,15,15,15h289.2c8.284,0,15-6.716,15-15S408.884,0,400.6,0z"/></svg>
                        <a class="auth-my-playlists-link">My Playlists</a>
                    </li>
                    <li>
                        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 435.104 435.104" xml:space="preserve"><circle cx="154.112" cy="377.684" r="52.736"/><path d="M323.072,324.436L323.072,324.436c-29.267-2.88-55.327,18.51-58.207,47.777c-2.88,29.267,18.51,55.327,47.777,58.207c3.468,0.341,6.962,0.341,10.43,0c29.267-2.88,50.657-28.94,47.777-58.207C368.361,346.928,348.356,326.924,323.072,324.436z"/><path d="M431.616,123.732c-2.62-3.923-7.059-6.239-11.776-6.144h-58.368v-1.024C361.476,54.637,311.278,4.432,249.351,4.428C187.425,4.424,137.22,54.622,137.216,116.549c0,0.005,0,0.01,0,0.015v1.024h-43.52L78.848,50.004C77.199,43.129,71.07,38.268,64,38.228H0v30.72h51.712l47.616,218.624c1.257,7.188,7.552,12.397,14.848,12.288h267.776c7.07-0.041,13.198-4.901,14.848-11.776l37.888-151.552C435.799,132.019,434.654,127.248,431.616,123.732z M249.344,197.972c-44.96,0-81.408-36.448-81.408-81.408s36.448-81.408,81.408-81.408s81.408,36.448,81.408,81.408C330.473,161.408,294.188,197.692,249.344,197.972z"/><path d="M237.056,118.1l-28.16-28.672l-22.016,21.504l38.912,39.424c2.836,2.894,6.7,4.55,10.752,4.608c3.999,0.196,7.897-1.289,10.752-4.096l64.512-60.928l-20.992-22.528L237.056,118.1z"/></svg>
                        <a class="auth-my-purchased-link">{{ __('web.PURCHASED') }}</a>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 20 20"><path fill="none" d="M0 0h20v20H0V0z"></path><path d="M15.95 10.78c.03-.25.05-.51.05-.78s-.02-.53-.06-.78l1.69-1.32c.15-.12.19-.34.1-.51l-1.6-2.77c-.1-.18-.31-.24-.49-.18l-1.99.8c-.42-.32-.86-.58-1.35-.78L12 2.34c-.03-.2-.2-.34-.4-.34H8.4c-.2 0-.36.14-.39.34l-.3 2.12c-.49.2-.94.47-1.35.78l-1.99-.8c-.18-.07-.39 0-.49.18l-1.6 2.77c-.1.18-.06.39.1.51l1.69 1.32c-.04.25-.07.52-.07.78s.02.53.06.78L2.37 12.1c-.15.12-.19.34-.1.51l1.6 2.77c.1.18.31.24.49.18l1.99-.8c.42.32.86.58 1.35.78l.3 2.12c.04.2.2.34.4.34h3.2c.2 0 .37-.14.39-.34l.3-2.12c.49-.2.94-.47 1.35-.78l1.99.8c.18.07.39 0 .49-.18l1.6-2.77c.1-.18.06-.39-.1-.51l-1.67-1.32zM10 13c-1.65 0-3-1.35-3-3s1.35-3 3-3 3 1.35 3 3-1.35 3-3 3z"></path></svg>
                        <a href="{{ route('frontend.settings.index') }}">Settings</a>
                    </li>
                </ul>
            </div>
            <div class="user-auth hide logout-wrap separate">
                <ul class="user_options">
                    <li class="lgout ripple-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"/><path d="M13 3h-2v10h2V3zm4.83 2.17l-1.42 1.42C17.99 7.86 19 9.81 19 12c0 3.87-3.13 7-7 7s-7-3.13-7-7c0-2.19 1.01-4.14 2.58-5.42L6.17 5.17C4.23 6.82 3 9.26 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9c0-2.74-1.23-5.18-3.17-6.83z"/></svg>
                        <a id="mobile-user-sign-out">Logout</a>
                    </li>
                </ul>
            </div>
            <div class="un-auth reg-wrap separate">
                <p>Login to make your Collection, Create Playlists and Favourite Songs</p>
                <div id="mobile-reg-btn" class="reg_btn red_btn">Login/ Register</div>
            </div>
        </div>
    </div>
</div>