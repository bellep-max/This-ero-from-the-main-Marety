<?php

namespace App\Models;

use App\Constants\RoleConstants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Group extends Model
{
    protected $table = 'musicengine_roles';

    protected $fillable = [
        'name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'group_id');
    }

    public function hasAccess(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public static function groupId()
    {
        if (!auth()->check()) {
            return RoleConstants::GUEST;
        }

        return isset(auth()->user()->group_id)
            ? auth()->user()->group_id
            : RoleConstants::GUEST;
    }

    private function hasPermission(string $permission): bool
    {
        $groupNumber = self::groupId();

        if (Cache::has('usergroup')) {
            $roles = Cache::get('usergroup');
        } else {
            $roles = self::all();
            Cache::forever('usergroup', $roles);
        }

        $role = $roles->firstWhere('id', $groupNumber);

        return $role[$permission] ?? false;
    }

    public static function getValue($permission, $default = null)
    {
        $groupNumber = self::groupId();

        if (Cache::has('usergroup')) {
            $roles = Cache::get('usergroup');
        } else {
            $roles = self::all();
            Cache::forever('usergroup', $roles);
        }

        $role = $roles->firstWhere('id', $groupNumber);

        return $role->permissions[$permission] ?? $default;
    }

    public static function getUserValue($permission, $user_id)
    {
        $user = User::find($user_id);

        $groupNumber = $user->group_id ?? RoleConstants::GUEST;

        if (Cache::has('usergroup')) {
            $groups = Cache::get('usergroup');
        } else {
            $groups = self::all();
            Cache::forever('usergroup', $groups);
        }

        $group = $groups->firstWhere('id', $groupNumber);

        return $group->permissions[$permission] ?? null;
    }
}
