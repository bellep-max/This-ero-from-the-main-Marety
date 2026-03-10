<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ER-153: Update users.country_id FK to reference nnjeim/world countries table.
 *
 * This migration runs after the nnjeim/world package creates its countries table.
 * It re-adds the foreign key constraint on users.country_id to reference the new
 * countries table from the nnjeim/world package.
 *
 * Note: Existing country_id values in users table will need to be migrated
 * separately to match the new country IDs from nnjeim/world.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add foreign key constraint to reference nnjeim/world countries table
            // The constraint is nullable to allow users without a country
            //$table->foreign('country_id')
            //    ->references('id')
            //    ->on('musicengine_countries')
            //    ->cascadeOnUpdate()
            //    ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //$table->dropForeign(['country_id']);
        });
    }
};
