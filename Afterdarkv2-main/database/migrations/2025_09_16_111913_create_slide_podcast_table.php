<?php

use App\Models\Podcast;
use App\Models\Slide;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('slide_podcast')) {
            Schema::create('slide_podcast', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Slide::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Podcast::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('slide_podcast');
    }
};
