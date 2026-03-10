<div class="lightbox lightbox-disable-collaborate hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5">
                Disable Collaboration
            </div>
            <div class="container-fluid flex-column gap-3">
                <div class="d-flex flex-column text-center gap-3">
                    <span>
                        {{ __('web.PLAYLIST_COLLABORATION_TURN_OFF_TITLE') }}
                    </span>
                    <span>
                        {{ __('web.PLAYLIST_COLLABORATION_TURN_OFF_DESCRIPTION') }}
                    </span>
                </div>
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <div class="btn-default btn-outline" id="cancel-playlist-collab-disable" data-translate-text="CANCEL">
                        {{ __('web.CANCEL') }}
                    </div>
                    <div class="btn-default btn-pink" id="confirm-playlist-collab-disable" data-translate-text="ACCEPT">
                        {{ __('web.ACCEPT') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>