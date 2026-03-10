<?php

namespace App\Console\Commands;

use App\Services\MigrationService;
use Illuminate\Console\Command;

class MigrateSongsToAdventures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adventures:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command migrates all songs dedicated to adventures into separate table';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        MigrationService::createAdventureEntries();
    }
}
