<?php

namespace App\Http\Controllers\Backend;

use App\Constants\RoleConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Role\RoleStoreRequest;
use App\Http\Requests\Backend\Role\RoleUpdateRequest;
use App\Models\Group;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class GroupController
{
    private const DEFAULT_ROUTE = 'backend.groups.index';

    public function index(): View|Application|Factory
    {
        return view('backend.groups.index')
            ->with([
                'groups' => Group::all(),
            ]);
    }

    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $groupId = Group::query()->insertGetId($request->validated());

        Cache::delete('usergroup');

        return redirect()->route('backend.groups.edit', $groupId);
    }

    public function edit(Group $group): View|Application|Factory
    {
        return view('backend.groups.edit')
            ->with([
                'group' => $group,
            ]);
    }

    public function update(Group $group, RoleUpdateRequest $request): RedirectResponse
    {
        $permissions = $request->input('save_role');
        $permissions['group_name'] = $request->input('group_name');

        $group->update([
            'permissions' => $permissions,
            'name' => $request->input('group_name'),
        ]);

        Cache::delete('usergroup');

        return MessageHelper::redirectMessage('Role successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Group $group): RedirectResponse
    {
        if (in_array($group->id, RoleConstants::getMainRoles())) {
            return redirect()
                ->route('backend.groups.index')
                ->with([
                    'status' => 'failed',
                    'message' => 'Role can not be deleted!',
                ]);
        }

        $group->delete();

        Cache::delete('usergroup');

        return MessageHelper::redirectMessage('Role successfully deleted!', self::DEFAULT_ROUTE);
    }
}
