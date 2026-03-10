<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ER-153: Create groups table and immediately rename to musicengine_roles.
 *
 * The rename happens immediately so that subsequent migrations (services, plans)
 * can reference Group::$table ('musicengine_roles') via foreign key constraints.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->longText('permissions');
                $table->timestamps();
            });

            // Immediately rename to musicengine_roles so FK constraints in later migrations work
            Schema::rename('groups', 'musicengine_roles');
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('musicengine_roles');
    }
};
