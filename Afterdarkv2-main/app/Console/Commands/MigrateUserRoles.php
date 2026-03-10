<?php

namespace App\Console\Commands;

use App\Services\MigrationService;
use Illuminate\Console\Command;

class MigrateUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-roles:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command migrates all user roles to spatie's";

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        MigrationService::setUserRoles();
    }
}
