@php use App\Services\Backend\BackendService; @endphp
@foreach ($comments as $index => $comment)
    @if(isset($comment->object) && !empty($comment->user))
        <tr>
            <td class="td-image">
                <a href="{{ route('backend.users.edit', $comment->user) }}"><img src="{{ $comment->user->artwork }}" alt="{{ $comment->user->username }}"/></a>
            </td>
            <td class="td-author desktop">
                <a href="{{ route('backend.users.edit', $comment->user) }}">{{ $comment->user->name }}</a>
            </td>
            <td class="w-25"><p class="text-wrap text-break">{{ $comment->content }}</p></td>
            <td class="td-object desktop">
                <a href="{{ $comment->object->permalink_url }}" target="_blank">{{ $comment->commentable_type }}</a>
            </td>
            <td class="td-created-at desktop">{{ $comment->created_at->diffForHumans() }}</td>
            <td class="td-approve">
                <label class="switch">
                    {!! BackendService::makeCheckbox('approve', $comment->approved) !!}
                    <span class="slider round"></span>
                </label>
            </td>
            <td class="desktop">
                <a class="row-button edit" href="{{ route('backend.comments.edit', $comment) }}" data-toggle="tooltip" data-placement="left" title="Edit this comment"><i class="fas fa-fw fa-edit"></i></a>
                <a class="row-button delete"  href="{{ route('backend.comments.destroy', $comment) }}" onclick="return confirm('Are you sure want to delete this comment?')" data-toggle="tooltip" data-placement="left" title="Delete this comment"><i class="fas fa-fw fa-trash"></i></a>
            </td>
        </tr>
    @endif
@endforeach
