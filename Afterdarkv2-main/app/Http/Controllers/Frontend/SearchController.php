<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\DefaultConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\SearchRequest;
use App\Http\Resources\Song\SongFullResource;
use App\Http\Resources\User\UserShortResource;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Spotify;
use View;

class SearchController extends Controller
{
    //    private $request;
    //
    //    private $term;
    //
    //    private int $limit;
    //
    //    private string $searchParam;
    //
    //    public function __construct(Request $request)
    //    {
    //        $this->request = $request;
    //        $this->term = $this->request->route('slug');
    //        $this->limit = 3;
    //        $this->searchParam = '%'.$this->term.'%';
    //    }
    private const TAKE_AMOUNT = 20;

    public function show(SearchRequest $request): Response
    {
        $searchString = "%{$request->input('search')}%";

        $songs = Song::query()
            ->where('title', 'LIKE', $searchString)
            ->where('is_visible', DefaultConstants::TRUE)
            ->has('user')
            ->with(['user:id,uuid,name,username', 'tags', 'genres'])
            ->paginate(self::TAKE_AMOUNT);

        $users = User::query()
            ->where('name', 'LIKE', $searchString)
            ->get();

        return Inertia::render('Search', [
            'searchString' => $request->input('search'),
            'songs' => Inertia::merge(SongFullResource::collection($songs->items())),
            'users' => UserShortResource::collection($users),
            'pagination' => Arr::except($songs->toArray(), 'items'),
        ]);
    }

    //    public function song()
    //    {
    //        if (config('settings.automate')) {
    //            if (intval($this->request->input('page')) < 2) {
    //                $songs = [];
    //                $data = Spotify::searchTracks($this->term)->get();
    //
    //                foreach ($data['tracks']['items'] as $item) {
    //                    $artists = handleSpotifyArtists($item['album']['artists']);
    //                    if ($item['album']['album_type'] != 'single') {
    //                        handleSpotifyAlbum($artists, $item['album']);
    //                    }
    //
    //                    $songs[] = handleSpotifySong($item, $artists);
    //                }
    //            }
    //        }
    //
    //        $result = isset($songs) && count($songs)
    //            ? $songs
    //            : Song::query()
    //                ->withoutGlobalScopes([NonAdventureScope::class])
    //                ->where('title', 'LIKE', $this->searchParam)
    //                ->paginate(20);
    //
    //        $view = View::make('trending.songs')
    //            ->with([
    //                'songs' => $result,
    //                'pageData' => [
    //                    'title' => 'Songs',
    //                    'desc' => '',
    //                ],
    //            ]);
    //
    //        if ($this->request->ajax()) {
    //            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::SEARCH_CHECK);
    //        }
    //
    //        $item = new \stdClass;
    //        $item->term = $this->term;
    //        getMetatags($item);
    //
    //        return $view;
    //    }
    //
    //    public function artist()
    //    {
    //        if (config('settings.automate')) {
    //            if (intval($this->request->input('page')) < 2) {
    //                $data = Spotify::searchArtists($this->term)->get();
    //                handleSpotifyArtists($data['artists']['items']);
    //            }
    //        }
    //
    //        $result = (object) [];
    //        $result->artists = Artist::query()
    //            ->where('name', 'like', $this->searchParam)
    //            ->paginate(20);
    //        $result->albums = Album::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->users = User::query()
    //            ->where('name', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->events = Event::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //
    //        $view = View::make('search.artist')
    //            ->with([
    //                'result' => $result,
    //                'term' => $this->term,
    //            ]);
    //
    //        if ($this->request->ajax()) {
    //            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::SEARCH_CHECK);
    //        }
    //
    //        $item = new \stdClass;
    //        $item->term = $this->term;
    //        getMetatags($item);
    //
    //        return $view;
    //    }
    //
    //    public function album()
    //    {
    //        if (config('settings.automate')) {
    //            if (intval($this->request->input('page')) < 2) {
    //                $albums = [];
    //                $data = Spotify::searchAlbums($this->term)->get();
    //                foreach ($data['albums']['items'] as $item) {
    //                    $artists = handleSpotifyArtists($item['artists']);
    //                    if ($item['album_type'] != 'single') {
    //                        $albums[] = handleSpotifyAlbum($artists, $item);
    //                    }
    //                }
    //            }
    //        }
    //
    //        $result = (object) [];
    //
    //        $result->albums = isset($albums) && count($albums)
    //            ? $albums
    //            : Album::query()->where('title', 'LIKE', $this->searchParam)->paginate(20);
    //
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->artists = Artist::query()
    //            ->where('name', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->users = User::query()
    //            ->where('name', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->events = Event::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //
    //        $view = View::make('search.album')
    //            ->with([
    //                'result' => $result,
    //                'term' => $this->term,
    //            ]);
    //
    //        if ($this->request->ajax()) {
    //            return $this->ajaxViewService->getRenderedSections($view, $this->request);
    //        }
    //
    //        $item = new \stdClass;
    //        $item->term = $this->term;
    //        getMetatags($item);
    //
    //        return $view;
    //    }
    //
    //    public function playlist()
    //    {
    //        $result = (object) [];
    //
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate(20);
    //        $result->artists = Artist::query()
    //            ->where('name', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->albums = Album::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->users = User::query()
    //            ->where('name', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->events = Event::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //
    //        $view = View::make('search.playlist')
    //            ->with([
    //                'result' => $result,
    //                'term' => $this->term,
    //            ]);
    //
    //        if ($this->request->ajax()) {
    //            return $this->ajaxViewService->getRenderedSections($view, $this->request);
    //        }
    //
    //        $item = new \stdClass;
    //        $item->term = $this->term;
    //        getMetatags($item);
    //
    //        return $view;
    //    }
    //
    //    public function user()
    //    {
    //        $result = (object) [];
    //
    //        $result->users = User::query()
    //            ->where('name', 'LIKE', $this->searchParam)
    //            ->paginate(20);
    //        $result->artists = Artist::query()
    //            ->where('name', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->albums = Album::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->events = Event::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //
    //        $view = View::make('search.user')
    //            ->with([
    //                'result' => $result,
    //                'term' => $this->term,
    //            ]);
    //
    //        if ($this->request->ajax()) {
    //            return $this->ajaxViewService->getRenderedSections($view, $this->request);
    //        }
    //
    //        $item = new \stdClass;
    //        $item->term = $this->term;
    //        getMetatags($item);
    //
    //        return $view;
    //    }
    //
    //    public function event()
    //    {
    //        $result = (object) [];
    //
    //        $result->events = Event::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate(20);
    //        $result->users = User::query()
    //            ->where('name', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->artists = Artist::query()
    //            ->where('name', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->albums = Album::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'LIKE', $this->searchParam)
    //            ->paginate($this->limit);
    //
    //        $view = View::make('search.event')
    //            ->with([
    //                'result' => $result,
    //                'term' => $this->term,
    //            ]);
    //
    //        if ($this->request->ajax()) {
    //            return $this->ajaxViewService->getRenderedSections($view, $this->request);
    //        }
    //
    //        $item = new \stdClass;
    //        $item->term = $this->term;
    //        getMetatags($item);
    //
    //        return $view;
    //    }
    //
    //    public function station()
    //    {
    //        $result = (object) [];
    //
    //        $result->stations = Station::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate(20);
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->artists = Artist::query()
    //            ->where('name', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->albums = Album::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->users = User::query()
    //            ->where('name', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->events = Event::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //
    //        $view = View::make('search.station')
    //            ->with([
    //                'result' => $result,
    //                'term' => $this->term,
    //            ]);
    //
    //        if ($this->request->ajax()) {
    //            return $this->ajaxViewService->getRenderedSections($view, $this->request);
    //        }
    //
    //        $item = new \stdClass;
    //        $item->term = $this->term;
    //        getMetatags($item);
    //
    //        return $view;
    //    }
    //
    //    public function podcast()
    //    {
    //        $result = (object) [];
    //
    //        $result->podcasts = Podcast::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate(20);
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->artists = Artist::query()
    //            ->where('name', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->albums = Album::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->playlists = Playlist::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->users = User::query()
    //            ->where('name', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //        $result->events = Event::query()
    //            ->where('title', 'like', $this->searchParam)
    //            ->paginate($this->limit);
    //
    //        $view = View::make('search.podcast')
    //            ->with([
    //                'result' => $result,
    //                'term' => $this->term,
    //            ]);
    //
    //        if ($this->request->ajax()) {
    //            return $this->ajaxViewService->getRenderedSections($view, $this->request);
    //        }
    //
    //        $item = new \stdClass;
    //        $item->term = $this->term;
    //        getMetatags($item);
    //
    //        return $view;
    //    }
    //
    //    public function tag()
    //    {
    //        $result = (object) [];
    //
    //        $result->songs = Song::query()
    //            ->whereHas('tags', function ($query) {
    //                $query->where('tag', 'like', $this->searchParam);
    //            })->get();
    //
    //        return view('search.tag')
    //            ->with([
    //                'result' => $result,
    //                'term' => $this->term,
    //            ]);
    //    }
}
