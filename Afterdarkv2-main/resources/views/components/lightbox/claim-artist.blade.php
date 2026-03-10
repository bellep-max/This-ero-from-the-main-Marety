<div class="lightbox lightbox-claimArtist hide">
    <div id="claimArtistLightbox">
        <div class="lightbox-header">
            <h2 class="title" data-translate-text="CLAIM_ARTIST">{{ __('web.CLAIM_ARTIST') }}</h2>
            @yield('lightbox-close')
        </div>
        <ul id="lightbox-stages-header" class="">
            <li class="lightbox-stage active" rel="account">
                <div class="circle">
                    <svg class="icon" height="24" viewBox="0 0 512 512" width="24"
                         xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="m459.669 82.906-196-81.377c-4.91-2.038-10.429-2.039-15.338 0l-196 81.377c-7.465 3.1-12.331 10.388-12.331 18.471v98.925c0 136.213 82.329 258.74 208.442 310.215 4.844 1.977 10.271 1.977 15.116 0 126.111-51.474 208.442-174.001 208.442-310.215v-98.925c0-8.083-4.865-15.371-12.331-18.471zm-27.669 117.396c0 115.795-68 222.392-176 269.974-105.114-46.311-176-151.041-176-269.974v-85.573l176-73.074 176 73.074zm-198.106 67.414 85.964-85.963c7.81-7.81 20.473-7.811 28.284 0s7.81 20.474-.001 28.284l-100.105 100.105c-7.812 7.812-20.475 7.809-28.284 0l-55.894-55.894c-7.811-7.811-7.811-20.474 0-28.284s20.474-7.811 28.284 0z"/>
                        </g>
                    </svg>
                </div> {{ __('web.VERIFY_ACCOUNT') }}
                <div class="arrow"></div>
            </li>
            <li class="lightbox-stage" rel="info">
                <div class="circle">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24">
                        <path fill="none" d="M0 0h24v24H0V0z"/>
                        <path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                    </svg>
                </div> {{ __('web.INFORMATION') }}
                <div class="arrow"></div>
            </li>
            <li class="lightbox-stage" rel="done">
                <div class="circle">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24">
                        <path fill="none" d="M0 0h24v24H0V0zm0 0h24v24H0V0z"/>
                        <path d="M16.59 7.58L10 14.17l-3.59-3.58L5 12l5 5 8-8zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                    </svg>
                </div> {{ __('web.FINISHED') }}
                <div class="arrow"></div>
            </li>
        </ul>
        <div class="lightbox-content">
            <div class="lightbox-error error hide"></div>
            <div class="lightbox-content-block">
                <div id="artist-claiming-stage-account" class="claiming-stage">
                    <p data-translate-text="VERIFY_ACCOUNT_EMAIL_MESSAGE">{{ __('web.VERIFY_ACCOUNT_EMAIL_MESSAGE') }}</p>
                    <div class="control-group floated"><label class="control-label" for="artist-claiming-email"
                                                              data-translate-text="FORM_EMAIL_ADDRESS">{{ __('web.FORM_EMAIL_ADDRESS') }}</label>
                        <div class="control">
                            <input type="text" id="artist-claiming-email" value="">
                        </div>
                    </div>
                    <div class="control-group floated"><label class="control-label" for="artist-claiming-fname"
                                                              data-translate-text="FORM_FULL_NAME">{{ __('web.FORM_FULL_NAME') }}</label>
                        <div class="control">
                            <input type="text" id="artist-claiming-fname" value="">
                        </div>
                    </div>
                    <div class="control-group password-container hide"><label class="control-label"
                                                                              for="artist-claiming-password"
                                                                              data-translate-text="FORM_PASSWORD_REQUIRED">>{{ __('web.FORM_PASSWORD_REQUIRED') }}</label>
                        <div class="control">
                            <input type="password" id="artist-claiming-password">
                        </div>
                    </div>
                </div>
                <div id="artist-claiming-stage-info" class="claiming-stage hide">
                    <div id="artist-claiming-name-container" class="control-group">
                        <label class="control-label" for="artist-claiming-email"
                               data-translate-text="FORM_ARTIST_OR_BAND">{{ __('web.FORM_ARTIST_OR_BAND') }}</label>
                        <form id="artist-claim-search-form" class="control artist-search-container ajax-form"
                              method="GET" action="{{ url('api/search/artist') }}">
                            <button type="submit" class="btn btn-secondary search"
                                    data-translate-text="SEARCH">{{ __('web.SEARH') }}</button>
                            <div id="artist-name-container">
                                <input type="text" name="q" value="" autocomplete="off">
                            </div>
                        </form>
                        <div id="artist-search-loading" class="hide">
                            <img class="page-loading" width="32" height="32">
                        </div>
                        <div id="window-claim-artist-selector" class="window-selector-container hide">
                                    <span id="selected-create-text" class="control-label d-block text-center mb-2"
                                          data-translate-text="LB_CLAIM_ARTIST_CHOOSE_PROFILE_TOP">{{ __('web.LB_CLAIM_ARTIST_CHOOSE_PROFILE_TOP') }}</span>
                            <div class="window-selector window-selector-selected snapshot">
                                <div class="module module-row tall artist">
                                    <div class="img-container">
                                        <img class="img" src="">
                                    </div>
                                    <div class="metadata">
                                        <span class="title artist-link"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="others hide">
                                        <span
                                                class="control-label text-center d-block text-center mb-2"
                                                data-translate-text="LB_CLAIM_ARTIST_CHOOSE_PROFILE_OTHERS"
                                        >
                                            {{ __('web.LB_CLAIM_ARTIST_CHOOSE_PROFILE_OTHERS') }}
                                        </span>
                                <div class="window-selector window-selector-others"></div>
                            </div>
                            <input type="hidden" name="artist_id"/>
                            <input type="hidden" name="artist_name"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control"><label class="control-label" for="artist-claiming-phone"
                                                    data-translate-text="FORM_PHONE">{{ __('web.FORM_PHONE') }}</label><input
                                    type="text" id="artist-claiming-phone" value=""></div>
                        <div class="control"><label class="control-label" for="artist-claiming-phone-ext"
                                                    data-translate-text="FORM_PHONE_EXT">{{ __('web.FORM_PHONE_EXT') }}</label><input
                                    type="text" id="artist-claiming-phone-ext" value=""></div>
                        <div class="control"><label class="control-label" for="artist-claiming-affiliation"
                                                    data-translate-text="FORM_ARTIST_AFFILIATION">{{ __('web.FORM_ARTIST_AFFILIATION') }}</label>
                            <select id="artist-claiming-affiliation" class="span2">
                                <option value="">Select an option</option>
                                <option value="Artist/Band Member">Artist/Band Member</option>
                                <option value="Manager">Manager</option>
                                <option value="Label">Label</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="message-container"><label class="control-label" for="artist-claiming-message"
                                                          data-translate-text="EXPLAIN_ARTIST_IDENTITY">{{ __('web.EXPLAIN_ARTIST_IDENTITY') }}</label>
                        <div class="control"><textarea id="artist-claiming-message"></textarea></div>
                    </div>
                    <h3 data-translate-text="SPEED_UP_ARTIST_PROCESS">{{ __('web.SPEED_UP_ARTIST_PROCESS') }}</h3>
                    <div class="connect-container">
                        <div class="twitter-icon-container icon-container">
                            <svg class="icon" width="16" height="16" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 512 512" xml:space="preserve"><path
                                        d="M512,97.248c-19.04,8.352-39.328,13.888-60.48,16.576c21.76-12.992,38.368-33.408,46.176-58.016c-20.288,12.096-42.688,20.64-66.56,25.408C411.872,60.704,384.416,48,354.464,48c-58.112,0-104.896,47.168-104.896,104.992c0,8.32,0.704,16.32,2.432,23.936c-87.264-4.256-164.48-46.08-216.352-109.792c-9.056,15.712-14.368,33.696-14.368,53.056c0,36.352,18.72,68.576,46.624,87.232c-16.864-0.32-33.408-5.216-47.424-12.928c0,0.32,0,0.736,0,1.152c0,51.008,36.384,93.376,84.096,103.136c-8.544,2.336-17.856,3.456-27.52,3.456c-6.72,0-13.504-0.384-19.872-1.792c13.6,41.568,52.192,72.128,98.08,73.12c-35.712,27.936-81.056,44.768-130.144,44.768c-8.608,0-16.864-0.384-25.12-1.44C46.496,446.88,101.6,464,161.024,464c193.152,0,298.752-160,298.752-298.688c0-4.64-0.16-9.12-0.384-13.568C480.224,136.96,497.728,118.496,512,97.248z"/></svg>
                            <img class="hide"/>
                        </div>
                        <h3 class="icon-name">Twitter</h3>
                        <span class="icon-message text-secondary small"
                              data-translate-text="TWITTER_ARTIST_VERIFY_INSTRUCTIONS">{{ __('web.TWITTER_ARTIST_VERIFY_INSTRUCTIONS') }}</span>
                        <a id="artist-twitter-connect" class="btn"
                           data-translate-text="SETTINGS_NAV_THIRD_PARTY" data-action="social-login"
                           data-service="twitter">{{ __('web.SETTINGS_NAV_THIRD_PARTY') }}</a>
                    </div>
                    <div class="connect-container facebook">
                        <div class="facebook-icon-container icon-container">
                            <svg class="icon" height="16" viewBox="0 0 24 24" width="16"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="m15.997 3.985h2.191v-3.816c-.378-.052-1.678-.169-3.192-.169-3.159 0-5.323 1.987-5.323 5.639v3.361h-3.486v4.266h3.486v10.734h4.274v-10.733h3.345l.531-4.266h-3.877v-2.939c.001-1.233.333-2.077 2.051-2.077z"/>
                            </svg>
                            <img class="hide"/>
                        </div>
                        <h3 class="icon-name">Facebook</h3>
                        <div id="facebook-verify-connect">
                                    <span class="icon-message text-secondary small"
                                          data-translate-text="FACEBOOK_ARTIST_VERIFY_INSTRUCTIONS">{{ __('web.FACEBOOK_ARTIST_VERIFY_INSTRUCTIONS') }}</span>
                            <a id="artist-facebook-connect" class="btn"
                               data-translate-text="SETTINGS_NAV_THIRD_PARTY" data-action="social-login"
                               data-service="facebook">{{ __('web.SETTINGS_NAV_THIRD_PARTY') }}</a>
                        </div>
                    </div>
                    <div class="connect-container passport">
                        <div class="passport-icon-container icon-container">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path d="M437.333,0h-42.667c35.346,0,64,28.654,64,64v384c0,35.346-28.654,64-64,64h42.667c35.346,0,64-28.654,64-64V64C501.333,28.654,472.68,0,437.333,0z"/>
                                <path d="M119.616,309.333c14.802,28.855,40.851,50.326,72,59.349c-8.816-18.747-14.577-38.783-17.067-59.349H119.616z"/>
                                <path d="M328.384,202.667c-14.799-28.849-40.839-50.319-71.979-59.349c8.815,18.747,14.577,38.783,17.067,59.349H328.384z"/>
                                <path d="M191.552,143.317c-31.125,9.039-57.148,30.508-71.936,59.349h54.869C176.975,182.1,182.736,162.064,191.552,143.317z"/>
                                <path d="M170.667,256c0-10.667,0.491-21.483,1.387-32h-60.843c-6.065,20.902-6.065,43.098,0,64h60.843C171.157,277.483,170.667,266.667,170.667,256z"/>
                                <path d="M224,138.667c-7.957,0-21.333,22.379-27.968,64h55.936C245.333,161.045,231.957,138.667,224,138.667z"/>
                                <path d="M224,373.333c7.957,0,21.333-22.379,27.968-64h-55.936C202.667,350.955,216.043,373.333,224,373.333z"/>
                                <path d="M275.947,224c0.896,10.517,1.387,21.333,1.387,32s-0.491,21.483-1.387,32h60.843c6.066-20.902,6.066-43.098,0-64H275.947z"/>
                                <path d="M437.333,448V64c0-35.346-28.654-64-64-64H32C20.218,0,10.667,9.551,10.667,21.333v469.333C10.667,502.449,20.218,512,32,512h341.333C408.68,512,437.333,483.346,437.333,448z M352,309.333C322.545,380.026,241.359,413.455,170.667,384c-33.762-14.068-60.599-40.904-74.667-74.667c-14.272-34.123-14.272-72.543,0-106.667C125.455,131.974,206.641,98.545,277.333,128c33.762,14.068,60.599,40.904,74.667,74.667C366.27,236.79,366.27,275.21,352,309.333z"/>
                                <path d="M193.493,224c-0.875,9.941-1.493,20.437-1.493,32s0.619,22.059,1.493,32h61.013c0.875-9.941,1.493-20.437,1.493-32s-0.619-22.059-1.493-32H193.493z"/>
                                <path d="M256.384,368.683c31.148-9.025,57.196-30.496,72-59.349h-54.869C271.005,329.903,265.221,349.939,256.384,368.683z"/>
                            </svg>
                        </div>
                        <h3 class="icon-name">Passport</h3>
                        <div id="passport-verify-connect">
                            <span class="icon-message text-secondary small" data-translate-text="">Scan and upload passport to get verify even faster.</span>
                            <div class="btn">
                                <span data-translate-text="">Browse file</span>
                                <form id="passport-form" enctype="multipart/form-data" method="post">
                                    <input name="passport" type="file">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="connect-container">
                        <input class="hide custom-checkbox" id="accept-terms" type="checkbox"
                               name="accept-terms">
                        <label class="cbx float-left" for="accept-terms"></label>
                        <label class="lbl ml-4" for="accept-terms"
                               data-translate-text="AGREE_ARTIST_TERMS">{{ __('web.AGREE_ARTIST_TERMS') }}</label>
                    </div>
                </div>
                <div id="artist-claiming-stage-message" class="hide">
                    <div class="claiming-success-badge">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24">
                            <path fill="none" d="M0 0h24v24H0V0zm0 0h24v24H0V0z"></path>
                            <path d="M16.59 7.58L10 14.17l-3.59-3.58L5 12l5 5 8-8zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path>
                        </svg>
                    </div>
                    <p class="claiming-success-message"
                       data-translate-text="POPUP_CLAIM_ARTIST_SENT">{{ __('web.POPUP_CLAIM_ARTIST_SENT') }}</p>
                </div>
            </div>
        </div>
        <div class="lightbox-footer">
            <div class="right">
                <a class="btn btn-primary continue" data-translate-text="CONTINUE">{{ __('web.CONTINUE') }}</a>
                <a class="btn btn-primary create hide"
                   data-translate-text="CREATE_ARTIST">{{ __('web.CREATE_ARTIST') }}</a>
                <a class="btn btn-primary finished hide" data-translate-text="CLOSE">{{ __('web.CLOSE') }}</a>
                <a class="btn btn-primary submit hide"
                   data-translate-text="ARTIST_CLAIM_SEND_REQUEST">{{ __('web.ARTIST_CLAIM_SEND_REQUEST') }}</a>
            </div>
            <div class="left">
                <a class="btn btn-secondary back hide" data-translate-text="BACK">{{ __('web.BACK') }}</a>
                <a class="btn btn-secondary close hide" data-translate-text="CLOSE">{{ __('web.CLOSE') }}</a>
            </div>
        </div>
    </div>
</div>