<div class="col-12 col-md-4 p-1">
    <a href="{{ $episode->permalink_url }}"
       class="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3"
    >
        <img src="{{ $episode->artwork_url }}"
             alt="" class="card-item-avatar"
        >
        <div class="title font-default color-text text-center">
            {{ \Illuminate\Support\Str::limit($episode->title, 25) }}
        </div>
        <div class="mt-auto d-flex flex-row justify-content-between align-items-end text-center font-merge gap-2">
            <div class="d-flex flex-column gap-1">
                <div class="color-pink fs-5">
                    {{ $episode->season }}
                </div>
                <div class="color-grey">
                    Season
                </div>
            </div>
            <div class="d-flex flex-column gap-1">
                <div class="color-pink fs-5">
                    {{ $episode->number }}
                </div>
                <div class="color-grey">
                    Number
                </div>
            </div>
        </div>
    </a>
</div>