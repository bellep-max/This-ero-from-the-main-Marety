<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PickAndChoosePlaylistAddChildRequest;
use App\Http\Requests\PickAndChoosePlaylistAddSongRequest;
use App\Http\Requests\PickAndChoosePlaylistCreateRequest;
use App\Models\PickAndChoosePlaylist;
use App\Models\PickAndChoosePlaylistSong;
use App\Services\AjaxViewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use View;

class PickAndChoosePlaylistController extends Controller
{
    private $request;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index()
    {
        $view = View::make('pickAndChoosePlaylist.index');

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function all(): JsonResponse
    {
        return response()->json(
            PickAndChoosePlaylist::query()
                ->with([
                    'songs.song',
                    'songs.children',
                ])->get()
        );
    }

    public function store(PickAndChoosePlaylistCreateRequest $request): JsonResponse
    {
        $playlist = PickAndChoosePlaylist::create($request->all());
        $playlist->addMediaFromBase64($request->getBase64Img())
            ->usingFileName(time() . '.jpg')
            ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));

        return response()->json($playlist);
    }

    public function addSong(PickAndChoosePlaylistAddSongRequest $request): JsonResponse
    {
        return response()->json(
            PickAndChoosePlaylistSong::create([
                'song_id' => $request->song_id,
                'pick_and_choose_playlist_id' => $request->playlist_id,
            ])
        );
    }

    public function addChild(PickAndChoosePlaylistAddChildRequest $request): JsonResponse
    {
        return response()->json(
            $request
                ->getSong()
                ->children()
                ->save($request->getChild())
        );
    }

    public function show(PickAndChoosePlaylist $pickAndChoosePlaylist): JsonResponse
    {
        return response()->json(
            $pickAndChoosePlaylist->load([
                'songs.song',
                'songs.children.song',
            ])
        );
    }
}
