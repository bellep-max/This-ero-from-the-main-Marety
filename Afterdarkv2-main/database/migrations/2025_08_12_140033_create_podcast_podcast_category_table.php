<?php

use App\Models\Podcast;
use App\Models\PodcastCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('podcast_podcast_category')) {
            Schema::create('podcast_podcast_category', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Podcast::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(PodcastCategory::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('podcast_podcast_category');
    }
};
