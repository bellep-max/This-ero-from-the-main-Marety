<button class="btn-default btn-outline d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-blog-filters" aria-controls="offcanvas-blog-filters">Filters</button>

<div class="offcanvas-xl offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="offcanvas-blog-filters" aria-labelledby="offcanvas-menu-label">
    <div class="offcanvas-header">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="d-flex flex-column w-100 bg-light rounded-5 gap-3 p-3">
        <div class="accordion__title">
            Filters
        </div>
        <div class="d-flex flex-column justify-content-start align-items-start gap-2">
            <div class="filter-title">
                CATEGORIES
            </div>
            @foreach($categories as $index => $category)
                <div class="filter-checkbox d-flex flex-row gap-1 align-items-center justify-content-start">
                    <input class="hide custom-checkbox"
                           id="checkbox-{{$index}}"
                           name="category"
                           data-action="filter-query"
                           data-term="category"
                           data-value="{{$category->id}}"
                           type="radio"
                    >
                    <label class="cbx radio" for="checkbox-{{$index}}"></label>
                    <label class="lbl" for="checkbox-{{$index}}">{{ $category->name }}</label>
                </div>
            @endforeach
        </div>
        <div class="separator"></div>
        <div class="d-flex flex-column justify-content-start align-items-start gap-2">
            <div class="filter-title">
                ARCHIVES
            </div>
            <ul class="blog_container__data__menu__item__archive">
                @foreach($archives as $archive)
                    <li class="blog_container__data__menu__item__archive__item {{ url()->current() == route('frontend.blog.browse.by.month', ['year' =>  \Carbon\Carbon::parse($archive->created_at)->format('Y'), 'month' => \Carbon\Carbon::parse($archive->created_at)->format('m')]) ? 'active' : '' }}">
                        <a href="{{ route('frontend.blog.browse.by.month', ['year' =>  \Carbon\Carbon::parse($archive->created_at)->format('Y'), 'month' => \Carbon\Carbon::parse($archive->created_at)->format('m')]) }}">{{ \Carbon\Carbon::parse($archive->created_at)->format('F Y') }} ({{ $archive->count }})</a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="separator"></div>
        <div class="d-flex flex-column justify-content-start align-items-start gap-2">
            <div class="filter-title">
                TAGS
            </div>
            <div class="d-flex flex-row gap-2 flex-wrap justify-content-start align-items-center">
                @foreach($tags as $tag)
                    <a href="{{ route('frontend.blog.tags', ['tag' => $tag->tag]) }}"
                       class="tag btn-tag {{ url()->current() == route('frontend.blog.tags', ['tag' => $tag->tag]) ? 'active' : ''}}"
                       name="tag"
                    >
                        {{ $tag->tag }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>