@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.stations.index') }}">Stations</a></li>
        <li class="breadcrumb-item active">{{ isset($station) ? $station->title : ' Add new Station' }}</li>
    </ol>
    @if(isset($station))
        <div class="row col-lg-12 media-info mb-3 station">
            <div class="media">
                <img class="mr-3" src="{{ $station->artwork }}" alt="{{ $station->title }}">
                <div class="media-body">
                    <h5 class="mt-0">{{ $station->title }}</h5>
                    <p>{{ $station->description }}</p>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.stations.update', $station) }}" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="title" value="{{ old('title', $station->title) }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Category</label>
                    <div class="col-sm-9">
                        <select multiple="" class="form-control select2-active" name="category[]">
                            {!! BackendService::radioCategorySelection(old('category', $station->category)) !!}
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
                        <textarea name="description" class="form-control" rows="5">{{ old('description', $station->description) }}</textarea>
                    </div>
                </div>
                <div class="form-group row filter-country">
                    <label class="col-sm-3 col-form-label">Country/City</label>
                    <div class="form-inline col-sm-9">
                        <div class="filter-country">
                            {!! BackendService::makeCountryDropdown('country_id', 'form-control select2-active filter-country-select', old('country_id', $station->country_code)) !!}
                        </div>
                        <div class="ml-3 @if(! $station->country_id) d-none @endif filter-city">
                            @if($station->country_id || $station->city_id)
                                {!! BackendService::makeCityDropDown($station->country_id, 'city_id', 'form-control select2-active filter-city-select', old('city_id', $station->city_id)) !!}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group row filter-language @if(! $station->country_code) d-none @endif">
                    <label class="col-sm-3 col-form-label">Language</label>
                    <div class="col-sm-9">
                        <div class="form-inline filter-language-select">
                            @if ($station->country_code || $station->language_id)
                                {!! BackendService::makeCountryLanguageDropDown($station->country_code, 'language_id', 'form-control select2-active', old('language_id', $station->language_id)) !!}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Stream url (only audio accepted)</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="url" name="stream_url" value="{{ old('stream_url', $station->stream_url) }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection