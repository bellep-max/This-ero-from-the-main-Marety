@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.categories.index') }}">Categories</a></li>
        <li class="breadcrumb-item active">{{ $category->name }}</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form role="form" method="POST" action="{{ route('backend.categories.update', $category) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3">Name
                        <span class="small">The name is how it appears on your site.</span></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="name" value="{{ old('name', $category->name) }}"
                               required>
                    </div>
                </div>
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3">Alternative name</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="alt_name"
                               value="{{ old('alt_name', $category->alt_name) }}">
                    </div>
                </div>
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3">Description</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" rows="2"
                                  name="description">{{ old('description', $category->description) }}</textarea>
                    </div>
                </div>

                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3">Meta title</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="meta_title"
                               value="{{ old('meta_title', $category->meta_title) }}" placeholder="Option">
                    </div>
                </div>
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3 col-form-label">Meta description</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" rows="2" name="meta_description"
                                  placeholder="Option">{{ old('meta_description', $category->meta_description) }}</textarea>
                    </div>
                </div>
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3 col-form-label">Meta keywords</label>
                    <div class="col-sm-9">
                        {!! BackendService::makeTagSelector('meta_keywords[]', old('meta_keywords', $category->meta_keywords)) !!}
                    </div>
                </div>
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3">Criterion of the news sort</label>
                    <div class="col-sm-9">
                        {!! BackendService::makeDropdown(\App\Constants\NewsSortConstants::getFullList(), "news_sort", old('news_sort', $category->meta_description) ) !!}
                    </div>
                </div>
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3">Show news published in the sub-categories</label>
                    <div class="col-sm-9">
                        {!! BackendService::makeDropdown(\App\Constants\NewsSubcategorySortConstants::getFullList(), "show_sub", old('show_sub', $category->meta_description) ) !!}
                    </div>
                </div>
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3">Disable publishing on the homepage</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('disable_main', old('disable_main', $category->disable_main)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3">Disable commenting in articles</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('disable_comments', old('disable_comments', $category->disable_comments)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                    <label class="col-sm-3">Exclude from site search</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('disable_search', old('disable_search', $category->disable_search)) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row border-bottom mb-4 pt-3 pb-3">
                    <label class="col-sm-3">Artwork (min width, height 300x300)</label>
                    <div class="col-sm-9">
                        <div class="input-group col-xs-12">
                            <input type="file" name="artwork" class="file-selector" accept="image/*">
                            <span class="input-group-addon"><i class="fas fa-fw fa-image"></i></span>
                            <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                            <span class="input-group-btn"><button class="browse btn btn-primary input-lg" type="button"><i
                                            class="fas fa-fw fa-file"></i> Browse</button></span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection