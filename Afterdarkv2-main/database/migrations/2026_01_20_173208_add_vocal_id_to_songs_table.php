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
            if (Schema::hasColumn('songs', 'vocal_id')) {
                $table->dropForeign(['vocal_id']);
                $table->dropColumn('vocal_id');
            }
        });
    }
};
