<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds missing foreign key constraints to improve data integrity.
     * 
     * Note: The following foreign keys are NOT added due to column type mismatches:
     * - users.artist_id (integer) -> artists.id (bigInteger) - requires column type change
     * - users.group_id (unsignedInteger) -> groups.id (bigInteger) - requires column type change
     * 
     * These should be addressed in a future migration with doctrine/dbal for column alterations.
     * 
     * Most other tables already have proper foreign key constraints defined in their
     * original migrations (subscriptions, plans, songs, albums, podcasts, etc.).
     */
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
        }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        }
};
