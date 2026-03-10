<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('radio_categories')) {
            Schema::create('radio_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('radio_categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->smallInteger('priority')->default(0)->index();
            $table->string('name')->nullable();
            $table->string('alt_name')->nullable();
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
        Schema::dropIfExists('radio');
    }
};
