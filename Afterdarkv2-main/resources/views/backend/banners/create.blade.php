@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.banners.index') }}">Banners</a></li>
        <li class="breadcrumb-item active">Add new banners</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.banners.store') }}" class="form-horizontal">
                @csrf
                <div class="form-group">
                    <label>The name of the banner (latin characters):</label>
                    <input class="form-control" type="text" name="banner_tag" value="{{ old('banner_tag') }}" required>
                </div>
                <div class="form-group">
                    <label>Description of the banner:</label>
                    <input class="form-control" type="text" name="description" value="{{ old('description') }}" required>
                </div>
                <div class="form-group">
                    <label>Start Date:</label>
                    <input class="form-control datetimepicker-with-form" type="datetime-local" name="started_at" value="{{ old('started_at') }}" placeholder="Pick a date" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>End Date:</label>
                    <input class="form-control datetimepicker-with-form" type="datetime-local" name="ended_at" value="{{ old('ended_at') }}" placeholder="Pick a date" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Banner code:</label>
                    <textarea name="code" class="form-control editor" rows="5">{{ old('code') }}</textarea>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="customCheck" name="approved">
                        <label class="custom-control-label" for="customCheck">Disabled</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection