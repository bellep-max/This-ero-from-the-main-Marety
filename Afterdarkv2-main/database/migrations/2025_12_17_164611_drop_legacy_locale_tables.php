<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * ER-153: Drop legacy MusicEngine locale tables to make way for nnjeim/world package.
 *
 * This migration drops the existing locale tables (countries, cities, regions, country_languages)
 * which will be replaced by the nnjeim/world package tables.
 *
 * Tables are dropped in order to respect foreign key constraints:
 * 1. country_languages (depends on countries)
 * 2. cities (depends on countries)
 * 3. countries (depends on regions)
 * 4. regions (no dependencies)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop tables in order respecting foreign key constraints
        // DO NOT DROP: renamed tables are not in conflict
        // Schema::dropIfExists('musicengine_country_languages');
        // Schema::dropIfExists('musicengine_cities');
        // Schema::dropIfExists('musicengine_countries');
        // Schema::dropIfExists('musicengine_regions');
    }

    public function down(): void
    {
        // The down migration is intentionally empty.
        // The legacy tables should not be recreated - use nnjeim/world package instead.
    }
};
