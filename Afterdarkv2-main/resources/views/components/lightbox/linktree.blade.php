<div class="lightbox lightbox-linktree hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-center align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5">
                {{ __('web.LINKTREE') }}
            </div>
            @if (auth()->user()->username == $profile->username)
                <form id="user-linktree-setup-form" class="d-flex flex-column gap-3 align-items-end justify-content-between w-100" method="POST" action="{{ route('frontend.auth.user.settings.linktree') }}">
                    <input type="text" class="form-control" name="linktree_link"
                           value="{{ $profile->linktree_link }}"
                           placeholder="{{ __('web.LINKTREE_INPUT_PLACEHOLDER') }}" autocomplete="off">
                </form>
                <div id="user-linktree-apply-button" class="btn-default btn-pink" data-translate-text="SAVE">{{ __('web.SAVE') }}</div>
            @endif
        </div>
    </div>
</div>