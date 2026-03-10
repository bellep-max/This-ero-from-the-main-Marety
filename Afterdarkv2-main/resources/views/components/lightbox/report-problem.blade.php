<div class="lightbox lightbox-report-problem hide">
    @yield('lightbox-close')
    <div class="lightbox-content">
        <div class="lightbox-content-block d-flex flex-column justify-content-start align-items-center gap-3 w-100">
            <div class="text-center font-default fs-5" data-translate-text="Report">
                Report
            </div>
            <p class="font-default fs-14">
                You can report after selecting a problem.
            </p>

            <form class="bootbox-form container-fluid" novalidate>
                <div class="row gy-3 gy-lg-4">
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-start gap-2">
                            <input class="hide custom-checkbox" id="type-no-art-checkbox" value="1" type="radio" name="bootbox-radio">
                            <label class="cbx radio" for="type-no-art-checkbox"></label>
                            <label class="lbl" for="type-no-art-checkbox">This track wasn't recorded by the artist shown</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-start gap-2">
                            <input class="hide custom-checkbox" id="type-problem-checkbox" value="2" type="radio" name="bootbox-radio">
                            <label class="cbx radio" for="type-problem-checkbox"></label>
                            <label class="lbl" for="type-problem-checkbox">There's an audio problem</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-start gap-2">
                            <input class="hide custom-checkbox" id="type-content-checkbox" value="3" type="radio" name="bootbox-radio">
                            <label class="cbx radio" for="type-content-checkbox"></label>
                            <label class="lbl" for="type-content-checkbox">Undesirable content</label>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-between align-items-center mt-3 w-100">
                        <a href="" class="btn-default btn-outline close" data-translate-text="CANCEL">
                            Cancel
                        </a>
                        <button id="report-form-submit-button" class="btn-default btn-pink">
                            {{ __('web.OK') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>