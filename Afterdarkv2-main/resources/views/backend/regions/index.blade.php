@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.radios.index') }}">Radio</a></li>
        <li class="breadcrumb-item active">Regions ({{ $regions->total() }})</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <a href="{{ route('backend.regions.create') }}" class="btn btn-primary">Add new region</a>
            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Manage radio regions</h6>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover mt-3">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Visible</th>
                            <th class="th-2action">Action</th>
                        </tr>
                        </thead>
                        @foreach ($regions as $index => $region)
                            <tr>
                                <td><a href="{{ route('backend.regions.edit', $region) }}">{{ $region->name }}</a></td>
                                <td>
                                    @if($region->is_visible)
                                        <span class="badge badge-success badge-pill">Yes</span>
                                    @else
                                        <span class="badge badge-danger badge-pill">No</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('backend.regions.edit', $region) }}" class="row-button edit"><i class="fas fa-fw fa-edit"></i></a>
                                    <a href="{{ route('backend.regions.destroy', $region) }}" onclick="return confirm('Are you sure want to delete this region?')" class="row-button delete"><i class="fas fa-fw fa-trash"></i></a>
                                </td>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection