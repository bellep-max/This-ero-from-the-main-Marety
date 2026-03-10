<div class="d-flex flex-column justify-content-start align-items-center w-100 gap-3 px-3" data-id="acc-children">
    @foreach($children as $i => $child)
        @include('frontend.components.adventure.root-accordion', ['index' => $i, 'child' => $child])
    @endforeach
</div>




