<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('musicengine_languages')) {
            Schema::create('musicengine_languages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50);
                $table->string('language', 50);
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('musicengine_languages');
    }
};
