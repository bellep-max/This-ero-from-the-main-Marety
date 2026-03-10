@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.radios.index') }}">Radio</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.regions.index') }}">Regions</a></li>
        <li class="breadcrumb-item active">Add new region</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.regions.store') }}" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Alternative name (for SEO URL)</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="alt_name" value="{{ old('alt_name') }}" required>
                        <p class="small text-info">Used to view all content in this category. This field is required. Only latin characters are allowed.</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Visible</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('is_visible', old('is_visible')) !!}
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