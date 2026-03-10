<?php

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
        Schema::table('podcasts', function (Blueprint $table) {
            if (!Schema::hasColumn('podcasts', 'is_visible')) {
                $table->boolean('is_visible')->default(1)->after('visibility');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            if (Schema::hasColumn('podcasts', 'is_visible')) {
                $table->dropColumn('is_visible');
            }
        });
    }
};
