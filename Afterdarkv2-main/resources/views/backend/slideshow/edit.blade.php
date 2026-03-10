@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.slideshow.index') }}">Slideshow</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.slideshow.update', $slide) }}" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Type</label>
                    <div class="col-sm-9">
                        {!! \App\Helpers\Helper::makeSlideShowDropDown( \App\Enums\SlideModelEnum::toArray(), "object_type", $slide->object_type)!!}
                    </div>
                </div>
                <div class="form-group row slide-show-selector @if ($slide->object_type != \App\Models\Song::class) d-none @endif" data-type="{{ \App\Constants\TypeConstants::SONG }}">
                    <label class="col-sm-3 col-form-label">Select a Song</label>
                    <div class="col-sm-9">
                        <select class="form-control select-ajax" data-ajax--url="{{ route('backend.songs.search') }}" name="object_id">
                            @if($slide->object && count(get_object_vars(($slide->object))))
                                <option value="{{ $slide->object->id }}" selected="selected" data-artwork="{{ $slide->object->artwork }}">{{ $slide->object->title }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row slide-show-selector @if ($slide->object_type != \App\Models\Artist::class) d-none @endif" data-type="{{ \App\Constants\TypeConstants::ARTIST }}">
                    <label class="col-sm-3 col-form-label">Select an Artist</label>
                    <div class="col-sm-9">
                        <select class="form-control select-ajax" data-ajax--url="{{ route('backend.artists.search') }}" name="object_id">
                            @if($slide->object && count(get_object_vars(($slide->object))))
                                <option value="{{ $slide->object->id }}" selected="selected" data-artwork="{{ $slide->object->artwork }}">{{ $slide->object->title }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row slide-show-selector @if ($slide->object_type != \App\Models\Album::class) d-none @endif" data-type="{{ \App\Constants\TypeConstants::ALBUM }}">
                    <label class="col-sm-3 col-form-label">Select Album</label>
                    <div class="col-sm-9">
                        <select class="form-control select-ajax" data-ajax--url="{{ route('backend.albums.search') }}" name="object_id">
                            @if($slide->object && count(get_object_vars(($slide->object))))
                                <option value="{{ $slide->object->id }}" selected="selected" data-artwork="{{ $slide->object->artwork }}">{{ $slide->object->title }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row slide-show-selector @if ($slide->object_type != \App\Models\Station::class) d-none @endif" data-type="{{ \App\Constants\TypeConstants::STATION }}">
                    <label class="col-sm-3 col-form-label">Select a Station</label>
                    <div class="col-sm-9">
                        <select class="form-control select-ajax" data-ajax--url="{{ route('backend.stations.search') }}" name="object_id">
                            @if($slide->object && count(get_object_vars(($slide->object))))
                                <option value="{{ $slide->object->id }}" selected="selected" data-artwork="{{ $slide->object->artwork }}">{{ $slide->object->title }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row slide-show-selector @if ($slide->object_type != \App\Models\Playlist::class) d-none @endif" data-type="{{ \App\Constants\TypeConstants::PLAYLIST }}">
                    <label class="col-sm-3 col-form-label">Select a Playlist</label>
                    <div class="col-sm-9">
                        <select class="form-control select-ajax" data-ajax--url="{{ route('backend.playlists.search') }}" name="object_id">
                            @if($slide->object && count(get_object_vars(($slide->object))))
                                <option value="{{ $slide->object->id }}" selected="selected" data-artwork="{{ $slide->object->artwork }}">{{ $slide->object->title }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row slide-show-selector @if ($slide->object_type != \App\Models\Podcast::class) d-none @endif" data-type="{{ \App\Constants\TypeConstants::PODCAST }}">
                    <label class="col-sm-3 col-form-label">Select a Podcast</label>
                    <div class="col-sm-9">
                        <select class="form-control select-ajax" data-ajax--url="{{ route('backend.podcasts.search') }}" name="object_id">
                            @if($slide->object && count(get_object_vars(($slide->object))))
                                <option value="{{ $slide->object->id }}" selected="selected" data-artwork="{{ $slide->object->artwork }}">{{ $slide->object->title }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row slide-show-selector @if ($slide->object_type != \App\Models\User::class) d-none @endif" data-type="{{ \App\Constants\TypeConstants::USER }}">
                    <label class="col-sm-3 col-form-label">Select a User</label>
                    <div class="col-sm-9">
                        <select class="form-control select-ajax" data-ajax--url="{{ route('backend.users.search') }}" name="object_id">
                            @if($slide->object && count(get_object_vars(($slide->object))))
                                <option value="{{ $slide->object->id }}" selected="selected" data-artwork="{{ $slide->object->artwork }}">{{ $slide->object->title }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Current Artwork</label>
                    <div class="col-sm-9">
                        <img class="container_photo" src="{{ $slide->artwork }}" alt="{{ $slide->title }}"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">New Artwork (min width, height 300x300, Image will be automatically crop and resize to 300/176 pixel)</label>
                    <div class="col-sm-9">
                        <input type="file" name="artwork" class="file-selector" accept="image/*">
                        <div class="input-group col-xs-12">
                            <span class="input-group-addon"><i class="fas fa-fw fa-image"></i></span>
                            <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                            <span class="input-group-btn"><button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-file"></i> Browse</button></span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Title</label>
                    <div class="col-sm-9">
                        <input name="title" class="form-control" value="{{ old('title', $slide->title) }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Title link</label>
                    <div class="col-sm-9">
                        <input name="title_link" class="form-control" value="{{ old('title_link', $slide->title_link) }}" placeholder="Optional">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Description</label>
                    <div class="col-sm-9">
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $slide->description) }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Visibility</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('is_visible', old('is_visible', $slide->is_visible)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the home page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_home', old('allow_home', $slide->allow_home)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the discover page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_discover', old('allow_discover', $slide->allow_discover)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the radio page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_radio', old('allow_radio', $slide->allow_radio)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the community page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_community', old('allow_community', $slide->allow_community)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the trending page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_trending', old('allow_trending', $slide->allow_trending)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the podcasts page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_podcasts', old('allow_podcasts', $slide->allow_podcasts)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the genres page section</label>
                    <div class="col-sm-9">
                        <select multiple="" class="form-control select2-active" name="genre[]">
                            {!! BackendService::genreSelection(implode(',', old('genre', $slideGenres))) !!}
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the radio category page section</label>
                    <div class="col-sm-9">
                        <select multiple="" class="form-control select2-active" name="radio[]">
                            {!! BackendService::radioCategorySelection(explode(',', old('radio', $slide->radio))) !!}
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the podcast category page section</label>
                    <div class="col-sm-9">
                        <select multiple="" class="form-control select2-active" name="podcast[]">
                            {!! BackendService::podcastCategorySelection(explode(',', old('podcast', $slide->podcast))) !!}
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection