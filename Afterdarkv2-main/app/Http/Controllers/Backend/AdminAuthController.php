<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-18
 * Time: 13:09.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Constants\TypeConstants;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Models\AlbumSong;
use App\Models\Email;
use App\Models\Episode;
use App\Models\Group;
use App\Models\PlaylistSong;
use App\Models\Song;
use App\Models\Upload;
use App\Models\User;
use App\Services\ArtworkService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getLogin(): Factory|Application|View|Redirector|RedirectResponse
    {
        return auth()->check()
            ? redirect('admin')
            : view('backend.login');
    }

    public function postLogin(): Application|Redirector|RedirectResponse
    {
        $this->request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        $login = [
            'email' => $this->request->input('email'),
            'password' => $this->request->input('password'),
        ];

        if (auth()->attempt($login, $this->request->input('remember')) && Group::getValue('admin_access')) {
            return redirect('admin');
        }

        return redirect()->back()
            ->with([
                'status' => 'failed',
                'message' => trans('auth.failed'),
            ]);
    }

    /**
     * action admin/logout.
     */
    public function getLogout(): RedirectResponse
    {
        auth()->logout();

        return redirect()->route('backend.login');
    }

    public function forgotPassword(): View|Application|Factory
    {
        return view('backend.forgot-password');
    }

    public function forgotPasswordPost(): RedirectResponse
    {
        $this->request->validate([
            'email' => 'string|email|exists:users',
        ]);

        $user = User::query()
            ->where('email', $this->request->input('email'))
            ->firstOrFail();

        $row = DB::table('password_resets')->select('email')->where('email', $user->email)->first();
        $token = Str::random(60);

        if (isset($row->email)) {
            DB::table('password_resets')->where('email', $user->email)->update([
                'token' => $token,
                'created_at' => Carbon::now(),

            ]);
        } else {
            DB::table('password_resets')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now(),

            ]);
        }

        (new Email)->resetPassword($user, route('backend.reset-password', ['token' => $token]));

        return MessageHelper::redirectMessage(trans('passwords.sent'));
    }

    public function resetPassword(): Factory|Application|View|RedirectResponse
    {
        $row = DB::table('password_resets')
            ->select('email')
            ->where('token', $this->request->route('token'))
            ->first();

        if (isset($row->email)) {
            $user = User::query()
                ->where('email', $row->email)
                ->firstOrFail();
            /*
             * Log user in then show the change password form
             */
            auth()->login($user);

            return view('backend.reset-password');
        } else {
            return redirect()
                ->route('backend.forgot-password')
                ->with([
                    'status' => 'failed',
                    'message' => trans('Your reset code is invalid or has expired.'),
                ]);
        }
    }

    public function resetPasswordPost(): RedirectResponse
    {
        if (!auth()->check()) {
            abort('403');
        }

        $this->request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        /**
         * Change user password.
         */
        $user = auth()->user()->update([
            'password' => bcrypt($this->request->input('password')),
        ]);

        /*
         * Delete password reset token
         */
        DB::table('password_resets')->where('email', $user->email)->delete();

        return MessageHelper::redirectMessage(__('passwords.reset'), 'backend.dashboard');
    }

    public function addSong(): JsonResponse
    {
        $this->request->validate([
            'object_type' => 'required|string',
            'object_id' => 'required|int',
            'song_id' => 'required|int',
        ]);

        $objectType = $this->request->input('object_type');
        $objectId = $this->request->input('object_id');
        $songId = $this->request->input('song_id');

        if ($objectType == 'playlist') {
            try {
                PlaylistSong::create([
                    'song_id' => $songId,
                    'playlist_id' => $objectId,
                ]);
            } catch (\Exception $e) {
            }
        } elseif ($objectType == 'album') {
            try {
                AlbumSong::create([
                    'song_id' => $songId,
                    'album_id' => $objectId,
                ]);
            } catch (\Exception $e) {
            }
        }

        return response()->json(Song::findOrFail($songId));
    }

    public function removeSong(): JsonResponse
    {
        $this->request->validate([
            'object_type' => 'required|string',
            'object_id' => 'required|int',
            'song_id' => 'required|int',
        ]);

        $objectType = $this->request->input('object_type');
        $objectId = $this->request->input('object_id');
        $songId = $this->request->input('song_id');

        if ($objectType == TypeConstants::PLAYLIST) {
            PlaylistSong::query()
                ->where('playlist_id', $objectId)
                ->where('song_id', $songId)
                ->delete();
        } elseif ($objectType == TypeConstants::ALBUM) {
            AlbumSong::query()
                ->where('album_id', $objectId)
                ->where('song_id', $songId)
                ->delete();
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function upload(): JsonResponse
    {
        /*
         * Call upload function, set isAdminPanel = true to force script automatic general info from ID3
         */

        if ($this->request->routeIs('backend.artist.upload.bulk')) {
            $song = (new Upload)->handle($this->request, $this->request->route('artistId'), null, true);
        } elseif ($this->request->routeIs('backend.album.upload.bulk')) {
            $song = (new Upload)->handle($this->request, null, $this->request->route('album_id'), true);
        } else {
            $song = (new Upload)->handle($this->request, null, null, true);
        }

        return response()->json($song);
    }

    public function uploadEpisode(): JsonResponse
    {
        $episode = (new Upload)->handleEpisode($this->request, $this->request->route('podcast_id'));

        return response()->json($episode);
    }

    public function editSong(): JsonResponse
    {
        $this->request->validate([
            'id' => 'required|numeric',
            'title' => 'required|max:100',
            'artistIds' => 'required|array',
            'copyright' => 'nullable|string|max:100',
            'created_at' => 'nullable|date_format:m/d/Y|after:' . Carbon::now(),
        ]);

        $song = Song::query()
            ->withoutGlobalScopes()
            ->findOrFail($this->request->input('id'));

        if (is_array($this->request->input('artistIds'))) {
            $song->artistIds = implode(',', $this->request->input('artistIds'));
        }

        if ($this->request->hasFile('artwork')) {
            ArtworkService::updateArtwork($this->request, $song);
        }

        $song->title = $this->request->input('title');

        if ($this->request->input('created_at')) {
            $song->created_at = Carbon::parse($this->request->input('created_at'));
        }

        if (is_array($this->request->input('genre'))) {
            $song->genre = implode(',', $this->request->input('genre'));
        }

        $song->copyright = $this->request->input('copyright');
        $song->save();

        return response()->json($song);
    }

    public function editEpisode(): JsonResponse
    {
        $this->request->validate([
            'id' => 'required|numeric',
            'title' => 'required|max:100',
            'description' => 'nullable|string',
            'season' => 'nullable|numeric',
            'number' => 'nullable|numeric',
            'type' => 'nullable|numeric:in:1,2,3',
            'created_at' => 'nullable|date_format:m/d/Y',
        ]);

        $episode = Episode::query()
            ->withoutGlobalScopes()
            ->findOrFail($this->request->input('id'));

        $episode->title = $this->request->input('title');
        $episode->description = $this->request->input('description');
        $episode->season = $this->request->input('season');
        $episode->number = $this->request->input('number');
        $episode->type = $this->request->input('type');

        if ($this->request->input('created_at')) {
            $episode->created_at = Carbon::parse($this->request->input('created_at'));
        }

        $episode->approved = DefaultConstants::TRUE;
        $episode->is_visible = $this->request->boolean('is_visible')
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        $episode->allow_download = $this->request->boolean('downloadable')
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        $episode->save();

        return response()->json($episode);
    }
}
