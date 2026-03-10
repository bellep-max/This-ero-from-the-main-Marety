@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active">SlideShow @if(Route::current()->getName() == 'radio') (Radio) @endif</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <a href="{{ route('backend.slideshow.create') }}" class="btn btn-primary">Add new slide</a>
            <div class="card mt-4">
                <div class="card-header p-0">
                    <ul class="nav">
                        <li class="nav-item"><a class="nav-link pl-3 pr-3  @if(Route::currentRouteName() == 'backend.slideshow.index') active @endif" href="{{ route('backend.slideshow.index') }}"><i class="fas fa-fw fa-border-all"></i> Overview ({{ \App\Models\Slide::query()->count() }})</a></li>
                        <li class="nav-item"><a href="{{ route('backend.slideshow.home') }}" class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.slideshow.home') active @endif"><i class="fas fa-fw fa-home"></i> Home ({{ \App\Models\Slide::query()->where('allow_home', 1)->count() }})</a></li>
                        <li class="nav-item"><a href="{{ route('backend.slideshow.discover')  }}" class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.slideshow.discover') active @endif"><i class="fas fa-fw fa-compass"></i> Discover ({{ \App\Models\Slide::query()->where('allow_discover', 1)->count() }})</a></li>
                        <li class="nav-item"><a href="{{ route('backend.slideshow.radio') }}" class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.slideshow.radio') active @endif"><i class="fas fa-fw fa-broadcast-tower"></i> Radio ({{ \App\Models\Slide::query()->where('allow_radio', 1)->count() }})</a></li>
                        <li class="nav-item"><a href="{{ route('backend.slideshow.community') }}" class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.slideshow.community') active @endif"><i class="fas fa-fw fa-users"></i> Community ({{ \App\Models\Slide::query()->where('allow_community', 1)->count() }})</a></li>
                        <li class="nav-item"><a href="{{ route('backend.slideshow.trending') }}" class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.slideshow.trending') active @endif"><i class="fas fa-fw fa-chart-line"></i> Trending ({{ \App\Models\Slide::query()->where('allow_trending', 1)->count() }})</a></li>
                        <li class="nav-item">
                            <div class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.slideshow.genre') active @endif clearfix">
                                <a href="javascript:;" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-fw fa-tags"></i> Genre ({{ \App\Models\Slide::query()->has('genres')->count() }})
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @foreach($genres as $genre)
                                        <a class="dropdown-item" href="{{ route('backend.slideshow.genre', $genre) }}">{{ $genre->name }} ({{ \App\Models\Slide::query()->whereHas('genres', fn ($q) => $q->where('genres.id', $genre->id))->count() }})</a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.slideshow.station-category') active @endif clearfix">
                                <a href="javascript:;" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-fw fa-smile"></i> Station Category ({{ \App\Models\Slide::query()->where('radio', '!=' , '')->count() }})
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @foreach($radio as $category)
                                        <a class="dropdown-item" href="{{ route('backend.slideshow.station-category', $category) }}">{{ $category->name }} ({{ \App\Models\Slide::query()->whereRaw("radio REGEXP '(^|,)(" . $category->id . ")(,|$)'")->count() }})</a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.slideshow.podcast-category') active @endif clearfix">
                                <a href="javascript:;" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-fw fa-smile"></i> Podcast Category ({{ \App\Models\Slide::query()->where('podcast', '!=' , '')->count() }})
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @foreach($podcast as $category)
                                        <a class="dropdown-item" href="{{ route('backend.slideshow.podcast-category', $category) }}">{{ $category->name }} ({{ \App\Models\Slide::query()->whereRaw("podcast REGEXP '(^|,)(" . $category->id . ")(,|$)'")->count() }})</a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('backend.slideshow.sort') }}">
                        @csrf
                        <table class="mt-4 table table-striped table-sortable">
                            <thead>
                            <tr>
                                <th class="th-handle"></th>
                                <th class="th-priority desktop">Priority</th>
                                <th width="100px"></th>
                                <th width="90px">Type</th>
                                <th class="desktop">Description</th>
                                <th class="desktop">Created by</th>
                                <th class="desktop">Created at</th>
                                <th class="desktop">Updated at</th>
                                <th class="th-2action">Action</th>
                            </tr>
                            </thead>
                            @foreach ($slides as $index => $slide)
                                <tr>
                                    <td><i class="handle fas fa-fw fa-arrows-alt"></i></td>
                                    <td class="desktop"><input type="hidden" name="slideshowIds[]" value="{{ $slide->id }}"></td>
                                    <td class="td-image">
                                        <a href="{{ route('backend.slideshow.edit', $slide) }}" class="row-button edit">
                                            <img class="media-object img-70-40" src="{{ url($slide->artwork) }}">
                                        </a>
                                    </td>
                                    <td><span class="label label-text">{{ ucwords($slide->object_type) }}</span></td>
                                    <td class="desktop">{{ $slide->description }}</td>
                                    <td class="desktop"><a href="{{ route('backend.users.edit', $slide->user_id) }}">{{ $slide->user?->name }}</a></td>
                                    <td class="desktop">{{ $slide->created_at->diffForHumans() }}</td>
                                    <td class="desktop">{{ $slide->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('backend.slideshow.edit', $slide) }}" class="row-button edit"><i class="fas fa-fw fa-edit"></i></a>
                                        <a href="{{ route('backend.slideshow.destroy', $slide) }}" class="row-button delete" onclick="return confirm('Are you sure?')"><i class="fas fa-fw fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <button type="submit" class="btn btn-primary mt-4">Save sort order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection