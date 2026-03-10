<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * ER-153: Rename groups table to musicengine_roles (if it exists).
 *
 * This migration handles existing databases where the 'groups' table wasn't
 * renamed immediately after creation. On fresh installs, the rename already
 * happened in 0001_01_01_000026, so this migration is a no-op.
 *
 * Note: users.group_id is intentionally left unchanged for the transitional phase.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Only rename if 'groups' table still exists (existing databases)
        // On fresh installs, the rename already happened in 0001_01_01_000026
        if (Schema::hasTable('groups')) {
            Schema::rename('groups', 'musicengine_roles');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('musicengine_roles')) {
            Schema::rename('musicengine_roles', 'groups');
        }
    }
};
