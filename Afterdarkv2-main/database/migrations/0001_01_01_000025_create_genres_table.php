<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('genres')) {
            Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('genres')->cascadeOnDelete()->cascadeOnUpdate();
            $table->mediumInteger('priority')->default(1);
            $table->boolean('discover')->index()->default(1);
            $table->string('name');
            $table->string('alt_name')->index();
            $table->string('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};
