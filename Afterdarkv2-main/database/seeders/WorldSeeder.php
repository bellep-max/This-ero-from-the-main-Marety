<?php

namespace Database\Seeders;

// Avoid memory_limit error
ini_set('memory_limit', '512M');

use Illuminate\Database\Seeder;
use Nnjeim\World\Actions\SeedAction;

class WorldSeeder extends Seeder
{
	public function run()
	{
		$this->call([
			SeedAction::class,
		]);
	}
}
