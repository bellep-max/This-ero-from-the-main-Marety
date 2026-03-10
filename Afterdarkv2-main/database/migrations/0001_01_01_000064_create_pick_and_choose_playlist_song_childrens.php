<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pick_and_choose_playlist_song_childrens')) {
            Schema::create('pick_and_choose_playlist_song_childrens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('song_id');
            $table->unsignedBigInteger('child_id');
            $table->timestamps();

            $table->foreign('song_id')->references('id')->on('pick_and_choose_playlist_song')->onDelete('cascade');
            $table->foreign('child_id')->references('id')->on('pick_and_choose_playlist_song')->onDelete('cascade');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('pick_and_choose_playlist_song_childrens');
    }
};
