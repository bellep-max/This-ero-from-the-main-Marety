<?php

namespace Database\Seeders;

use App\Enums\VocalGenderEnum;
use App\Models\Vocal;
use Illuminate\Database\Seeder;

class VocalSeeder extends Seeder
{
    public function run(): void
    {
        foreach (VocalGenderEnum::getVoices() as $code => $name) {
            Vocal::updateOrCreate(['code' => $code, 'name' => $name]);
        }
    }
}
