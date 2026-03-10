<div class="lightbox lightbox-delete-playlist hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5" data-translate-text="POPUP_DELETE_PLAYLIST_TITLE">
                Delete Playlist?
            </div>
            <div class="error hide response">
                <div class="message"></div>
            </div>
            <div id="deletePlaylist">
                <form class="ajax-form container-fluid" method="POST" action="" novalidate>
                    <p data-translate-text="POPUP_DELETE_PLAYLIST_MESSAGE">{{ __('web.POPUP_DELETE_PLAYLIST_MESSAGE') }}</p>
                    <input name="id" type="hidden">
                    <div class="d-flex flex-row justify-content-between align-items-center mt-3 w-100">
                        <a id="confirm-playlist-delete" class="btn-default btn-pink fw-bolder" data-translate-text="DELETE">
                            {{ __('web.DELETE') }}
                        </a>
                        <a class="btn-default btn-outline close" data-translate-text="CANCEL">
                            {{ __('web.CANCEL') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>