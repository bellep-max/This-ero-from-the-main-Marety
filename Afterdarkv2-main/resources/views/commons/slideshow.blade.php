<div class="content home-section swiper apple-music-look home-content-container">
    <div class="swiper-container swiper-container-slide">
        <div class="swiper-wrapper gap-3">
            @foreach ($slides as $slide)
                @isset($slide->object->id)
                    <script>var {{ $slide->object_type }}_data_{{ $slide->object->id }} = @json($slide->object)</script>
                    <div class="swiper-slide module-cell-lg draggable rounded-3" data-toggle="contextmenu" data-trigger="right" data-type="{{ $slide->object_type }}" data-id="{{$slide->object->id}}">
                        <div class="d-flex flex-column text-start p-2">
                            @if ($slide->title_link)
                                <a class="title font-default color-text" href="{{ url($slide->title_link) }}">{{ $slide->title }}</a>
                            @else
                                <span class="title font-default color-text">{{ $slide->title }}</span>
                            @endif
                            @if (isset($slide->object->name) && isset($slide->object->permalink_url))
                                <a class="fs-14 font-merge color-grey text-truncate" href="{{ $slide->object->permalink_url }}">{{ $slide->object->name }}</a>
                            @else
                                <span class="fs-14 font-merge color-grey text-truncate" href="{{ $slide->object->permalink_url }}">{{ $slide->object->title }}</span>
                            @endif
                            <span class="fs-12 font-merge color-grey text-truncate">{{ $slide->description }}</span>
                        </div>
                        <div class="position-relative overflow-hidden rounded-bottom-3">
                            <a href="{{ $slide->object->permalink_url }}">
                                <img class="img-fluid" src="{{ $slide->artwork }}" alt="{{ $slide->title ?? '' }}">
                            </a>
                            <div class="actions position-absolute top-50 start-50 translate-middle">
                                @if (in_array($slide->object_type, [\App\Constants\TypeConstants::SONG, \App\Constants\TypeConstants::STATION]))
                                    <a class="btn-default btn-outline btn-rnd play-object" data-type="{{ $slide->object_type }}" data-id="{{ $slide->object->id }}">
                                        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.6 0 80L0 432c0 17.4 9.4 33.4 24.5 41.9s33.7 8.1 48.5-.9L361 297c14.3-8.7 23-24.2 23-41s-8.7-32.2-23-41L73 39z"/></svg>
{{--                                        <svg class="icon-pause" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/><path d="M0 0h24v24H0z" fill="none"/></svg>--}}
{{--                                        <svg class="icon-waiting embed_spin" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 252.264 252.264" xml:space="preserve"><path d="M248.988,80.693c-3.423-2.335-8.089-1.452-10.422,1.97l-15.314,22.453c-9.679-44.721-49.575-78.354-97.123-78.354c-26.544,0-51.498,10.337-70.265,29.108c-2.929,2.929-2.928,7.678,0.001,10.606c2.929,2.929,7.678,2.929,10.606-0.001c15.933-15.937,37.12-24.713,59.657-24.713c41.32,0,75.815,29.921,82.98,69.228l-26.606-18.147c-3.423-2.336-8.089-1.452-10.422,1.97c-2.334,3.422-1.452,8.088,1.971,10.422l39.714,27.087c0.003,0.002,0.005,0.003,0.007,0.005c0.97,0.661,2.039,1.064,3.128,1.225c0.362,0.053,0.727,0.08,1.091,0.08c2.396,0,4.751-1.146,6.203-3.274l26.764-39.242C253.293,87.693,252.41,83.027,248.988,80.693z"></path><path d="M187.196,184.351c-16.084,16.863-37.77,26.15-61.065,26.15c-41.317-0.001-75.813-29.921-82.978-69.227l26.607,18.147c1.293,0.882,2.764,1.305,4.219,1.305c2.396,0,4.751-1.145,6.203-3.274c2.334-3.422,1.452-8.088-1.97-10.422l-39.714-27.087c-0.002-0.001-0.004-0.003-0.006-0.005c-3.424-2.335-8.088-1.452-10.422,1.97L1.304,161.149c-2.333,3.422-1.452,8.088,1.97,10.422c1.293,0.882,2.764,1.304,4.219,1.304c2.397,0,4.751-1.146,6.203-3.275l15.313-22.453c9.68,44.72,49.577,78.352,97.121,78.352c27.435,0,52.977-10.938,71.919-30.797c2.859-2.997,2.747-7.745-0.25-10.604C194.8,181.241,190.053,181.353,187.196,184.351z"></path></svg>--}}
                                    </a>
                                @else
                                    <a class="btn-default btn-outline btn-rnd play-object" data-type="{{ $slide->object_type }}" data-id="{{ $slide->object->id }}">
                                        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.6 0 80L0 432c0 17.4 9.4 33.4 24.5 41.9s33.7 8.1 48.5-.9L361 297c14.3-8.7 23-24.2 23-41s-8.7-32.2-23-41L73 39z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    <a class="home-pageable-nav previous-pageable-nav btn-default btn-outline btn-rounded swiper-arrow-left">
        <svg height="16" viewBox="0 0 501.5 501.5" width="16" xmlns="http://www.w3.org/2000/svg"><g><path d="M302.67 90.877l55.77 55.508L254.575 250.75 358.44 355.116l-55.77 55.506L143.56 250.75z"></path></g></svg>
    </a>
    <a class="home-pageable-nav next-pageable-nav btn-default btn-outline btn-rounded swiper-arrow-right">
        <svg height="16" viewBox="0 0 501.5 501.5" width="16" xmlns="http://www.w3.org/2000/svg"><g><path d="M302.67 90.877l55.77 55.508L254.575 250.75 358.44 355.116l-55.77 55.506L143.56 250.75z"></path></g></svg>
    </a>
</div>