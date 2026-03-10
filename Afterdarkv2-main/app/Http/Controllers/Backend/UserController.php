<?php

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Constants\RoleConstants;
use App\Constants\StatusConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\User\UserIndexRequest;
use App\Http\Requests\Backend\User\UserMassActionRequest;
use App\Http\Requests\Backend\User\UserStoreRequest;
use App\Http\Requests\Backend\User\UserUpdateRequest;
use App\Models\Comment;
use App\Models\Group;
use App\Models\User;
use App\Services\ArtworkService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController
{
    private const DEFAULT_ROUTE = 'backend.users.index';

    public function index(UserIndexRequest $request): View|Application|Factory
    {
        $users = User::query()
            ->withoutGlobalScopes()
            ->when($request->has('username'), function ($query) use ($request) {
                $query->when($request->has('exact_username'), function ($query) use ($request) {
                    $query->where('username', '=', $request->input('username'));
                }, function ($query) use ($request) {
                    $query->where('username', 'LIKE', '%' . $request->input('username') . '%');
                });
            })
            ->when($request->has('email'), function ($query) use ($request) {
                $query->where('email', 'LIKE', '%' . $request->input('email') . '%');
            })
            ->when($request->has('created_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('created_from'));
            })
            ->when($request->has('created_until'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->input('created_until'));
            })
            ->when($request->has('logged_from'), function ($query) use ($request) {
                $query->whereDate('last_activity', '>=', $request->input('logged_from'));
            })
            ->when($request->has('logged_until'), function ($query) use ($request) {
                $query->whereDate('last_activity', '<=', $request->input('logged_until'));
            })
            ->when($request->has('comment_count_from'), function ($query) use ($request) {
                $query->whereDate('comment_count', '>=', intval($request->input('comment_count_from')));
            })
            ->when($request->has('comment_count_until'), function ($query) use ($request) {
                $query->whereDate('comment_count', '<=', intval($request->input('comment_count_until')));
            })
            ->when($request->has('banned'), function ($query) {
                $query->has('ban');
            })
            ->when($request->has('comment_disabled'), function ($query) {
                $query->has('allow_comments', DefaultConstants::FALSE);
            })
            ->when($request->has('group'), function ($query) use ($request) {
                $query->where('group_id', $request->input('group'));
            });

        return view('backend.users.index')
            ->with([
                'users' => $request->has('results_per_page')
                    ? $users->paginate(intval($request->input('results_per_page')))
                    : $users->paginate(20),
                'total_users' => User::query()->count(),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.users.create');
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $user);
        }

        // update user group
        if (Group::getValue('admin_roles')) {
            $user->update([
                'group_id' => $request->input('role'),
            ]);
        }

        return MessageHelper::redirectMessage('User successfully created!', self::DEFAULT_ROUTE);
    }

    public function edit(User $user): View|Application|Factory
    {
        if ($user->group_id && ($user->group_id == RoleConstants::ADMIN && auth()->user()->group_id != RoleConstants::ADMIN)) {
            abort(403);
        }

        return view('backend.users.edit')
            ->with([
                'user' => $user,
            ]);
    }

    public function update(User $user, UserUpdateRequest $request): RedirectResponse
    {
        if ($user->group_id && ($user->group_id == RoleConstants::ADMIN && auth()->user()->group_id != RoleConstants::ADMIN)) {
            abort(403);
        }

        if ($request->input('removeArtwork')) {
            $user->clearMediaCollection('artwork');
        } elseif ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $user);
        }

        // update user group
        if (Group::getValue('admin_roles')) {
            $user->update([
                'group_id' => $request->input('role'),
            ]);
        }

        if ($request->input('deleteComments')) {
            $user->comments()->delete();
        }

        $user->update($request->all());

        if ($request->input('banned')) {
            if (auth()->id() == $user->id) {
                return redirect()->route('backend.users.edit', ['user' => $user->id])
                    ->with([
                        'status' => StatusConstants::FAILED,
                        'message' => "You can't ban yourself.",
                    ]);
            }

            $user->ban()->updateOrCreate([
                'reason' => $request->input('reason'),
                'end_at' => $request->input('end_at'),
            ]);
        }

        return MessageHelper::redirectMessage('User successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->group_id && ($user->group_id == RoleConstants::ADMIN && auth()->user()->group_id != RoleConstants::ADMIN)) {
            abort(403);
        }

        $user->delete();

        return MessageHelper::redirectMessage('User successfully deleted!');
    }

    public function batch(UserMassActionRequest $request)
    {
        if ($request->input('action') == 'change_usergroup') {
            if (Group::getValue('admin_roles')) {
                return view('backend.commons.mass_usergroup')
                    ->with([
                        'message' => 'Change usergroup',
                        'subMessage' => 'Change User Group for Chosen Users (<strong>' . count($request->input('ids')) . '</strong>)',
                        'action' => $request->input('action'),
                        'ids' => $request->input('ids'),
                    ]);
            } else {
                abort(403);
            }
        } elseif ($request->input('action') == 'save_change_usergroup') {
            if (Group::getValue('admin_roles')) {
                foreach ($request->input('ids') as $id) {
                    if ($user = User::withoutGlobalScopes()->find($id)) {
                        $user->update([
                            'group_id' => $request->input('role'),
                        ]);
                    }
                }

                return MessageHelper::redirectMessage('Users successfully updated!', self::DEFAULT_ROUTE);
            } else {
                abort(403);
            }
        } elseif ($request->input('action') == 'ban_user') {
            return view('backend.commons.mass_ban_user')
                ->with([
                    'message' => 'Ban user',
                    'subMessage' => 'Ban Chosen Users (<strong>' . count($request->input('ids')) . '</strong>)',
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == 'save_ban_user') {
            foreach ($request->input('ids') as $id) {
                if ($user = User::query()->withoutGlobalScopes()->where('id', $id)->whereNot('id', auth()->id())->first()) {
                    $user->ban()->updateOrCreate([
                        'reason' => $request->input('ban_reason'),
                        'end_at' => Carbon::parse(($request->input('ban_end_at'))),
                    ]);
                }
            }

            return MessageHelper::redirectMessage('Users successfully banned!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == 'delete_comment') {
            Comment::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $request->input('ids'))
                ->delete();

            return MessageHelper::redirectMessage("User's comments successfully deleted!");
        } elseif ($request->input('action') == 'delete') {
            User::destroy($request->input('ids'));

            return MessageHelper::redirectMessage('Users successfully deleted!');
        }
    }

    public function search(Request $request): JsonResponse
    {
        $result = User::query()
            ->where('name', 'LIKE', "%{$request->input('q')}%")
            ->paginate(20);

        return response()->json($result);
    }
}
