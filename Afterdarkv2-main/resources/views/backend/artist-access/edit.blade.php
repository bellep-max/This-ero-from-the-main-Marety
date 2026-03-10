@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.artist.access.index') }}">Artist Claim Requests</a></li>
        <li class="breadcrumb-item active">{{ $artist_request->artist_name }}</li>
    </ol>
        <div class="main-section text-center">
            <div class="row">
                <div class="col-lg-12 col-sm-12 col-12 profile-header"></div>
            </div>
            <div class="row user-detail">
                <div class="col-lg-12 col-sm-12 col-12">
                    @if ($artist_request->artist)
                        <img src="{{ $artist_request->artist->artwork }}" class="rounded-circle img-thumbnail">
                    @else
                        <img src="{{ $artist_request->user->artwork }}" class="rounded-circle img-thumbnail">
                    @endif
                    <h5>{{ $artist_request->artist_name }}</h5>
                    <p class="text-muted">Artist</p>
                    <table class="mt-4 table table-striped">
                        <tbody>
                        <tr>
                            <td>Requested by</td>
                            <td>
                                <a href="{{ route('backend.users.edit', ['user' => $artist_request->user]) }}">
                                    <img class="img-square-24" src="{{ $artist_request->user->artwork }}">
                                    <span>{{ $artist_request->user->name }}</span>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Requester's affiliation</td>
                            <td>
                                {{ $artist_request->affiliation }}
                            </td>
                        </tr>
                        <tr>
                            <td>Message to admin</td>
                            <td>
                                {{ $artist_request->message }}
                            </td>
                        </tr>
                        <tr>
                            <td>E-Mail</td>
                            <td>{{ $artist_request->user->email }}</td>
                        </tr>
                        <tr>
                            <td>Phone contact</td>
                            <td>{{ $artist_request->phone }} (ext: {{ $artist_request->ext }})</td>
                        </tr>
                        <tr>
                            <td>Requested date</td>
                            <td>{{ $artist_request->created_at }}</td>
                        </tr>
                        <tr>
                            <td>Facebook profile</td>
                            <td>
                                @if ($artist_request->user->connects->firstWhere('service', 'facebook'))
                                    <img class="img-square-24" src="{{ $artist_request->user->connects->firstWhere('service', 'facebook')->provider_artwork }}">
                                    <span class="badge badge-success"> Verified</span>
                                @else
                                    <span class="badge badge-warning">Unverified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Twitter profile</td>
                            <td>
                                @if($artist_request->user->connects->firstWhere('service', 'twitter'))
                                    <img class="img-square-24" src="{{ $artist_request->user->connects->firstWhere('service', 'facebook')->provider_artwork }}">
                                    <span class="badge badge-success"> Verified</span>
                                @else
                                    <span class="badge badge-warning">Unverified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Is approved?</td>
                            <td>
                                @if($artist_request->approved)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-warning">No</span>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <form role="form" method="post" action="">
                    @csrf
                    <button type="submit" class="btn btn-success">Approve</button>
                    <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Reject</button>
                </form>
                <div class="m-5 collapse" id="collapseExample">
                    <form role="form" method="post" action="">
                        @csrf
                        <input type="hidden" name="reject" value="1">
                        <div class="form-group">
                            <label>Comment</label>
                            <textarea class="form-control" rows="3" name="comment"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">Reject &amp; Send Email to the artist</button>
                    </form>
                </div>
            </div>
        </div>
@endsection