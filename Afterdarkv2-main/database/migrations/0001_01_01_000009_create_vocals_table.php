<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vocals')) {
            Schema::create('vocals', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);
            $table->string('code', 5);
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('vocals');
    }
};
