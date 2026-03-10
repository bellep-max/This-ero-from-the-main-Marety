<?php

use App\Models\Vocal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // Add is_visible column if it doesn't exist
            if (!Schema::hasColumn('songs', 'is_visible')) {
                $table->boolean('is_visible')->default(1)->index()->after('visibility');
            }

            // Add published_at column if it doesn't exist
            if (!Schema::hasColumn('songs', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('pending');
            }

            // Add uuid column if it doesn't exist
            if (!Schema::hasColumn('songs', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }

            // Add is_adventure column if it doesn't exist
            if (!Schema::hasColumn('songs', 'is_adventure')) {
                $table->boolean('is_adventure')->nullable()->default(0)->after('pending');
            }

            // Add is_patron column if it doesn't exist
            if (!Schema::hasColumn('songs', 'is_patron')) {
                $table->boolean('is_patron')->default(false)->after('is_adventure');
            }

            // Add referral_plays column if it doesn't exist
            if (!Schema::hasColumn('songs', 'referral_plays')) {
                $table->integer('referral_plays')->default(0)->after('plays');
            }

            // Add vocal_id column if it doesn't exist
            if (!Schema::hasColumn('songs', 'vocal_id')) {
                $table->foreignIdFor(Vocal::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            if (Schema::hasColumn('songs', 'is_visible')) {
                $table->dropColumn('is_visible');
            }
            if (Schema::hasColumn('songs', 'published_at')) {
                $table->dropColumn('published_at');
            }
            if (Schema::hasColumn('songs', 'uuid')) {
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('songs', 'is_adventure')) {
                $table->dropColumn('is_adventure');
            }
            if (Schema::hasColumn('songs', 'is_patron')) {
                $table->dropColumn('is_patron');
            }
            if (Schema::hasColumn('songs', 'referral_plays')) {
                $table->dropColumn('referral_plays');
            }
            if (Schema::hasColumn('songs', 'vocal_id')) {
                $table->dropForeign(['vocal_id']);
                $table->dropColumn('vocal_id');
            }
        });
    }
};
