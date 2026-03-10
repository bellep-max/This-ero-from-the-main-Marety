<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasRoles;

    protected $fillable = ['name'];

    // ...
    public static function boot()
    {
        parent::boot();

        // here assign this team to a global user with global default role
        self::created(function ($model) {
           // temporary: get session team_id for restore at end
//           $session_team_id = getPermissionsTeamId();
           // set actual new team_id to package instance
//           setPermissionsTeamId($model);
           // get the admin user and assign roles/permissions on new team model
//           User::find('your_user_id')->assignRole('Super Admin');
           // restore session team_id to package instance using temporary value stored above
//           setPermissionsTeamId($session_team_id);
        });
    }
    // ...
}
