@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.cities.index') }}">Cities</a></li>
        <li class="breadcrumb-item active">{{ $city->name }}</li>
    </ol>
    <div class="row col-lg-12 media-info mb-3 country">
        <div class="media">
            <img class="mr-3" src="{{ $city->artwork }}" alt="{{ $city->name }}">
            <div class="media-body">
                <h5 class="mt-0">{{ $city->name }}</h5>
                <p>{{ $city->continent }}</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.cities.update', $city) }}" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="name" value="{{ old('name', $city->name) }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Country</label>
                    <div class="col-sm-9">
                        {!! BackendService::makeCountryDropdown('country_id', 'form-control slide-show-type', old('country_id', $city->country_id)) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Fixed</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('fixed', old('fixed', $city->fixed)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Artwork</label>
                    <div class="input-group col-sm-9">
                        <input type="file" name="artwork" class="file-selector" accept="image/*">
                        <span class="input-group-addon"><i class="fas fa-fw fa-image"></i></span>
                        <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                        <span class="input-group-btn">
                            <button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-file"></i> Browse</button>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection