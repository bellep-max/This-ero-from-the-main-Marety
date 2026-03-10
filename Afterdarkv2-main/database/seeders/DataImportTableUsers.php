<?php

namespace Database\Seeders;

class DataImportTableUsers extends DataImportTable
{
    protected function preInsert(array &$item) {
        if (in_array($item['id'], [1, 3, 45])) {
            $item['group_id'] = 1;
        } else {
            $item['group_id'] = 5;
        }
    }
}
