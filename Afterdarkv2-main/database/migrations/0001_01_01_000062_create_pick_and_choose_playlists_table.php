<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pick_and_choose_playlists')) {
            Schema::create('pick_and_choose_playlists', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('desc');
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('pick_and_choose_playlists');
    }
};
