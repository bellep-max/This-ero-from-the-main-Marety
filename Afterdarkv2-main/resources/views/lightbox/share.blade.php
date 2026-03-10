@yield('lightbox-close')
<form id="share-lightbox">
    <div class="share-lightbox__close">
        
    </div>
    <div class="share_container">
        <div class="share_container__tabs">
            <div id="share-embed" class="share_container__tabs__item active share-svc" data-share-svc="embed">
                Embed
            </div>
            <div id="share-facebook" class="share_container__tabs__item share-svc" data-share-svc="facebook">
                Facebook
            </div>
            <div id="share-twitter" class="share_container__tabs__item share-svc" data-share-svc="twitter">
                Twitter
            </div>
            <div id="share-more" class="share_container__tabs__item share-svc" data-share-svc="more">
                More
            </div>
        </div>

        <div class="svc-box embed">
            <div class="share_container__style">
                <div class="share_container__style__item nav-widget active" data-widget="picture">
                    Picture
                </div>
                <div class="share_container__style__item nav-widget" data-widget="classic">
                    Classic
                </div>
                <div class="share_container__style__item nav-widget" data-widget="mini">
                    Mini
                </div>
            </div>
            <div class="share_container__iframe">
                <iframe id="svc-embed-iframe" class="picture" frameborder="0" src="/share/embed/dark/song/14" class="picture"></iframe>
            </div>
            <div class="share_container__options">
                <input type='checkbox' class='share_container__options__switch' id="share_container__options__switch" checked>
                <label for='share_container__options__switch'></label>
            </div>
            <div class="share_container__iframe_code">
                <input id="embed-code" name="embed-code" readonly="readonly" value='<iframe width="100%" height="400" frameborder="0" src="/share/embed/dark/song/12"></iframe>'>
            </div>
            <div class="lightbox-footer">
                <div id="share-link">
                    <input id="share-lightbox-url" class="share-url" readonly="readonly">
                    <div class="copy-background">
                        <a class="btn copier" id="share-lightbox-copy" data-translate-text="SHARE_COPY">Copy</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="svc-box twitter hide">
            <p class="label" data-translate-text="BROADCAST_TWEET">{{ __('web.BROADCAST_TWEET') }}</p><textarea id="share-message-tw"></textarea><span id="twitter-char-count" class="">45</span>
            <div class="lightbox-footer">
                <div id="share-link">
                    <div id="icon-background">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="34" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"></path>
                        </svg>
                    </div>
                    <input id="share-lightbox-url" class="share-url" readonly="readonly">
                    <div class="copy-background">
                        <a class="btn copier" id="share-lightbox-copy" data-translate-text="SHARE_COPY">Copy</a>
                    </div>
                </div>
                <div class="right-btns">
                    <a class="btn btn-primary submit" data-translate-text="SHARE">Share</a>
                </div>
            </div>
        </div>

        <div class="svc-box facebook hide">
            <p class="mb-0" data-translate-text="LB_SHARE_FACEBOOK_MSG">{!! __('web.LB_SHARE_FACEBOOK_MSG') !!}</p>
            <div class="lightbox-footer">
                <div id="share-link">
                    <div id="icon-background">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="34" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"></path>
                        </svg>
                    </div>
                    <input id="share-lightbox-url" class="share-url" readonly="readonly">
                    <div class="copy-background">
                        <a class="btn copier" id="share-lightbox-copy" data-translate-text="SHARE_COPY">Copy</a>
                    </div>
                </div>
                <div class="right-btns">
                    <a class="btn btn-primary submit" data-translate-text="SHARE">Share</a>
                </div>
            </div>
        </div>

        <div class="svc-box more">
            <p data-translate-text="LB_SHARE_MORE_MSG">Clicking on the networks below will open a new window:</p>
            <div class="share-more-option reddit">
                <div class="icon-container icon-reddit">
                    <svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m21.325 9.308c-.758 0-1.425.319-1.916.816-1.805-1.268-4.239-2.084-6.936-2.171l1.401-6.406 4.461 1.016c0 1.108.89 2.013 1.982 2.013 1.113 0 2.008-.929 2.008-2.038s-.889-2.038-2.007-2.038c-.779 0-1.451.477-1.786 1.129l-4.927-1.108c-.248-.067-.491.113-.557.365l-1.538 7.062c-2.676.113-5.084.928-6.895 2.197-.491-.518-1.184-.837-1.942-.837-2.812 0-3.733 3.829-1.158 5.138-.091.405-.132.837-.132 1.268 0 4.301 4.775 7.786 10.638 7.786 5.888 0 10.663-3.485 10.663-7.786 0-.431-.045-.883-.156-1.289 2.523-1.314 1.594-5.115-1.203-5.117zm-15.724 5.41c0-1.129.89-2.038 2.008-2.038 1.092 0 1.983.903 1.983 2.038 0 1.109-.89 2.013-1.983 2.013-1.113.005-2.008-.904-2.008-2.013zm10.839 4.798c-1.841 1.868-7.036 1.868-8.878 0-.203-.18-.203-.498 0-.703.177-.18.491-.18.668 0 1.406 1.463 6.07 1.488 7.537 0 .177-.18.491-.18.668 0 .207.206.207.524.005.703zm-.041-2.781c-1.092 0-1.982-.903-1.982-2.011 0-1.129.89-2.038 1.982-2.038 1.113 0 2.008.903 2.008 2.038-.005 1.103-.895 2.011-2.008 2.011z"></path></svg>
                </div>
                <span class="title">Reddit</span>
                <a target="_blank" class="btn btn-secondary" data-translate-text="SHARE" href="https://www.reddit.com/submit?title=Made%20to%20Love&amp;url=http%3A%2F%2Flocalhost%2Fsong%2F12%2Fmade-to-love">Share</a>
            </div>
            <div class="share-more-option pinterest">
                <div class="icon-container icon-pinterest">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" preserveAspectRatio="xMidYMid meet" height="1em" width="1em" viewBox="0 0 40 40">
                        <g>
                            <path d="m37.3 20q0 4.7-2.3 8.6t-6.3 6.2-8.6 2.3q-2.4 0-4.8-0.7 1.3-2 1.7-3.6 0.2-0.8 1.2-4.7 0.5 0.8 1.7 1.5t2.5 0.6q2.7 0 4.8-1.5t3.3-4.2 1.2-6.1q0-2.5-1.4-4.7t-3.8-3.7-5.7-1.4q-2.4 0-4.4 0.7t-3.4 1.7-2.5 2.4-1.5 2.9-0.4 3q0 2.4 0.8 4.1t2.7 2.5q0.6 0.3 0.8-0.5 0.1-0.1 0.2-0.6t0.2-0.7q0.1-0.5-0.3-1-1.1-1.3-1.1-3.3 0-3.4 2.3-5.8t6.1-2.5q3.4 0 5.3 1.9t1.9 4.7q0 3.8-1.6 6.5t-3.9 2.6q-1.3 0-2.2-0.9t-0.5-2.4q0.2-0.8 0.6-2.1t0.7-2.3 0.2-1.6q0-1.2-0.6-1.9t-1.7-0.7q-1.4 0-2.3 1.2t-1 3.2q0 1.6 0.6 2.7l-2.2 9.4q-0.4 1.5-0.3 3.9-4.6-2-7.5-6.3t-2.8-9.4q0-4.7 2.3-8.6t6.2-6.2 8.6-2.3 8.6 2.3 6.3 6.2 2.3 8.6z"></path>
                        </g>
                    </svg>
                </div>
                <span class="title">Pinterest</span>
                <a target="_blank" class="btn btn-secondary" data-translate-text="SHARE" href="https://pinterest.com/pin/create/button/?url=http%3A%2F%2Flocalhost%2Fsong%2F12%2Fmade-to-love&amp;media=https%3A%2F%2Ferocast.s3.us-east-2.wasabisys.com%2F26%2Fconversions%2FNqfkx3Zvnv-lg.jpg&amp;description=Made%20to%20Love">Share</a>
            </div>
            <div class="share-more-option linkedin">
                <div class="icon-container icon-linkedin">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" preserveAspectRatio="xMidYMid meet" height="1em" width="1em" viewBox="0 0 40 40">
                        <g>
                            <path d="m13.3 31.7h-5v-16.7h5v16.7z m18.4 0h-5v-8.9c0-2.4-0.9-3.5-2.5-3.5-1.3 0-2.1 0.6-2.5 1.9v10.5h-5s0-15 0-16.7h3.9l0.3 3.3h0.1c1-1.6 2.7-2.8 4.9-2.8 1.7 0 3.1 0.5 4.2 1.7 1 1.2 1.6 2.8 1.6 5.1v9.4z m-18.3-20.9c0 1.4-1.1 2.5-2.6 2.5s-2.5-1.1-2.5-2.5 1.1-2.5 2.5-2.5 2.6 1.2 2.6 2.5z"></path>
                        </g>
                    </svg>
                </div>
                <span class="title">LinkedIn</span>
                <a target="_blank" class="btn btn-secondary" data-translate-text="SHARE" href="mailto:info@example.com?&amp;subject=Made%20to%20Love&amp;body=http%3A%2F%2Flocalhost%2Fsong%2F12%2Fmade-to-love">Share</a>
            </div>
            <div class="share-more-option email">
                <div class="icon-container icon-email">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" preserveAspectRatio="xMidYMid meet" height="1em" width="1em" viewBox="0 0 40 40">
                        <g>
                            <path d="m33.4 13.4v-3.4l-13.4 8.4-13.4-8.4v3.4l13.4 8.2z m0-6.8q1.3 0 2.3 1.1t0.9 2.3v20q0 1.3-0.9 2.3t-2.3 1.1h-26.8q-1.3 0-2.3-1.1t-0.9-2.3v-20q0-1.3 0.9-2.3t2.3-1.1h26.8z"></path>
                        </g>
                    </svg>
                </div>
                <span class="title">Email</span>
                <a target="_blank" href="mailto:info@example.com?&amp;subject=Made%20to%20Love&amp;body=http%3A%2F%2Flocalhost%2Fsong%2F12%2Fmade-to-love" class="btn btn-secondary" data-translate-text="SHARE">Share</a>
            </div>
            <div class="lightbox-footer">
                <div id="share-link">
                    <div id="icon-background">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="34" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"></path>
                        </svg>
                    </div>
                    <input id="share-lightbox-url" class="share-url" readonly="readonly">
                    <div class="copy-background">
                        <a class="btn copier" id="share-lightbox-copy" data-translate-text="SHARE_COPY">Copy</a>
                    </div>
                </div>
                <div class="right-btns">
                    <a class="btn btn-primary submit" data-translate-text="SHARE">Share</a>
                </div>
            </div>
        </div>

    </div>
</form>