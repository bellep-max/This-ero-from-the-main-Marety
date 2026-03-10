@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item"><a href="{{ route('backend.playlists.index') }}">Playlists</a></li>
        <li class="breadcrumb-item active">{{ $playlist->title }}</li>
    </ol>
    <div class="row col-lg-12 media-info mb-3 playlist">
        <div class="media">
            <img class="mr-3" src="{{ $playlist->artwork }}" alt="{{ $playlist->title }}">
            <div class="media-body">
                <h5 class="mt-0">{{ $playlist->title }}</h5>
                <p>Created by: {{ $playlist->user?->name }}</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form role="form" method="POST" action="{{ route('backend.playlists.update', $playlist) }}" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="title" value="{{ old('title', $playlist->title) }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Artwork (min width, height 300x300)</label>
                    <div class="input-group col-sm-9">
                        <input type="file" name="artwork" class="file-selector" accept="image/*">
                        <span class="input-group-addon"><i class="fas fa-fw fa-image"></i></span>
                        <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                        <span class="input-group-btn">
                            <button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-file"></i> Browse</button>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Description</label>
                    <div class="col-sm-9">
                        <textarea name="description" class="form-control" rows="5">{{ old('description', $playlist->description) }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Genres</label>
                    <div class="col-sm-9">
                        <select multiple="" class="form-control select2-active" name="genre[]">
                            {!! BackendService::genreSelection(old('genre') ?? $playlistGenres) !!}
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection