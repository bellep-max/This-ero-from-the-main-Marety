@php use App\Constants\DefaultConstants; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active">Channels</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <a href="{{ route('backend.channels.create') }}" class="btn btn-primary">Add new channel</a>
            <div class="card mt-4">
                <div class="card-header p-0">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link pl-3 pr-3  @if(Route::currentRouteName() == 'backend.channels.index') active @endif"
                               href="{{ route('backend.channels.index') }}">
                                <i class="fas fa-fw fa-border-all"></i>Overview ({{ \App\Models\Channel::query()->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.channels.home') active @endif"
                               href="{{ route('backend.channels.home') }}">
                                <i class="fas fa-fw fa-home"></i>Home ({{ \App\Models\Channel::query()->where('allow_home', DefaultConstants::TRUE)->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.channels.discover') active @endif"
                               href="{{ route('backend.channels.discover') }}">
                                <i class="fas fa-fw fa-compass"></i> Discover ({{ \App\Models\Channel::query()->where('allow_discover', DefaultConstants::TRUE)->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.channels.radio') active @endif"
                               href="{{ route('backend.channels.radio') }}">
                                <i class="fas fa-fw fa-broadcast-tower"></i> Radio ({{ \App\Models\Channel::query()->where('allow_radio', DefaultConstants::TRUE)->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.channels.community') active @endif"
                               href="{{ route('backend.channels.community') }}">
                                <i class="fas fa-fw fa-users"></i> Community ({{ \App\Models\Channel::query()->where('allow_community', DefaultConstants::TRUE)->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.channels.trending') active @endif"
                               href="{{ route('backend.channels.trending') }}">
                                <i class="fas fa-fw fa-chart-line"></i> Trending ({{ \App\Models\Channel::query()->where('allow_trending', DefaultConstants::TRUE)->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <div class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.channels.genre') active @endif clearfix">
                                <a href="javascript:;" class="dropdown-toggle" id="dropdownMenuButton"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-fw fa-tags"></i> Genre
                                    ({{ \App\Models\Channel::query()->has('genres')->count() }})
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @foreach($genres as $genre)
                                        <a class="dropdown-item"
                                           href="{{ route('backend.channels.genre', $genre) }}">{{ $genre->name }}
                                            ({{ \App\Models\Channel::query()->whereHas('genres', fn ($q) => $q->where('genres.id', $genre->id))->count() }}
                                            )</a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.channels.station-category') active @endif clearfix">
                                <a href="javascript:;" class="dropdown-toggle" id="dropdownMenuButton"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-fw fa-smile"></i> Station Category
                                    ({{ \App\Models\Channel::query()->has('radioCategories')->count() }})
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @foreach($radio_categories as $radio_category)
                                        <a class="dropdown-item"
                                           href="{{ route('backend.channels.station-category', $radio_category) }}">{{ $radio_category->name }}
                                            ({{ \App\Models\Channel::query()->whereHas("radioCategories", fn ($q) => $q->where('radio_category_id', $radio_category->id))->count() }}
                                            )</a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="nav-link pl-3 pr-3 @if(Route::currentRouteName() == 'backend.channels.podcast-category') active @endif clearfix">
                                <a href="javascript:;" class="dropdown-toggle" id="dropdownMenuButton"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-fw fa-smile"></i> Podcast Category
                                    ({{ \App\Models\Channel::query()->has('podcastCategories')->count() }})
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @foreach($podcast_categories as $podcast_category)
                                        <a class="dropdown-item"
                                           href="{{ route('backend.channels.podcast-category', $podcast_category) }}">{{ $podcast_category->name }}
                                            ({{ \App\Models\Channel::query()->whereHas("podcastCategories", fn ($q) => $q->where('podcast_category_id', $podcast_category->id))->count() }}
                                            )</a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('backend.channels.sort') }}">
                        @csrf
                        <table class="mt-4 table table-striped table-sortable">
                            <thead>
                            <tr>
                                <th class="th-handle"></th>
                                <th class="th-priority">Priority</th>
                                <th>Name</th>
                                <th>Created by</th>
                                <th class="th-3action">Type</th>
                                <th>Created at</th>
                                <th>Updated at</th>
                                <th class="th-2action">Action</th>
                            </tr>
                            </thead>
                            @foreach ($channels as $index => $channel)
                                <tr>
                                    <td><i class="handle fas fa-fw fa-arrows-alt"></i></td>
                                    <td><input type="hidden" name="object_ids[]" value="{{ $channel->id }}"></td>
                                    <td><a href="{{ route('backend.channels.edit', $channel) }}"
                                           class="row-button edit">{{ $channel->title }}</a></td>
                                    <td>
                                        <a href="{{ route('backend.users.edit', $channel->user) }}">{{ $channel->user?->name }}</a>
                                    </td>
                                    <td>{{ $channel->object_type }}</td>
                                    <td>{{ $channel->created_at->diffForHumans() }}</td>
                                    <td>{{ $channel->updated_at->diffForHumans() }}</td>

                                    <td>
                                        <a href="{{ route('backend.channels.edit', $channel) }}"
                                           class="row-button edit"><i class="fas fa-fw fa-edit"></i></a>
                                        <a href="{{ route('backend.channels.destroy', $channel) }}"
                                           class="row-button delete" onclick="return confirm('Are you sure?')"><i
                                                    class="fas fa-fw fa-trash"></i></a>
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