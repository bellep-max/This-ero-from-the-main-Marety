<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Team;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {

		$role = Role::findByName(RoleEnum::SuperAdmin->value);
		//$team = Team::find(config('permission.default_team_id'));

		//$role->team_id = $team->id;
		$role->team_id = config('permission.default_team_id');
        setPermissionsTeamId(config('permission.default_team_id'));

        $superAdminUser = User::updateOrCreate([
            'name' => 'Erocast Admin',
            'username' => 'ErocastAdmin',
            'password' => bcrypt('password'),
            'session_id' => null,
            'email' => 'erocastme@gmail.com',
            'email_verified' => 0,
            'email_verified_code' => null,
            'email_verified_at' => null,
            'bio' => '',
            'gender' => 'O',
            'birth' => '2021-10-15',
            'allow_comments' => 1,
            'comment_count' => 0,
            'restore_queue' => 0,
            'persist_shuffle' => 0,
            'play_pause_fade' => 0,
            'crossfade_amount' => 5,
            'hd_streaming' => 1,
            'activity_privacy' => 0,
            'notif_follower' => 0,
            'notif_playlist' => 0,
            'notif_shares' => 0,
            'notif_features' => 0,
            'trialed' => 0,
            'payment_method' => null,
            'payment_paypal' => null,
            'payment_bank' => null,
            'linktree_link' => null,
        ]);

        $superAdminUser->assignRole($role);
    }
}
