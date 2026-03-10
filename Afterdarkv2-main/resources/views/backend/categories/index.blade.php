@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active">Create and Manage News Categories</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <a href="{{ route('backend.categories.create') }}" class="btn btn-primary">Add category</a>
            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary float-left">Categories</h6>
                </div>
                <div class="card-body">
                    <form role="form" method="POST" action="{{ route('backend.categories.sort') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="dd nestable-with-handle">
                            <ol class="dd-list">
                                {!! $nestable_categories !!}
                            </ol>
                        </div>
                        <input type="hidden" name="list" id="cartSortList">
                        <button type="submit" class="btn btn-primary mt-4">Save sort order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection