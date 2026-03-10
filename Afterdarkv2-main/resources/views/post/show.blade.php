{{-- @extends('non_background_index') --}}
@extends('index')
@section('content')

<div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
    <div class="container d-flex flex-column gap-4">
        <div class="row">
            <div class="col text-start">
                <div class="text-white">
                    <div class="block-title color-light text-truncate">
                        {{ $post->title }}
                    </div>
                    <a href="{{ route('frontend.blog') }}" class="block-description color-light">
                        <span data-translate-text="BACK">{{ __('web.BACK') }}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="d-flex flex-column justify-content-center align-items-center gap-4 bg-light rounded-5 p-5 h-100">
                    @if ($post->getFirstMediaUrl('artwork'))
                        <img class="img-fluid" alt="{{ $post->title }}" src="{{ $post->getFirstMediaUrl('artwork') }}"/>
                    @endif
                    <div class="post-content">
                        {!! $post->full_content ?: $post->short_content !!}
                    </div>
                    {!! $pages !!}
                </div>
            </div>
        </div>
        @if($post->allow_comments)
            <div class="row">
                <div class="col">
                    @include('frontend.default.comments.index', ['object' => (Object) ['id' => $post->id, 'type' => \App\Models\Post::class, 'title' => $post->title]])
                </div>
            </div>
        @endif
    </div>
</div>
@endsection