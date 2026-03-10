<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('banner_tag');
            $table->string('description')->nullable();
            $table->text('code');
            $table->boolean('approved')->default(0);
            $table->boolean('short_place')->default(0);
            $table->boolean('bstick')->default(0);
            $table->boolean('main')->default(0);
            $table->string('category')->nullable();
            $table->string('group_level', 100)->default('all');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->boolean('fpage')->default(0);
            $table->boolean('innews')->default(0);
            $table->string('device_level', 10)->default('');
            $table->boolean('allow_views')->default(0);
            $table->integer('max_views')->default(0);
            $table->boolean('allow_counts')->default(0);
            $table->integer('max_counts')->default(0);
            $table->integer('views')->default(0);
            $table->integer('clicks')->default(0);
            $table->mediumInteger('rubric')->default(0);
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
