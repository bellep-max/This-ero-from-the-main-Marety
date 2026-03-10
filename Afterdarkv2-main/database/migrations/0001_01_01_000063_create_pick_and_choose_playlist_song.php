<?php

use App\Models\PickAndChoosePlaylist;
use App\Models\Song;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pick_and_choose_playlist_song')) {
            Schema::create('pick_and_choose_playlist_song', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Song::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(PickAndChoosePlaylist::class)->constrained(indexName: 'pick_and_choose_playlist_id_foreign')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('pick_and_choose_playlist_song');
    }
};
