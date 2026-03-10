<?php

namespace App\Http\Controllers\Frontend;

use Aerni\Spotify\SpotifySeed;
use App\Constants\DefaultConstants;
use App\Http\Controllers\Controller;
use App\Http\Resources\Adventure\AdventureFullResource;
use App\Http\Resources\Genre\GenreShortResource;
use App\Http\Resources\Song\SongFullResource;
use App\Models\Genre;
use App\Services\MetatagService;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class GenreController extends Controller
{
    private const TAKE_AMOUNT = 20;

    public function __construct(private readonly MetatagService $metatagService) {}

    public function index(): Response
    {

        //        getMetatags();

        //        $tracks = Channel::query()
        //            ->where('allow_home', DefaultConstants::TRUE)
        //            ->where('alt_name', 'new-audios')
        //            ->orderBy('priority', 'asc')
        //            ->first();

        return Inertia::render('Genre/Index', [
            'genres' => GenreShortResource::collection(Genre::query()
                ->orderBy('priority', 'asc')
                ->discover()
                ->get()),
        ]);
    }

    public function show(Genre $genre): Response
    {
        $this->metatagService->setMetatags($genre);

        // todo COMMENTED DUE TO CHANGES IN SPOTIFY PACKAGE

        //        if (config('settings.automate') && intval($this->request->input('page')) < 2) {
        //            $songs = [];
        //            $seed = SpotifySeed::setGenres([$genre->alt_name]);
        //            $data = Spotify::recommendations($seed)->get();
        //
        //            foreach ($data['tracks'] as $item) {
        //                $artists = handleSpotifyArtists($item['album']['artists']);
        //
        //                if ($item['album']['album_type'] != 'single') {
        //                    handleSpotifyAlbum($artists, $item['album']);
        //                }
        //
        //                $songs[] = handleSpotifySong($item, $artists);
        //            }
        //        }

        //        if (isset($songs) && count($songs)) {
        //            $genre->songs = $songs;
        //        } else {
        $userIds = $genre
            ->songs()
            ->pluck('user_id');

        if (auth()->check()) {
            $patronUsers = collect($userIds)->unique()->filter(function (int $userId) {
                return (bool) auth()->user()->activeUserSubscription($userId);
            })->toArray();
        } else {
            $patronUsers = collect($userIds)->unique();
        }

        $songs = $genre
            ->songs()
            ->visible()
            ->when(auth()->check(), function ($query) use ($patronUsers) {
                $query->orWhere(function (Builder $query) use ($patronUsers) {
                    $query->where('is_patron', true)
                        ->whereIn('user_id', $patronUsers);
                });
            },
                function ($query) {
                    $query->where('is_patron', false);
                })
            ->with([
                'user:id,uuid,name,username',
                'tags',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(self::TAKE_AMOUNT);

        $adventures = $genre
            ->adventures()
            ->visible()
            ->with([
                'user:id,uuid,name,username',
                'tags',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(self::TAKE_AMOUNT);
        //        }

        return Inertia::render('Genre/Show', [
            'genre' => GenreShortResource::make($genre),
            'songs' => SongFullResource::collection($songs),
            'adventures' => AdventureFullResource::collection($adventures),
            //            'slides' => $genre->slides()->orderBy('priority'),
            //            'channels' => $genre->channels()->orderBy('priority'),
            'related' => GenreShortResource::collection(Genre::query()->whereNot('id', $genre->id)->get()),
        ]);
    }
}
