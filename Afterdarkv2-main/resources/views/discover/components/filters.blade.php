<button class="btn-default btn-outline d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-discover-filters" aria-controls="offcanvas-discover-filters">Filters</button>

<div class="offcanvas-xl offcanvas-start" tabindex="-1" id="offcanvas-discover-filters" aria-labelledby="offcanvas-menu-label">
    <div class="offcanvas-header">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="d-flex flex-column w-100 bg-light rounded-5 px-3 py-4 gap-3">
        <div class="fs-4 font-default" id="offcanvas-menu-label">
            Filters
        </div>
        <div class="accordion filters" id="filter-accordion">
            <div class="accordion-item border-0">
                <div class="accordion-button collapsed profile-button gap-2" role="button" type="button" data-bs-toggle="collapse" data-bs-target="#genre-collapse" aria-expanded="false" aria-controls="genre-collapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M6.73164 11.479C5.96718 11.479 5.20272 11.4802 4.43826 11.4785C3.57618 11.4768 3.00184 10.9024 3.00128 10.0398C2.99957 8.50404 2.99957 6.96831 3.00128 5.43258C3.00241 4.58186 3.57788 4.00241 4.4235 4.00128C5.96661 3.99957 7.50971 3.99957 9.05225 4.00128C9.90128 4.00241 10.4773 4.57845 10.4785 5.42861C10.4807 6.97115 10.4807 8.51426 10.4785 10.0568C10.4773 10.9024 9.89674 11.4762 9.04544 11.4779C8.27417 11.4807 7.50291 11.479 6.73164 11.479Z" fill="#E836C5"/>
                        <path d="M12.52 7.72844C12.52 6.96398 12.5189 6.20008 12.5206 5.43562C12.5223 4.57525 13.0978 4.00262 13.9621 4.00205C15.4979 4.00092 17.0342 4.00035 18.5699 4.00205C19.4195 4.00319 19.9972 4.57809 19.9984 5.42768C20.0006 6.97022 20.0006 8.51333 19.9984 10.0559C19.9972 10.9026 19.4178 11.4781 18.567 11.4792C17.0313 11.4809 15.495 11.4809 13.9593 11.4792C13.0967 11.4787 12.5217 10.9043 12.52 10.0428C12.5183 9.27098 12.52 8.49971 12.52 7.72844Z" fill="#E836C5"/>
                        <path d="M6.72941 20.9966C5.96495 20.9966 5.20049 20.9977 4.43603 20.996C3.57452 20.9943 3.00189 20.4194 3.00132 19.5556C3.00018 18.0199 2.99962 16.4842 3.00132 14.9485C3.00245 14.0989 3.57849 13.52 4.42581 13.5189C5.96892 13.5172 7.51203 13.5172 9.05457 13.5189C9.90189 13.52 10.4779 14.0977 10.4791 14.9485C10.4813 16.491 10.4813 18.0341 10.4791 19.5766C10.4779 20.4126 9.89849 20.9926 9.06536 20.9955C8.28671 20.9989 7.50806 20.9966 6.72941 20.9966Z" fill="#E836C5"/>
                        <path d="M16.2645 13.5195C18.3042 13.5144 19.9897 15.1881 19.9983 17.2255C20.0062 19.2987 18.3411 20.9905 16.2861 20.9956C14.2027 21.0013 12.5262 19.3367 12.5205 17.2578C12.5154 15.1989 14.1925 13.5241 16.2645 13.5195Z" fill="#E836C5"/>
                    </svg>
                    <span class="font-default">Genre</span>
                </div>
                <div id="genre-collapse" class="accordion-collapse collapse" data-bs-parent="#filter-accordion">
                    <div class="accordion-body d-flex flex-column gap-2">
                        @foreach($discover->genres as $genre)
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" id="genre-{{ $genre->id }}-checkbox" type="checkbox" name="genre" value="{{ $genre->id }}" data-action="filter-query" data-term="genre" data-value="{{ $genre->id }}" data-mask="{{ $genre->name }}">
                                <label class="cbx" for="genre-{{ $genre->id }}-checkbox"></label>
                                <label class="lbl" for="genre-{{ $genre->id }}-checkbox">{!! $genre->name !!}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="accordion-item border-0">
                <div class="accordion-button collapsed profile-button gap-2" role="button" type="button" data-bs-toggle="collapse" data-bs-target="#voice-collapse" aria-expanded="false" aria-controls="voice-collapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12.7505 4C13.0052 4.06344 13.2692 4.10297 13.5132 4.19471C14.7806 4.67101 15.5107 5.59091 15.7195 6.92463C15.7469 7.09788 15.7483 7.27649 15.7483 7.45266C15.7503 9.18362 15.7517 10.9151 15.7488 12.646C15.7469 13.808 15.2935 14.7489 14.356 15.4482C13.0589 16.4154 11.1161 16.2266 10.0152 15.0348C9.39006 14.358 9.08993 13.5611 9.08945 12.646C9.08847 10.8985 9.08408 9.15044 9.09091 7.40288C9.09677 5.89054 9.94834 4.68175 11.3558 4.18056C11.5915 4.09663 11.8438 4.05905 12.0887 4C12.3098 4 12.5304 4 12.7505 4Z" fill="#E836C5"/>
                        <path d="M11.8184 19.7446C11.8184 19.2029 11.8184 18.6857 11.8184 18.1654C11.5208 18.1 11.2226 18.0508 10.9337 17.9673C9.55848 17.5715 8.50389 16.7502 7.76554 15.5278C7.26484 14.6986 7.02621 13.7909 7.00034 12.8247C6.98814 12.3791 7.30486 12.0883 7.72162 12.1664C7.98758 12.2161 8.174 12.4235 8.18815 12.711C8.21499 13.2531 8.29649 13.7817 8.48828 14.2941C9.1627 16.0943 11.1757 17.2553 13.0687 16.9156C14.7382 16.616 15.8528 15.6517 16.4316 14.0632C16.5917 13.6235 16.6219 13.1609 16.6541 12.6983C16.6854 12.2474 17.1763 12.0034 17.5613 12.241C17.7629 12.3655 17.8434 12.5577 17.8414 12.7881C17.8244 15.1515 16.2891 17.2265 14.0223 17.9361C13.7451 18.0229 13.4538 18.0634 13.1688 18.1249C13.1214 18.1352 13.0736 18.1435 13.0229 18.1532C13.0229 18.6822 13.0229 19.1995 13.0229 19.7446C13.0824 19.7446 13.1405 19.7446 13.198 19.7446C13.8598 19.7446 14.5215 19.7407 15.1828 19.7466C15.6064 19.7505 15.8753 20.1155 15.7611 20.5147C15.6957 20.7431 15.501 20.9061 15.2589 20.9334C15.2208 20.9378 15.1818 20.9378 15.1432 20.9378C13.3293 20.9378 11.5154 20.9373 9.70147 20.9388C9.47064 20.9388 9.27739 20.8666 9.14953 20.6679C8.89381 20.2692 9.16173 19.7554 9.63802 19.7481C10.2993 19.7378 10.961 19.7446 11.6228 19.7441C11.6818 19.7446 11.7418 19.7446 11.8184 19.7446Z" fill="#E836C5"/>
                    </svg>
                    <span class="font-default">Voice</span>
                </div>
                <div id="voice-collapse" class="accordion-collapse collapse" data-bs-parent="#filter-accordion">
                    <div class="accordion-body d-flex flex-column gap-2">
                        @foreach(__('web.GENDER_TAGS') as $key => $item)
                            <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                                <input class="hide custom-checkbox" id="type-{{ str_slug($item) }}-checkbox" type="radio" name="vocal" data-action="filter-query" data-term="vocal" data-value="{{ $key }}" data-mask="{{ $item }}">
                                <label class="cbx radio" for="type-{{ str_slug($item) }}-checkbox"></label>
                                <label class="lbl" for="type-{{ str_slug($item) }}-checkbox">{{ $item }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="accordion-item border-0">
                <div class="accordion-button collapsed profile-button gap-2" role="button" type="button" data-bs-toggle="collapse" data-bs-target="#tags-collapse" aria-expanded="false" aria-controls="tags-collapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M11.5003 20.9988C9.06308 20.9988 6.62583 20.9995 4.18931 20.9988C3.5869 20.9988 3.15235 20.6776 3.02809 20.1384C3.00411 20.0331 3.0012 19.9211 3.0012 19.8121C2.99975 14.9376 2.99975 10.0639 3.00048 5.18938C3.00048 4.45326 3.45247 4.00055 4.18713 4.00055C9.06163 3.99982 13.9354 3.99982 18.8099 4.00055C19.5453 4.00055 19.9995 4.45253 19.9995 5.1872C20.0002 10.0617 20.0002 14.9355 19.9995 19.81C19.9995 20.5454 19.5467 20.9988 18.8128 20.9995C16.3741 20.9995 13.9368 20.9988 11.5003 20.9988ZM8.25938 15.7464C8.25938 17.1017 8.25938 18.4286 8.25938 19.7606C8.67576 19.7606 9.07616 19.7606 9.49472 19.7606C9.49472 18.4177 9.49472 17.0864 9.49472 15.7551C10.8398 15.7551 12.166 15.7551 13.511 15.7551C13.511 17.0951 13.511 18.4264 13.511 19.7591C13.9303 19.7591 14.3293 19.7591 14.7522 19.7591C14.7522 18.4155 14.7522 17.085 14.7522 15.7384C16.1016 15.7384 17.4278 15.7384 18.7598 15.7384C18.7598 15.3213 18.7598 14.9209 18.7598 14.5024C17.4169 14.5024 16.0856 14.5024 14.7536 14.5024C14.7536 13.158 14.7536 11.8319 14.7536 10.4861C16.0943 10.4861 17.4249 10.4861 18.7583 10.4861C18.7583 10.0668 18.7583 9.66711 18.7583 9.24419C17.4147 9.24419 16.0849 9.24419 14.7384 9.24419C14.7384 7.89477 14.7384 6.56859 14.7384 5.23588C14.3213 5.23588 13.9216 5.23588 13.503 5.23588C13.503 6.57877 13.503 7.91003 13.503 9.24129C12.158 9.24129 10.8318 9.24129 9.48673 9.24129C9.48673 7.90131 9.48673 6.57005 9.48673 5.23734C9.06744 5.23734 8.66777 5.23734 8.24558 5.23734C8.24558 6.58095 8.24558 7.91148 8.24558 9.258C6.89615 9.258 5.56998 9.258 4.238 9.258C4.238 9.67511 4.238 10.0755 4.238 10.4941C5.58088 10.4941 6.91286 10.4941 8.24412 10.4941C8.24412 11.8384 8.24412 13.1646 8.24412 14.5104C6.90342 14.5104 5.57289 14.5104 4.23945 14.5104C4.23945 14.9297 4.23945 15.3293 4.23945 15.7457C5.5787 15.7464 6.90414 15.7464 8.25938 15.7464Z" fill="#E836C5"/>
                        <path d="M9.5 10.4961C10.8385 10.4961 12.164 10.4961 13.4996 10.4961C13.4996 11.8295 13.4996 13.1557 13.4996 14.495C12.1727 14.495 10.8429 14.495 9.5 14.495C9.5 13.1695 9.5 11.8397 9.5 10.4961Z" fill="#E836C5"/>
                    </svg>
                    <span class="font-default">Tags</span>
                </div>
                <div id="tags-collapse" class="accordion-collapse collapse" data-bs-parent="#filter-accordion">
                    <div class="accordion-body d-flex flex-column gap-3">
                        <form class="input-group filter-form">
                            <span class="input-group-text" id="basic-addon1">
                                <svg class="icon search" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
                                <path d="M0 0h24v24H0z" fill="none"></path>
                                </svg>
                            </span>
                            <input class="form-control" name="tags[]" placeholder="Find a tag...">
                        </form>
                        <div class="d-flex flex-row justify-content-start align-items-center flex-wrap gap-1">
                            @foreach($tags as $tag)
                                <div class="btn-default btn-outline btn-narrow" id="tag-{{ $tag->id }}-checkbox" name="tag" data-action="filter-tag-query" data-term="tag" data-value="{{ $tag->tag }}" data-mask="{{ $tag->tag }}">{{ $tag->tag }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item border-0">
                <div class="accordion-button collapsed profile-button gap-2" role="button" type="button" data-bs-toggle="collapse" data-bs-target="#duration-collapse" aria-expanded="false" aria-controls="duration-collapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="24" viewBox="0 0 23 24" fill="none">
                        <path d="M11.9227 6.15493C15.2691 6.17949 17.9512 8.9925 17.9352 12.4603C17.9197 15.928 15.1886 18.7425 11.8696 18.7116C8.55146 18.6806 5.87308 15.8381 5.90085 12.3772C5.92909 8.9424 8.64937 6.13136 11.9227 6.15493ZM12.4824 10.2578C12.4824 9.7003 12.4829 9.14281 12.4815 8.58531C12.4815 8.51262 12.4791 8.43697 12.4622 8.36723C12.3906 8.07694 12.1247 7.88783 11.8535 7.92712C11.5537 7.97035 11.356 8.20661 11.3555 8.53717C11.3536 9.81131 11.3574 11.0854 11.3527 12.3601C11.3518 12.616 11.4445 12.8026 11.6403 12.9529C12.1741 13.3631 12.7027 13.7815 13.2332 14.1966C13.6795 14.5458 14.1248 14.8955 14.5715 15.2438C14.7753 15.4024 14.9951 15.4246 15.214 15.289C15.4268 15.1574 15.511 14.9506 15.4804 14.6947C15.4564 14.4962 15.3354 14.369 15.19 14.255C14.3258 13.5797 13.4639 12.9009 12.5982 12.2269C12.5144 12.1616 12.4796 12.0948 12.481 11.9848C12.4853 11.4096 12.4824 10.834 12.4824 10.2578Z" fill="#E836C5"/>
                        <path d="M11.9356 20.8989C8.16747 20.896 4.85032 18.1242 4.0261 14.2571C3.11714 9.99318 5.50086 5.69139 9.50243 4.38533C10.8096 3.95849 12.1399 3.89022 13.4856 4.16086C13.8203 4.22815 14.0176 4.51893 13.9611 4.85687C13.9083 5.17221 13.6118 5.3785 13.2861 5.31662C11.471 4.97082 9.76227 5.26553 8.20372 6.30439C6.43005 7.48716 5.35258 9.18076 5.04661 11.3557C4.51047 15.1649 6.80239 18.64 10.386 19.5118C14.1418 20.4259 17.9786 17.8423 18.7238 13.8961C18.7647 13.68 18.7996 13.4624 18.8241 13.2438C18.8669 12.8597 19.1135 12.623 19.4539 12.6534C19.7735 12.6819 19.9891 12.9776 19.9491 13.3499C19.7777 14.9522 19.2293 16.3987 18.2658 17.6611C17.0137 19.3016 15.3958 20.3297 13.4188 20.7182C12.9311 20.814 12.4308 20.8405 11.9356 20.8989Z" fill="#E836C5"/>
                        <path d="M16.7195 5.95986C16.7186 6.39947 16.286 6.6819 15.9104 6.47757C15.4434 6.22363 14.9802 5.96084 14.5203 5.69315C14.2685 5.54678 14.1791 5.21473 14.3019 4.94163C14.4219 4.67443 14.7241 4.51136 14.9765 4.64005C15.4961 4.90529 16.0017 5.20196 16.5044 5.50159C16.6588 5.59344 16.7219 5.76879 16.7195 5.95986Z" fill="#E836C5"/>
                        <path d="M18.8068 8.15899C18.7955 8.45616 18.6463 8.66934 18.4331 8.74694C18.2147 8.82602 17.9722 8.76856 17.8244 8.57896C17.4949 8.15703 17.1692 7.73117 16.851 7.29991C16.6778 7.06513 16.7173 6.73309 16.9244 6.53219C17.1311 6.33179 17.4728 6.29446 17.6592 6.51599C18.0414 6.96984 18.3912 7.45415 18.7447 7.93354C18.7969 8.00476 18.7965 8.11774 18.8068 8.15899Z" fill="#E836C5"/>
                        <path d="M19.8888 11.0423C19.886 11.393 19.693 11.6268 19.4158 11.672C19.1418 11.7167 18.874 11.5551 18.7963 11.2781C18.6513 10.7628 18.5134 10.2451 18.3797 9.72641C18.3067 9.44349 18.4607 9.1473 18.7214 9.04023C18.9766 8.9356 19.292 9.02844 19.4063 9.28729C19.6601 9.86247 19.8008 10.4745 19.8888 11.0423Z" fill="#E836C5"/>
                    </svg>
                    <span class="font-default">Duration (minute)</span>
                </div>
                <div id="duration-collapse" class="accordion-collapse collapse" data-bs-parent="#filter-accordion">
                    <div class="accordion-body d-flex flex-column gap-4">
                        <div class="slider-container">
                            {{-- <input id="bpm-slider" type="text" class="span2" value="" data-slider-min="10" data-slider-max="30" data-slider-step="1" data-slider-value="[0,600]"/> --}}
                            <div id="bpm-slider" type="text" class="span2" value="" data-slider-min="10" data-slider-max="30" data-slider-step="1" data-slider-value="[0,600]">
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-between align-items-center timeStart gap-3">
                            <input name="timeStart" type="number" class="form-control bpm-form-control" step="1" value="1">
                            <span class="text-secondary">to</span>
                            <input name="timeStop" type="number" class="form-control bpm-form-control" step="1" value="60">
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item border-0">
                <div class="accordion-button collapsed profile-button gap-2" role="button" type="button" data-bs-toggle="collapse" data-bs-target="#release-collapse" aria-expanded="false" aria-controls="release-collapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3.00087 8.82422C8.93191 8.82422 14.8389 8.82422 20.7665 8.82422C20.7704 8.91116 20.7773 8.99368 20.7773 9.07669C20.7778 12.3062 20.7778 15.5362 20.7778 18.7658C20.7778 19.6995 20.1452 20.4358 19.2139 20.5807C19.0695 20.6033 18.9216 20.6136 18.7752 20.6141C14.0555 20.6156 9.33616 20.6156 4.61637 20.6156C3.6507 20.6156 3.04311 20.0321 3.00529 19.0649C2.99645 18.8444 3.00136 18.6238 3.00136 18.4033C3.00136 15.2902 3.00136 12.1765 3.00136 9.06343C3.00087 8.98975 3.00087 8.91705 3.00087 8.82422ZM17.2334 12.0208C17.2334 11.7944 17.2364 11.568 17.2329 11.342C17.2285 11.0454 17.0605 10.8744 16.7658 10.872C16.3193 10.8685 15.8728 10.8685 15.4263 10.872C15.1567 10.8739 14.9759 11.0282 14.9681 11.2944C14.9543 11.7649 14.9553 12.2365 14.9671 12.707C14.9735 12.96 15.1316 13.125 15.3846 13.1324C15.8674 13.1461 16.3512 13.1461 16.8336 13.1309C17.0988 13.1226 17.226 12.9678 17.2319 12.7002C17.2373 12.4732 17.2329 12.2468 17.2334 12.0208ZM11.6786 18.1204C11.8987 18.1204 12.1192 18.1233 12.3393 18.1199C12.6448 18.115 12.7956 17.9799 12.8029 17.6744C12.8132 17.222 12.8128 16.7691 12.8039 16.3167C12.798 16.0255 12.6315 15.8585 12.3442 15.8531C11.8918 15.8447 11.4389 15.8442 10.986 15.8536C10.6967 15.8599 10.5391 16.0328 10.5366 16.3241C10.5332 16.7642 10.5332 17.2048 10.5366 17.6454C10.5391 17.9514 10.708 18.1159 11.018 18.1204C11.238 18.1228 11.4586 18.1204 11.6786 18.1204ZM17.2329 17.0142C17.2334 17.0142 17.2334 17.0142 17.2339 17.0142C17.2339 16.7819 17.2369 16.5491 17.2329 16.3167C17.228 16.0387 17.0708 15.8604 16.7943 15.854C16.3296 15.8432 15.8645 15.8437 15.3998 15.8545C15.1464 15.8604 14.9769 16.0171 14.9695 16.2671C14.9558 16.75 14.9558 17.2333 14.972 17.7161C14.9804 17.9632 15.1606 18.115 15.4111 18.1179C15.8758 18.1233 16.3409 18.1233 16.8056 18.1174C17.0753 18.114 17.2241 17.9622 17.2314 17.693C17.2378 17.4671 17.2329 17.2407 17.2329 17.0142ZM11.6712 10.8705C11.4512 10.8705 11.2307 10.868 11.0106 10.871C10.71 10.8749 10.54 11.0375 10.5371 11.3337C10.5327 11.7802 10.5332 12.2266 10.5371 12.6731C10.5396 12.9521 10.6948 13.128 10.9752 13.1348C11.4458 13.1471 11.9173 13.1461 12.3879 13.1329C12.6531 13.125 12.7902 12.9929 12.7975 12.7331C12.8113 12.2502 12.8123 11.7669 12.798 11.2841C12.7902 11.0252 12.6114 10.8769 12.3506 10.8715C12.1241 10.8675 11.8977 10.871 11.6712 10.8705ZM6.16066 16.9793C6.16066 17.2058 6.15722 17.4322 6.16164 17.6587C6.16704 17.944 6.33503 18.1155 6.62089 18.1189C7.05559 18.1238 7.4898 18.1233 7.9245 18.1189C8.20988 18.1159 8.41421 17.947 8.42501 17.6626C8.44269 17.1984 8.43827 16.7328 8.42403 16.2681C8.41666 16.0235 8.25604 15.8644 8.01242 15.8575C7.53548 15.8447 7.05756 15.8442 6.58062 15.857C6.32324 15.8639 6.16852 16.0427 6.16262 16.3005C6.15624 16.5265 6.16066 16.7529 6.16066 16.9793ZM6.16016 11.9978C6.16016 12.2183 6.15771 12.4383 6.16066 12.6589C6.16458 12.9507 6.31587 13.128 6.6037 13.1358C7.05019 13.1481 7.49717 13.1476 7.94366 13.1348C8.23051 13.1265 8.41863 12.9428 8.42698 12.6594C8.43975 12.207 8.43877 11.7536 8.42501 11.3013C8.41715 11.0321 8.23984 10.8749 7.97263 10.8725C7.51976 10.8685 7.06689 10.8685 6.61402 10.8725C6.33797 10.8749 6.16753 11.0449 6.16115 11.3189C6.15624 11.5444 6.16066 11.7713 6.16016 11.9978Z" fill="#E836C5"/>
                        <path d="M17.1814 4.02478C17.2177 4.01299 17.2349 4.00267 17.2516 4.00267C17.8882 4.01201 18.5292 3.98155 19.1603 4.04393C19.9266 4.11958 20.5863 4.76745 20.7071 5.46689C20.7184 5.53271 20.7233 5.60099 20.7233 5.66779C20.7243 6.32303 20.7238 6.97827 20.7238 7.64579C14.8129 7.64579 8.91376 7.64579 3.00776 7.64579C3.00776 6.91294 3.00334 6.1909 3.01169 5.46886C3.01316 5.33084 3.05393 5.18987 3.09617 5.05675C3.31033 4.37548 3.82411 4.0012 4.53584 4.00022C5.04912 3.99973 5.56241 4.00022 6.09927 4.00022C6.09927 4.08372 6.09927 4.14905 6.09927 4.21388C6.09927 4.67953 6.09633 5.14566 6.10026 5.6113C6.10369 5.96987 6.33307 6.21202 6.65676 6.21153C6.98095 6.21055 7.20591 5.97036 7.20836 5.60885C7.21229 5.0823 7.20935 4.55575 7.20935 4.01495C10.1619 4.01495 13.1001 4.01495 16.0575 4.01495C16.061 4.07733 16.0678 4.14217 16.0678 4.20652C16.0688 4.68444 16.0644 5.16285 16.0708 5.64077C16.0742 5.9178 16.2324 6.1241 16.4726 6.19385C16.6917 6.25721 16.953 6.17911 17.0704 5.97772C17.1372 5.86279 17.1715 5.71396 17.1755 5.57937C17.1882 5.12601 17.1804 4.67265 17.1804 4.2188C17.1814 4.15249 17.1814 4.08667 17.1814 4.02478Z" fill="#E836C5"/>
                    </svg>
                    <span class="font-default">Release date</span>
                </div>
                <div id="release-collapse" class="accordion-collapse collapse" data-bs-parent="#filter-accordion">
                    <div class="accordion-body d-flex flex-column gap-2">
                        <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                            <input class="hide custom-checkbox" id="type-recent-checkbox" type="radio" name="release" data-action="filter-query" data-term="release" data-value="recent" data-mask="Recent">
                            <label class="cbx radio" for="type-recent-checkbox"></label>
                            <label class="lbl" for="type-recent-checkbox">Recent</label>
                        </div>
                        <div class="filter-checkbox d-flex align-items-center justify-content-start gap-2">
                            <input class="hide custom-checkbox" id="type-older-checkbox" type="radio" name="release" data-action="filter-query" data-term="release" data-value="older" data-mask="Older">
                            <label class="cbx radio" for="type-older-checkbox"></label>
                            <label class="lbl" for="type-older-checkbox">Older</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{--        <div class="filter-items-container">--}}
{{--            <div data-testid="filter-options-container" class="store-filter-distrack">--}}
{{--                <div class="filter-items"></div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="d-sm-none d-flex flex-column justify-content-between align-items-center gap-3">
            <div class="store-filter-mobile-count"><span class="total-filter">0</span> Filters Selected</div>
            <div class="d-flex flex-row gap-3 justify-content-center align-items-center">
                <button class="btn-default btn-outline" data-action="clear-filter">Cancel</button>
                <button class="btn-default btn-pink" data-action="apply-filter">Apply Filters</button>
            </div>
        </div>
    </div>
</div>