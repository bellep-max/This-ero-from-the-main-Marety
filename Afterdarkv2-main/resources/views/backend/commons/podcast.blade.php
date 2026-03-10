<tr>
    <td class="td-image">
        <img class="media-object img-square-24" src="{{ $podcast->artwork }}">
    </td>
    <td><a href="{{ route('backend.podcasts.edit', $podcast) }}">{{ \Illuminate\Support\Str::limit($podcast->title, 25) }}</a></td>
    @isset($podcast->user)
        <td><a href="{{ route('backend.users.edit', $podcast->user) }}">{{ $podcast->user->username }}</a></td>
    @else
        <td>Unknown</td>
    @endif
    @isset($podcast->artist)
        <td><a href="{{ route('backend.artists.edit', $podcast->artist) }}">{{ $podcast->artist->name }}</a></td>
    @else
        <td>Unknown</td>
    @endif
    <td class="desktop">
        @foreach($podcast->categories as $category)
            <a href="{{ route('backend.categories.edit', $category) }}" title="{{ $category->name }}">{{$category->name}}</a>
            @if(!$loop->last), @endif
        @endforeach
    </td>
    <td>{{ \Illuminate\Support\Str::limit($podcast->description, 25) }}</td>
    <td>{{ $podcast->episodes_count }}</td>
    <td>{{ $podcast->loves }}</td>
    <td>
        <a href="{{ route('backend.podcasts.edit', $podcast) }}" class="row-button edit"><i class="fas fa-fw fa-edit"></i></a>
        <a href="{{ route('backend.podcasts.episodes.show', $podcast) }}"><i class="fas fa-fw fa-list"></i></a>
        <a href="{{ route('backend.podcasts.destroy', $podcast) }}" onclick="return confirm('Are you sure want to delete this podcast?')" class="row-button delete"><i class="fas fa-fw fa-trash"></i></a>
    </td>
    <td>
        <label class="engine-checkbox">
            <input name="ids[]" class="multi-check-box" type="checkbox" value="{{ $podcast->id }}">
            <span class="checkmark"></span>
        </label>
    </td>
</tr>