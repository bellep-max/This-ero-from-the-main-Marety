<div id="header-container">
    <div id="logo" class="desktop">
        <a href="{{ route('frontend.homepage') }}" class="logo-link"></a>
    </div>
    <div id="header-search-container" class="desktop">
        <form id="header-search">
            <span class="prediction"></span>
            <input class="search" name="q" value="" autocomplete="off" type="text" placeholder="Search for songs, artists, genres">
            <svg class="icon search" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        </form>
        <div class="tooltip suggest hide">
            <div class="search-suggest-content-scroll">
                <div id="search-suggest-content-container"></div>
            </div>
        </div>
    </div>
    <div id="header-user-assets" class="session desktop">
        <div id="header-account-group" class="user-asset hide">
            <a id="profile-button" class="">
                <img class="profile-img" width="16" height="16">
                <span class="caret"></span>
            </a>
        </div>
        <a id="header-signup-btn" class="create-account" data-translate-text="BECOME_A_MEMBER">{{ __('web.BECOME_A_MEMBER') }}</a>
        <div id="account-buttons" class="user-asset">
            <div class="btn-group no-border-left">
                @if(auth()->check())
                    <a
                        style="
                            background: #005ff8;
                            border-radius: 4px;
                            width: auto;
                            padding: 0 24px;
                            font-size: 14px;
                            font-weight: 500;
                            color: #fff;" 
                        id="upload-button" 
                        href="{{route('frontend.auth.upload.index')}}"
                        class="btn btn-upload after-login"
                    >
                        Upload
                    </a>
                @endif
                <a id="settings-button" class="btn">
                    <svg height="29" viewBox="0 0 24 24" width="15" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0h24v24H0z" fill="none"/>
                        <path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"/>
                    </svg>
                </a>
                <a id="upload-button" href="{{ route('frontend.auth.upload.index') }}" class="btn upload-music hide">
                    <svg height="29" viewBox="0 0 24 24" width="15" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                    </svg>
                </a>
                <a id="notification-button" class="btn hide">
                    <span id="header-notification-pill" class="notification-pill hide">
                        <span id="header-notification-count" class="notification-count">0</span>
                    </span>
                    <svg height="29" viewBox="0 0 24 24" width="15" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                </a>
                <a id="cart-button" data-action="show-cart" class="btn hide">
                    <span class="header-cart-notification-pill notification-pill hide">
                        <span class="notification-count">0</span>
                    </span>
                    <svg height="29" width="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" xml:space="preserve">
                        <path d="M307.286,277.558c-8.284,0-15.024,6.74-15.024,15.024c0,8.284,6.74,15.024,15.024,15.024c8.285,0,15.024-6.74,15.024-15.024C322.31,284.298,315.571,277.558,307.286,277.558z"/>
                        <path d="M187.186,277.558c-8.284,0-15.024,6.74-15.024,15.024c0,8.284,6.74,15.024,15.024,15.024s15.024-6.74,15.024-15.024C202.21,284.298,195.47,277.558,187.186,277.558z"/>
                        <path d="M512,97.433H63.541l-4.643-59.324H0V68.11h31.153l25.793,329.548h38.067c-3.394,6.992-5.301,14.835-5.301,23.117c0,29.289,23.829,53.117,53.117,53.117c29.289,0,53.118-23.829,53.118-53.117c0-8.281-1.907-16.123-5.301-23.117h130.727c-3.394,6.992-5.301,14.835-5.301,23.117c0,29.289,23.829,53.117,53.118,53.117c29.289,0,53.117-23.829,53.117-53.117c0-8.281-1.907-16.123-5.301-23.117h36.558L512,97.433z M352.311,292.583c0,24.827-20.199,45.025-45.025,45.025c-24.827,0-45.025-20.199-45.025-45.025c0-24.828,20.199-45.025,45.025-45.025c5.267,0,10.323,0.917,15.024,2.587v-62.661h-90.099v105.099c0,24.827-20.199,45.025-45.025,45.025s-45.025-20.199-45.025-45.025c0-24.828,20.199-45.025,45.025-45.025c5.267,0,10.322,0.917,15.024,2.587v-92.662h150.101V292.583z"/>
                    </svg>
                </a>
            </div>
        </div>
        <a id="header-login-btn" class="login" data-translate-text="SIGN_IN">{{ __('web.SIGN_IN') }}</a>
    </div>

    <!-- mobile nav  -->
    <div id="header-nav-btn" class="mobile">
        <svg class="menu hide" xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24.75 24.75" xml:space="preserve"><path d="M0,3.875c0-1.104,0.896-2,2-2h20.75c1.104,0,2,0.896,2,2s-0.896,2-2,2H2C0.896,5.875,0,4.979,0,3.875z M22.75,10.375H2c-1.104,0-2,0.896-2,2c0,1.104,0.896,2,2,2h20.75c1.104,0,2-0.896,2-2C24.75,11.271,23.855,10.375,22.75,10.375z M22.75,18.875H2c-1.104,0-2,0.896-2,2s0.896,2,2,2h20.75c1.104,0,2-0.896,2-2S23.855,18.875,22.75,18.875z"></path></svg>
        <svg class="back hide" width="24" height="24" viewBox="0 0 24 24"><path d="M19.7 11H7.5l5.6-5.6L11.7 4l-8 8 8 8 1.4-1.4L7.5 13h12.2z"></path></svg>
    </div>
    <div id="header-nav-logo" class="mobile">
    </div>
    <div id="header-user-menu" class="mobile">
        <svg class="un-auth" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        <img class="user-auth hide">
    </div>
    <div id="header-cart-menu" data-action="show-cart" class="mobile">
        <span class="header-cart-notification-pill notification-pill hide">
            <span class="notification-count">0</span>
        </span>
        <svg height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" xml:space="preserve"><path d="M307.286,277.558c-8.284,0-15.024,6.74-15.024,15.024c0,8.284,6.74,15.024,15.024,15.024c8.285,0,15.024-6.74,15.024-15.024C322.31,284.298,315.571,277.558,307.286,277.558z"/><path d="M187.186,277.558c-8.284,0-15.024,6.74-15.024,15.024c0,8.284,6.74,15.024,15.024,15.024s15.024-6.74,15.024-15.024C202.21,284.298,195.47,277.558,187.186,277.558z"/><path d="M512,97.433H63.541l-4.643-59.324H0V68.11h31.153l25.793,329.548h38.067c-3.394,6.992-5.301,14.835-5.301,23.117c0,29.289,23.829,53.117,53.117,53.117c29.289,0,53.118-23.829,53.118-53.117c0-8.281-1.907-16.123-5.301-23.117h130.727c-3.394,6.992-5.301,14.835-5.301,23.117c0,29.289,23.829,53.117,53.118,53.117c29.289,0,53.117-23.829,53.117-53.117c0-8.281-1.907-16.123-5.301-23.117h36.558L512,97.433z M352.311,292.583c0,24.827-20.199,45.025-45.025,45.025c-24.827,0-45.025-20.199-45.025-45.025c0-24.828,20.199-45.025,45.025-45.025c5.267,0,10.323,0.917,15.024,2.587v-62.661h-90.099v105.099c0,24.827-20.199,45.025-45.025,45.025s-45.025-20.199-45.025-45.025c0-24.828,20.199-45.025,45.025-45.025c5.267,0,10.322,0.917,15.024,2.587v-92.662h150.101V292.583z"/></svg>
    </div>
    <div id="header-settings-menu" class="mobile">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 20 20"><path fill="none" d="M0 0h20v20H0V0z"/><path d="M15.95 10.78c.03-.25.05-.51.05-.78s-.02-.53-.06-.78l1.69-1.32c.15-.12.19-.34.1-.51l-1.6-2.77c-.1-.18-.31-.24-.49-.18l-1.99.8c-.42-.32-.86-.58-1.35-.78L12 2.34c-.03-.2-.2-.34-.4-.34H8.4c-.2 0-.36.14-.39.34l-.3 2.12c-.49.2-.94.47-1.35.78l-1.99-.8c-.18-.07-.39 0-.49.18l-1.6 2.77c-.1.18-.06.39.1.51l1.69 1.32c-.04.25-.07.52-.07.78s.02.53.06.78L2.37 12.1c-.15.12-.19.34-.1.51l1.6 2.77c.1.18.31.24.49.18l1.99-.8c.42.32.86.58 1.35.78l.3 2.12c.04.2.2.34.4.34h3.2c.2 0 .37-.14.39-.34l.3-2.12c.49-.2.94-.47 1.35-.78l1.99.8c.18.07.39 0 .49-.18l1.6-2.77c.1-.18.06-.39-.1-.51l-1.67-1.32zM10 13c-1.65 0-3-1.35-3-3s1.35-3 3-3 3 1.35 3 3-1.35 3-3 3z"/></svg>
    </div>
    <div id="header-nav-title" class="mobile"></div>
</div>