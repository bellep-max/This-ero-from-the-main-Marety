@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active">Banners ({{ $banners->total() }}) - Manage promotional materials</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <a href="{{ route('backend.banners.create') }}" class="btn btn-primary">Add banner</a>
            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Manage promotional materials</h6>
                </div>
                <div class="card-body">
                    <form id="mass-action-form" method="POST" action="{{ route('backend.albums.batch') }}" class="mt-3">
                        @csrf
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th>Variable tag</th>
                                <th>Code</th>
                                <th>Start</th>
                                <th>End</th>
                                <th class="th-3action">Status</th>
                                <th class="th-3action">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($banners as $banner)
                                <tr>
                                    <td>{{ $banner->description }}</td>
                                    <td>&lcub;!! Advert::get('{!! $banner->banner_tag !!}') !!}</td>
                                    <td><pre class="m-0 text-wrap"><code>{{ $banner->code }}</code></pre></td>
                                    <td>{{ $banner->started_at }}</td>
                                    <td>{{ $banner->ended_at }}</td>
                                    <td>
                                        <span class="badge
                                            @if($banner->approved)
                                                badge-success">active
                                            @else
                                                badge-danger">disabled
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <a class="row-button upload" href="{{ route('backend.banners.disable', $banner) }}"><i class="fas fa-fw fa-ban"></i></a>
                                        <a class="row-button edit" href="{{ route('backend.banners.edit', $banner) }}"><i class="fas fa-fw fa-edit"></i></a>
                                        <a class="row-button delete" onclick="return confirm('Are you sure want to delete this banner?');" href="{{ route('backend.banners.delete', $banner) }}"><i class="fas fa-fw fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection