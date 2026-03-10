<div class="col-12 col-md-4 p-1">
    <a href="{{ route('frontend.user.podcasts.episodes', ['user' => $profile, 'podcastVisible' => $podcast]) }}"
       class="card-item d-flex flex-column justify-content-start align-items-center gap-2 p-3"
    >
        <img src="{{ $podcast->artwork_url }}"
             alt="" class="card-item-avatar"
        >
        <div class="title font-default color-text text-center">
            {{ \Illuminate\Support\Str::limit($podcast->title, 25) }}
        </div>
        <div class="mt-auto d-flex flex-row justify-content-between align-items-end text-center font-merge gap-2">
            <div class="d-flex flex-column gap-1">
                <div class="color-pink fs-5">
                    {{ $podcast->episodes()->count() }}
                </div>
                <div class="color-grey">
                    Episodes
                </div>
            </div>
            <div class="d-flex flex-column gap-1">
                <div class="color-pink fs-5">
                    {{ $podcast->episodes()->max('season') }}
                </div>
                <div class="color-grey">
                    Seasons
                </div>
            </div>
        </div>
    </a>
</div>