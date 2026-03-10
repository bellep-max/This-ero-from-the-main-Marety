{{-- @extends('non_background_index') --}}
@extends('index')

@section('pagination')
    @foreach($posts as $index => $post)
        @include('frontend.default.post.components.post-item', ['post' => $post])
    @endforeach
@endsection
    
@section('content')
<div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
    <div class="container">
        <div class="row">
            <div class="col text-start block-title color-light">
                Blog
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                @include('frontend.default.post.components.filters')
            </div>
            <div class="col col-xl-9">
                <div id="posts-grid" class="d-flex flex-column w-100 h-100 gap-4 store-item-list infinity-load-more">
                    @yield('pagination')
                </div>
            </div>
        </div>
    </div>
</div>

{{--<div class="blog_container">--}}
{{--    <div class="blog_container__title">--}}
{{--        Blog--}}
{{--    </div>--}}
{{--    <div class="blog_container__data">--}}
{{--        <div class="blog_container__data__menu">--}}

{{--        </div>--}}
{{--        <div class="store-item-list" style="margin-left:0">--}}
{{--            @yield('pagination')--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
@endsection