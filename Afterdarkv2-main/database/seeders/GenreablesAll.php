<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class GenreablesAll extends Seeder
{
    /**
     * This class is a wrapper (not parent) for calling any DataImportTable
     * seeders that populate the genreables table.
     */

    private array $genreable_types = [
        'Albums',
        'Artists',
        'Channels',
        'Slides',
        'Songs',
    ];

    private string $seederPrefix = 'DataImportTable';
    private string $destTable = 'genreables';

    public function run(): void
    {
        // Truncate the genreables table here
        // Individual genreables seeders should have empty_destination = false
        $destConnection = trim(config('data-import-table.defaults.dest_connection'));
        $destConn = Schema::connection($destConnection);
        $destDB = DB::connection($destConnection);
        $dest_row_count = $destDB->table($this->destTable)->count();

        if ($dest_row_count == 0) {
            $this->command->info('Destination table ' . $destConnection . '.' . $this->destTable . ' is empty.');
        } else {
            $this->command->warn('Destroying ' . $dest_row_count . ' rows in ' . $destConnection . '.' . $this->destTable . '.');
        }

        $destDB->table($this->destTable)->truncate();

        // Loop over targeted types
        foreach ($this->genreable_types as $gtype) {
            $seederClassName = Str::Singular($gtype) . 'Genreables';

            // Check for seeder class
            $seederClass = '\\Database\\Seeders\\' . $this->seederPrefix . $seederClassName;

            if (class_exists($seederClass)) {
                $this->command->info('Genreables: Found seeder for ' . $gtype . ': ' . $seederClassName);
            } else {
                $this->command->warn('Genreables: missing seeder for ' . $gtype . ': ' . $seederClassName);

                continue;
            }

            $this->command->info('Genreables: Calling seeder for ' . $gtype . ': ' . $seederClassName . ' ...');

            $this->call([$seederClass]);
        }
    }
}
