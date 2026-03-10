@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.channels.index') }}">Channels</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.channels.update', $channel) }}" class="form-horizontal">
                @csrf
                @method('PATCH')
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Title</label>
                    <div class="col-sm-9">
                        <input name="title" class="form-control" value="{{ old('title', $channel->title) }}"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Description</label>
                    <div class="col-sm-9">
                        <input name="description" class="form-control" value="{{ old('description', $channel->description) }}"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Type</label>
                    <div class="col-sm-9">
                        {!!  BackendService::makeChannelDropDown(
                            \App\Constants\TypeConstants::getChannelsList(),
                            "type",
                            old('type', $channel->type)
                            )
                        !!}
                    </div>
                </div>
                <div class="form-group slide-show-selector @if ($channel->type != \App\Constants\TypeConstants::SONG) d-none @endif" data-type="{{ \App\Constants\TypeConstants::SONG }}">
                    <label>Select a Song</label>
                    <div class="multi-artists">
                        <select class="form-control multi-selector" data-ajax--url="{{ route('backend.songs.search') }}" name="object_ids[]" multiple="">
                            @foreach ($channel->songs as $song)
                                <option value="{{ $song->id }}" selected="selected" data-artwork="{{ $song->artwork }}">{{ $song->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group slide-show-selector @if ($channel->type != \App\Constants\TypeConstants::ARTIST) d-none @endif" data-type="{{ \App\Constants\TypeConstants::ARTIST }}">
                    <label>Select an Artist</label>
                    <div class="multi-artists">
                        <select class="form-control multi-selector" data-ajax--url="{{ route('backend.artists.search') }}" name="object_ids[]" multiple="">
                            @foreach ($channel->artists as $artist)
                                <option value="{{ $artist->id }}" selected="selected" data-artwork="{{ $artist->artwork }}">{{ $artist->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group slide-show-selector @if ($channel->type != \App\Constants\TypeConstants::ALBUM) d-none @endif" data-type="{{ \App\Constants\TypeConstants::ALBUM }}">
                    <label>Select album(s)</label>
                    <div class="multi-artists">
                        <select class="form-control multi-selector" data-ajax--url="{{ route('backend.albums.search') }}" name="object_ids[]" multiple="">
                            @foreach ($channel->albums as $album)
                                <option value="{{ $album->id }}" selected="selected" data-artwork="{{ $album->artwork }}">{{ $album->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group slide-show-selector @if ($channel->type != \App\Constants\TypeConstants::STATION) d-none @endif" data-type="{{ \App\Constants\TypeConstants::STATION }}">
                    <label>Select a Station</label>
                    <div class="multi-artists">
                        <select class="form-control multi-selector" data-ajax--url="{{ route('backend.stations.search') }}" name="object_ids[]" multiple="">
                            @foreach ($channel->stations as $station)
                                <option value="{{ $station->id }}" selected="selected" data-artwork="{{ $station->artwork }}">{{ $station->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group slide-show-selector @if ($channel->type != \App\Constants\TypeConstants::PLAYLIST) d-none @endif" data-type="{{ \App\Constants\TypeConstants::PLAYLIST }}">
                    <label>Select a Playlist</label>
                    <div class="multi-artists">
                        <select class="form-control multi-selector" data-ajax--url="{{ route('backend.playlists.search') }}" name="object_ids[]" multiple="">
                            @foreach ($channel->playlists as $playlist)
                                <option value="{{ $playlist->id }}" selected="selected" data-artwork="{{ $playlist->artwork }}">{{ $playlist->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group slide-show-selector @if ($channel->type != \App\Constants\TypeConstants::PODCAST) d-none @endif" data-type="{{ \App\Constants\TypeConstants::PODCAST }}">
                    <label>Select a Podcast show</label>
                    <div class="multi-artists">
                        <select class="form-control multi-selector" data-ajax--url="{{ route('backend.podcasts.search') }}" name="object_ids[]" multiple="">
                            @foreach ($channel->podcasts as $podcast)
                                <option value="{{ $podcast->id }}" selected="selected" data-artwork="{{ $podcast->artwork }}">{{ $podcast->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group slide-show-selector @if ($channel->type != \App\Constants\TypeConstants::USER) d-none @endif" data-type="{{ \App\Constants\TypeConstants::USER }}">
                    <label>Select a User</label>
                    <div class="multi-artists">
                        <select class="form-control multi-selector" data-ajax--url="{{ route('backend.users.search') }}" name="object_ids[]" multiple="">
                            @foreach ($channel->users as $user)
                                <option value="{{ $user->id }}" selected="selected" data-artwork="{{ $user->artwork }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card mb-4 py-3 border-left-warning">
                    <div class="card-body card-small">
                        Fact: Leave the field empty to automatically load the latest item of the object type. For example you can create a channel that automatically get just released album.
                    </div>
                </div>
                <div class="card mb-4 py-3 border-left-info">
                    <div class="card-body card-small">
                        Move item (drag & drop) to re-arrange
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Meta Title</label>
                    <div class="col-sm-9">
                        <input name="meta_title" class="form-control" value="{{ old('meta_title', $channel->meta_title) }}"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Meta description</label>
                    <div class="col-sm-9">
                        <input name="meta_description" class="form-control" value="{{ old('meta_description', $channel->meta_description) }}"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Visibility</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('is_visible', old('is_visible', $channel->is_visible)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the home page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_home', old('allow_home', $channel->allow_home)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the discover page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_discover', old('allow_discover', $channel->allow_discover)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the radio page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_radio', old('allow_radio', $channel->allow_radio)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the community page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_community', old('allow_community', $channel->allow_community)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the podcasts page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_podcasts', old('allow_podcasts', $channel->allow_podcasts)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the trending page section</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('allow_trending', old('allow_trending', $channel->allow_trending)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the genres page section</label>
                    <div class="col-sm-9">
                        <select multiple="" class="form-control select2-active" name="genre[]">
                            {!! BackendService::genreSelection(explode(',', old('genre', $channel->genre))) !!}
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the radio category page section</label>
                    <div class="col-sm-9">
                        <select multiple="" class="form-control select2-active" name="radio_categories[]">
                            {!! BackendService::radioCategorySelection(explode(',', old('radio', $channel->radio)))  !!}
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Publish on the podcast category page section</label>
                    <div class="col-sm-9">
                        <select multiple="" class="form-control select2-active" name="podcast_categories[]">
                            {!! BackendService::podcastCategorySelection(explode(',', old('podcast', $channel->podcast)))  !!}
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection