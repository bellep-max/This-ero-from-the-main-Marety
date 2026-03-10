@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active">Podcast Categories</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <a class="btn btn-primary" href="{{ route('backend.podcast-categories.create') }}">Add new Podcast category</a>
            <form method="post" action="{{ route('backend.podcast-categories.sort') }}">
                @csrf
                <table class="mt-4 table table-striped table-sortable">
                    <thead>
                    <tr>
                        <th class="th-handle"></th>
                        <th class="th-priority">Priority</th>
                        <th class="th-wide-image"></th>
                        <th>Name</th>
                        <th class="desktop">User-friendly URL</th>
                        <th class="th-3action">Action</th>
                    </tr>
                    </thead>
                    @foreach ($categories as $index => $category)
                        <tr>
                            <td><i class="handle fas fa-fw fa-arrows-alt"></i></td>
                            <td><input type="hidden" name="categoryIds[]" value="{{ $category->id }}"></td>
                            <td><img src="{{ $category->artwork }}" width="100" height="30"></td>
                            <td class="desktop"><a href="{{ route('backend.podcast-categories.edit', $category) }}">{{ $category->name }}</a></td>
                            <td>{{ $category->alt_name }}</td>
                            <td>
                                <a href="{{ route('backend.podcast-categories.edit', $category) }}" class="row-button edit"><i class="fas fa-fw fa-edit"></i></a>
                                <a href="{{ route('backend.podcast-categories.destroy', $category) }}" class="row-button delete" onclick="return confirm('Are you sure?')"><i class="fas fa-fw fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <button type="submit" class="btn btn-primary mt-4">Save sort order</button>
            </form>
        </div>
    </div>
@endsection