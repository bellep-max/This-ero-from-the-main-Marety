<?php

use App\Models\Artist;
use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('podcasts')) {
            Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Artist::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignIdFor(Country::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Language::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('rss_feed_url')->nullable();
            $table->boolean('allow_comments')->default(1)->index();
            $table->mediumInteger('comment_count')->default(0);
            $table->boolean('allow_download')->default(0)->index();
            $table->integer('loves')->default(0);
            $table->boolean('explicit')->default(0)->index();
            $table->boolean('is_visible')->default(1);
            $table->boolean('approved')->nullable()->default(1)->index();
            $table->timestamps();

            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'description']);
            }
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
