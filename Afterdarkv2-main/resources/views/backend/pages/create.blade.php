@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.pages.index') }}">Pages</a></li>
        <li class="breadcrumb-item active">Add new page</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.pages.store') }}" class="form-horizontal">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Title</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="title" value="{{ old('title') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">User-friendly URL</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="alt_name" value="{{ old('alt_name') }}" placeholder="Option">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Content</label>
                    <div class="col-sm-9">
                        <textarea name="content" class="form-control default editor" rows="5">{{ old('content') }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Meta title</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="meta_title" value="{{ old('meta_title') }}" placeholder="Option">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Meta description</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="meta_description" value="{{ old('meta_description') }}" placeholder="Option">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Meta keywords</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" placeholder="Option">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection