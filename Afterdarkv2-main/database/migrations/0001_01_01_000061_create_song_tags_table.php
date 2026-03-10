<?php

use App\Models\Song;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('song_tags')) {
            Schema::create('song_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Song::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('tag')->default('')->index();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('song_tags');
    }
};
