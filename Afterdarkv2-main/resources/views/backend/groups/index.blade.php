@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active">User Groups</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.groups.store') }}">
                @csrf
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="name" placeholder="Create new group and role" autocomplete="off">
                    <span class="input-group-append">
			        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Start</button>
			    </span>
                </div>
            </form>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="th-2action">ID</th>
                    <th>Group</th>
                    <th class="desktop th-4action">Member(s)</th>
                    <th class="th-2action">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($groups as $group)
                    <tr>
                        <td>{{ $group->id }}</td>
                        <td>
                            <a href="{{ route('backend.groups.edit', $group) }}">{{ $group->name }}</a>
                            @isset($group->permissions['admin_access'])
                                <span class="text-danger">(has access to the Administration Panel)</span>
                            @endisset
                        </td>
                        @if ($group->id !== \App\Constants\RoleConstants::GUEST)
                            <td class="desktop">{{ $group->users()->count() }}</td>
                        @else
                            <td class="desktop">-</td>
                        @endif
                        <td>
                            <a href="{{ route('backend.groups.edit', $group) }}" class="row-button edit"><i class="fas fa-fw fa-edit"></i></a>
                            @if (!in_array($group->id, \App\Constants\RoleConstants::getMainRoles()))
                                <a href="{{ route('backend.groups.destroy', $group) }}" class="row-button delete" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-fw fa-trash"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
