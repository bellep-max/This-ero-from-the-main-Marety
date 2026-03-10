<?php

use App\Models\Channel;
use App\Models\PodcastCategory;
use App\Models\RadioCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('channel_podcast_category')) {
            Schema::create('channel_podcast_category', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Channel::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(PodcastCategory::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
        }
        Schema::create('channel_radio_category', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Channel::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(RadioCategory::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_podcast_category');
        Schema::dropIfExists('channel_radio_category');
    }
};
