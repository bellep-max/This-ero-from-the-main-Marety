<?php

use App\Models\Genre;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('genreables')) {
            Schema::create('genreables', function (Blueprint $table) {
            $table->id();
            $table->morphs('genreable');
            $table->foreignIdFor(Genre::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('genreables');
    }
};
