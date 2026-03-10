<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->mediumInteger('posi')->default(1);
            $table->string('name');
            $table->string('alt_name');
            $table->string('description')->nullable();
            $table->string('news_sort', 10)->nullable();
            $table->string('news_msort', 4)->nullable();
            $table->smallInteger('news_number')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('show_sub')->default(0);
            $table->boolean('allow_rss')->default(1);
            $table->boolean('disable_search')->default(0);
            $table->boolean('disable_main')->default(0);
            $table->boolean('disable_comments')->default(0);
            $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
