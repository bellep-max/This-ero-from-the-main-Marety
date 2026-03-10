<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\User;
use Illuminate\Console\Command;

class AddRoleToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:addrole {email} {rolename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $rolename = $this->argument('rolename');

        $user = User::where('email', $email)->firstOrFail();
        $role = Group::where('name', $rolename)->firstOrFail();

        $user->assignRole($role->id);

        $this->info("Роль $rolename успешно добавлена пользователю $email.");

        return 0;
    }
}
