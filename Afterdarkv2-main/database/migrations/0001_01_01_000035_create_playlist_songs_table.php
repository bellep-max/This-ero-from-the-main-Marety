<?php

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('playlist_songs')) {
            Schema::create('playlist_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Song::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Playlist::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->smallInteger('priority')->default(0);
            $table->unique(['song_id', 'playlist_id'], 'PlaylistSong');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('playlist_songs');
    }
};
