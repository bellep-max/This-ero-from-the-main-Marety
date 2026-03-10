@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active">Radio</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <a class="btn btn-primary" href="{{ route('backend.radios.create') }}">Add new Radio category</a>
            <form method="POST" action="{{ route('backend.radios.sort') }}">
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
                    @foreach ($radio_categories as $radio)
                        <tr>
                            <td><i class="handle fas fa-fw fa-arrows-alt"></i></td>
                            <td><input type="hidden" name="radioIds[]" value="{{ $radio->id }}"></td>
                            <td><img src="{{ $radio->artwork }}" alt="{{ $radio->name }}" width="100" height="30"></td>
                            <td class="desktop"><a href="{{ route('backend.radios.edit', $radio) }}">{{ $radio->name }}</a></td>
                            <td>{{ $radio->alt_name }}</td>
                            <td>
                                <a href="{{ route('backend.radios.edit', $radio) }}" class="row-button edit"><i class="fas fa-fw fa-edit"></i></a>
                                <a href="{{ route('backend.radios.destroy', $radio) }}" class="row-button delete" onclick="return confirm('Are you sure?')"><i class="fas fa-fw fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <button type="submit" class="btn btn-primary mt-4">Save sort order</button>
            </form>
        </div>
    </div>
@endsection