@extends('index')
@section('content')
<div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
    <div class="container">
        <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
            <div class="d-block">
                <div class="block-title color-light text-truncate">
                    {{ $podcast_title }}
                </div>
                <div class="block-description color-light">
                    {{ $episode->title }}
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <div class="container d-flex flex-column w-100 gap-4 bg-light rounded-5 p-3 p-lg-5">
                    <div class="row gy-3">
                        <div class="col-12 col-lg-4 d-flex justify-content-center justify-content-xl-start">
                            <img src="{{ $episode->artwork_url }}" alt="{{ $episode->title }}"
                                 class="img-fluid rounded-4 border-pink">
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column gap-4">
                                @auth
                                    <div class="d-flex flex-row gap-4 justify-content-xl-start justify-content-center align-items-center flex-wrap">
                                        @include('frontend.components.circled-text', ['title' => $episode->loves, 'description' => 'Likes'])
                                        @include('frontend.components.circled-text', ['title' => $episode->plays, 'description' => 'Total Plays'])
                                        @include('frontend.components.circled-text', ['title' => $episode->time, 'description' => 'Duration'])
                                    </div>
                                @endauth
                                <script>var episode_data_{{ $episode->id }} = {!! json_encode($episode) !!}</script>
                                <div class="d-flex flex-row align-items-center py-3 border-top border-bottom gap-3">
                                    <a class="mat-focus-indicator btn-white rounded-circle mat-fab mat-button-base mat-accent ng-star-inserted play-object"
                                       style="filter: drop-shadow(0 0px 5px #DEDEDE)" data-type="episode"
                                       data-id="{{ $episode->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="59" height="60" viewBox="0 0 59 60" fill="none">
                                            <path d="M45.1177 30L21.6912 43.5252L21.6912 16.4747L45.1177 30Z"/>
                                        </svg>
                                    </a>
                                    <div class="d-flex flex-column align-items-start justify-content-start gap-2">
                                        <span class="font-default fs-4">{{ $episode->title }}</span>
                                        <span class="font-merge fs-14">{{ $episode->user->name }}</span>
                                        <div class="d-flex flex-row justify-content-start align-items-center gap-2">
                                            <svg width="19" height="20" viewBox="0 0 19 20" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 12.1915C5.78699 12.235 5.56438 12.2226 5.35456 12.1526C5.03993 12.0477 4.77985 11.8222 4.63154 11.5255C4.48322 11.2289 4.45881 10.8855 4.56369 10.5709C4.66857 10.2562 4.89413 9.99616 5.19077 9.84784L6.69063 9.09792C6.88127 9.00252 7.09314 8.95746 7.30611 8.96703C7.51907 8.9766 7.72605 9.04048 7.90738 9.15259C8.08869 9.2647 8.23833 9.42132 8.34206 9.60757C8.44578 9.79381 8.50015 10.0035 8.5 10.2167M6 12.1915L8.5 10.2167M6 12.1915V16.2163C6 16.5478 6.1317 16.8658 6.36612 17.1002C6.60054 17.3346 6.91848 17.4663 7.25 17.4663C7.58152 17.4663 7.89946 17.3346 8.13388 17.1002C8.3683 16.8658 8.5 16.5478 8.5 16.2163V10.2167M6 12.1915L8.5 10.2167M14.5 11.7155C14.4997 11.1105 14.2998 10.5225 13.9315 10.0426C13.563 9.56255 13.0464 9.21743 12.4618 9.06078C11.8772 8.90413 11.2573 8.94469 10.6981 9.17617C10.1408 9.40689 9.67487 9.81441 9.37199 10.3359C9.28676 10.4767 9.23014 10.6329 9.20541 10.7956C9.18033 10.9606 9.18855 11.129 9.22959 11.2908C9.27063 11.4526 9.34367 11.6045 9.44438 11.7376C9.5451 11.8707 9.67146 11.9823 9.816 12.0658L10.0661 11.6329L9.816 12.0658C9.96054 12.1493 10.1203 12.203 10.286 12.2237C10.4516 12.2445 10.6197 12.2318 10.7804 12.1865C10.941 12.1412 11.091 12.0642 11.2214 11.96L10.9093 11.5694L11.2214 11.96C11.3491 11.858 11.4556 11.7319 11.5348 11.5889C11.5516 11.5612 11.5738 11.5369 11.5999 11.5176C11.6273 11.4974 11.6586 11.4829 11.6917 11.4752C11.7249 11.4675 11.7593 11.4666 11.7928 11.4726C11.8263 11.4787 11.8583 11.4915 11.8867 11.5104C11.915 11.5292 11.9393 11.5536 11.9579 11.5821C11.9765 11.6107 11.9891 11.6427 11.9948 11.6763C12.0006 11.7098 11.9995 11.7442 11.9915 11.7774C11.9835 11.8105 11.9688 11.8416 11.9483 11.8688L11.9481 11.8691L9.25001 15.4663L9.25 15.4663C9.11072 15.652 9.0259 15.8728 9.00505 16.1041C8.9842 16.3353 9.02815 16.5677 9.13197 16.7753L9.53852 16.572L9.13197 16.7753C9.23578 16.983 9.39536 17.1576 9.59283 17.2796L9.8557 16.8543L9.59284 17.2796C9.79031 17.4017 10.0179 17.4663 10.25 17.4663H13.25C13.5815 17.4663 13.8995 17.3346 14.1339 17.1002C14.3683 16.8658 14.5 16.5478 14.5 16.2163C14.5 15.8848 14.3683 15.5668 14.1339 15.3324C13.8995 15.098 13.5815 14.9663 13.25 14.9663H12.7499L13.9444 13.3735C13.9447 13.373 13.9451 13.3726 13.9454 13.3721C14.3069 12.8957 14.5018 12.3136 14.5 11.7155ZM14.5 11.7155C14.5 11.7152 14.5 11.715 14.5 11.7147L14 11.7163L14.5 11.7161C14.5 11.7159 14.5 11.7157 14.5 11.7155ZM14.25 1.96631V2.46631H14.75H17C17.2652 2.46631 17.5196 2.57167 17.7071 2.7592C17.8946 2.94674 18 3.20109 18 3.46631V18.4663C18 18.7315 17.8946 18.9859 17.7071 19.1734C17.5196 19.361 17.2652 19.4663 17 19.4663H2C1.73478 19.4663 1.48043 19.361 1.29289 19.1734C1.10536 18.9859 1 18.7315 1 18.4663V3.46631C1 3.20109 1.10536 2.94674 1.29289 2.7592C1.48043 2.57167 1.73478 2.46631 2 2.46631H4.25H4.75V1.96631V1.21631C4.75 1.15 4.77634 1.08642 4.82322 1.03953C4.87011 0.992648 4.9337 0.966309 5 0.966309C5.0663 0.966309 5.12989 0.992648 5.17678 1.03953C5.22366 1.08642 5.25 1.15 5.25 1.21631V1.96631V2.46631H5.75H13.25H13.75V1.96631V1.21631C13.75 1.15 13.7763 1.08642 13.8232 1.03953C13.8701 0.992648 13.9337 0.966309 14 0.966309C14.0663 0.966309 14.1299 0.992648 14.1768 1.03953C14.2237 1.08642 14.25 1.15 14.25 1.21631V1.96631ZM1.5 6.46631V6.96631H2H17H17.5V6.46631V3.46631V2.96631H17H14.75H14.25V3.46631V4.21631C14.25 4.28261 14.2237 4.3462 14.1768 4.39308L14.5303 4.74664L14.1768 4.39309C14.1299 4.43997 14.0663 4.46631 14 4.46631C13.9337 4.46631 13.8701 4.43997 13.8232 4.39309L13.4697 4.74664L13.8232 4.39308C13.7763 4.3462 13.75 4.28261 13.75 4.21631V3.46631V2.96631H13.25H5.75H5.25V3.46631V4.21631C5.25 4.28261 5.22366 4.3462 5.17678 4.39308L5.53033 4.74664L5.17678 4.39309C5.12989 4.43997 5.0663 4.46631 5 4.46631C4.9337 4.46631 4.87011 4.43997 4.82322 4.39309C4.77634 4.3462 4.75 4.28261 4.75 4.21631V3.46631V2.96631H4.25H2H1.5V3.46631V6.46631Z"
                                                      stroke="#E836C5"/>
                                            </svg>
                                            <span class="fs-14 font-merge">
                                                {{ $episode->created_at->format('d.m.Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-row justify-content-start align-items-center gap-2">
                                    <a class="share btn-default btn-outline btn-narrow"
                                       data-type="episode" data-id="{{ $episode->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 19"
                                             fill="none">
                                            <path d="M13.5 12.56C12.93 12.56 12.42 12.785 12.03 13.1375L6.6825 10.025C6.72 9.8525 6.75 9.68 6.75 9.5C6.75 9.32 6.72 9.1475 6.6825 8.975L11.97 5.8925C12.375 6.2675 12.9075 6.5 13.5 6.5C14.745 6.5 15.75 5.495 15.75 4.25C15.75 3.005 14.745 2 13.5 2C12.255 2 11.25 3.005 11.25 4.25C11.25 4.43 11.28 4.6025 11.3175 4.775L6.03 7.8575C5.625 7.4825 5.0925 7.25 4.5 7.25C3.255 7.25 2.25 8.255 2.25 9.5C2.25 10.745 3.255 11.75 4.5 11.75C5.0925 11.75 5.625 11.5175 6.03 11.1425L11.37 14.2625C11.3325 14.42 11.31 14.585 11.31 14.75C11.31 15.9575 12.2925 16.94 13.5 16.94C14.7075 16.94 15.69 15.9575 15.69 14.75C15.69 13.5425 14.7075 12.56 13.5 12.56Z"/>
                                        </svg>
                                        Share
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($episode->description)
                        <div class="row">
                            <div class="col">
                                <div class="font-default">
                                    <span class="fw-bold">Summary: </span>
                                    <span>{{ $episode->description }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col">
                            @if($episode->allow_comments)
                                @include('frontend.default.comments.index', ['object' => (Object) ['id' => $episode->id, 'type' => 'App\Models\Episode', 'title' => $episode->title]])
                            @else
                                <p class="text-center">Comments are turned off.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection