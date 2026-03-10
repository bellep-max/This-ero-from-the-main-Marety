<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('metatags')) {
            Schema::create('metatags', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('priority')->nullable()->index();
            $table->string('url')->default('');
            $table->string('info')->nullable();
            $table->string('page_title');
            $table->text('page_description')->nullable();
            $table->string('page_keywords')->nullable();
            $table->boolean('auto_keyword')->default(0);
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('metatags');
    }
};
