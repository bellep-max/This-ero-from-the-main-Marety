@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.albums.index') }}">Albums</a></li>
        <li class="breadcrumb-item active">Create new album</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form role="form" action="" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="form-group">
                    <label>Album Name</label>
                    <input name="name" class="form-control" required>
                </div>
                <div class="form-group multi-artists">
                    <label>Artists</label>
                    <select class="form-control multi-selector" data-ajax--url="{{ route('backend.artists.search') }}" name="artistIds[]" multiple="">
                    </select>
                </div>
                <div class="form-group">
                    <label>Artwork</label>
                    <div class="input-group col-xs-12">
                        <input type="file" name="artwork" class="file-selector" accept="image/*" required>
                        <span class="input-group-addon"><i class="fas fa-fw fa-image"></i></span>
                        <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                        <span class="input-group-btn"><button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-file"></i> Browse</button></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" rows="3" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label>Genre(s)</label>
                    <select multiple="" class="form-control select2-active" name="genre[]">
                        {!! BackendService::genreSelection(0, 0) !!}
                    </select>
                </div>
                <div class="form-group">
                    <label>Copyright</label>
                    <input type="text" class="form-control" name="copyright" value="">
                </div>
                <div class="form-group">
                    <label>Released At</label>
                    <input type="text" class="form-control datepicker" name="released_at" value="" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Schedule Publish</label>
                    <input type="text" class="form-control datepicker" name="created_at" value="" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection