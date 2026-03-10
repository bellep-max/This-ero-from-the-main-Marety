<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Constants\RoleConstants;
use App\Constants\TypeConstants;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Auth\LoginStoreRequest;
use App\Http\Requests\Frontend\Auth\UserInfoValidateRequest;
use App\Http\Requests\Frontend\Auth\UsernameValidateRequest;
use App\Http\Requests\Frontend\Auth\UserSignupRequest;
use App\Http\Requests\Frontend\Connect\ConnectAutopostUpdateRequest;
use App\Http\Requests\Frontend\Connect\ConnectDeleteRequest;
use App\Http\Resources\User\UserShortResource;
use App\Models\Activity;
use App\Models\Album;
use App\Models\Artist;
use App\Models\ArtistRequest;
use App\Models\Ban;
use App\Models\Collaborator;
use App\Models\Connect;
use App\Models\Email;
use App\Models\Genre;
use App\Models\Group;
use App\Models\HashTag;
use App\Models\Notification;
use App\Models\Playlist;
use App\Models\PlaylistSong;
use App\Models\Podcast;
use App\Models\Report;
use App\Models\Song;
use App\Models\User;
use App\Services\ArtworkService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    public function userInfoValidate(UserInfoValidateRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
        ]);
    }

    public function usernameValidate(UsernameValidateRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Create user.
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function store(UserSignupRequest $request): RedirectResponse|JsonResponse
    {
        $verifyCoder = Str::random(32);

        $groupId = Group::query()
            ->where('name', $request->input('group'))
            ->value('id');

        $user = User::create([
            'email' => $request->email,
            'name' => $request->name ? strip_tags($request->name) : strip_tags($request->username),
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email_verified_code' => $verifyCoder,
            'group_id' => $groupId,
        ]);

        $user->assignRole($request->input('role'));

        /* Send activation email if registration method is advanced */
        if (config('settings.registration_method') == 1) {
            if ($request->input('isArtist') == 'on') {
                $user->artistRequests()->create();
            }

            (new Email)->verifyAccount($user, route('frontend.account.verify', ['code' => $verifyCoder]));

            return response()->json([
                'activation' => true,
                'email' => 'sent',
            ]);
        }

        /** If registration method is simplified then login the user right away  */
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (!auth()->attempt($credentials, true)) {
            return response()->json([
                'success' => false,
            ], 400);
        }

        /* send welcome email */
        (new Email)->newUser(auth()->user());

        if ($request->is('api*')) {
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;

            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addWeek();
            }

            $token->save();

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
            ]);
        }

        return redirect()->back()->with([
            'success' => true,
            'data' => UserShortResource::make($user),
        ]);
    }

    /**
     * Check if banned and get user IP.
     */
    private function userBannedCheck(): void
    {
        if (auth()->user()->banned) {
            $banned = Ban::find(auth()->id());

            if ($banned->end_at) {
                if (Carbon::now()->timestamp >= Carbon::parse($banned->end_at)->timestamp) {
                    User::query()
                        ->where('id', $banned->user_id)
                        ->update([
                            'banned' => DefaultConstants::FALSE,
                        ]);

                    Ban::query()
                        ->where('user_id', $banned->user_id)
                        ->delete();
                } else {
                    response()->json([
                        'message' => 'Unauthorized',
                        'errors' => ['message' => [__('auth.banned', ['banned_reason' => $banned->reason, 'banned_time' => Carbon::parse($banned->end_at)->format('H:i F j Y')])]],
                    ], 403);

                    return;
                }
            }
        }

        auth()->user()->update([
            'logged_ip' => request()->ip(),
        ]);
    }

    /**
     * Login user and create token.
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(LoginStoreRequest $request)
    {
        $credentials = $request->validated();

        if (!auth()->attempt($credentials, true)) {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => ['message' => __('auth.failed')],
            ], 401);
        }

        if (config('settings.registration_method') == 1) {
            if (!auth()->user()->email_verified) {
                auth()->logout();

                return response()->json([
                    'message' => 'Unauthorized',
                    'errors' => ['message' => [__('auth.email_verification_required')]],
                ], 401);
            }
        }

        $this->userBannedCheck();

        //        if (request()->is('api*')) {
        //            $user = request()->user();
        //            $tokenResult = $user->createToken('Personal Access Token');
        //            $token = $tokenResult->token;
        //            $token->expires_at = Carbon::now()->addWeeks(30);
        //            $token->save();
        //
        //            return response()->json([
        //                'access_token' => $tokenResult->accessToken,
        //                'token_type' => 'Bearer',
        //                'expires_at' => Carbon::parse(
        //                    $tokenResult->token->expires_at
        //                )->toDateTimeString(),
        //            ]);
        //        }

        return $request->user();
    }

    private function createdToken($user): JsonResponse
    {
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(30);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);
    }

    public function socialiteAuth(string $provider): \Symfony\Component\HttpFoundation\RedirectResponse|RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function socialiteCallback(string $provider): JsonResponse|RedirectResponse
    {
        try {
            $socialiteUser = Socialite::driver($provider)->user();

            $connect = Connect::query()
                ->where('provider_id', $socialiteUser->id)
                ->where('service', $provider)
                ->first();

            if (auth()->check()) {
                if (isset($connect->id) && $connect->user_id != auth()->id()) {
                    return redirect()->route('frontend.homepage');
                }

                Connect::updateOrCreate([
                    'user_id' => auth()->id(),
                    'provider_id' => $socialiteUser->id,
                    'service' => $provider,
                ], [
                    'provider_name' => $socialiteUser->name,
                    'provider_artwork' => $socialiteUser->avatar ?? '',
                    'provider_email' => $socialiteUser->email ?? null,
                ]);
            } else {
                if (isset($connect->user_id)) {
                    $authUser = User::find($connect->user_id);

                    if (isset($authUser->id)) {
                        if (request()->is('api*')) {
                            return $this->createdToken($authUser);
                        } else {
                            Auth::loginUsingId($authUser->id);

                            return redirect()->route('frontend.homepage');
                        }
                    } else {
                        $connect->delete();
                    }
                }

                if (isset($socialiteUser->email)) {
                    if ($authUser = User::firstWhere('email', $socialiteUser->email)) {
                        if (request()->is('api*')) {
                            return $this->createdToken($authUser);
                        }

                        auth()->loginUsingId($authUser->id);

                        Connect::create([
                            'user_id' => auth()->id(),
                            'provider_id' => $socialiteUser->id,
                            'provider_name' => $socialiteUser->name,
                            'provider_email' => $socialiteUser->email,
                            'provider_artwork' => $socialiteUser->avatar ?? null,
                            'service' => $provider,
                        ]);

                        $this->userBannedCheck();

                        return redirect()->route('frontend.homepage');
                    }
                }

                $user = User::create([
                    'name' => $socialiteUser->name ?? $socialiteUser->nickname,
                    'username' => Str::slug($socialiteUser->nickname) ?? strtolower(Str::random()),
                    'password' => bcrypt(Str::random()),
                    'email' => $socialiteUser->email ?? null,
                ]);

                if ($socialiteUser->avatar) {
                    $user->addMediaFromUrl($socialiteUser->avatar)
                        ->usingFileName(time() . '.jpg')
                        ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
                }

                Connect::create([
                    'user_id' => $user->id,
                    'provider_id' => $socialiteUser->id,
                    'provider_name' => $socialiteUser->name,
                    'provider_email' => $socialiteUser->email ?? null,
                    'provider_artwork' => $socialiteUser->avatar ?? '',
                    'service' => $provider,
                ]);

                if (request()->is('api*')) {
                    return $this->createdToken($user);
                }

                auth()->loginUsingId($user->id);
            }

            return redirect()->route('frontend.homepage');
        } catch (Exception $exception) {
            return redirect()->route('frontend.homepage');
        }
    }

    /**
     * Logout user (Revoke the token).
     *
     * @return [string] message
     */
    public function logout()
    {
        auth()->logout();
        session()->invalidate();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.',
        ], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return [json] user object
     */
    public function user(): JsonResponse
    {
        $user = request()->user();

        if (request()->is('api*')) {
            return response()->json($user);
        }

        // get user playlist
        $menu = [];

        if ($user->playlists()->exists()) {
            foreach ($user->playlists as $playlist) {
                $menu['playlist_id_' . $playlist->id]['name'] = $playlist->title;
            }
        }

        $user->playlists_menu = $menu;

        $user->setRelation('collaborations', $user->collaborations()->with('user')->get());

        $menu = [];

        // get user collaborate playlist
        if (count($user->collaborations)) {
            foreach ($user->collaborations as $playlist) {
                $menu['playlist_id_' . $playlist->id]['name'] = $playlist->title;
            }
        }

        $user->collaborate_playlists_menu = $menu;

        // get user subscribed playlist
        $user->setRelation('subscribed', $user->subscribed()->with('user')->get());

        /**
         * Check and send user back to default group if there is time limit.
         */
        if ($user->ends_at && Carbon::now()->gt(Carbon::parse($user->ends_at))) {
            $user->update([
                'group_id' => config('settings.default_usergroup', RoleConstants::MEMBER),
                'end_at' => null,
            ]);
        }

        $user = $user->makeVisible([
            'banned',
            'location',
            'email',
            'email_verified_at',
            'last_seen_notif',
            'logged_ip',
            'gender',
            'birthyear',
            'city',
            'country',
            'activity_privacy',
            'created_at',
            'updated_at',
            'restore_queue',
            'persist_shuffle',
            'play_pause_fade',
            'crossfade_amount',
            'notif_follower',
            'notif_playlist',
            'notif_shares',
            'notif_features',
            'email_verified',
            'can_stream_high_quality',
            'can_upload',
        ])
            ->makeHidden(['roles']);

        $user->can_upload = auth()->user()->can_upload;
        $user->can_stream_high_quality = auth()->user()->can_stream_high_quality;
        $user->allow_genres = Genre::query()->discover()->get()->makeHidden(['artwork', 'created_at', 'description', 'media', 'meta_description', 'meta_keywords', 'meta_title', 'parent_id', 'priority', 'priority', 'updated_at', 'alt_name']);
        $user->should_subscribe = !isset($user->group_id) || $user->group_id == config('settings.default_usergroup', RoleConstants::MEMBER);
        $user->can_download = (bool) Group::getValue('option_download');
        $user->can_download_high_quality = (bool) Group::getValue('option_download_hd');
        $user->admin_panel = (bool) Group::getValue('admin_access');

        if (Group::getValue('admin_access')) {
            $user->admin_panel_url = url(env('APP_ADMIN_PATH', 'admin'));
        }

        return response()->json($user);
    }

    public function settingsAccount(): JsonResponse
    {
        request()->validate([
            'password' => 'required|string',
        ]);

        $user = auth()->user();

        if (request()->input('email') != $user->email) {
            request()->validate([
                'email' => 'required|string|email|unique:users',
            ]);
        }

        if (request()->input('username') != $user->username) {
            request()->validate([
                'username' => 'required|string|alpha_dash|unique:users',
            ]);
        }

        if (Hash::check(request()->input('password'), $user->password)) {
            if (request()->input('email') != $user->email) {
                $user->update([
                    'email' => request()->input('email'),
                ]);
            }

            if (request()->input('username') != $user->email) {
                $user->update([
                    'username' => request()->input('username'),
                ]);
            }

            auth()->setUser($user);

            return response()->json($user);
        } else {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => [
                    'message' => ['Wrong password.'],
                ],
            ], 401);
        }
    }

    public function settingsPreferences(): JsonResponse
    {
        $user = auth()->user();

        $user->update([
            'restore_queue' => request()->has('restore_queue') ? DefaultConstants::TRUE : DefaultConstants::FALSE,
            'play_pause_fade' => request()->has('play_pause_fade') ? DefaultConstants::TRUE : DefaultConstants::FALSE,
            'allow_comments' => request()->has('allow_comments') ? DefaultConstants::TRUE : DefaultConstants::FALSE,
        ]);

        auth()->setUser($user);

        return response()->json(request()->user());
    }

    public function notifications()
    {
        $user = auth()->user();
        $user->last_seen_notif = Carbon::now();
        $user->save();

        if (request()->is('api*')) {
            return response()->json($user->notifications()->toArray());
        }

        return view('commons.notification')
            ->with([
                'notifications' => $user->notifications(),
            ]);
    }

    public function deleteNotification(): array
    {
        $res = ['status' => false];
        $id = (int) request()->id;
        $role = request()->role;
        if ($id) {
            $notification = $role === 'activity' ? Activity::find($id) : Notification::find($id);

            if ($notification) {
                $notification->delete();
                $res['status'] = true;
            }
        }

        return $res;
    }

    public function notificationCount(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'notification_count' => $user->notification_count,
            'last_seen_notif' => $user->last_seen_notif,
        ]);
    }

    public function playlists()
    {
        if (request()->is('api*')) {
            return response()->json((new Playlist)->get('playlists.user_id = ?', auth()->id(), 20));
        }
    }

    public function subscribed()
    {
        if (request()->is('api*')) {
            return response()->json((new Playlist)->getUserSubscribedPlaylists(auth()->id(), 20));
        }
    }

    public function favorite(): JsonResponse
    {
        request()->validate([
            'id' => 'required',
            'object_type' => 'required',
            'action' => 'required|boolean',
        ]);

        $objectId = intval(request()->input('id'));
        $objectType = request()->input('object_type');
        $action = request()->input('action');

        $action
            ? Helper::makeFavorite(ActionConstants::LOVE, $objectId, $objectType)
            : Helper::makeFavorite(ActionConstants::UNLOVE, $objectId, $objectType);

        return response()->json(['success' => true]);
    }

    public function songFavorite(): JsonResponse
    {
        request()->validate([
            'ids' => 'required|string',
        ]);

        foreach (explode(',', request()->input('ids')) as $id) {
            Helper::makeFavorite(ActionConstants::LOVE, $id, TypeConstants::SONG);
        }

        return response()->json(['success' => true]);
    }

    public function library(): JsonResponse
    {
        request()->validate([
            'id' => 'required|integer',
            'object_type' => 'required',
            'action' => 'required|boolean',
        ]);

        Helper::makeLibrary(
            request()->input('action')
                ? ActionConstants::LOVE
                : ActionConstants::UNLOVE,
            intval(request()->input('id')),
            request()->input('object_type')
        );

        return response()->json(['success' => true]);
    }

    public function songLibrary(): JsonResponse
    {
        request()->validate([
            'ids' => 'required|string',
        ]);

        foreach (explode(',', request()->input('ids')) as $id) {
            Helper::makeLibrary(ActionConstants::LOVE, $id, TypeConstants::SONG);
        }

        return response()->json(['success' => true]);
    }

    public function collaborativePlaylist(): JsonResponse
    {
        request()->validate([
            'id' => 'required|integer',
            'action' => 'required|in:invite,cancel,accept',
        ]);

        $playlistId = request()->input('id');
        $action = request()->input('action');

        if ($action == ActionConstants::INVITE) {
            try {
                request()->validate([
                    'friend_id' => 'required|integer',
                ]);
                $friendId = request()->input('friend_id');
                $playlist = Playlist::findOrFail($playlistId);

                Collaborator::create([
                    'user_id' => auth()->id(),
                    'playlist_id' => $playlistId,
                    'friend_id' => $friendId,
                    'approved' => DefaultConstants::FALSE,
                ]);

                Helper::pushNotification(
                    $friendId,
                    $playlist->id,
                    Playlist::class,
                    ActionConstants::INVITE_COLLAB,
                    $playlist->id
                );

                return response()->json(['success' => true]);
            } catch (Exception $e) {
                return response()->json(['success' => true, 'message' => 'Duplicated row']);
            }
        } elseif ($action == ActionConstants::ACCEPT) {
            $collaborator = Collaborator::query()
                ->where('friend_id', auth()->id())
                ->where('playlist_id', $playlistId)
                ->first();

            Collaborator::query()
                ->where('friend_id', auth()->id())
                ->where('playlist_id', $playlistId)
                ->update([
                    'approved' => DefaultConstants::TRUE,
                ]);

            Notification::query()
                ->where('notificationable_type', Playlist::class)
                ->where('notificationable_id', $playlistId)
                ->where('action', ActionConstants::INVITE_COLLAB)
                ->where('user_id', auth()->id())
                ->delete();

            Helper::pushNotification(
                $collaborator->user_id,
                $playlistId,
                Playlist::class,
                ActionConstants::ACCEPTED_COLLAB,
                $playlistId
            );
        } elseif ($action == ActionConstants::CANCEL) {
            Collaborator::query()
                ->where('friend_id', auth()->id())
                ->where('playlist_id', $playlistId)
                ->delete();

            Notification::query()
                ->where('notificationable_type', Playlist::class)
                ->where('notificationable_id', $playlistId)
                ->where('action', ActionConstants::INVITE_COLLAB)
                ->where('user_id', auth()->id())
                ->delete();
        }

        return response()->json(['success' => true]);
    }

    public function editPlaylist(): JsonResponse
    {
        request()->validate([
            'id' => 'required',
            'title' => 'required',
        ]);

        $playlist = Playlist::query()
            ->withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->findOrFail(request()->input('id'));

        if (!$playlist) {
            abort(403, 'You do not have permission to edit this playlist');
        }

        $genre = request()->input('genre');

        $playlist->title = strip_tags(request()->input('title'));
        $playlist->description = strip_tags(request()->input('description'));
        $playlist->genre = is_array($genre)
            ? implode(',', request()->input('genre'))
            : null;

        if (request()->hasFile('artwork')) {
            ArtworkService::updateArtwork(request(), $playlist);
        }

        $playlist->is_visible = request()->boolean('is_visible')
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        $playlist->save();

        return response()->json($playlist);
    }

    public function createPlaylist(): JsonResponse
    {
        request()->validate([
            'playlistName' => 'required|string',
            'genre' => 'nullable|array',
            'artwork' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:' . config('settings.image_max_file_size'),
        ]);

        $playlist = new Playlist;

        $genre = request()->input('genre');

        $playlist->genre = is_array($genre)
            ? implode(',', request()->input('genre'))
            : null;

        if (request()->hasFile('artwork')) {
            ArtworkService::updateArtwork(request(), $playlist);
        }

        $playlist->title = strip_tags(request()->input('playlistName'));
        $playlist->user_id = auth()->id();

        $playlist->is_visible = request()->boolean('is_visible')
            ? DefaultConstants::TRUE
            : DefaultConstants::FALSE;

        $playlist->save();

        return response()->json($playlist);
    }

    public function deletePlaylist(): JsonResponse
    {
        request()->validate([
            'playlist_id' => 'required|int|exists:playlists,id',
        ]);

        Playlist::query()
            ->withoutGlobalScopes()
            ->where('id', request()->input('playlist_id'))
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['success' => true]);
    }

    public function addToPlaylist(): JsonResponse
    {
        request()->validate([
            'mediaId' => 'required|int',
            'mediaType' => 'required',
            'playlist_id' => 'required|int',
        ]);

        $mediaId = request()->input('mediaId');
        $mediaType = request()->input('mediaType');
        $playlistId = request()->input('playlist_id');

        if (
            Playlist::query()
                ->withoutGlobalScopes()
                ->where('id', $playlistId)
                ->where('user_id', auth()->id())
                ->doesntExist() ||
            Collaborator::query()
                ->where('playlist_id', $playlistId)
                ->where('friend_id', auth()->id())
                ->exists()
        ) {
            abort(403);
        }

        if ($mediaType == TypeConstants::SONG) {
            try {
                PlaylistSong::create([
                    'song_id' => $mediaId,
                    'playlist_id' => $playlistId,
                ]);

                Helper::makeActivity(
                    auth()->id(),
                    $playlistId,
                    Playlist::class,
                    ActionConstants::ADD_TO_PLAYLIST,
                    $mediaId
                );
            } catch (Exception $e) {
                return response()->json([
                    'success' => true,
                    'message' => 'Duplicated row',
                ]);
            }
        } elseif ($mediaType == TypeConstants::ALBUM) {
            $album = Album::findOrFail($mediaId);
            $album->setRelation('songs', $album->songs()->get());

            foreach ($album->songs as $song) {
                try {
                    PlaylistSong::create([
                        'song_id' => $mediaId,
                        'playlist_id' => $playlistId,
                    ]);

                    Helper::makeActivity(
                        auth()->id(),
                        $playlistId,
                        Playlist::class,
                        ActionConstants::ADD_TO_PLAYLIST,
                        $song->id
                    );
                } catch (Exception $e) {
                }
            }

            return response()->json($album->songs);
        } elseif ($mediaType == TypeConstants::PLAYLIST) {
            $playlist = Playlist::findOrFail($mediaId);
            $playlist->setRelation('songs', $playlist->songs()->get());

            foreach ($playlist->songs as $song) {
                try {
                    PlaylistSong::create([
                        'song_id' => $mediaId,
                        'playlist_id' => $playlistId,
                    ]);

                    Helper::makeActivity(
                        auth()->id(),
                        $playlistId,
                        Playlist::class,
                        ActionConstants::ADD_TO_PLAYLIST,
                        $song->id
                    );
                } catch (Exception $e) {
                }
            }

            return response()->json($playlist->songs);
        } elseif ($mediaType == TypeConstants::QUEUE) {
            request()->validate([
                'mediaItems' => 'required|array',
            ]);
            $mediaItems = request()->input('mediaItems');

            if (is_array($mediaItems)) {
                foreach ($mediaItems as $item) {
                    $item = intval($item);
                    if ($item) {
                        try {
                            PlaylistSong::create([
                                'song_id' => $mediaId,
                                'playlist_id' => $playlistId,
                            ]);

                            Helper::makeActivity(
                                auth()->id(),
                                $playlistId,
                                Playlist::class,
                                ActionConstants::ADD_TO_PLAYLIST,
                                $item
                            );
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function removeFromPlaylist(): JsonResponse
    {
        request()->validate([
            'song_id' => 'required|int',
            'playlist_id' => 'required|int',
        ]);

        PlaylistSong::query()
            ->where('playlist_id', request()->input('playlist_id'))
            ->where('song_id', request()->input('song_id'))
            ->delete();

        return response()->json(['success' => true]);
    }

    public function managePlaylist(): JsonResponse
    {
        request()->validate([
            'playlist_id' => 'required|int',
            'removeIds' => 'nullable|string',
            'nextOrder' => 'required|string',
        ]);

        $playlistId = request()->input('playlist_id');
        $removeIds = strip_tags(json_decode(request()->input('removeIds')));
        $nextOrder = strip_tags(json_decode(request()->input('nextOrder')));

        if (is_array($removeIds)) {
            PlaylistSong::query()
                ->where('playlist_id', $playlistId)
                ->whereIn('song_id', $removeIds)
                ->delete();
        }

        if (is_array($nextOrder)) {
            foreach ($nextOrder as $index => $trackId) {
                PlaylistSong::query()
                    ->where('playlist_id', $playlistId)
                    ->where('song_id', $trackId)
                    ->update([
                        'priority' => $index,
                    ]);
            }
        }

        if (request()->is('api*')) {
            $playlist = Playlist::findOrFail($playlistId);
            $playlist->setRelation('songs', $playlist->songs()->get());

            return response()->json($playlist->songs);
        }

        return response()->json(['success' => true]);
    }

    public function setPlaylistCollaboration(): JsonResponse
    {
        request()->validate([
            'playlist_id' => 'required|int',
            'action' => 'required|boolean',
        ]);

        $playlist = Playlist::findOrFail(request()->input('playlist_id'));
        $playlist->update([
            'collaboration' => request()->input('action'),
        ]);

        return response()->json(['success' => true]);
    }

    public function createPodcast(): JsonResponse
    {
        request()->validate([
            'title' => 'required|string',
            'rss_feed_url' => 'nullable|url',
            'description' => 'nullable|string',
            'country_id' => 'nullable|exists:countries,id',
            'is_visible' => 'sometimes|accepted',
            'explicit' => 'sometimes|accepted',
            'allow_comments' => 'sometimes|accepted',
            'allow_download' => 'sometimes|accepted',
            'artwork' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:' . config('settings.image_max_file_size'),
        ]);

        $podcast = Podcast::create([
            'title' => strip_tags(request()->input('title')),
            'description' => strip_tags(request()->input('description')),
            'country_id' => request()->input('country_id'),
            'rss_feed_url' => request()->input('rss_feed_url'),
            'user_id' => auth()->id(),
            'is_visible' => request()->boolean('is_visible')
                ? DefaultConstants::TRUE
                : DefaultConstants::FALSE,
            'explicit' => request()->boolean('explicit')
                ? DefaultConstants::TRUE
                : DefaultConstants::FALSE,
            'allow_comments' => request()->boolean('allow_comments')
                ? DefaultConstants::TRUE
                : DefaultConstants::FALSE,
            'allow_download' => request()->boolean('allow_download')
                ? DefaultConstants::TRUE
                : DefaultConstants::FALSE,
        ]);

        if (request()->hasFile('artwork')) {
            ArtworkService::updateArtwork(request(), $podcast);
        }

        return response()->json(['status' => 'success', 'podcast' => $podcast]);
    }

    public function updatePodcast(Podcast $podcastVisible): JsonResponse
    {
        request()->validate([
            'title' => 'required|string',
            'rss_feed_url' => 'nullable|url',
            'description' => 'nullable|string',
            'is_visible' => 'sometimes|accepted',
            'explicit' => 'sometimes|accepted',
            'allow_comments' => 'sometimes|accepted',
            'allow_download' => 'sometimes|accepted',
            'artwork' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:' . config('settings.image_max_file_size'),
        ]);

        $podcastVisible->update([
            'title' => strip_tags(request()->input('title')),
            'description' => strip_tags(request()->input('description')),
            'rss_feed_url' => request()->input('rss_feed_url'),
            'is_visible' => request()->boolean('is_visible')
                ? DefaultConstants::TRUE
                : DefaultConstants::FALSE,
            'explicit' => request()->boolean('explicit')
                ? DefaultConstants::TRUE
                : DefaultConstants::FALSE,
            'allow_comments' => request()->boolean('allow_comments')
                ? DefaultConstants::TRUE
                : DefaultConstants::FALSE,
            'allow_download' => request()->boolean('allow_download')
                ? DefaultConstants::TRUE
                : DefaultConstants::FALSE,
        ]);

        if (request()->hasFile('artwork')) {
            ArtworkService::updateArtwork(request(), $podcastVisible);
        }

        return response()->json($podcastVisible);
    }

    public function removeActivity(): JsonResponse
    {
        request()->validate([
            'id' => 'required|int|exists:activities,id',
        ]);

        Activity::query()
            ->where('user_id', auth()->id())
            ->where('id', request()->input('id'))
            ->delete();

        return response()->json(['success' => true]);
    }

    public function suggest(): JsonResponse
    {
        $songs = Song::query()
            ->where('plays', '>', 20)
            ->get();

        return response()->json($songs);
    }

    public function artistClaim()
    {
        request()->validate([
            'stage' => 'string|required',
        ]);

        if (request()->input('stage') == 'account') {
            request()->validate([
                'email' => 'string|required|email',
                'fullName' => 'string|required|max:50',
            ]);

            if (request()->input('email') != auth()->user()->email) {
                request()->validate([
                    'email' => 'required|string|email|unique:users',
                ]);
            }

            return response()->json(auth()->user()->connect);
        } elseif (request()->input('stage') == 'info') {
            request()->validate([
                'artist_id' => 'nullable|integer',
                'artist_name' => 'required|string|min:3:max:30',
                'phone' => 'required|string|min:5:max:15',
                'ext' => 'nullable|numeric|digits_between:1,3',
                'affiliation' => 'required|string',
                'message' => 'nullable|string',
            ]);

            /* Insert artist request to user database */
            if (
                ArtistRequest::query()
                    ->where('user_id', auth()->id())
                    ->exists()
            ) {
                return response()->json([
                    'message' => 'You can not claim this artist profile',
                    'errors' => [
                        'message' => ['You have already claimed another artist profile, please wait for our email.'],
                    ],
                ], 403);
            }

            ArtistRequest::create([
                'user_id' => auth()->id(),
                'artist_id' => request()->input('artist_id'),
                'artist_name' => request()->input('artist_name'),
                'phone' => request()->input('phone'),
                'ext' => request()->input('ext'),
                'affiliation' => request()->input('affiliation'),
                'message' => request()->input('message'),
            ]);

            return response()->json(['success' => true]);
        }
    }

    public function checkRole()
    {
        request()->validate([
            'permission' => 'required|string',
        ]);

        if (Group::getValue('option_hd_stream')) {
            return response()->json([
                request()->input('permission') => true,
            ]);
        } else {
            abort(403, "You don't have the permission!");
        }
    }

    public function cancelSubscription(): JsonResponse
    {
        if (!auth()->user()->subscription) {
            abort(403);
        }

        auth()->user()->subscription()->delete();

        auth()->user()->update([
            'group_id' => config('settings.default_usergroup', RoleConstants::MEMBER),
            'ends_at' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function getMention(): JsonResponse
    {
        request()->validate([
            'term' => 'required|string',
        ]);

        $users = User::query()
            ->where('name', 'like', '%' . request()->input('term') . '%')
            ->limit(5)
            ->get();

        return response()->json($users);
    }

    public function getHashTag(): JsonResponse
    {
        request()->validate([
            'term' => 'required|string',
        ]);

        $tags = HashTag::query()
            ->where('tag', 'like', '%' . request()->input('term') . '%')
            ->groupBy('tag')
            ->limit(5)
            ->get();

        return response()->json($tags);
    }

    public function postFeed()
    {
        request()->merge(['body' => trim(strip_tags(preg_replace("/\s|&nbsp;/", ' ', request()->input('content'))))]);

        request()->validate([
            'object' => 'required|array',
            'body' => 'required|string|min:' . config('settings.share_min_chars', 1) . '|max:' . config('settings.share_max_chars', 160),
        ]);

        $content = strip_tags(request()->input('content'), '<tag>');

        $notificationType = match (request()->input('object')['type']) {
            TypeConstants::SONG => Song::class,
            TypeConstants::ALBUM => Album::class,
            TypeConstants::ARTIST => Artist::class,
            TypeConstants::PLAYLIST => Playlist::class,
            default => null,
        };

        $activity = Activity::create([
            'user_id' => auth()->id(),
            'activityable_id' => request()->input('object')['id'],
            'activityable_type' => $notificationType,
            'events' => $content,
            'action' => ActionConstants::POST_FEED,
        ]);

        Helper::pushNotificationMentioned(
            $content,
            request()->input('object')['id'],
            $notificationType,
            ActionConstants::SHARED_MUSIC,
            $activity->id
        );

        // handle hashtag
        preg_match_all('/#(\w+)/', $content, $allMatches);

        if (count($allMatches[1])) {
            foreach ($allMatches[1] as $tag) {
                if ($tag) {
                    HashTag::create([
                        'hashable_id' => $activity->id,
                        'hashable_type' => Activity::class,
                        'tag' => $tag,
                    ]);
                }
            }
        }

        if (request()->is('api*')) {
            return response()->json($activity);
        }

        return view('commons.activity')
            ->with([
                'activities' => [$activity],
                'type' => 'full',
            ]);
    }

    public function report(): JsonResponse
    {
        request()->validate([
            'reportable_type' => 'required|string|in:App\Models\Comment,App\Song',
            'reportable_id' => 'required|string',
            'message' => 'required|string|max:255',
        ]);

        Report::updateOrCreate([
            'user_id' => auth()->id(),
            'reportable_type' => request()->input('reportable_type'),
            'reportable_id' => request()->input('reportable_id'),
        ], [
            'message' => request()->input('message'),
            'created_at' => Carbon::now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function updateConnection(Connect $connect, ConnectAutopostUpdateRequest $request): JsonResponse
    {
        $connect->update($request->validated());

        return response()->json(['success' => true]);
    }

    public function deleteConnection(ConnectDeleteRequest $request): JsonResponse
    {
        Connect::destroy($request->validated());

        return response()->json(['success' => true]);
    }
}
