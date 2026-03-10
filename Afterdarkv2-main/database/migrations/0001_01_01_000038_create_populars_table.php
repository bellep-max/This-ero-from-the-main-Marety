<?php

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('populars')) {
            Schema::create('populars', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Song::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Artist::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->smallInteger('plays')->default(0);
            $table->smallInteger('favorites')->default(0);
            $table->smallInteger('collections')->default(0);
            $table->date('created_at')->nullable();
            $table->unique(['song_id', 'created_at'], 'trackId');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('populars');
    }
};
