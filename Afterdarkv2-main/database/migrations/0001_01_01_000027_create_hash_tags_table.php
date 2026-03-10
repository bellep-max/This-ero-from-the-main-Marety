<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hash_tags')) {
            Schema::create('hash_tags', function (Blueprint $table) {
            $table->id();
            $table->integer('hashable_id')->default(0);
            $table->string('hashable_type', 50);
            $table->string('tag', 100)->nullable()->index();
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('hash_tags');
    }
};
