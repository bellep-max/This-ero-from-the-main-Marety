<?php

use App\Models\Song;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('song_spotify_logs')) {
            Schema::create('song_spotify_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Song::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('spotify_id', 30)->nullable()->unique();
            $table->string('artwork_url')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('youtube', 12)->nullable();
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('song_spotify_logs');
    }
};
