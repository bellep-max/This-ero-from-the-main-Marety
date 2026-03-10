@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.podcasts.index') }}">Podcasts</a></li>
        <li class="breadcrumb-item active">{{ isset($podcast) ? $podcast->title : ' Add new podcast' }}</li>
    </ol>
    @if(isset($podcast))
        <div class="row col-lg-12 media-info mb-3 podcast">
            <div class="media">
                <img class="mr-3" src="{{ $podcast->artwork }}">
                <div class="media-body">
                    <h5 class="mt-0">{{ $podcast->title }}</h5>
                    <p>{{ $podcast->description }}</p>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.podcasts.update', $podcast) }}" class="form-horizontal" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="title" value="{{ old('title', $podcast->title) }}" required>
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
                <div class="form-group row multi-artists">
                    <label class="col-sm-3 col-form-label">Artists</label>
                    <div class="col-sm-9">
                        <select class="form-control multi-selector" data-ajax--url="{{ route('api.search.artist') }}" name="artist_id">
                            @if ($podcast->artist)
                                <option value="{{ $podcast->artist->id }}" selected="selected" data-artwork="{{ $podcast->artist->artwork }}" data-title="{{ $podcast->artist->name }}">{{ $podcast->artist->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Description</label>
                    <div class="col-sm-9">
                        <textarea name="description" class="form-control" rows="5">{{ old('description', $podcast->description) }}</textarea>
                    </div>
                </div>
                <div class="form-group row filter-country">
                    <label class="col-sm-3 col-form-label">Country</label>
                    <div class="col-sm-9">
                        <div class="form-inline">
                            <div class="filter-country">
                                {!! \App\Helpers\Helper::makeCountryDropdown('country_id', 'form-control select2-active filter-country-select', old('country_id', $podcast->country_id)) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row filter-language @if(! $podcast->country_id) d-none @endif">
                    <label class="col-sm-3 col-form-label">Language</label>
                    <div class="col-sm-9">
                        <div class="form-inline filter-language-select">
                            @if ($podcast->country_id || $podcast->language_id)
                                {!! \App\Helpers\Helper::makeCountryLanguageDropDown($podcast->country_id, 'language_id', 'form-control select2-active', old('language_id', $podcast->language_id)) !!}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Podcast RSS (option - the system will automatically fetch the rss everyday for getting new episode)</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="url" name="rss_feed_url" value="{{ old('rss_feed_url', $podcast->rss_feed_url) }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection