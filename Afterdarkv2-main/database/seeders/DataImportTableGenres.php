<?php

namespace Database\Seeders;

use \App\Models\Genre;

class DataImportTableGenres extends DataImportTable
{
    private array $toDisable = [16, 17];

    // Note: parent_id = 0 becomes null to satify genres_parent_id_foreign constraint.
    private array $addGenres = [
        ["name" => "TestGenres", "alt_name" => "testgenres", "discover" => 1, "priority" => 21, "parent_id" => null, "meta_title" => null, "description" => null, "meta_keywords" => null, "meta_description" => null],
        ["name" => "ASMR", "alt_name" => "asmr", "discover" => 1, "priority" => 1, "parent_id" => null, "meta_title" => "ASMR", "description" => null, "meta_keywords" => "", "meta_description" => null],
        ["name" => "Fiction", "alt_name" => "fiction", "discover" => 1, "priority" => 7, "parent_id" => null, "meta_title" => "Fiction", "description" => null, "meta_keywords" => "", "meta_description" => null],
        ["name" => "Romantic", "alt_name" => "romantic", "discover" => 1, "priority" => 19, "parent_id" => null, "meta_title" => "Romantic", "description" => null, "meta_keywords" => "", "meta_description" => null],
        ["name" => "Sleep", "alt_name" => "sleep", "discover" => 1, "priority" => 20, "parent_id" => null, "meta_title" => "Sleep", "description" => null, "meta_keywords" => "", "meta_description" => null],
        ["name" => "Erotica", "alt_name" => "erotica", "discover" => 1, "priority" => 3, "parent_id" => null, "meta_title" => "Erotica", "description" => null, "meta_keywords" => "", "meta_description" => null],
        ["name" => "Fan Fiction", "alt_name" => "fan-fiction", "discover" => 1, "priority" => 5, "parent_id" => null, "meta_title" => "Fan Fiction", "description" => null, "meta_keywords" => "", "meta_description" => null],
    ];

    protected function runMore() {

        if (is_array($this->addGenres)) {
            $this->command->info('Creating additional genres.');

            foreach ($this->addGenres as $genreData) {
                $genre = new Genre();
                $genre->fill($genreData);
                $genre->save();
            }
        }

        $this->command->info('Cleaning up genres.');

        $genres = Genre::orderBy('name')->get();

        $priority = 0;

        foreach ($genres as $genre) {
            $priority++;

            $genre->priority = $priority;
            $genre->parent_id = null;

            $this->command->info('Updating "' . $genre->name . '"');

            if (in_array($genre->id, $this->toDisable)) {
                $genre->discover = 0;
            }

            $genre->save();
        }
    }
}
