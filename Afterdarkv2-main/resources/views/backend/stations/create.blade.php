@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.stations.index') }}">Stations</a></li>
        <li class="breadcrumb-item active">Add new Station</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.stations.store') }}" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="title" value="{{ old('title') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Category</label>
                    <div class="col-sm-9">
                        <select multiple="" class="form-control select2-active" name="category[]">
                            {!! BackendService::radioCategorySelection(old('category')) !!}
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Edit artwork</label>
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
                        <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="form-group row filter-country">
                    <label class="col-sm-3 col-form-label">Country/City</label>
                    <div class="col-sm-9 form-inline">
                        <div class="filter-country">
                            {!! BackendService::makeCountryDropdown('country_id', 'form-control select2-active filter-country-select', old('country_id')) !!}
                        </div>
                        <div class="ml-3 d-none filter-city">
                            {!! BackendService::makeCityDropDown('', 'city_id', 'form-control select2-active filter-city-select', old('city_id')) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Stream url (only audio accepted)</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="url" name="stream_url" value="{{ old('stream_url') }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection