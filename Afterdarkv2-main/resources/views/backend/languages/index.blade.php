@extends('backend.index')
@section('content')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('backend.dashboard') }}">Control Panel</a>
    </li>
    <li class="breadcrumb-item active">Languages</li>
</ol>
<div class="row">
    <div class="col-lg-12">
        <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Add new language</button>
        <table class="mt-4 table table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th class="th-2action">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($languages as $language => $name)
                <tr>
                    <td>
                        <a href="{{ route('backend.languages.translations.show', $language) }}">
                            {{ trans('langcode.' . $name) }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('backend.languages.translations.show', $language) }}">
                            {{ $language }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('backend.languages.translations.show', $name) }}" class="row-button edit"><i class="fas fa-fw fa-edit"></i></a>
                        @if ($language != "en")
                            <a href="{{ route('backend.languages.destroy', $name) }}" onclick="return confirm('Are you sure want to delete this language?')" class="row-button delete"><i class="fas fa-fw fa-trash"></i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('backend.languages.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create new language</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Choose a language to create</label>
                        {!! makeDropdown(__('langcode'), "locale") !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection