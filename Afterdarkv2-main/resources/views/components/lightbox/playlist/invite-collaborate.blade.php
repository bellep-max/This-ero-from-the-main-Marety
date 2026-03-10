<div class="lightbox lightbox-invite-collaborate hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5">
                Invite Collaborators
            </div>
            <div class="error-container"></div>
            <form id="invite-collaborators-lightbox" class="w-100">
                <input type="hidden" name="feedbackType" value="">
                <div class="lightbox-content-block container-fluid">
                    <div id="friends-can-collaborate" class="row gy-3">
                        <p class="invite-collaborate-loading"
                           data-translate-text="PLEASE_WAIT">{{ __('web.PLEASE_WAIT') }}</p>
                        <div class="invite-to-collaborate col-12 col-md-4 p-1 hide">
                            <a class="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3">
                                <img src="" alt="" class="card-item-avatar">
                                <div class="title font-default color-text text-center"></div>
                                <a class="btn-default btn-pink mt-2 fw-bolder w-100 invite-friend"
                                   data-translate-text="INVITE">{{ __('web.INVITE') }}</a>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>