<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Constants\PaymentMethodConstants;
use App\Constants\TypeConstants;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Episode\EpisodeEditRequest;
use App\Http\Requests\Frontend\Event\EventDeleteRequest;
use App\Http\Requests\Frontend\Event\EventEditRequest;
use App\Http\Requests\Frontend\Event\EventStoreRequest;
use App\Http\Requests\Frontend\Podcast\PodcastEditRequest;
use App\Http\Requests\Frontend\Withdraw\WithdrawStoreRequest;
use App\Models\Activity;
use App\Models\Album;
use App\Models\AlbumSong;
use App\Models\Artist;
use App\Models\Country;
use App\Models\CountryLanguage;
use App\Models\Episode;
use App\Models\Event;
use App\Models\Genre;
use App\Models\Group;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use App\Models\Popular;
use App\Models\Song;
use App\Models\Upload;
use App\Services\AjaxViewService;
use App\Services\ArtworkService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Intervention\Image\Laravel\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Exceptions\InvalidBase64Data;

class ArtistManagementController extends Controller
{
    private $artist;

    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);
        $this->artist->setRelation('albums', $this->artist->albums()->withoutGlobalScopes()->paginate(20));
        $this->artist->setRelation('songs', $this->artist->songs()->orderBy('plays', 'desc')->paginate(10));

        $counts = Popular::query()
            ->select(DB::raw('sum(plays) AS playSong'), DB::raw('sum(favorites) AS favoriteSong'), DB::raw('sum(collections) AS collectSong'))
            ->where('artist_id', $this->artist->id)
            ->first();

        $songsRevenue = DB::table('stream_stats')
            ->select(DB::raw('sum(revenue) AS total, count(*) AS count'))
            ->where('streamable_type', (new Song)->getMorphClass())
            ->where('user_id', auth()->id())
            ->first();

        $episodesRevenue = DB::table('stream_stats')
            ->select(DB::raw('sum(revenue) AS total, count(*) AS count'))
            ->where('streamable_type', (new Episode)->getMorphClass())
            ->where('user_id', auth()->id())
            ->first();

        if ($request->is('api*')) {
            return response()->json([
                'artist' => $this->artist,
                'albums' => $this->artist->albums,
                'songs' => $this->artist->songs,
                'songs_revenue' => $songsRevenue,
                'episodes_revenue' => $episodesRevenue,
                'counts' => $counts,
            ]);
        }

        $view = View::make('artist-management.index')
            ->with([
                'songs' => $this->artist->songs,
                'albums' => $this->artist->albums,
                'artist' => $this->artist,
                'songs_revenue' => $songsRevenue,
                'episodes_revenue' => $episodesRevenue,
                'counts' => $counts,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function withdraw(WithdrawStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($data['amount'] > auth()->user()->balance) {
            abort(403, "No, don't do this.");
        }

        auth()->user()->withdraws()->create([
            'amount' => $data['amount'],
        ]);

        return response()
            ->json([
                'success' => true,
            ]);
    }

    public function reports(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);

        $view = View::make('artist-management.reports')
            ->with([
                'artist' => $this->artist,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function events(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);
        $this->artist->setRelation('songs', $this->artist->songs()->paginate(20));

        $view = View::make('artist-management.events')
            ->with([
                'songs' => $this->artist->songs,
                'artist' => $this->artist,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function uploaded(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);
        $this->artist->setRelation('songs', $this->artist->songs()->withoutGlobalScopes()->orderBy('approved', 'asc')->latest()->paginate(20));

        $view = View::make('artist-management.uploaded')
            ->with([
                'songs' => $this->artist->songs,
                'artist' => $this->artist,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request)
            : $view;
    }

    public function profile(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);

        $view = View::make('artist-management.profile')
            ->with([
                'artist' => $this->artist,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function saveProfile(Request $request): JsonResponse
    {
        $artist = Artist::findOrFail(auth()->user()->artist_id);

        $request->validate([
            'name' => 'required|max:100',
            'location' => 'nullable|max:100',
            'website' => 'nullable|url|max:100',
            'facebook' => 'nullable|url|max:100',
            'twitter' => 'nullable|url|max:100',
            'bio' => 'nullable|max:180',
            'genre' => 'nullable|array',
        ]);

        $artist->name = $request->input('name');
        $artist->location = $request->input('location');
        $artist->website = $request->input('website');
        $artist->facebook = $request->input('facebook');
        $artist->twitter = $request->input('twitter');
        $artist->bio = $request->input('bio');

        $genre = $request->input('genre');

        $artist->genre = is_array($genre) ? implode(',', $genre) : null;

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $artist);
        }

        $artist->save();

        $user = auth()->user();

        if ($request->input('payment_method') == PaymentMethodConstants::PAYPAL) {
            $request->validate([
                'paypal_email' => 'required|email',
            ]);

            $user->update([
                'payment_method' => PaymentMethodConstants::PAYPAL,
                'payment_paypal' => $request->input('paypal_email'),
            ]);
        }

        if ($request->input('payment_method') == PaymentMethodConstants::BANK) {
            $request->validate([
                'bank_details' => 'required|string',
            ]);

            $user->update([
                'payment_method' => PaymentMethodConstants::BANK,
                'payment_bank' => $request->input('bank_details'),
            ]);
        }

        return response()->json($artist);
    }

    /**
     * @throws FileCannotBeAdded|FileIsTooBig|FileDoesNotExist|InvalidBase64Data
     */
    public function editSongPost(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'title' => 'required|string|max:100',
            'vocal' => 'required|string|in:1,2,3,4,5,6',
            'genre' => 'nullable|array',
            'created_at' => 'nullable|date_format:m/d/Y H:i|after:now',
            'released_at' => 'nullable|date_format:m/d/Y H:i|after:now',
            'is_visible' => 'nullable|string',
            'allow_comments' => 'nullable|string',
            'allow_download' => 'nullable|string',
            'notification' => 'nullable|string',
            'is_explicit' => 'nullable|string',
            'is_patron' => 'nullable|string',
            'description' => 'nullable|string',
            'script' => 'nullable|string',
            'redirect' => 'nullable|string|in:true,false',
            'tags' => 'sometimes|required|array',
            'tags.*' => 'sometimes|required|string|alpha_num',
        ]);
        /*
         * Validate if song belongs to artist (by user_id)
         */

        if ($song = Song::query()->withoutGlobalScopes()->where('user_id', '=', auth()->id())->where('id', '=', $request->input('id'))->first()) {
            /*
             * Change artwork if needed
             */

            if ($request->input('created_at')) {
                $song->created_at = Carbon::parse($request->input('created_at'));
            }

            if ($request->input('released_at')) {
                $song->released_at = Carbon::parse($request->input('released_at'));
            }

            if ($request->hasFile('artwork')) {
                ArtworkService::updateArtwork($request, $song);
            }

            $song->title = $request->input('title');
            $song->description = $request->input('description');
            $song->script = $request->input('script');
            $song->is_visible = $request->boolean('is_visible');
            $song->allow_comments = $request->boolean('allow_comments');
            $song->allow_download = $request->boolean('allow_download');
            $song->is_explicit = $request->boolean('is_explicit');
            $song->is_patron = $request->boolean('is_patron');

            $genre = $request->input('genre');

            $song->genre = is_array($genre) ? implode(',', $genre) : null;

            if (!$song->approved && Group::getValue('artist_mod')) {
                $song->approved = DefaultConstants::TRUE;
            }

            if ($request->input('selling')) {
                $request->validate([
                    'price' => 'required|numeric|min:' . Group::getValue('monetization_song_min_price') . '|max:' . Group::getValue('monetization_song_max_price'),
                ]);
                $song->price = $request->input('price');
            }

            $song->selling = $request->input('selling') ? DefaultConstants::TRUE : DefaultConstants::FALSE;
            $song->vocal = $request->input('vocal') ?? DefaultConstants::FALSE;

            if ($request->input('notification')) {
                if ($request->input('created_at')) {
                    Helper::makeActivity(
                        auth()->id(),
                        auth()->user()->artist_id,
                        Artist::class,
                        ActionConstants::ADD_SONG,
                        $song->id,
                        false,
                        Carbon::parse($request->input('created_at'))
                    );
                } else {
                    Helper::makeActivity(
                        auth()->id(),
                        auth()->user()->artist_id,
                        Artist::class,
                        ActionConstants::ADD_SONG,
                        $song->id
                    );
                }
            }

            $song->copyright = $request->input('copyright');
            $song->save();

            if ($tags = $request->input('tags')) {
                if (is_array($tags)) {
                    foreach ($tags as $tag) {
                        $song->tags()->updateOrCreate([
                            'tag' => $tag,
                        ]);
                    }
                } else {
                    foreach (explode(',', $tags) as $tag) {
                        $song->tags()->updateOrCreate([
                            'tag' => $tag,
                        ]);
                    }
                }

                $song->tags()->whereNotIn('tag', $tags)->delete();
            }

            return $request->boolean('redirect') ? redirect()->back() : response()->json($song);
        } else {
            abort(403, 'Not your song.');
        }
    }

    /**
     * Get Available Genres (set available genre in Admin panel user group and role).
     */
    public function genres(Request $request): JsonResponse
    {
        $allowGenres = Genre::query()
            ->discover()
            ->get();

        $selectedGenres = [];

        if ($request->input('object_type')) {
            if ($request->input('object_type') == TypeConstants::SONG) {
                $song = Song::withoutGlobalScopes()->findOrFail($request->input('id'));
                $selectedGenres = explode(',', $song->genre);
            } elseif ($request->input('object_type') == TypeConstants::ALBUM) {
                if ($request->input('id')) {
                    $song = Album::withoutGlobalScopes()->findOrFail($request->input('id'));
                    $selectedGenres = explode(',', $song->genre);
                }
            }
        }

        $allowGenres = $allowGenres->map(function ($genre) use ($selectedGenres) {
            $genre->selected = in_array($genre->id, $selectedGenres);

            return $genre;
        });

        return $request->ajax()
            ? response()->json($allowGenres)
            : $allowGenres;
    }

    public function categories(Request $request)
    {
        return $request->ajax()
            ? response()->json(PodcastCategory::all())
            : PodcastCategory::all();
    }

    public function countries(Request $request)
    {
        return $request->ajax()
            ? response()->json(Country::all())
            : Country::all();
    }

    public function languages(Request $request)
    {
        $languages = CountryLanguage::query()
            ->where('country_code', $request->input('country_code'))
            ->get();

        return $request->ajax()
            ? response()->json($languages)
            : $languages;
    }

    public function artistChart(): JsonResponse
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);

        $fromDate = now()->subDays(15);
        $toDate = now();

        $rows = Popular::query()
            ->select(DB::raw('sum(plays) AS playSong'), DB::raw('sum(favorites) AS favoriteSong'), DB::raw('sum(collections) AS collectSong'), DB::raw('DATE(created_at) as date'))
            ->whereNowOrPast('created_at')
            ->whereDate('created_at', '>=', $fromDate)
            ->where('artist_id', $this->artist->id)
            ->groupBy('date')
            ->limit(50)
            ->get()
            ->toArray();

        $rows = Helper::insertMissingData($rows, ['playSong', 'favoriteSong', 'collectSong'], $fromDate, $toDate);

        $data = [];

        foreach ($rows as $item) {
            $item = (array) $item;
            $data['playSong'][] = $item['playSong'];
            $data['favoriteSong'][] = $item['favoriteSong'];
            $data['collectSong'][] = $item['collectSong'];
            $data['period'][] = Carbon::parse($item['date'])->format('m/d');
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function songChart(Request $request): JsonResponse
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);

        $from = strtotime(date('Y-m-d')) - (14 * 24 * 60 * 60);
        $from = date('Y-m-d', $from);

        $playData = Popular::query()
            ->select(DB::raw('sum(plays) AS playSong'), DB::raw('sum(favorites) AS favoriteSong'), DB::raw('sum(channels) AS collectSong'), 'created_at')
            ->where('popular.created_at', '<=', date('Y-m-d'))
            ->where('popular.created_at', '>=', $from)
            ->where('popular.song_id', $request->route('id'))
            // ->groupBy('popular.trackId')
            ->groupBy('popular.created_at')
            ->limit(50)
            ->get();

        $data = Helper::insertMissingDate(
            $playData,
            ActionConstants::PLAY_SONG,
            $from,
            date('Y-m-d')
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function albums(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);
        $this->artist->setRelation('albums', $this->artist->albums()->withoutGlobalScopes()->paginate(20));

        $view = View::make('artist-management.albums')
            ->with([
                'artist' => $this->artist,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function createAlbum(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'type' => 'required|numeric|between:1,10',
            'description' => 'nullable|string|max:1000',
            'copyright' => 'nullable|string|max:100',
            'created_at' => 'nullable|date_format:m/d/Y|after:' . Carbon::now(),
            'released_at' => 'nullable|date_format:m/d/Y|before:' . Carbon::now(),
            'artwork' => 'required|image|mimes:jpeg,png,jpg,gif|max:' . config('settings.image_max_file_size'),
        ]);

        $album = new Album;

        $album->title = $request->input('title');
        $album->artistIds = auth()->user()->artist_id;
        $album->type = $request->input('type');
        $album->description = $request->input('description');
        $album->copyright = $request->input('copyright');
        $album->is_visible = $request->input('is_visible');
        $album->user_id = auth()->id();

        $genre = $request->input('genre');

        $album->genre = is_array($genre) ? implode(',', $genre) : null;

        if ($request->input('released_at')) {
            $album->created_at = Carbon::parse($request->input('released_at'));
        }

        if ($request->input('created_at')) {
            $album->created_at = Carbon::parse($request->input('created_at'));
        }

        if (Group::getValue('artist_mod')) {
            $album->approved = DefaultConstants::TRUE;
        }

        $album->addMediaFromBase64(
            base64_encode(
                Image::make($request->file('artwork'))
                    ->fit(config('settings.image_artwork_max'), config('settings.image_artwork_max'))
                    ->encode('jpg', config('settings.image_jpeg_quality', 90))
                    ->encoded)
        )
            ->usingFileName(time() . '.jpg')
            ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));

        $album->is_visible = $request->boolean('is_visible')
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        $album->allow_comments = $request->boolean('comments')
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        if ($request->input('selling')) {
            $request->validate([
                'price' => 'required|numeric|min:' . Group::getValue('monetization_album_min_price') . '|max:' . Group::getValue('monetization_album_max_price'),
            ]);

            $album->selling = DefaultConstants::TRUE;
            $album->price = $request->input('price');
        } else {
            $album->selling = DefaultConstants::FALSE;
        }

        $album->save();

        return $album->makeVisible(['approved']);
    }

    public function showAlbum(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);
        $album = Album::withoutGlobalScopes()->findOrFail($request->route('id'));
        $album->makeVisible(['description']);
        $album->setRelation('songs', $album->songs()->withoutGlobalScopes()->get());

        $view = View::make('artist-management.edit-album')
            ->with([
                'artist' => $this->artist,
                'album' => $album,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function deleteAlbum(Request $request): JsonResponse
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);

        if (Album::withoutGlobalScopes()->where('user_id', auth()->id())->where('id', $request->input('id'))->exists()) {
            abort(403, 'Not your album.');
        }

        $album = Album::query()
            ->withoutGlobalScopes()
            ->findOrFail($request->input('id'));

        if (intval(Group::getValue('artist_day_edit_limit')) != 0 && Carbon::parse($album->created_at)->addDay(Group::getValue('artist_day_edit_limit'))->lt(Carbon::now())) {
            return response()->json([
                'message' => 'React the limited time to edit',
                'errors' => [
                    'message' => [__('web.POPUP_DELETE_ALBUM_DENIED')],
                ],
            ], 403);
        }

        $album->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function deleteSong(Request $request): JsonResponse
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);

        if (Song::withoutGlobalScopes()->where('user_id', auth()->id())->where('id', $request->input('id'))->exists()) {
            abort(403, 'Not your song.');
        }

        $song = Song::query()
            ->withoutGlobalScopes()
            ->findOrFail($request->input('id'));

        if (intval(Group::getValue('artist_day_edit_limit')) != 0 && Carbon::parse($song->created_at)->addDay(Group::getValue('artist_day_edit_limit'))->lt(Carbon::now())) {
            return response()->json([
                'message' => 'React the limited time to edit',
                'errors' => [
                    'message' => [__('web.POPUP_DELETE_SONG_DENIED')],
                ],
            ], 403);
        }

        $song->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function deletePodcast(Request $request): JsonResponse
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);
        if (!Podcast::withoutGlobalScopes()->where('user_id', auth()->id())->where('id', $request->input('id'))->exists()) {
            abort(403, 'Not your podcast.');
        }

        $podcast = Podcast::query()
            ->withoutGlobalScopes()
            ->findOrFail($request->input('id'));

        if (intval(Group::getValue('artist_podcast_day_edit_limit')) != 0 && Carbon::parse($podcast->created_at)->addDay(Group::getValue('artist_podcast_day_edit_limit'))->lt(Carbon::now())) {
            return response()->json([
                'message' => 'React the limited time to edit',
                'errors' => [
                    'message' => [__('web.POPUP_DELETE_PODCAST_DENIED')],
                ],
            ], 403);
        }

        $podcast->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function sortAlbumSongs(Request $request): JsonResponse
    {
        $request->validate([
            'album_id' => 'required|int',
            'removeIds' => 'nullable|string',
            'nextOrder' => 'required|string',
        ]);

        $albumId = $request->input('album_id');
        $removeIds = json_decode($request->input('removeIds'));
        $nextOrder = json_decode($request->input('nextOrder'));

        if (is_array($removeIds)) {
            AlbumSong::query()
                ->where('album_id', $albumId)
                ->whereIn('song_id', $removeIds)
                ->delete();
        }

        if (is_array($nextOrder)) {
            foreach ($nextOrder as $index => $trackId) {
                AlbumSong::query()
                    ->where('album_id', $albumId)
                    ->where('song_id', $trackId)
                    ->update([
                        'priority' => $index,
                    ]);
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function editAlbum(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'type' => 'required|numeric|between:1,10',
            'description' => 'nullable|string|max:1000',
            'copyright' => 'nullable|string|max:100',
            'created_at' => 'nullable|date_format:m/d/Y|after:' . Carbon::now(),
            'released_at' => 'nullable|date_format:m/d/Y|before:' . Carbon::now(),
        ]);

        if (!Album::withoutGlobalScopes()->where('user_id', auth()->id())->where('id', $request->input('id'))->exists()) {
            abort(403, 'Not your album.');
        }

        $album = Album::query()
            ->withoutGlobalScopes()
            ->findOrFail($request->input('id'));

        if (intval(Group::getValue('artist_day_edit_limit')) != 0 && Carbon::parse($album->created_at)->addDay(Group::getValue('artist_day_edit_limit'))->lt(Carbon::now())) {
            return response()->json([
                'message' => 'React the limited time to edit',
                'errors' => [
                    'message' => [__('web.POPUP_EDIT_ALBUM_DENIED')],
                ],
            ], 403);
        }

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $album);
        }

        $album->title = $request->input('title');
        $album->description = $request->input('description');
        $album->is_visible = $request->input('visibility');
        $album->type = $request->input('type');
        $album->description = $request->input('description');
        $album->copyright = $request->input('copyright');

        $genre = $request->input('genre');

        $album->genre = is_array($genre)
            ? implode(',', $request->input('genre'))
            : null;

        $album->is_visible = $request->boolean('is_visible')
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        $album->allow_comments = $request->boolean('comments')
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        if ($request->input('selling')) {
            $request->validate([
                'price' => 'required|numeric|min:' . Group::getValue('monetization_album_min_price') . '|max:' . Group::getValue('monetization_album_max_price'),
            ]);
            $album->selling = DefaultConstants::TRUE;
            $album->price = $request->input('price');
        } else {
            $album->selling = DefaultConstants::FALSE;
        }

        $album->save();

        return response()->json($album);
    }

    public function uploadAlbum(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);

        $view = View::make('artist-management.upload')
            ->with([
                'artist' => $this->artist,
                'album' => Album::withoutGlobalScopes()->findOrFail($request->route('id')),
                'allowGenres' => Genre::query()->discover()->get(),
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function handleUpload(Request $request): JsonResponse
    {
        $this->artist = Artist::query()
            ->findOrFail(auth()->user()->artist_id);

        $album = Album::query()
            ->withoutGlobalScopes()
            ->findOrFail($request->route('id'));

        /* Check if user have permission to upload */

        if (!Group::getValue('artist_allow_upload')) {
            abort(403);
        }

        $res = (new Upload)->handle($request, auth()->user()->artist_id, $album->id);

        return response()->json($res);
    }

    public function podcasts(Request $request)
    {
        $this->artist = Artist::query()
            ->findOrFail(auth()->user()->artist_id);

        $this->artist->setRelation('podcasts', $this->artist->podcasts()->withoutGlobalScopes()->paginate(20));

        $view = View::make('artist-management.podcasts')
            ->with([
                'artist' => $this->artist,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function importPodcast(Request $request): JsonResponse
    {
        $request->validate([
            'rss_url' => 'required|string',
            'country' => 'required|string|max:3',
            'language' => 'required|int',
            'created_at' => 'nullable|date_format:m/d/Y|after:' . Carbon::now(),
        ]);

        $podcast = new Podcast;
        @libxml_use_internal_errors(true);
        $rss = @simplexml_load_file($request->input('rss_url'));

        if ($rss === false) {
            return response()->json([
                'message' => 'error',
                'errors' => [
                    'message' => ['Can not fetch the rss.'],
                ],
            ], 403);
        }

        if (!isset($rss->channel)) {
            return response()->json([
                'message' => 'error',
                'errors' => [
                    'message' => ['RSS format does not match a podcast feed.'],
                ],
            ], 403);
        }

        $podcast->artist_id = auth()->user()->artist_id;
        $podcast->title = $rss->channel->title;
        $podcast->description = $rss->channel->description;
        $podcast->rss_feed_url = $request->input('rss_url');
        $podcast->country_code = $request->input('country');
        $podcast->language_id = $request->input('language');
        $podcast->user_id = auth()->id();

        $podcast->addMediaFromUrl(reset($rss->channel->image->url))
            ->usingFileName(time() . '.jpg')
            ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
        $podcast->created_at = Carbon::parse($rss->channel->pubDate);
        $podcast->updated_at = Carbon::parse($rss->channel->lastBuildDate);
        $podcast->save();

        if (isset($rss->channel->item)) {
            foreach ($rss->channel->item as $item) {
                if (!Episode::query()->where('created_at', Carbon::parse($item->pubDate))->where('podcast_id', $podcast->id)->exists()) {
                    $episode = new Episode;

                    $episode->podcast_id = $podcast->id;
                    $episode->title = $item->title;
                    $episode->description = $item->description;
                    $episode->created_at = Carbon::parse($item->pubDate);
                    $episode->type = $item->enclosure['type'];
                    $episode->stream_url = $item->enclosure['url'];
                    $itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
                    $episode->type = 1;
                    $episode->duration = intval(reset($itunes->duration));
                    $episode->explicit = (reset($itunes->explicit) == 'clean' || reset($itunes->explicit) == 'no')
                        ? DefaultConstants::FALSE
                        : DefaultConstants::TRUE;

                    $episode->save();
                }
            }
        }

        return response()->json($podcast);
    }

    public function createPodcast(Request $request): Podcast
    {
        if (!Group::getValue('artist_allow_podcast')) {
            abort(403, 'No permission!');
        }

        $data = $request->validated();

        $podcast = Podcast::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'country_id' => $data['country'],
            'language_id' => $data['language_id'],
            'is_visible' => $data['is_visible'] ?? null,
            'allow_comments' => $data['allow_comments'] ?? null,
            'allow_download' => $data['allow_download'] ?? null,
            'explicit' => $data['explicit'] ?? null,
            'user_id' => auth()->id(),
            'artist_id' => auth()->user()->artist_id,
            'category' => is_array($data['category']) ? implode(',', $data['category']) : null,
            'created_at' => array_key_exists('created_at', $data) ? Carbon::parse($data['created_at']) : null,
            'approved' => Group::getValue('artist_podcast_mod') ? DefaultConstants::TRUE : DefaultConstants::FALSE,
        ]);

        $podcast->addMediaFromBase64(
            base64_encode(
                Image::make($request->file('artwork'))
                    ->fit(config('settings.image_artwork_max'), config('settings.image_artwork_max'))
                    ->encode('jpg', config('settings.image_jpeg_quality', 90))
                    ->encoded
            )
        )
            ->usingFileName(time() . '.jpg')
            ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));

        return $podcast->makeVisible(['approved']);
    }

    public function editPodcast(PodcastEditRequest $request)
    {
        $data = $request->validated();

        $podcast = Podcast::findOrFail($request->input('id'));
        $podcast->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'country_id' => $data['country_id'],
            'language_id' => $data['language_id'],
            'is_visible' => $data['is_visible'] ?? null,
            'allow_comments' => $data['allow_comments'] ?? null,
            'allow_download' => $data['allow_download'] ?? null,
            'explicit' => $data['explicit'] ?? null,
            'user_id' => auth()->id(),
            'category' => is_array($data['category']) ? implode(',', $data['category']) : null,
            'created_at' => array_key_exists('created_at', $data) ? Carbon::parse($data['created_at']) : null,
            'approved' => Group::getValue('artist_podcast_mod') ? DefaultConstants::TRUE : DefaultConstants::FALSE,
        ]);

        if ($request->file('artwork')) {
            $podcast->clearMediaCollection('artwork');
            $podcast->addMediaFromBase64(
                base64_encode(
                    Image::make($request->file('artwork'))
                        ->fit(config('settings.image_artwork_max'), config('settings.image_artwork_max'))
                        ->encode('jpg', config('settings.image_jpeg_quality', 90))
                        ->encoded
                )
            )
                ->usingFileName(time() . '.jpg')
                ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
        }

        return $podcast->makeVisible(['approved']);
    }

    public function showPodcast(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);
        $podcast = Podcast::withoutGlobalScopes()->findOrFail($request->route('id'));
        $podcast->setRelation('episodes', $podcast->episodes()->with('podcast')->withoutGlobalScopes()->paginate(20));

        $view = View::make('artist-management.edit-podcast')
            ->with([
                'artist' => $this->artist,
                'podcast' => $podcast,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function uploadPodcast(Request $request)
    {
        $this->artist = Artist::findOrFail(auth()->user()->artist_id);
        $podcast = Podcast::withoutGlobalScopes()->findOrFail($request->route('id'));

        $view = View::make('artist-management.podcast-upload')
            ->with([
                'artist' => $this->artist,
                'podcast' => $podcast,
            ]);

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function handlePodcastUpload(Request $request)
    {
        /* Check if user have permission to upload */
        if (!Group::getValue('artist_allow_upload')) {
            abort(403);
        }

        $this->artist = Artist::findOrFail(auth()->user()->artist_id);
        $podcast = Podcast::withoutGlobalScopes()->findOrFail($request->route('id'));

        $res = (new Upload)->handleEpisode($request, $podcast->id);

        return response()->json($res);
    }

    public function editEpisode(EpisodeEditRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (Episode::withoutGlobalScopes()->where('user_id', '=', auth()->id())->where('id', '=', $data['id'])->exists()) {
            abort(403, 'Not your episode.');
        }

        $episode = Episode::query()->withoutGlobalScopes()->findOrFail($data['id']);

        if (intval(Group::getValue('artist_day_edit_limit')) != 0 && Carbon::parse($episode->created_at)->addDay(Group::getValue('artist_day_edit_limit'))->lt(Carbon::now())) {
            return response()->json([
                'message' => 'React the limited time to edit',
                'errors' => ['message' => [__('web.POPUP_EDIT_SONG_DENIED')]],
            ], 403);
        }

        $episode->title = $data['title'];
        $episode->description = $data['description'];
        $episode->season = $data['season'];
        $episode->number = $data['number'];
        $episode->type = $data['type'];

        if (array_key_exists('created_at', $data)) {
            $episode->created_at = Carbon::parse($data['created_at']);
        }

        if ($episode->podcast->category && Group::getValue('artist_podcast_mod')) {
            if (Group::getValue('artist_trusted_genre')) {
                $trustedSection = is_array(Group::getValue('artist_podcast_trusted_categories'))
                    ? Group::getValue('artist_podcast_trusted_categories')
                    : [];
                $episode->approved = !array_diff(explode(',', $episode->podcast->catetory), $trustedSection)
                    ? DefaultConstants::TRUE
                    : DefaultConstants::FALSE;
            }
        }

        $episode->is_visible = $data['is_visible']
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        $episode->downloadable = $data['downloadable']
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        if (array_key_exists('notification', $data)) {
            if (array_key_exists('created_at', $data)) {
                Helper::makeActivity(
                    auth()->id(),
                    auth()->user()->artist_id,
                    Artist::class,
                    ActionConstants::ADD_EPISODE,
                    $episode->id,
                    false,
                    Carbon::parse($data['created_at'])
                );
            } else {
                Helper::makeActivity(
                    auth()->id(),
                    auth()->user()->artist_id,
                    Artist::class,
                    ActionConstants::ADD_EPISODE,
                    $episode->id
                );
            }
        }

        $episode->save();

        return response()->json($episode);
    }

    public function createEvent(EventStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $event = Event::create([
            'artist_id' => auth()->user()->artist_id,
            'title' => $data['title'],
            'location' => $data['location'],
            'link' => $data['link'],
            'started_at' => $data['started_at'] ? Carbon::parse($data['started_at']) : null,
        ]);

        Helper::makeActivity(
            auth()->id(),
            auth()->user()->artist_id,
            Artist::class,
            ActionConstants::ADD_EVENT,
            $event->id,
            false
        );

        return response()->json($event);
    }

    public function editEvent(EventEditRequest $request): JsonResponse
    {
        $data = $request->validated();

        $event = Event::query()
            ->where('artist_id', auth()->user()->artist_id)
            ->where('id', $data['id'])
            ->firstOrFail();

        $event->update([
            'title' => $data['title'],
            'location' => $data['location'],
            'link' => $data['link'],
            'started_at' => Carbon::parse($data['started_at']),
        ]);

        return response()->json($event);
    }

    public function deleteEvent(EventDeleteRequest $request): JsonResponse
    {
        $data = $request->validated();

        Event::query()
            ->where('artist_id', auth()->user()->artist_id)
            ->where('id', $data['id'])
            ->firstOrFail()
            ->delete();

        Activity::query()
            ->where('user_id', auth()->id())
            ->where('activityable_id', auth()->user()->artist_id)
            ->where('activityable_type', Artist::class)
            ->where('action', ActionConstants::ADD_EVENT)
            ->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
