<?php

use App\Models\Artist;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('artist_spotify_logs')) {
            Schema::create('artist_spotify_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Artist::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('spotify_id', 30)->nullable()->unique();
            $table->string('artwork_url')->nullable();
            $table->boolean('fetched')->default(0)->index();
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('artist_spotify_logs');
    }
};
