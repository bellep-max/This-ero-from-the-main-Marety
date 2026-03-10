@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.posts.index') }}">Posts</a></li>
        <li class="breadcrumb-item active">Add new article</li>
    </ol>
    <form method="POST" action="{{ route('backend.posts.store') }}" class="article-form form-horizontal" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-sm-8 col-12">
                <div class="card">
                    <div class="card-header p-0 position-relative">
                        <ul class="nav">
                            <li class="nav-item"><a class="nav-link active" href="#news" data-toggle="pill"><i class="fas fa-fw fa-newspaper"></i> News</a></li>
                            <li class="nav-item"><a href="#advanced" class="nav-link" data-toggle="pill"><i class="fas fa-fw fa-cog"></i> Advanced</a></li>
                            <li class="nav-item"><a href="#voting" class="nav-link" data-toggle="pill"><i class="fas fa-fw fa-poll"></i> Voting</a></li>
                            <li class="nav-item"><a href="#access" class="nav-link" data-toggle="pill"><i class="fas fa-fw fa-lock"></i> Access</a></li>
                        </ul>
                        <a class="btn btn-link post-fullscreen"><i class="fas fa-expand-arrows-alt"></i></a>
                    </div>
                    <div class="card-body">
                        <div class="tab-content mt-2" id="myTabContent">
                            <div id="news" class="tab-pane fade show active">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Title</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="title" value="{{ old('title') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Publish</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="datetime-local" name="published_at" value="{{ old('published_at') }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Categories</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2-active" multiple="" name="category[]">
                                            {!! \App\Services\Backend\BackendService::categorySelection(old('category')) !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Short Content</label>
                                    <textarea data-filemanager-plugin-path="{{ asset('backend/js/filePlugin.js') }}" data-responsive-filemanager-plugin-path="{{ asset('backend/js/tinyPlugin.js') }}" data-external-filemanager-path="{{ route('backend.posts.media.index')}}" name="short_content" class="form-control post editor" rows="5">{{ old('short_content') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Full Content</label>
                                    <textarea data-filemanager-plugin-path="{{ asset('backend/js/filePlugin.js') }}" data-responsive-filemanager-plugin-path="{{ asset('backend/js/tinyPlugin.js') }}" data-external-filemanager-path="{{ route('backend.posts.media.index')}}" name="full_content" class="form-control post editor" rows="20">{{ old('full_content') }}</textarea>
                                </div>
                            </div>
                            <div id="advanced" class="tab-pane fade">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">User-friendly URL</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="alt_name" value="{{ old('alt_name') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Tags</label>
                                    <div class="col-sm-9">
                                        {!! \App\Services\Backend\BackendService::makeTagSelector('tags[]', old('tags')) !!}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Validity term</label>
                                    <div class="col-sm-9">
                                        <div class="form-inline">
                                            <div class="input-group mr-3">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Date</div>
                                                </div>
                                                <input type="datetime-local" class="form-control datetimepicker-with-form" name="log_expires" autocomplete="off" value="{{ old('log_expires') }}">
                                            </div>
                                            <select name="log_action" class="form-control select2-active">
                                                <option value="">--- Choose the action ---</option>
                                                @foreach(\App\Constants\LogActionConstants::getList() as $value => $name)
                                                    <option value="{{ $value }}" @if(old('log_action') == $value) selected @endif>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info">Manual addition of meta tags for the article. Meta tags for this article can be added manually or generated automatically. Leave the fields blank if you want meta tags to be generated automatically.</div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Title meta tag</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="meta_title" value="{{ old('meta_title') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Description meta tag</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="meta_description" value="{{ old('title') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Keywords meta tag</label>
                                    <div class="col-sm-9">
                                        {!! \App\Services\Backend\BackendService::makeTagSelector('meta_keywords[]', old('meta_keywords')) !!}
                                    </div>
                                </div>
                            </div>
                            <div id="voting" class="tab-pane fade">
                                <div class="alert alert-info">Adding voting to an article is optional. Just leave it blank if you do not want to add a voting to this article.</div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Voting question</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="poll_title" value="{{ old('poll_title') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Voting answer<span class="text-muted small">Each new line is a new answer option.</span></label>
                                    <div class="col-sm-9">
                                        <textarea name="poll_answers" class="form-control" rows="5">{{ old('poll_answers') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-4 col-form-label">Allow multiple selection</label>
                                    <div class="col-sm-9 col-8">
                                        <label class="switch">
                                            {!! \App\Services\Backend\BackendService::makeCheckbox('poll_multiple', old('poll_multiple')) !!}
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">End at</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="datetime-local" name="poll_ended_at" value="{{ old('poll_ended_at') }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-4 col-form-label">Visibility</label>
                                    <div class="col-sm-9 col-8">
                                        <label class="switch">
                                            {!! \App\Services\Backend\BackendService::makeCheckbox('poll_visibility', old('poll_visibility')) !!}
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="access" class="tab-pane fade">
                                @if(cache()->has('usergroup'))
                                    @foreach(cache()->get('usergroup') as $group)
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">{{ $group->name }}</label>
                                            <div class="col-sm-9">
                                                {!! \App\Services\Backend\BackendService::makeDropdown(
                                                        \App\Constants\PermissionConstants::getList(),
                                                        'group_extra[' . $group->id . ']',
                                                        isset($options) && isset($options[$group->id]) ? $options[$group->id] : 0
                                                    )
                                                !!}
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="alert alert-info">Note: You can configure additional news access parameters for different groups in this section, but these options are valid only for the full articles.</div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary article-submit">Save</button>
                        <button type="reset" class="btn btn-info article-reset">Reset</button>

                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Options</h6>
                    </div>
                    <div class="card-body">
                        <div class="featured-image">
                            <div id="featured-image" class="post-set-featured-image">
                                <span class="set">Set featured image</span>
                                <img id="artwork" class="d-none" src="">
                                <div class="post-remove-featured-image d-none">Remove featured image</div>
                            </div>
                            <input id="artwork_picker" type="file" name="artwork" accept="image/*"/>
                            <input id="remove_artwork" type="hidden" name="remove_artwork"/>
                        </div>
                        <div class="form-group row mt-5">
                            <div class="col-sm-12">
                                <label class="switch">
                                    {!! \App\Services\Backend\BackendService::makeCheckbox('is_visible', old('is_visible')) !!}
                                    <span class="slider round"></span>
                                </label>
                                <label class="pl-6 col-form-label">Visibility</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label class="switch">
                                    {!! \App\Services\Backend\BackendService::makeCheckbox('approved', old('approved')) !!}
                                    <span class="slider round"></span>
                                </label>
                                <label class="pl-6 col-form-label">Approve this article</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label class="switch">
                                    {!! \App\Services\Backend\BackendService::makeCheckbox('allow_main', old('allow_main')) !!}
                                    <span class="slider round"></span>
                                </label>
                                <label class="pl-6 col-form-label">Publish on the main page section</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label class="switch">
                                    {!! \App\Services\Backend\BackendService::makeCheckbox('fixed', old('fixed')) !!}
                                    <span class="slider round"></span>
                                </label>
                                <label class="pl-6 col-form-label">Stick to the top of the news</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label class="switch">
                                    {!! \App\Services\Backend\BackendService::makeCheckbox('allow_comments', old('allow_comments')) !!}
                                    <span class="slider round"></span>
                                </label>
                                <label class="pl-6 col-form-label">Allow comments</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label class="switch">
                                    {!! \App\Services\Backend\BackendService::makeCheckbox('disable_index', old('disable_index')) !!}
                                    <span class="slider round"></span>
                                </label>
                                <label class="pl-6 col-form-label">Disable page indexation for the search engines</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection