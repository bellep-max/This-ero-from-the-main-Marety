<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('musicengine_regions')) {
            Schema::create('musicengine_regions', function (Blueprint $table) {
                $table->id();
                $table->char('name', 52)->default('');
                $table->string('alt_name')->nullable()->index();
                $table->boolean('fixed')->default(0)->index();
                $table->boolean('is_visible')->default(1)->index();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('musicengine_regions');
    }
};
