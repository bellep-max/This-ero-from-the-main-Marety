<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('emails')) {
            Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index();
            $table->string('description')->nullable();
            $table->string('subject');
            $table->text('content');
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
