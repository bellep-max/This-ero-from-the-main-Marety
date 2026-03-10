@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.songs.index') }}">Songs</a></li>
        <li class="breadcrumb-item active"> {{ $song->title }} - @foreach($song->artists as $artist)<a href="{{ route('backend.artists.edit', $artist) }}" title="{{$artist->name}}">{{$artist->name}}</a>@if(!$loop->last), @endif @endforeach</li>
    </ol>
    <div class="row">
        <div class="col-lg-12 media-info mb-3 album">
            <div class="media mb-3">
                <img class="mr-3" src="{{ $song->artwork }}" alt="{{ $song->title }}">
                <div class="media-body">
                    <h5 class="m-0">{{ $song->title }} - @foreach($song->artists as $artist)<a href="{{ route('backend.artists.edit', $artist) }}" title="{{$artist->name}}">{{$artist->name}}</a>@if(!$loop->last), @endif @endforeach</h5>
                    <h5>
                        @if($song->mp3)
                            <span class="badge badge-pill badge-dark">MP3</span>
                        @endif
                        @if($song->hd)
                            <span class="badge badge-pill badge-danger">HD</span>
                        @endif
                        @if($song->hls)
                            <span class="badge badge-pill badge-warning">HLS</span>
                        @endif
                    </h5>
                    <p class="m-0"><a href="{{ $song->permalink_url }}" class="btn btn-warning" target="_blank">Preview @if(! $song->approved) (only Moderator) @endif</a></p>
                </div>
            </div>
        </div>
        <div class="col-lg-12 media-info mb-3 song">
            <iframe width="100%" height="60" frameborder="0" src="{{ asset('share/embed/dark/song/' . $song->id) }}"></iframe>
        </div>
        <div class="col-lg-12">
            <form role="form" action="{{ route('backend.songs.update', $song) }}" enctype="multipart/form-data" method="POST">
                @method('PATCH')
                <div class="card">
                    <div class="card-header p-0 position-relative">
                        <ul class="nav">
                            <li class="nav-item"><a class="nav-link active" href="#overview" data-toggle="pill"><i class="fas fa-fw fa-newspaper"></i> Overview</a></li>
                            <li class="nav-item"><a href="#streamable" class="nav-link" data-toggle="pill"><i class="fas fa-fw fa-lock"></i> Advanced</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content mt-2" id="myTabContent">
                            <div id="overview" class="tab-pane fade show active">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Track Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="title" value="{{ old('title', $song->title) }}" required>
                                    </div>
                                </div>
                                <div class="form-group row multi-artists">
                                    <label class="col-sm-3 col-form-label">Artist(s)</label>
                                    <div class="col-sm-9">
                                        <select class="form-control multi-selector" data-ajax--url="{{ route('backend.artists.search') }}" name="artistIds[]" multiple="">
                                            @foreach ($song->artists as $index => $artist)
                                                <option value="{{ $artist->id }}" selected="selected" data-artwork="{{ $artist->artwork }}" data-title="{{ $artist->name }}">{{ $artist->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row select2-artwork">
                                    <label class="col-sm-3 col-form-label">Album(s)</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select-ajax" data-ajax--url="{{ route('backend.albums.search') }}" name="albumIds[]">
                                            @if($song->album)
                                                <option value="{{ $song->album->id }}" selected="selected" data-artwork="{{ $song->album->artwork }}"  data-title="{{ $song->album->title }}">{{ $song->album->title }}</option>
                                            @endif
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
                                    <label class="col-sm-3 col-form-label">Genres</label>
                                    <div class="col-sm-9">
                                        <select multiple="" class="form-control select2-active" name="genre[]">
                                            {!! BackendService::genreSelection($songGenres) !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Released At</label>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" class="form-control datepicker" name="released_at" value="{{ old('released_at', $song->released_at) }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Copyright</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="copyright" value="{{ old('copyright', $song->copyright) }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Youtube ID</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="youtube_id" value="{{ old('youtube_id', $song->log->youtube ?? '') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Allow to comment</label>
                                    <div class="col-sm-9">
                                        <label class="switch">
                                            {!! BackendService::makeCheckbox('allow_comments', $song->allow_comments ) !!}
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Approve this song</label>
                                    <div class="col-sm-9">
                                        <label class="switch">
                                            {!! BackendService::makeCheckbox('approved', $song->approved ) !!}
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="streamable" class="tab-pane fade">
                                <div class="alert alert-info">Note: You can configure additional song playable and downloadable parameters for different groups in this section.</div>
                                @if(cache()->has('usergroup'))
                                    @foreach(cache()->get('usergroup') as $group)
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">{{ $group->name }}</label>
                                            <div class="col-sm-9">
                                                {!! BackendService::makeDropdown([
                                                        0 => 'Group Settings',
                                                        1 => 'Playable',
                                                        2 => 'Playable And Downloadable',
                                                        3 => 'Play And Download Denied'
                                                    ], 'group_extra[' . $group->id . ']', isset($options) && isset($options[$group->id]) ? $options[$group->id] : 0)
                                                !!}
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <input type="hidden" name="file_id" value="{{ $song->file_id }}">
                        <button type="submit" class="btn btn-primary">Save</button>
                        @if(! $song->approved)
                            <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Reject</button>
                        @endif
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <a class="btn btn-danger"  href="{{ route('backend.songs.destroy', $song) }}" onclick="return confirm('Are you sure want to delete this song?')" data-toggle="tooltip" data-placement="left" title="Delete this song"><i class="fas fa-fw fa-trash"></i></a>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-12">
            <div class="mt-5 collapse" id="collapseExample">
                <form role="form" method="POST" action="{{ route('backend.songs.reject', $song) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Comment</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" rows="3" name="comment"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">Reject & Send Email to the artist</button>
                </form>
            </div>

        </div>
    </div>
@endsection