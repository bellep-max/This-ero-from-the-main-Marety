<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('post_logs')) {
            Schema::create('post_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('post_id')->default(0)->index('news_id');
            $table->string('expires', 15)->default('')->index('expires');
            $table->boolean('action')->default(0);
            $table->string('move_category')->nullable();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('post_logs');
    }
};
