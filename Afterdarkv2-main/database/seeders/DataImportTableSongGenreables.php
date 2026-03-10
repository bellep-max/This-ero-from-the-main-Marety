<?php

namespace Database\Seeders;

class DataImportTableSongGenreables extends DataImportTable
{

    protected function preInsert(array &$item) {
        $data = [];
        $this->batchQueue = [];
        $genres = explode(',', $item['genre_id']);

        foreach ($genres as $genre_id) {
            $row = [
                'id' => null,
                'genreable_type' => 'App\\Models\\Song',
                'genreable_id' => $item['id'],
                'genre_id' => (int) $genre_id
            ];

            $data[] = $row;
        }

        $this->batchQueue = $data;
    }
}
