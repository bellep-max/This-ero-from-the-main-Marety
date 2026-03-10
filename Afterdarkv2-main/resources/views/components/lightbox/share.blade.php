<div class="lightbox lightbox-share hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <form id="share-lightbox" class="container-fluid d-flex flex-column justify-content-start align-items-center w-100 gap-3">
                <div class="tabs-header d-flex flex-row justify-content-around text-center w-100">
                    <div id="share-embed" class="px-1 px-md-3 pt-1 tab-item tab-item-active share-svc" data-share-svc="embed">
                        Embed
                    </div>
                    <div id="share-facebook" class="px-1 px-md-3 pt-1 tab-item share-svc" data-share-svc="facebook">
                        Facebook
                    </div>
                    <div id="share-twitter" class="px-1 px-md-3 pt-1 tab-item share-svc" data-share-svc="twitter">
                        Twitter
                    </div>
                    <div id="share-more" class="px-1 px-md-3 pt-1 tab-item share-svc" data-share-svc="more">
                        More
                    </div>
                </div>
                <div class="svc-box embed d-flex flex-column align-items-center justify-content-start gap-3">
                    <div class="d-flex flex-row justify-content-center align-items-center w-100 gap-3">
                        <div class="btn-default btn-pink btn-narrow nav-widget active" data-widget="picture">
                            Picture
                        </div>
                        <div class="btn-default btn-outline btn-narrow nav-widget" data-widget="classic">
                            Classic
                        </div>
                        <div class="btn-default btn-outline btn-narrow nav-widget" data-widget="mini">
                            Mini
                        </div>
                    </div>
                    <div class="share_container__iframe p-2">
                        <iframe id="svc-embed-iframe" class="picture" frameborder="0" src="/share/embed/dark/song/14"></iframe>
                    </div>
                    <div class="d-flex flex-row align-items-center justify-content-center">
                        <input type="checkbox" class="share_container__options__switch" id="share_container__options__switch" checked>
                        <label for="share_container__options__switch"></label>
                    </div>
                    <input id="embed-code" class="form-control" name="embed-code" value='<iframe width="100%" height="400" frameborder="0" src="/share/embed/dark/song/12"></iframe>' readonly>
                </div>

                <div class="svc-box twitter hide d-flex flex-column justify-content-start align-items-center gap-3 w-100">
                    <span class="label" data-translate-text="BROADCAST_TWEET">{{ __('web.BROADCAST_TWEET') }}</span>
                    <textarea class="form-control" id="share-message-tw"></textarea>
                    <span id="twitter-char-count" class="">45</span>
                </div>

                <div class="svc-box facebook hide d-flex flex-column justify-content-start align-items-center gap-3 w-100">
                    <p class="mb-0" data-translate-text="LB_SHARE_FACEBOOK_MSG">{!! __('web.LB_SHARE_FACEBOOK_MSG') !!}</p>
                </div>

                <div class="svc-box more d-flex flex-column justify-content-start align-items-center gap-3 w-100">
                    <span class="font-merge fs-6 text-center" data-translate-text="LB_SHARE_MORE_MSG">
                        Clicking on the networks below will open a new window:
                    </span>
                    <a target="_blank" class="btn-default btn-pink" href="https://www.reddit.com/submit?title=Made%20to%20Love&amp;url=http%3A%2F%2Flocalhost%2Fsong%2F12%2Fmade-to-love">
                        <svg height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M373 138.6c-25.2 0-46.3-17.5-51.9-41l0 0c-30.6 4.3-54.2 30.7-54.2 62.4l0 .2c47.4 1.8 90.6 15.1 124.9 36.3c12.6-9.7 28.4-15.5 45.5-15.5c41.3 0 74.7 33.4 74.7 74.7c0 29.8-17.4 55.5-42.7 67.5c-2.4 86.8-97 156.6-213.2 156.6S45.5 410.1 43 323.4C17.6 311.5 0 285.7 0 255.7c0-41.3 33.4-74.7 74.7-74.7c17.2 0 33 5.8 45.7 15.6c34-21.1 76.8-34.4 123.7-36.4l0-.3c0-44.3 33.7-80.9 76.8-85.5C325.8 50.2 347.2 32 373 32c29.4 0 53.3 23.9 53.3 53.3s-23.9 53.3-53.3 53.3zM157.5 255.3c-20.9 0-38.9 20.8-40.2 47.9s17.1 38.1 38 38.1s36.6-9.8 37.8-36.9s-14.7-49.1-35.7-49.1zM395 303.1c-1.2-27.1-19.2-47.9-40.2-47.9s-36.9 22-35.7 49.1c1.2 27.1 16.9 36.9 37.8 36.9s39.3-11 38-38.1zm-60.1 70.8c1.5-3.6-1-7.7-4.9-8.1c-23-2.3-47.9-3.6-73.8-3.6s-50.8 1.3-73.8 3.6c-3.9 .4-6.4 4.5-4.9 8.1c12.9 30.8 43.3 52.4 78.7 52.4s65.8-21.6 78.7-52.4z"/></svg>
                        <span>Reddit</span>
                    </a>
                    <a target="_blank" class="btn-default btn-pink" href="https://pinterest.com/pin/create/button/?url=http%3A%2F%2Flocalhost%2Fsong%2F12%2Fmade-to-love&amp;media=https%3A%2F%2Ferocast.s3.us-east-2.wasabisys.com%2F26%2Fconversions%2FNqfkx3Zvnv-lg.jpg&amp;description=Made%20to%20Love">
                        <svg height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M496 256c0 137-111 248-248 248-25.6 0-50.2-3.9-73.4-11.1 10.1-16.5 25.2-43.5 30.8-65 3-11.6 15.4-59 15.4-59 8.1 15.4 31.7 28.5 56.8 28.5 74.8 0 128.7-68.8 128.7-154.3 0-81.9-66.9-143.2-152.9-143.2-107 0-163.9 71.8-163.9 150.1 0 36.4 19.4 81.7 50.3 96.1 4.7 2.2 7.2 1.2 8.3-3.3 .8-3.4 5-20.3 6.9-28.1 .6-2.5 .3-4.7-1.7-7.1-10.1-12.5-18.3-35.3-18.3-56.6 0-54.7 41.4-107.6 112-107.6 60.9 0 103.6 41.5 103.6 100.9 0 67.1-33.9 113.6-78 113.6-24.3 0-42.6-20.1-36.7-44.8 7-29.5 20.5-61.3 20.5-82.6 0-19-10.2-34.9-31.4-34.9-24.9 0-44.9 25.7-44.9 60.2 0 22 7.4 36.8 7.4 36.8s-24.5 103.8-29 123.2c-5 21.4-3 51.6-.9 71.2C65.4 450.9 0 361.1 0 256 0 119 111 8 248 8s248 111 248 248z"/></svg>
                        <span>Pinterest</span>
                    </a>
                    <a target="_blank" class="btn-default btn-pink" href="mailto:info@example.com?&amp;subject=Made%20to%20Love&amp;body=http%3A%2F%2Flocalhost%2Fsong%2F12%2Fmade-to-love">
                        <svg height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"/></svg>
                        <span>LinkedIn</span>
                    </a>
                    <a target="_blank" class="btn-default btn-pink" href="mailto:info@example.com?&amp;subject=Made%20to%20Love&amp;body=http%3A%2F%2Flocalhost%2Fsong%2F12%2Fmade-to-love">
                        <svg height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M373 138.6c-25.2 0-46.3-17.5-51.9-41l0 0c-30.6 4.3-54.2 30.7-54.2 62.4l0 .2c47.4 1.8 90.6 15.1 124.9 36.3c12.6-9.7 28.4-15.5 45.5-15.5c41.3 0 74.7 33.4 74.7 74.7c0 29.8-17.4 55.5-42.7 67.5c-2.4 86.8-97 156.6-213.2 156.6S45.5 410.1 43 323.4C17.6 311.5 0 285.7 0 255.7c0-41.3 33.4-74.7 74.7-74.7c17.2 0 33 5.8 45.7 15.6c34-21.1 76.8-34.4 123.7-36.4l0-.3c0-44.3 33.7-80.9 76.8-85.5C325.8 50.2 347.2 32 373 32c29.4 0 53.3 23.9 53.3 53.3s-23.9 53.3-53.3 53.3zM157.5 255.3c-20.9 0-38.9 20.8-40.2 47.9s17.1 38.1 38 38.1s36.6-9.8 37.8-36.9s-14.7-49.1-35.7-49.1zM395 303.1c-1.2-27.1-19.2-47.9-40.2-47.9s-36.9 22-35.7 49.1c1.2 27.1 16.9 36.9 37.8 36.9s39.3-11 38-38.1zm-60.1 70.8c1.5-3.6-1-7.7-4.9-8.1c-23-2.3-47.9-3.6-73.8-3.6s-50.8 1.3-73.8 3.6c-3.9 .4-6.4 4.5-4.9 8.1c12.9 30.8 43.3 52.4 78.7 52.4s65.8-21.6 78.7-52.4z"/></svg>
                        <span>Email</span>
                    </a>
                </div>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 w-100 gap-3 gap-md-5">
                    <div id="share-link" class="input-group">
                        <input type="text" id="share-lightbox-url" class="form-control" aria-label="Username" aria-describedby="share-lightbox-copy" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="share-lightbox-copy" data-translate-text="SHARE_COPY">Copy</button>
                    </div>
                    <a class="btn-default btn-pink fw-bolder submit" data-translate-text="SHARE">Share</a>
                </div>
            </form>
        </div>
    </div>
</div>