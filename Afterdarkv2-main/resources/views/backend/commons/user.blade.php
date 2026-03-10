@php use App\Constants\RoleConstants; @endphp
@foreach ($users as $index => $user)
    <tr @if($user->group_id &&  ($user->group_id == RoleConstants::ADMIN && auth()->user()->group_id != RoleConstants::ADMIN)) class="overlay-permission"
        @endif @if($user->ban) class="overlay-banned" @endif>
        <td class="td-image">
            <div class="play" data-id="{{ $user->id }}" data-type="user"><img src="{{ $user->artwork }}"
                                                                              alt="{{ $user->username }}"/></div>
        </td>
        <td><a href="{{ route('backend.users.edit', $user) }}">{{ $user->name }}</a></td>
        <td class="desktop">{{ $user->username }}</td>
        <td class="desktop"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
        @if(\App\Models\Group::getValue('admin_roles'))
            @if(isset($user->group->name))
                <td class="desktop"><a data-toggle="tooltip" title="Edit group: {{ $user->group->name }}"
                                       href="{{ route('backend.groups.edit', $user->group) }}">{{ $user->group->name }}</a>
                </td>
            @else
                <td class="desktop"><span class="badge badge-warning">missing</span></td>
            @endif
        @else
            @if(isset($user->group->name))
                <td class="desktop">
                    <span data-toggle="tooltip"
                          title="Group: {{ $user->group->name }}"
                          href="{{ route('backend.groups.edit', $user->group) }}"
                    >
                        {{ $user->group->name }}
                    </span>
                </td>
            @else
                <td class="desktop"><span class="badge badge-warning">missing</span></td>
            @endif
        @endif
        <td class="desktop">{{ $user->created_at->diffForHumans() }}</td>
        @if($user->last_activity)
            <td class="desktop">
                {{ $user->last_activity->diffForHumans() }}
            </td>
        @else
            <td class="desktop">Unknown</td>
        @endif
        <td class="text-center desktop">{{ $user->post_count }}</td>
        <td class="text-center desktop">{{ $user->song_count }}</td>
        <td class="text-center desktop">{{ $user->comment_count }}</td>
        <td class="desktop">
            <a class="row-button edit" href="{{ route('backend.users.edit', $user) }}"><i class="fas fa-fw fa-edit"></i></a>
            <a class="row-button delete"
               onclick="var r=confirm('By deleting this user, all song which linked to this user will be deleted too, Are you sure want to delete this user?');if (r==true){window.location='{{ route('backend.users.destroy', ['user' => $user->id]) }}'}; return false;"
               href="{{ route('backend.users.destroy', $user) }}"><i class="fas fa-fw fa-trash"></i></a>
        </td>
        <td>
            @if($user->group_id || $user->group_id != RoleConstants::ADMIN || auth()->user()->group_id == RoleConstants::ADMIN)
                <label class="engine-checkbox">
                    <input name="ids[]" class="multi-check-box" type="checkbox" value="{{ $user->id }}">
                    <span class="checkmark"></span>
                </label>
            @endif
        </td>
    </tr>
@endforeach
