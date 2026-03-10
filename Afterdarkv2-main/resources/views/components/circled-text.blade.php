<div class="position-relative">
    @include('frontend.components.circle')
    <div class="d-flex flex-column text-center position-absolute top-50 start-50 translate-middle">
        <div class="color-pink fs-4">
            {{ $title }}
        </div>
        <div class="color-grey">
            {{ $description }}
        </div>
    </div>
</div>