<div class="d-flex flex-column justify-content-start align-items-center w-100 gap-3 px-3" data-id="acc-children">
    @foreach($finals as $i => $final)
        @include('frontend.components.adventure.final-accordion', ['innerIndex' => $i, 'child' => $final])
    @endforeach
</div>
