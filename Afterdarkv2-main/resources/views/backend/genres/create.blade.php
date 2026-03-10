@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.genres.index') }}">Genres</a></li>
        <li class="breadcrumb-item active">Add new genre</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.genres.store') }}" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">User-friendly URL</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="alt_name" value="{{ old('alt_name') }}" placeholder="Option">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Description</label>
                    <div class="col-sm-9">
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Meta title</label>
                    <div class="col-sm-9">
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" placeholder="Option">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Meta description</label>
                    <div class="col-sm-9">
                        <textarea name="meta_description" class="form-control" rows="2" placeholder="Option">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Meta keywords</label>
                    <div class="col-sm-9">
                        {!! BackendService::makeTagSelector('meta_keywords[]', old('meta_keywords')) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Artwork (min width, height 300x300, Image will be automatically crop and resize to 300/176 pixel)</label>
                    <div class="input-group col-sm-9">
                        <input type="file" name="artwork" class="file-selector" accept="image/*" required>
                        <span class="input-group-addon"><i class="fas fa-fw fa-image"></i></span>
                        <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                        <span class="input-group-btn">
                            <button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-file"></i> Browse</button>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Show on Discover page</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('discover', old('discover')) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection