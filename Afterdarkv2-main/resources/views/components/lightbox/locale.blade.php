<div class="lightbox lightbox-locale hide">
    <div class="lbcontainer">
        <div id="choose-locale">
            <div class="lightbox-header">
                <h2 class="title" data-translate-text="LANGUAGE">{{ __('web.LANGUAGE') }}</h2>
                @yield('lightbox-close')
            </div>
            <div class="lightbox-content">
                <div class="lightbox-content-block  separated-content">
                    <ul class="languages row">
                        @if(Cache::has('languages'))
                            @foreach(Cache::get('languages') as $code => $language)
                                <li class="language col-4"><a
                                            class="@if($code == \Session::get('website_language', config('app.locale'))) active @endif"
                                            rel="{{ $code }}">{{ $language }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                    <div class="clear noHeight"></div>
                </div>
            </div>
            <div class="lightbox-footer">
                <div class="right"></div>
                <div class="left"><a class="btn btn-secondary close"
                                     data-translate-text="CLOSE">{{ __('web.CLOSE') }}</a></div>
            </div>
        </div>
    </div>
</div>