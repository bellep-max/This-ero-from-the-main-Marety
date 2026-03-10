<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Team;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $team = Team::create([
            //'id' => config('permission.default_team_id'),
            'name' => 'Global Default Team'
        ]);
        $team->save();

        $unauthRole = Role::updateOrCreate(
            [
                'name' => 'unauthenticated',
                'team_id' => null,
            ],
            [
                'name' => 'unauthenticated',
                'team_id' => null,
            ],
        );

        $listenerRole = Role::updateOrCreate(
            [
                'name' => RoleEnum::Listener,
                'team_id' => null,
            ],
            [
                'name' => RoleEnum::Listener,
                'team_id' => null,
            ]
        );

        $creatorRole = Role::updateOrCreate(
            [
                'name' => RoleEnum::Creator,
                'team_id' => null,
            ],
            [
                'name' => RoleEnum::Creator,
                'team_id' => null,
            ]
        );

        $adminRole = Role::updateOrCreate(
            [
                'name' => RoleEnum::Admin,
                'team_id' => null,
            ],
            [
                'name' => RoleEnum::Admin,
                'team_id' => null,
            ]
        );

        $superAdminRole = Role::updateOrCreate(
            [
                'name' => RoleEnum::SuperAdmin,
                'team_id' => null,
            ],
            [
                'name' => RoleEnum::SuperAdmin,
                'team_id' => null,
            ]
        );

        $this->setRolePermissions($unauthRole);
        $this->setRolePermissions($creatorRole);
        $this->setRolePermissions($listenerRole);
        $this->setRolePermissions($adminRole);
        $this->setRolePermissions($superAdminRole);
    }

    private function setRolePermissions(Role $role): void
    {
        switch ($role->name) {
            case RoleEnum::Listener:
                $permissions = [
                    Permission::updateOrCreate(
                        ['name' => PermissionEnum::PATRON_USER],
                        ['name' => PermissionEnum::PATRON_USER]
                    ),
                ];

                $role->syncPermissions($permissions);
                break;
            case RoleEnum::Creator:
                $permissions = [
                    Permission::updateOrCreate(
                        ['name' => PermissionEnum::PATRON_USER],
                        ['name' => PermissionEnum::PATRON_USER]
                    ),
                    Permission::updateOrCreate(
                        ['name' => PermissionEnum::SUBSCRIBE_TO_PREMIUM],
                        ['name' => PermissionEnum::SUBSCRIBE_TO_PREMIUM]
                    ),
                    Permission::updateOrCreate(
                        ['name' => PermissionEnum::UPLOAD_AUDIO],
                        ['name' => PermissionEnum::UPLOAD_AUDIO]
                    ),
                ];

                $role->syncPermissions($permissions);
                break;
            case RoleEnum::Admin:
            case RoleEnum::SuperAdmin:
                $permissions = [
                    Permission::updateOrCreate(
                        ['name' => PermissionEnum::ADMIN_ACCESS],
                        ['name' => PermissionEnum::ADMIN_ACCESS]
                    ),
                    Permission::updateOrCreate(
                        ['name' => PermissionEnum::PATRON_USER],
                        ['name' => PermissionEnum::PATRON_USER]
                    ),
                    Permission::updateOrCreate(
                        ['name' => PermissionEnum::SUBSCRIBE_TO_PREMIUM],
                        ['name' => PermissionEnum::SUBSCRIBE_TO_PREMIUM]
                    ),
                    Permission::updateOrCreate(
                        ['name' => PermissionEnum::UPLOAD_AUDIO],
                        ['name' => PermissionEnum::UPLOAD_AUDIO]
                    ),
                ];

                $role->syncPermissions($permissions);
                break;
            case 'unauthenticated':
                $permissions = [];

                $role->syncPermissions($permissions);
                break;
        }
    }
}
