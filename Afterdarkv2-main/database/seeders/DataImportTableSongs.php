<?php

namespace Database\Seeders;

class DataImportTableSongs extends DataImportTable
{
    protected function preInsert(array &$item) {
        if (array_key_exists('explicit', $item)) {
            unset($item['explicit']);
        }

        $item['is_explicit'] = 1;

        $item['published_at'] = $item['released_at'] . ' 00:00:00';
    }
}
